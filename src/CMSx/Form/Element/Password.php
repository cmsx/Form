<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

/**
 * Отрисовка стандартного INPUT`а
 */
class Password extends Element
{
  public function render()
  {
    return HTML::Password($this->getName(), $this->getValue(), $this->getAttributes());
  }
}
