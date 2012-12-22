<?php

use CMSx\Form\Element\Textarea;

require_once __DIR__.'/../../init.php';

class PasswordTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new Textarea('test');
    $exp = '<textarea id="form-test" name="test" placeholder=""></textarea>';
    $this->assertEquals($exp, $e->render(), 'Рендер по-умолчанию');

    $e->setRows(10)->setCols(100);
    $this->assertSelectCount('textarea[cols=100]', true, $e->render(), 'Атрибут cols');
    $this->assertSelectCount('textarea[rows=10]', true, $e->render(), 'Атрибут rows');
  }
}
