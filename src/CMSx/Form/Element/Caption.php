<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class Caption extends Element
{
  protected $tag = 'h3';

  public function render()
  {
    return HTML::Tag($this->tag, $this->getLabel(), $this->getAttributes(false));
  }

  /** Тег, в котором будет отображаться заголовок */
  public function setTag($tag)
  {
    $this->tag = $tag;

    return $this;
  }

  /** Тег, в котором будет отображаться заголовок */
  public function getTag()
  {
    return $this->tag;
  }
}
