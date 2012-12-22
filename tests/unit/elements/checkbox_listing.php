<?php

use CMSx\Form\Element\CheckboxListing;

require_once __DIR__ . '/../../init.php';

class CheckboxListingTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new CheckboxListing('test');
    $e->setOptions('one', 'two');
    $e->setOptionsIgnoreKeys();
    $exp = '<label><input name="test[]" type="checkbox" value="one" /> one</label>' . "\n"
      . '<label><input name="test[]" type="checkbox" value="two" /> two</label>' . "\n";
    $act = $e->render();
    $this->assertEquals($exp, $act, 'Список чекбоксов по значениям');

    $e = new CheckboxListing('test');
    $e->setOptions(array(1 => 'one', 2 => 'two'));
    $e->setSeparator('<br />');
    $exp = '<label><input name="test[]" type="checkbox" value="1" /> one</label><br />' . "\n"
      . '<label><input name="test[]" type="checkbox" value="2" /> two</label><br />' . "\n";
    $act = $e->render();
    $this->assertEquals($exp, $act, 'Список чекбоксов ключ-значение с разделителем');


  }
}