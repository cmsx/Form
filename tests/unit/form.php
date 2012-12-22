<?php

require_once __DIR__.'/../init.php';

class FormTest extends PHPUnit_Framework_TestCase
{
  function testFormTagRender()
  {
    $f = new \CMSx\Form();
    $t = $f->render();

    $this->assertSelectCount('form[method=POST]', true, $t, 'Форма с методом POST');
    $this->assertSelectCount('form[action=]', true, $t, 'Форма с action по умолчанию');
    $this->assertSelectCount('form[enctype=multipart/form-data]', true, $t, 'Форма с enctype по-умолчанию');
  }

  function testFormAction()
  {
    $f = new \CMSx\Form();
    $f->setAction('/hello/');
    $this->assertSelectCount('form[action=/hello/]', true, $f->render(), 'Форма с заданным action');
  }

  function testFormName()
  {
    $f1 = new \CMSx\Form();
    $f1->setName('myform');

    $f2 = new \CMSx\Form('myform');
    $this->assertSelectCount('form[id=form-myform]', true, $f1->render(), 'Установка имени для формы через конструктор');
    $this->assertSelectCount('form[id=form-myform]', true, $f2->render(), 'Установка имени для формы через сеттер');
  }

  function testToString()
  {
    $f = new \CMSx\Form();
    $this->assertEquals($f->render(), (string)$f, 'Приведение формы к строковому типу');
  }

  function testFormAttr()
  {
    $f1 = new \CMSx\Form();
    $f1->setFormAttr('myclass');

    $f2 = new \CMSx\Form();

    $this->assertSelectCount('form[class=myclass]', true, $f1->render(), 'Установка класса f1');
    $this->assertSelectCount('form[class=myclass]', true, $f2->render('myclass'), 'Установка класса f2');

    $attr = array('one' => '1', 'hello' => 'world');
    $f1 = new \CMSx\Form();
    $f1->setFormAttr($attr);

    $f2 = new \CMSx\Form();

    $t1 = $f1->render();
    $t2 = $f2->render($attr);

    $this->assertSelectCount('form[one=1]', true, $t1, 'Атрибут t1 one');
    $this->assertSelectCount('form[hello=world]', true, $t1, 'Атрибут t1 hello');

    $this->assertSelectCount('form[one=1]', true, $t2, 'Атрибут t2 one');
    $this->assertSelectCount('form[hello=world]', true, $t2, 'Атрибут t2 hello');
  }
}