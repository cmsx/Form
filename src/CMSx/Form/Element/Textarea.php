<?php

namespace CMSx\Form\Element;

use CMSx\Form\Element;
use CMSx\HTML;

class Textarea extends Element
{
  protected $rows;
  protected $cols;

  public function render()
  {
    return HTML::Textarea(
      $this->getName(),
      $this->getValue(),
      $this->getAttributes(),
      $this->getRows(),
      $this->getCols()
    );
  }

  public function setCols($cols)
  {
    $this->cols = $cols;

    return $this;
  }

  public function getCols()
  {
    return !empty($this->cols) ? $this->cols : null;
  }

  public function setRows($rows)
  {
    $this->rows = $rows;

    return $this;
  }

  public function getRows()
  {
    return !empty($this->rows) ? $this->rows : null;
  }
}
