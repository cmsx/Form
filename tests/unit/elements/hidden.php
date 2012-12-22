<?php

use CMSx\Form\Element\Hidden;

require_once __DIR__.'/../../init.php';

class HiddenTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new Hidden('test');
    $exp = '<input id="form-test" name="test" type="hidden" />';
    $this->assertEquals($exp, $e->render(), 'Рендер по-умолчанию');
  }
}