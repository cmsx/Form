<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class CheckboxListing extends Element
{
  protected $separator;

  public function render()
  {
    return HTML::CheckboxListing(
      $this->options,
      $this->getName(),
      $this->getTaintedValue(),
      $this->separator,
      $this->ignore_keys
    );
  }

  /** Соединитель для соседних чекбоксов */
  public function setSeparator($separator)
  {
    $this->separator = $separator;

    return $this;
  }

  /** Соединитель для соседних чекбоксов */
  public function getSeparator()
  {
    return $this->separator;
  }

  //Меняем публичность методов связанных с опциями

  public function setOptions($options, $_ = null)
  {
    return parent::setOptions($options, $_);
  }

  public function setOptionsIgnoreKeys($on = true)
  {
    return parent::setOptionsIgnoreKeys($on);
  }

  public function getOptions()
  {
    return parent::getOptions();
  }
}