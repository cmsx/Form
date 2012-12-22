<?php

namespace CMSx;

use CMSx\Form\Element;
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

  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setAction($action)
  {
    $this->action = $action;

    return $this;
  }

  public function getAction()
  {
    return $this->action;
  }

  /** Отрисовка формы */
  public function render($attr = null)
  {
    return HTML::Form(null, $this->getAction(), null, $this->getFormAttributes($attr));
  }

  /** @return Element */
  public function field($field)
  {
    return isset($this->fields[$field]) ? $this->fields[$field] : false;
  }

  /** @return Form\Element\Checkbox */
  public function addCheckbox($field, $label = null)
  {
    return $this->fields[$field] = new Checkbox($field, $label, $this);
  }

  /** @return Form\Element\Checkbox */
  public function addCheckboxListing($field, $label = null, $options = null)
  {
    $l = new CheckboxListing($field, $label, $this);
    if ($options) {
      $l->setOptions($options);
    }
    return $this->fields[$field] = $l;
  }

  /** @return Form\Element\Hidden */
  public function addHidden($field, $label = null)
  {
    return $this->fields[$field] = new Hidden($field, $label, $this);
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
}