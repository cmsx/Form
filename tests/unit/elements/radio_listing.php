<?php

use CMSx\Form\Element\RadioListing;

require_once __DIR__ . '/../../init.php';

class RadioListingTest extends PHPUnit_Framework_TestCase
{
  function testRender()
  {
    $e = new RadioListing('test');
    $e->setOptions('one', 'two');
    $e->setOptionsIgnoreKeys();
    $exp = '<label><input name="test" type="radio" value="one" /> one</label>' . "\n"
      . '<label><input name="test" type="radio" value="two" /> two</label>' . "\n";
    $act = $e->render();
    $this->assertEquals($exp, $act, 'Список радиобатонов по значениям');

    $e = new RadioListing('test');
    $e->setOptions(array(1 => 'one', 2 => 'two'));
    $e->setSeparator('<br />');
    $exp = '<label><input name="test" type="radio" value="1" /> one</label><br />' . "\n"
      . '<label><input name="test" type="radio" value="2" /> two</label><br />' . "\n";
    $act = $e->render();
    $this->assertEquals($exp, $act, 'Список радиобатонов ключ-значение с разделителем');


  }
}