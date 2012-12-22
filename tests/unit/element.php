<?php

require_once '../init.php';

class ElementOptions extends \CMSx\Form\Element
{
  public function setOptions($options, $_ = null)
  {
    return parent::setOptions($options, $_);
  }

  public function setOptionsIgnoreKeys($on = true)
  {
    return parent::setOptionsIgnoreKeys($on);
  }

  public function getOptions()
  {
    return parent::getOptions();
  }
}

class ElementTest extends PHPUnit_Framework_TestCase
{
  function testRenderInput()
  {
    $e = new ElementOptions('test');
    $e->setDefaultValue(123);
    $exp = '<input id="form-test" name="test" placeholder="" type="text" value="123" />';
    $this->assertEquals($exp, $e->render(), 'Элемент по-умолчанию');

    $e->setLabelAsPlaceholder();
    $this->assertSelectCount('input[placeholder=Test]', true, $e->render(), 'Плейсхолдер');

    $e->setFormName('myform');
    $act = $e->render();
    $this->assertSelectCount('input[id=form-myform-test]', true, $act, 'Атрибут ID инпута');
    $this->assertGreaterThan(0, strpos($act, 'myform[test]'), 'Атрибут NAME инпута');
  }

  function testValidationStatus()
  {
    $e = new ElementOptions('test');
    $this->assertFalse($e->getIsValidated(), 'Изначально форма не проверялась');
    $this->assertFalse($e->getIsValid(), 'Изначально форма не валидна');

    $e->setFilter('is_numeric');
    $e->validate('abc');

    $this->assertTrue($e->getIsValidated(), 'Форма проверялась №1');
    $this->assertFalse($e->getIsValid(), 'Форма не валидна');

    $e->validate(123);

    $this->assertTrue($e->getIsValidated(), 'Форма проверялась №2');
    $this->assertTrue($e->getIsValid(), 'Форма валидна');
  }

  function testLabel()
  {
    $e1 = new ElementOptions('test');
    $this->assertEquals('Test', $e1->getLabel(), 'Лейбл из названия поля');

    $e1->setLabel('Тест');
    $e2 = new ElementOptions('test', 'Тест');
    $this->assertEquals('Тест', $e1->getLabel(), 'Заданный лейбл e1');
    $this->assertEquals('Тест', $e2->getLabel(), 'Заданный лейбл e2');
  }

  function testID()
  {
    $e = new ElementOptions('test');
    $this->assertEquals('form-test', $e->getId(), 'ID элемента без имени формы');

    $e->setFormName('myform');
    $this->assertEquals('form-myform-test', $e->getId(), 'ID элемента в форме');
  }

  function testPlaceholder()
  {
    $e = new ElementOptions('test', 'Тест');
    $this->assertEmpty($e->getPlaceholder(), 'По-умолчанию плейсхолдер выключен');

    $e->setLabelAsPlaceholder();
    $this->assertEquals('Тест', $e->getPlaceholder(), 'Плейсхолдер = лейбл');

    $e->setPlaceholder('Бла-бла');
    $this->assertEquals('Бла-бла', $e->getPlaceholder(), 'Произвольный плейсхолдер');

    $e->setLabelAsPlaceholder(false);
    $e->setPlaceholder(false);
    $this->assertEmpty($e->getPlaceholder(), 'Плейсхолдер выключен');
  }

  function testOptions()
  {
    $e = new ElementOptions('test');

    $e->setOptions('one', 'two');
    $e->setOptionsIgnoreKeys();
    $exp = array(0 => 'one', 1 => 'two');

    $this->assertEquals($exp, $e->getOptions(), 'Опции без ключей');
    $this->assertTrue($e->validate('one'), 'Если значение есть в списке - всё ок');
    $this->assertFalse($e->validate('three'), 'Если значения нет - ошибка');
    $this->assertFalse($e->validate(1), 'Ключи отключены и не являются допустимыми значениями');

    $exp = array(1 => 'one', 2 => 'two');
    $e->setOptions($exp);
    $e->setOptionsIgnoreKeys(false);

    $this->assertEquals($exp, $e->getOptions(), 'Опции ключ-значение');
    $this->assertTrue($e->validate(1), 'Ключи включены и являются единственно допустимыми значениями');
    $this->assertFalse($e->validate('one'), 'Значения массива не являются допустимыми');
  }

  function testIsRequired()
  {
    $e = new ElementOptions('test');
    $this->assertTrue($e->validate(null), 'Необязательное поле');

    $e->setIsRequired();
    $this->assertFalse($e->validate(null), 'Обязательное поле');
    $this->assertFalse($e->getValue(), 'Значение не установилось');

    $this->assertTrue($e->validate(123), 'Значение проходит');
    $this->assertEquals(123, $e->getValue(), 'Значение установлено');
  }

  /** @dataProvider dataForFilter */
  function testFilter($filter, $value, $passing)
  {
    $e = new ElementOptions('test');
    $e->setFilter($filter);

    $this->assertEquals($passing, $e->validate($value), 'Фильтр не проходит');
    $this->assertEquals(!$passing, $e->hasErrors(), 'В форме есть ошибки');
    $this->assertEquals($passing ? $value : false, $e->getValue(), 'Значение не установилось!');
  }

  function testValueEscaping()
  {
    $e = new ElementOptions('test');
    $e->validate('<a href="http://www.hello.ru">Hello """</a>');
    $this->assertEquals(
      '&lt;a href=&quot;http://www.hello.ru&quot;&gt;Hello &quot;&quot;&quot;&lt;/a&gt;',
      $e->getValue(),
      'Экранирование спецсимволов'
    );
  }

  function testRegexp()
  {
    $e = new ElementOptions('test');
    $e->setRegexp('/^[a-z]+$/i');

    $this->assertFalse($e->validate(123), 'Регулярка не проходит');
    $this->assertTrue($e->hasErrors(), 'В форме есть ошибки');
    $this->assertFalse($e->getValue(), 'Значение не установилось!');

    $this->assertEquals(123, $e->getTaintedValue(), 'Сырое значение доступно в элементе');

    $this->assertTrue($e->validate('abc'), 'Регулярка проходит');
    $this->assertFalse($e->hasErrors(), 'Ошибок нет');
    $this->assertEquals('abc', $e->getValue(), 'Значение установилось');
  }

  function testDefaultValue()
  {
    $e = new ElementOptions('test');
    $e->setIsRequired();
    $e->setDefaultValue(123);

    $this->assertEquals(123, $e->getValue(), 'Значение по-умолчанию до отправки');

    $e->validate(null);
    $this->assertEmpty($e->getValue(), 'После валидации значения по-умолчанию не используются');

    $e->validate(321);

    $this->assertFalse($e->hasErrors(), 'Задали значение, ошибок не возникло');
    $this->assertEquals(321, $e->getValue(), 'Новое значение установлено');
  }

  function testErrors()
  {
    $err = 'Текст ошибки';

    $e = new ElementOptions('test');
    $this->assertFalse($e->hasErrors(), 'Ошибок нет');

    $e->addError($err);
    $this->assertTrue($e->hasErrors(), 'Ошибка установлена');

    $this->assertEquals(1, count($e->getErrors()), 'Должна быть 1 ошибка');
    $this->assertEquals($err, array_shift($e->getErrors()), 'Текст ошибки совпадает');

    $e->addError($err . '123', true); //Добавляем ошибку с очищением предыдущих
    $this->assertEquals(1, count($e->getErrors()), 'Опять должна быть 1 ошибка');
    $this->assertEquals($err . '123', array_shift($e->getErrors()), 'Текст ошибки . 123 совпадает');
  }

  function dataForFilter()
  {
    return array(
      array('is_numeric', 'abc', false),
      array('is_numeric', 123, true),
      array(
        function ($value) {
          return is_numeric($value);
        }, 'abc', false),
      array(
        function ($value) {
          return is_numeric($value);
        }, 123, true),
      array(array($this, 'isNumeric'), 'abc', false),
      array(array($this, 'isNumeric'), 123, true),
    );
  }

  /** Метод для тестирования фильтров */
  public function isNumeric($val)
  {
    return is_numeric($val);
  }
}