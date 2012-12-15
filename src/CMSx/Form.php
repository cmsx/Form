<?php

namespace CMSx;

class Form
{
  /** Атрибут action для формы */
  protected $action;
  /** Имя формы, также используется для поля name в инпутах */
  protected $name;
  /** Атрибуты для тега form */
  protected $form_attr;

  public function __toString()
  {
    try {
      return $this->render();
    } catch (\Exception $e) {}
  }

  function __construct($name = null)
  {
    if ($name) {
      $this->setName($name);
    }
  }

  public function setFormAttr($form_attr)
  {
    $this->form_attr = $form_attr;

    return $this;
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

  function render($attr = null)
  {
    return HTML::Form(null, $this->action, null, $this->getFormAttr($attr));
  }

  protected function getFormAttr($attr = null)
  {
    $a = $attr ? : $this->form_attr;
    if ($this->name) {
      $a['id'] = 'form-' . $this->name;
    }
    return $a;
  }
}