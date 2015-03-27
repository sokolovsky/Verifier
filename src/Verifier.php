<?php
namespace Verifier;

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
        return $this->_useDependency;
    }

    /**
     * Initialize field validation on the path $ path.
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return Field
     */
    public function field($label, $path = null) {
        return $this->_getItem('\Verifier\Field', $label, $path);
    }


    /**
     * Checking your way to $ path
     * @param string $label The label for the field (key when displaying errors)
     * @param string $path  Path to the value of the field (if empty, then the value will be all data)
     * @return FieldsList
     */
    public function each($label, $path = null) {
        return $this->_getItem('\Verifier\FieldsList', $label, $path);
    }

    public function hasItem($path) {
        try {
            $this->_getDataByPath($path);
            return true;
        } catch (DatatypeException $e) {
        }
        return false;
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

    public function refresh() {
        $this->_items = array();
        return $this;
    }

    /**
     * Item init by type
     * @param string $className
     * @param string $label
     * @param string $path
     * @return Item
     * @throws ErrorCodeException
     */
    private function _getItem($className, $label, $path = null) {
        if (is_subclass_of($className, 'Item')) {
            throw new ErrorCodeException("Class `$className` not successor from `Item`");
        }
        is_null($path) && ($path = $label);
        if (!isset($this->_items[$path])) {
            $value = $this->_getDataByPath($path);
            $this->_items[$path] = new $className($this, $value, $label);
        }
        return $this->_items[$path];

    }

    private function _getDataByPath($path) {
        $arPath = explode('.', $path);
        $value = $this->_data;
        while($pathItem = array_shift($arPath)) {
            if (!property_exists($value, $pathItem)) {
                throw new DatatypeException("Error field path `$path`");
            }
            $value = $value->$pathItem;
            if (count($arPath)) {
                if (is_scalar($value)) {
                    throw new DatatypeException("Error field path  `$path` ($pathItem) " .  var_export($value, true));
                } else {
                    $value = (object)$value;
                }
            }
        }
        return $value;
    }
}
