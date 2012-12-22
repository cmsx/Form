<?php

use CMSx\Form\Element\Select;

require_once __DIR__ . '/../../init.php';

class SelectTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new Select('test');
    $exp = '<select id="form-test" name="test">' . "\n" . '<option value="">Test</option>' . "\n" . '</select>';
    $this->assertEquals($exp, $e->render(), 'Рендер по-умолчанию');

    $e->setOptions('one', 'two');
    $e->setOptionsIgnoreKeys();
    $e->validate('two');

    $act = $e->render();
    $this->assertSelectCount('select option', 3, $act, 'Три Option');
    $this->assertGreaterThan(
      0, strpos($act, '<option selected="selected" value="two">two</option>'), 'Выбран пункт two'
    );

    $e = new Select('test');
    $e->setOptions(array(0 => 'Zero', 1 => 'One', 2 => 'Two'));
    $this->assertSelectCount('option[selected=selected]', false, $e->render(), 'Ни один пункт не выбран');

    $e->validate(0);
    $this->assertGreaterThan(
      0, strpos($e->render(), '<option selected="selected" value="0">Zero</option>'), 'Выбран пункт 2'
    );

    $e->validate(2);

    $act = $e->render();
    $this->assertSelectCount('select option', 4, $act, 'Четыре Option');
    $this->assertGreaterThan(0, strpos($act, '<option selected="selected" value="2">Two</option>'), 'Выбран пункт 2');
  }
}