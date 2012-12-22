<?php

use CMSx\Form\Element\Caption;
use CMSx\HTML;

require_once __DIR__.'/../../init.php';

class CaptionTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new Caption('test', 'Heading');
    $exp = '<h3 id="form-test">Heading</h3>';
    $this->assertEquals($exp, $e->render());

    $e->setTag('span');
    $e->setAttributes('hello');
    $exp = '<span class="hello" id="form-test">Heading</span>';
    $this->assertEquals($exp, $e->render());
  }
}