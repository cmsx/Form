<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class Hidden extends Element
{
  public function render()
  {
    return HTML::Hidden($this->getName(), $this->getValue(), $this->getAttributes(false));
  }
}
