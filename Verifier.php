<?php
namespace Verifier;
require_once __DIR__.'/Item.php';
require_once __DIR__.'/Field.php';
require_once __DIR__.'/Each.php';
require_once __DIR__.'/Conditions.php';

/**
 * Verification of data
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
     * @param array | object $data Data for verification
     */
    public function __construct($data) {
        if (is_scalar($data)) {
            throw new DatatypeException("Верификатор работает только с полями");
        }
        $this->_data = (object) $data;
    }

    /**
     * Setting the flag of the fallacy in the fields.
     */
    public function setAsChanged() {
        $this->_wasChange = true;
    }

    /**
     * Initialize field validation on the path $ path.
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return Field
     */
    public function field($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Field($this, $value, $label);
        return $this->_items[$path];
    }


    /**
     * Checking your way to $ path
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return Each
     */
    public function each($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Each($this, $value, $label);
        return $this->_items[$path];
    }
    /**
     * Getting errors.
     * @return array
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
     * Symptom validity fields.
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

