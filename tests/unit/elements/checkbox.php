<?php

use CMSx\Form\Element\Checkbox;

require_once __DIR__.'/../../init.php';

class CheckboxTest extends PHPUnit_Framework_TestCase
{
  function testValidation()
  {
    $e = new Checkbox('test');

    $e->validate(1);
    $this->assertSelectCount('input[checked=checked]', true, $e->render());

    $e->validate(2);
    $this->assertSelectCount('input[checked=checked]', false, $e->render());
  }

  function testRender()
  {
    $e = new Checkbox('test');
    $exp = '<label><input id="form-test" name="test" type="checkbox" value="1" /> Test</label>';
    $this->assertEquals($exp, $e->render(), 'Рендер по-умолчанию');

    $e->setCheckboxValue(123);
    $this->assertSelectCount('label input[value=123]', true, $e->render(), 'Значение чекбокса');

    $e->setIsChecked();
    $this->assertSelectCount('label input[checked=checked]', true, $e->render(), 'Чекбокс отмечен');

    $this->assertSelectCount('label input[type=checkbox]', false, $e->render(false), 'Отрисовка без лейбла');
  }

  function testIsChecked()
  {
    $e = new Checkbox('test');
    $this->assertFalse($e->getIsChecked(), 'По-умолчанию чекбокс выключен');

    $e->setIsChecked();

    $this->assertTrue($e->getIsChecked(), 'Чекбокс отмечен');

    $e->setIsChecked(false);
    $this->assertFalse($e->getIsChecked(), 'Чекбокс не отмечен');

    $e->setDefaultValue(1);
    $this->assertTrue($e->getIsChecked(), 'Установлено верное значение по-умолчанию - чекбокс включен');

    $e->setDefaultValue(2);
    $this->assertFalse($e->getIsChecked(), 'Установлено неверное значение по-умолчанию - чекбокс выключен');

    $e->setCheckboxValue(2);
    $this->assertTrue($e->getIsChecked(), 'Значение чекбокса и значение по-умолчанию совпадают');
  }

  /** Нужно, т.к. getValue() переопределен */
  function testCleanValue()
  {
    $str = '<a href="http://">hello """</a>';
    $e = new Checkbox('test');
    $e->setCheckboxValue($str);
    $e->setIsChecked();

    $exp = htmlspecialchars($str);
    $this->assertEquals($exp, $e->getValue(), 'Чистые данные');
    $this->assertEquals($str, $e->getValue(false), 'Грязные данные');
  }
}