<?php

namespace CMSx;

use CMSx\Form\Element;
use CMSx\Form\Element\Caption;
use CMSx\Form\Element\Checkbox;
use CMSx\Form\Element\CheckboxListing;
use CMSx\Form\Element\Hidden;
use CMSx\Form\Element\Input;
use CMSx\Form\Element\Password;
use CMSx\Form\Element\RadioListing;
use CMSx\Form\Element\Select;
use CMSx\Form\Element\Textarea;

class Form
{
  /** Атрибут action для формы */
  protected $action;
  /** Имя формы, также используется для поля name в инпутах */
  protected $name;
  /** Атрибуты для тега form */
  protected $form_attributes;
  /** @var Element[] Поля формы */
  protected $fields;
  /** Конфигурация кнопки отправки */
  protected $submit = array(
    'attr' => null,
    'text' => 'Отправить'
  );
  /** Массив ошибок формы */
  protected $errors = array();
  /** Вызывалась ли валидация формы */
  protected $is_validated;

  /** Шаблон для отрисовки всех полей. fields, submit */
  protected $tmpl_fields = "<table>\n%s%s</table>\n";
  /** Шаблон для отрисовки кнопки отправки */
  protected $tmpl_submit = "<tr><td colspan=\"2\">%s</td></tr>\n";
  /** Шаблон для отрисовки поля. is_required, label, input, info, errors */
  protected $tmpl_element = "<tr><th>%s%s:</th><td>%s %s</td></tr>\n";
  /** Шаблон для отрисовки чекбокса. is_required, label, input, info, errors */
  protected $tmpl_checkbox = "<tr><td colspan=\"2\">%3\$s %4\$s</td></tr>\n";
  /** Шаблон для отрисовки заголовка (Caption). is_required, label, input, info, errors */
  protected $tmpl_caption = "<tr><td colspan=\"2\">%3\$s\n%4\$s</td></tr>\n";
  /** Шаблон для отрисовки скрытого поля. is_required, label, input, info, errors */
  protected $tmpl_hidden = "%3\$s\n";
  /** Шаблон для отрисовки текстового поля. is_required, label, input, info, errors */
  protected $tmpl_textarea = "<tr><th colspan=\"2\">%s%s</th></tr><tr><td colspan=\"2\">%s %s</td></tr>\n";

  function __construct($name = null)
  {
    if ($name) {
      $this->setName($name);
    }
  }

  function __toString()
  {
    try {
      return $this->render();
    } catch (\Exception $e) {
    }
  }

  /** Проверка и выполнение действия формы */
  public function validateAndProcess($data = null)
  {
    return $this->validate($data) && $this->process();
  }

  /** Для реализации действий формы при наследовании */
  public function process()
  {
    return true;
  }

  /** Валидация формы */
  public function validate($data = null)
  {
    $this->errors       = array();
    $this->is_validated = true;

    if (is_null($data)) {
      if (!$this->isSent()) {
        return false;
      }
      $data = !empty($this->name) ? $_POST[$this->name] : $_POST;
    }

    $this->validateFields($this->beforeValidation($data));
    $this->afterValidation($data);

    return $this->isValid();
  }

  /** Проверка, отправлена ли форма */
  public function isSent()
  {
    return $this->name ? isset($_POST[$this->name]) : count($_POST) > 0;
  }

  /** Валидна ли форма */
  public function isValid()
  {
    return $this->isValidated() && !$this->hasErrors();
  }

  /** Была ли форма на валидации */
  public function isValidated()
  {
    return (bool)$this->is_validated;
  }

  /** Есть ли ошибки в форме */
  public function hasErrors()
  {
    return count($this->errors) > 0;
  }

  /**
   * Получение ошибок
   * $plain - объединить все ошибки.
   * Если $plain === true, склеивание через \n, иначе используется $plain
   */
  public function getErrors($plain = false)
  {
    if (!$this->hasErrors()) {
      return false;
    }

    if ($plain) {
      $plain = $plain === true ? "\n" : $plain;

      $out = array();
      foreach ($this->errors as $w => $arr) {
        foreach ($arr as $str) {
          $out[] = $str;
        }
      }

      return join($plain, $out);
    }

    return $this->errors;
  }

  /** Установка произвольной ошибки */
  public function addError($msg)
  {
    $this->errors['_user'][] = $msg;

    return $this;
  }

  /** Установка значений по умолчанию */
  public function setDefaultValues($data)
  {
    foreach ($data as $field => $value) {
      if ($f = $this->field($field)) {
        $f->setDefaultValue($value);
      }
    }

    return $this;
  }

  /** Значение поля формы. */
  public function getValue($field)
  {
    if (!empty($field) && $f = $this->field($field)) {
      return $f->getValue(false);
    }

    return false;
  }

  /** Значение всех полей формы */
  public function getValues()
  {
    $out = array();
    foreach ($this->fields as $f => $el) {
      $out[$f] = $el->getValue(false);
    }

    return $out;
  }

  /** Имя формы используемое для адресации */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /** Имя формы используемое для адресации */
  public function getName()
  {
    return $this->name;
  }

  /** Атрибут action для формы */
  public function setAction($action)
  {
    $this->action = $action;

    return $this;
  }

  /** Атрибут action для формы */
  public function getAction()
  {
    return $this->action;
  }

  /** Отрисовка формы */
  public function render($attr = null)
  {
    return HTML::Form(
      sprintf(
        $this->tmpl_fields,
        $this->renderFields(),
        $this->renderSubmit()
      ),
      $this->getAction(),
      null,
      $this->getFormAttributes($attr)
    );
  }

  /**
   * Отрисовка кнопки отправить форму.
   * $only_button - вывести только кнопку, без окружения
   */
  public function renderSubmit($only_button = false)
  {
    $b = HTML::Button($this->submit['text'], true, $this->submit['attr']);

    return $only_button ? $b : sprintf($this->tmpl_submit, $b);
  }

  /** Отрисовка полей формы */
  public function renderFields()
  {
    $out = '';
    if ($this->fields) {
      foreach ($this->fields as $el) {
        $out .= $this->renderElement($el);
      }
    }

    return $out;
  }

  /** Отрисовка одного элемента формы */
  public function renderElement(Element $element)
  {
    if ($element instanceof Checkbox) {
      return $this->renderElementByTemplate($this->tmpl_checkbox, $element);
    } elseif ($element instanceof Caption) {
      return $this->renderElementByTemplate($this->tmpl_caption, $element);
    } elseif ($element instanceof Hidden) {
      return $this->renderElementByTemplate($this->tmpl_hidden, $element);
    } elseif ($element instanceof Textarea) {
      return $this->renderElementByTemplate($this->tmpl_textarea, $element);
    }

    return $this->renderElementByTemplate($this->tmpl_element, $element);
  }

  /** @return Element */
  public function field($field)
  {
    return isset($this->fields[$field]) ? $this->fields[$field] : false;
  }

  /** @return Form\Element\Caption */
  public function addCaption($field, $label, $info = null)
  {
    $c = new Caption($field, $label, $this);
    if ($info) {
      $c->setInfo($info);
    }

    return $this->fields[$field] = $c;
  }

  /** @return Form\Element\Checkbox */
  public function addCheckbox($field, $label = null, $value = null)
  {
    $c = new Checkbox($field, $label, $this);
    if ($value) {
      $c->setCheckboxValue($value);
    }

    return $this->fields[$field] = $c;
  }

  /** @return Form\Element\CheckboxListing */
  public function addCheckboxListing($field, $label = null, $options = null)
  {
    $l = new CheckboxListing($field, $label, $this);
    if ($options) {
      $l->setOptions($options);
    }

    return $this->fields[$field] = $l;
  }

  /** @return Form\Element\Hidden */
  public function addHidden($field, $label = null, $value = null)
  {
    $h = new Hidden($field, $label, $this);
    if ($value) {
      $h->setDefaultValue($value);
    }

    return $this->fields[$field] = $h;
  }

  /** @return Form\Element\Password */
  public function addPassword($field, $label = null)
  {
    return $this->fields[$field] = new Password($field, $label, $this);
  }

  /** @return Form\Element\RadioListing */
  public function addRadioListing($field, $label = null, $options = null)
  {
    $l = new RadioListing($field, $label, $this);
    if ($options) {
      $l->setOptions($options);
    }

    return $this->fields[$field] = $l;
  }

  /** @return Form\Element\Select */
  public function addSelect($field, $label = null, $options = null)
  {
    $s = new Select($field, $label, $this);
    if ($options) {
      $s->setOptions($options);
    }

    return $this->fields[$field] = $s;
  }

  /** @return Form\Element\Textarea */
  public function addTextarea($field, $label = null)
  {
    return $this->fields[$field] = new Textarea($field, $label, $this);
  }

  /** @return Form\Element\Input */
  public function addInput($field, $label = null)
  {
    return $this->fields[$field] = new Input($field, $label, $this);
  }

  /** Атрибуты тега формы */
  public function setFormAttributes($form_attr)
  {
    $this->form_attributes = $form_attr;

    return $this;
  }

  /** Атрибуты тега формы */
  public function getFormAttributes($attr = null)
  {
    $a = HTML::AttrConvert($attr ? : $this->form_attributes);
    if ($this->name) {
      $a['id'] = 'form-' . $this->name;
    }

    return $a;
  }

  /**
   * Свойства кнопки отправить
   * Если $attr = null, то он не изменяется.
   * Чтобы явно затереть атрибуты, нужно передать false или 0
   */
  public function setSubmit($text, $attr = null)
  {
    $this->submit['text'] = $text;
    if (!is_null($attr)) {
      if (!empty($attr)) {
        $this->submit['attr'] = $attr;
      } else {
        $this->submit['attr'] = null;
      }
    }

    return $this;
  }

  /** Шаблон для отрисовки заголовка. is_required, label, input, info, errors */
  public function setTmplCaption($tmpl_caption)
  {
    $this->tmpl_caption = $tmpl_caption;

    return $this;
  }

  /** Шаблон для отрисовки заголовка. is_required, label, input, info, errors */
  public function getTmplCaption()
  {
    return $this->tmpl_caption;
  }

  /** Шаблон для отрисовки чекбокса. is_required, label, input, info, errors */
  public function setTmplCheckbox($tmpl_checkbox)
  {
    $this->tmpl_checkbox = $tmpl_checkbox;

    return $this;
  }

  /** Шаблон для отрисовки чекбокса. is_required, label, input, info, errors */
  public function getTmplCheckbox()
  {
    return $this->tmpl_checkbox;
  }

  /** Шаблон для отрисовки поля. is_required, label, input, info, errors */
  public function setTmplElement($tmpl_element)
  {
    $this->tmpl_element = $tmpl_element;

    return $this;
  }

  /** Шаблон для отрисовки поля. is_required, label, input, info, errors */
  public function getTmplElement()
  {
    return $this->tmpl_element;
  }

  /** Шаблон для отрисовки всех полей. fields, submit */
  public function setTmplFields($tmpl_fields)
  {
    $this->tmpl_fields = $tmpl_fields;

    return $this;
  }

  /** Шаблон для отрисовки всех полей. fields, submit */
  public function getTmplFields()
  {
    return $this->tmpl_fields;
  }

  /** Шаблон для отрисовки скрытого поля. is_required, label, input, info, errors */
  public function setTmplHidden($tmpl_hidden)
  {
    $this->tmpl_hidden = $tmpl_hidden;

    return $this;
  }

  /** Шаблон для отрисовки скрытого поля. is_required, label, input, info, errors */
  public function getTmplHidden()
  {
    return $this->tmpl_hidden;
  }

  /** Шаблон для отрисовки кнопки отправки */
  public function setTmplSubmit($tmpl_submit)
  {
    $this->tmpl_submit = $tmpl_submit;

    return $this;
  }

  /** Шаблон для отрисовки кнопки отправки */
  public function getTmplSubmit()
  {
    return $this->tmpl_submit;
  }

  /** Шаблон для отрисовки текстового поля. is_required, label, input, info, errors */
  public function setTmplTextarea($tmpl_textarea)
  {
    $this->tmpl_textarea = $tmpl_textarea;

    return $this;
  }

  /** Шаблон для отрисовки текстового поля. is_required, label, input, info, errors */
  public function getTmplTextarea()
  {
    return $this->tmpl_textarea;
  }

  /**
   * Отрисовка элемента формы по шаблону
   * is_required, label, input, info, errors
   */
  protected function renderElementByTemplate($tmpl, Element $element)
  {
    return sprintf(
      $tmpl,
      $this->renderIsRequired($element->getIsRequired()),
      $element->getLabel(),
      $element->render(),
      $element->getInfo(),
      $this->renderElementErrors($element->getErrors())
    );
  }

  /** Отрисовка ошибок для элемента */
  protected function renderElementErrors($errors)
  {
    return join('<br />', (array)$errors);
  }

  /** Отрисовка значка, что поле обязательное */
  protected function renderIsRequired($is_required)
  {
    return $is_required ? ' * ' : '';
  }

  /** Проверить все поля формы */
  protected function validateFields($data)
  {
    if (!$this->fields) {
      $this->addError('В форме нет полей');
      return false;
    }

    if (is_array($data)) {
      foreach ($this->fields as $field => $element) {
        $val = isset ($data[$field]) ? $data[$field] : null;
        if (!$element->validate($val)) {
          $this->errors[$field] = $element->getErrors();
        }
      }
    }
  }

  /**
   * Метод для пред-проверки или изменения входных данных.
   * Вызывается до валидации. Должен возвращать $data.
   * Если будут установлены ошибки, validate() вернет false.
   */
  protected function beforeValidation($data)
  {
    return $data;
  }

  /**
   * Метод для пост-проверки. Вызывается после валидации.
   * Если будут установлены ошибки, validate() вернет false.
   */
  protected function afterValidation($data)
  {
  }
}