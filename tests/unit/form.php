<?php

require_once __DIR__.'/../init.php';

class FormTest extends PHPUnit_Framework_TestCase
{
  function testValidation()
  {
    $f = new \CMSx\Form('test');
    $f->addInput('one')
      ->setIsRequired(true);
    $f->addInput('two')
      ->setFilter('is_numeric');

    $this->assertFalse($f->hasErrors(), 'Форма не содержит ошибок');
    $this->assertFalse($f->isValidated(), 'Для формы еще не вызывалась валидация');
    $this->assertFalse($f->isValid(), 'Форма еще проверялась');

    $v = $f->validate(array('two' => 'abc'));

    $this->assertFalse($v, 'Невалидная форма');
    $this->assertTrue($f->hasErrors(), 'Форма содержит ошибки');
    $this->assertTrue($f->isValidated(), 'Для формы вызывалась валидация');
    $this->assertFalse($f->isValid(), 'Форма невалидна');

  }

  function testDefaultValues()
  {
    $f = new \CMSx\Form('test');
    $f->addInput('one');
    $f->addInput('two');
    $f->setDefaultValues(array('one' => 1, 'two' => 2, 'three' => 3));

    $this->assertEquals(array('one' => 1, 'two' => 2), $f->getValues(), 'Массив значений формы');
    $this->assertEquals(1, $f->getValue('one'), 'Значение для one');
    $this->assertEquals(2, $f->getValue('two'), 'Значение для two');
    $this->assertFalse($f->getValue('three'), 'Значение для three не существует');
  }

  function testFormRender()
  {
    $f = new \CMSx\Form('test');
    $f->addCheckbox('hello', 'Привет', 13)
      ->setAttributes('ch');
    $f->addInput('test');
    $f->addCaption('hi', 'Hi!', 'Some text here');
    $f->addTextarea('text', 'Текст')
      ->setIsRequired();
    $f->addHidden('id', null, 12)
      ->setAttributes('hid');
    $f->addCheckboxListing('cl', 'Группа', array('one', 'two'))
      ->setOptionsIgnoreKeys();
    $f->setSubmit('Привет', 'sub');

    echo $r = $f->render();

    $this->assertSelectCount('form table tr td label input.ch[type=checkbox]', true, $r, 'Один чекбокс');
    $this->assertSelectCount('form table tr td label input.ch[value=13]', true, $r, 'Один чекбокс');
    $this->assertSelectCount('form table tr td input[type=text]', true, $r, 'Один инпут');
    $this->assertSelectCount('form table tr td h3', true, $r, 'Один заголовок');
    $this->assertSelectCount('form input.hid[type=hidden]', true, $r, 'Один hidden-инпут');
    $this->assertSelectCount('form input.hid[value=12]', true, $r, 'Один hidden-инпут значение 12');
    $this->assertSelectCount('form tr td button.sub[type=submit]', true, $r, 'Одна кнопка отправить');
    $this->assertGreaterThan(0, strpos($r, '* Текст'), 'Поле текст обязательное');
  }

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
    $f1->setFormAttributes('myclass');

    $f2 = new \CMSx\Form();

    $this->assertSelectCount('form[class=myclass]', true, $f1->render(), 'Установка класса f1');
    $this->assertSelectCount('form[class=myclass]', true, $f2->render('myclass'), 'Установка класса f2');

    $attr = array('one' => '1', 'hello' => 'world');
    $f1 = new \CMSx\Form();
    $f1->setFormAttributes($attr);

    $f2 = new \CMSx\Form();

    $t1 = $f1->render();
    $t2 = $f2->render($attr);

    $this->assertSelectCount('form[one=1]', true, $t1, 'Атрибут t1 one');
    $this->assertSelectCount('form[hello=world]', true, $t1, 'Атрибут t1 hello');

    $this->assertSelectCount('form[one=1]', true, $t2, 'Атрибут t2 one');
    $this->assertSelectCount('form[hello=world]', true, $t2, 'Атрибут t2 hello');
  }
}