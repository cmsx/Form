<?php

namespace CMSx\Form;

use CMSx\HTML;
use CMSx\Form;

/**
 * Базовый класс, на основе которого создаются элементы формы - INPUT`ы, SELECT`ы и т.п.
 */
abstract class Element
{
  /** @var Form */
  protected $form;

  protected $id;
  protected $name;
  protected $info;
  protected $label;
  protected $field;
  protected $value;
  protected $regexp;
  protected $filter;
  protected $errors;
  protected $default;
  protected $options;
  protected $form_name;
  protected $attributes;
  protected $ignore_keys;
  protected $is_required;
  protected $placeholder;
  protected $is_validated;
  protected $label_as_placeholder;

  /** Шаблон обязательно поле не заполнено */
  protected $tmpl_err_required = 'Обязательное поле "%s" не заполнено';
  /** Шаблон ошибки по фильтру или регулярному выражению */
  protected $tmpl_err_wrong = 'Поле "%s" заполнено некорректно';
  /** Шаблон ошибки при выборе из списка */
  protected $tmpl_err_option = 'Поле "%s" не содержит такого варианта';

  function __construct($field, $label = null, Form $form = null)
  {
    $this->field = $field;
    $this->name  = $field;
    $this->id    = $field;
    $this->label = $label;
    if ($form) {
      $this->setForm($form);
    }
    $this->init();
  }

  function __toString()
  {
    return $this->render();
  }

  /** Отрисовка элемента */
  public function render()
  {
    return HTML::Input($this->getName(), $this->getValue(), $this->getAttributes());
  }

  /** Проверка значения */
  public function validate($data)
  {
    $this->errors       = null;
    $this->value        = $data;
    $this->is_validated = true;

    if (!empty($data)) {
      if ($this->options && !$this->checkValueIsInOptions($data)) {
        $this->errors[] = sprintf($this->tmpl_err_option, $this->label);
      }
      if ($this->regexp && !preg_match($this->regexp, $data)) {
        $this->errors[] = sprintf($this->tmpl_err_wrong, $this->label);
      }
      if ($this->filter && !call_user_func_array($this->filter, array($data))) {
        $this->errors[] = sprintf($this->tmpl_err_wrong, $this->label);
      }
    } else {
      if ($this->is_required) {
        $this->errors[] = sprintf($this->tmpl_err_required, $this->label);
      }
    }

    if ($this->hasErrors()) {
      return false;
    }

    return true;
  }

  /** Атрибуты для HTML тега инпута */
  public function setAttributes($attribute)
  {
    $this->attributes = $attribute;

    return $this;
  }

  /** Массив атрибутов для HTML тега инпута */
  public function getAttributes()
  {
    return HTML::AttrConvert(
      $this->attributes, array(
        'id'          => $this->getId(),
        'placeholder' => $this->getPlaceholder()
      )
    );
  }

  /** Значение по-умолчанию. Используется только при выводе пользователю. */
  public function setDefaultValue($default)
  {
    $this->default = $default;

    return $this;
  }

  /** Значение по-умолчанию. Используется только при выводе пользователю. */
  public function getDefaultValue()
  {
    return $this->default;
  }

  /** Проверка наличия ошибок после проверки */
  public function hasErrors()
  {
    return is_array($this->errors) && count($this->errors) > 0;
  }

  /** Установка ошибки на поле. $clean - очистить текущие ошибки */
  public function addError($error, $clean = false)
  {
    if ($clean) {
      $this->errors = array();
    }

    $this->errors[] = $error;

    return $this;
  }

  /** Если в форме есть ошибки - возвращает массив ошибок. Если нет, false */
  public function getErrors()
  {
    return $this->hasErrors() ? $this->errors : false;
  }

  /** Имя поля используемое для адресации в форме */
  public function setField($field)
  {
    $this->field = $field;

    return $this;
  }

  /** Имя поля используемое для адресации в форме */
  public function getField()
  {
    return $this->field;
  }

  /** Фильтр - любой callable возвращающий bool */
  public function setFilter($filter)
  {
    $this->filter = $filter;

    return $this;
  }

  /** Фильтр - любой callable возвращающий bool */
  public function getFilter()
  {
    return $this->filter;
  }

  /** Атрибут ID для элемента */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  /** Атрибут ID для элемента */
  public function getId()
  {
    return 'form-' . (!empty($this->form_name) ? $this->form_name . '-' : '') . $this->id;
  }

  /** Описание поля */
  public function setInfo($info)
  {
    $this->info = $info;

    return $this;
  }

  /** Описание поля */
  public function getInfo()
  {
    return $this->info;
  }

  /** Является ли поле обязательным */
  public function setIsRequired($is_required = true)
  {
    $this->is_required = (bool)$is_required;

    return $this;
  }

  /** Является ли поле обязательным */
  public function getIsRequired()
  {
    return $this->is_required;
  }

  /** Название поля для отображения */
  public function setLabel($label)
  {
    $this->label = $label;

    return $this;
  }

  /** Название поля для отображения */
  public function getLabel()
  {
    return !empty($this->label) ? $this->label : ucfirst($this->field);
  }

  /** Использовать название как плейсхолдер */
  public function setLabelAsPlaceholder($label_as_placeholder = true)
  {
    $this->label_as_placeholder = $label_as_placeholder;

    return $this;
  }

  /** Использовать название как плейсхолдер */
  public function getLabelAsPlaceholder()
  {
    return $this->label_as_placeholder;
  }

  /** Атрибут name для тегов */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /** Атрибут name для тегов */
  public function getName()
  {
    return !empty($this->form_name)
      ? $this->form_name . '[' . $this->name . ']'
      : $this->name;
  }

  public function setPlaceholder($placeholder)
  {
    $this->placeholder = $placeholder;

    return $this;
  }

  /** Атрибут плейсхолдер */
  public function getPlaceholder()
  {
    return !empty($this->placeholder)
      ? $this->placeholder
      : ($this->label_as_placeholder
        ? $this->getLabel()
        : '');
  }

  /** Регулярка для проверки входного значения */
  public function setRegexp($regexp)
  {
    $this->regexp = $regexp;

    return $this;
  }

  /** Регулярка для проверки входного значения */
  public function getRegexp()
  {
    return $this->regexp;
  }

  /**
   * Значение поля формы
   * @param bool $clean - экранировать ли вывод
   */
  public function getValue($clean = true)
  {
    if (!$this->is_validated) {
      return $this->getDefaultValue();
    }

    return !$this->hasErrors() && $this->value
      ? ($clean
        ? htmlspecialchars($this->value)
        : $this->value
      )
      : false;
  }

  /** "Сырое" значение переданное в элемент, даже если оно не прошло валидацию */
  public function getTaintedValue()
  {
    return $this->value;
  }

  /** Имя формы, в которой находится элемент */
  public function setFormName($form_name)
  {
    $this->form_name = $form_name;

    return $this;
  }

  /** Имя формы, в которой находится элемент */
  public function getFormName()
  {
    return $this->form_name;
  }

  /** Запускалась ли валидация. Не означает что форма валидна! см. getIsValid() */
  public function getIsValidated()
  {
    return (bool)$this->is_validated;
  }

  /** Валидная ли форма */
  public function getIsValid()
  {
    return !$this->hasErrors() && $this->getIsValidated();
  }

  /** Форма, к которой относится элемент */
  public function setForm(\CMSx\Form $form)
  {
    $this->form      = $form;
    $this->form_name = $form->getName();

    return $this;
  }

  /** Шаблон SPRINTF для ошибки выбора из опций. Единственный параметр %s - название поля */
  public function setTmplErrOption($tmpl_err_option)
  {
    $this->tmpl_err_option = $tmpl_err_option;

    return $this;
  }

  /** Шаблон SPRINTF для ошибки выбора из опций. Единственный параметр %s - название поля */
  public function getTmplErrOption()
  {
    return $this->tmpl_err_option;
  }

  /** Шаблон SPRINTF для ошибки обязательного поля. Единственный параметр %s - название поля */
  public function setTmplErrRequired($tmpl_err_required)
  {
    $this->tmpl_err_required = $tmpl_err_required;

    return $this;
  }

  /** Шаблон SPRINTF для ошибки обязательного поля. Единственный параметр %s - название поля */
  public function getTmplErrRequired()
  {
    return $this->tmpl_err_required;
  }

  /** Шаблон SPRINTF для ошибки неправильно заполненного поля. Единственный параметр %s - название поля */
  public function setTmplErrWrong($tmpl_err_wrong)
  {
    $this->tmpl_err_wrong = $tmpl_err_wrong;

    return $this;
  }

  /** Шаблон SPRINTF для ошибки неправильно заполненного поля. Единственный параметр %s - название поля */
  public function getTmplErrWrong()
  {
    return $this->tmpl_err_wrong;
  }

  /**
   * Форма, к которой относится элемент
   * @return \CMSx\Form
   */
  public function getForm()
  {
    return $this->form;
  }

  /**
   * Опции используются для SELECT или групп чекбоксов\радиобатонов
   * Можно передавать набор опций прямо в функцию, или через массив $options
   */
  protected function setOptions($options, $_ = null)
  {
    if (is_array($options) || is_null($options)) {
      $this->options = $options;
    } else {
      $this->options = func_get_args();
    }

    return $this;
  }

  /** Использовать значения массива опций как значения поля */
  protected function setOptionsIgnoreKeys($on = true)
  {
    $this->ignore_keys = $on;

    return $this;
  }

  /** Опции используются для SELECT или групп чекбоксов\радиобатонов */
  protected function getOptions()
  {
    return $this->options;
  }

  /** Проверка есть ли значение в списке опций */
  protected function checkValueIsInOptions($value)
  {
    return $this->ignore_keys
      ? in_array($value, $this->options)
      : array_key_exists($value, $this->options);
  }

  /** Для настроек при наследовании */
  protected function init()
  {

  }
}