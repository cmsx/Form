<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class Checkbox extends Element
{
  protected $is_checked;
  protected $checkbox_value = 1;

  /** Шаблон обязательное поле не заполнено */
  protected $tmpl_err_required = 'Вы должны подтвердить "%s"';

  public function render($with_label = true)
  {
    return HTML::Checkbox(
      $this->getName(),
      $this->getIsChecked(),
      $this->getCheckboxValue(),
      $this->getAttributes(false),
      $with_label ? $this->getLabel() : null
    );
  }

  public function getIsChecked()
  {
    return $this->getValue() == $this->checkbox_value;
  }

  /**
   * Значение поля формы
   * @param bool $clean - экранировать ли вывод
   */
  public function getValue($clean = true)
  {
    return $this->is_checked
      ? ($clean
        ? htmlspecialchars($this->checkbox_value)
        : $this->checkbox_value)
      : parent::getValue($clean);
  }

  /** Установка состояния checked */
  public function setIsChecked($on = true)
  {
    $this->is_checked = (bool)$on;

    return $this;
  }

  /** Аттрибут value для чекбокса, по умолчанию = 1 */
  public function setCheckboxValue($checkbox_value)
  {
    $this->checkbox_value = $checkbox_value;

    return $this;
  }

  /** Аттрибут value для чекбокса */
  public function getCheckboxValue()
  {
    return $this->checkbox_value;
  }
}
