<?php

use CMSx\Form\Element\Password;

require_once __DIR__.'/../../init.php';

class PasswordTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new Password('test');
    $exp = '<input id="form-test" name="test" placeholder="" type="password" value="" />';
    $this->assertEquals($exp, $e->render(), 'Рендер по-умолчанию');
  }
}