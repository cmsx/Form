<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class Select extends Element
{
  protected $label_as_placeholder = true;

  public function render()
  {
    $opt = null;
    if (is_array($this->options)) {
      $opt = HTML::OptionListing($this->options, $this->getValue(), null, (bool)$this->ignore_keys);
    }

    return HTML::Select($opt, $this->getName(), null, $this->getAttributes(false), $this->getPlaceholder());
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