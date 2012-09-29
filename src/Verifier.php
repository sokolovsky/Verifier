<?php
namespace Verifier;
require_once __DIR__.'/Item.php';
require_once __DIR__.'/Field.php';
require_once __DIR__.'/Each.php';
require_once __DIR__.'/Conditions.php';

/**
 * Verification of data
 *
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
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

    private $_useDependency = false;
    /**
     * @param array | object $data Data for verification
     */
    public function __construct($data) {
        if (is_scalar($data)) {
            throw new DatatypeException("The input data must be an array or object");
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
     * Setting uses dependency by other fields
     * @return Verifier
     */
    public function useDependency() {
        $this->_useDependency = true;
        return $this;
    }

    /**
     * Sign used dependency by fields
     * @return boolean
     */
    public function isUseDependency() {
        return $this->useDependency();
    }

    /**
     * Initialize field validation on the path $ path.
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return Field
     */
    public function field($label, $path = null) {
        return $this->_initItem('\Verifier\Field', $label, $path);
    }


    /**
     * Checking your way to $ path
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return Each
     */
    public function each($label, $path = null) {
        return $this->_initItem('\Verifier\Each', $label, $path = null);
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

    /**
     * Item init by type
     * @param string $className
     * @param string $label
     * @param string $path
     * @return Item
     * @throws ErrorCodeException
     */
    private function _initItem($className, $label, $path = null) {
        if (is_subclass_of($className, 'Item')) {
            throw new ErrorCodeException("Class `$className` not successor from `Item`");
        }
        is_null($path) && ($path = $label);
        $value = $this->_getDataByPath($path);
        $this->_items[$path] = new $className($this, $value, $label);
        return $this->_items[$path];

    }

    private function _getDataByPath($path) {
        $arPath = explode('.', $path);
        $value = $this->_data;
        while($pathItem = array_shift($arPath)) {
            $value = $value->$pathItem;
            if (count($arPath)) {
                if (is_scalar($value->$pathItem)) {
                    throw new DatatypeException("Error field path  `$path` ($pathItem) " .  var_export($value, true));
                } else {
                    $value = (object)$value;
                }
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

