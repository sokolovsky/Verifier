<?php
namespace Verifier;
require_once __DIR__.'/Item.php';
require_once __DIR__.'/Field.php';
require_once __DIR__.'/Each.php';
require_once __DIR__.'/Conditions.php';

/**
 * Вирификатор данных
 *
 * @author Максим Соколовский (my.sokolovsky@gmail.com)
 */
class Verifier {

    /**
     *
     * @var ArrayObject
     */
    private $_data;

    private $_items = array();

    private $_errors;

    private $_wasChange = true;

    /**
     *
     * @param array | object $data Данные для верификации
     */
    public function __construct($data) {
        if (is_scalar($data)) {
            throw new DatatypeException("Верификатор работает только с полями");
        }
        $this->_data = (object) $data;
    }

    /**
     * Установка признака смены ошибочности у полей.
     */
    public function setAsChanged() {
        $this->_wasChange = true;
    }

    /**
     * Инициализация проверки поля по пути $path.
     * @param string $label Метка для поля (ключ при показе ошибок)
     * @param string $path  Путь к значению поля (если пустой, то значением будут являться все данные)
     * @return Field
     */
    public function field($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Field($this, $value, $label);
        return $this->_items[$path];
    }


    /**
     * Проверка списка по пути $path
     * @param string $label Метка для поля (ключ при показе ошибок)
     * @param string $path  Путь к значению поля (если пустой, то значением будут являться все данные)
     * @return Each
     */
    public function each($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Each($this, $value, $label);
        return $this->_items[$path];
    }
    /**
     * Выполнение проверки.
     * @return type
     */
    public function getErrors() {
        if ($this->_wasChange) {
            $this->_errors = array();
            if (!empty ($this->_items)) {
                /* @var $field Field */
                foreach ($this->_items as $path => $field) {
                    if ($field->hasErrors()) {
                        $this->_errors[$field->getLabel()] = $field->getErrors();
                    }
                }
            }
            $this->_wasChange = false;
        }
        return $this->_errors;
    }

    /**
     * Признак валидности полей
     * @return bool
     */
    public function isValid() {
        return count($this->getErrors()) == 0;
    }

    private function _getDataByPath($path) {
        $arPath = explode('.', $path);
        $value = $this->_data;
        while($pathItem = array_shift($arPath)) {
            if (count($arPath)) {
                if (is_scalar($value->$pathItem)) {
                    throw new \Exception("Некоректный путь `$path` ($pathItem) " .  var_export($value, true));
                }
                $value = (object)$value->$pathItem;
            }
        }
        return $value;
    }
}

class Exception extends \Exception {
}

class DatatypeException extends Exception {
}

class ErrorCodeException extends Exception {
}

