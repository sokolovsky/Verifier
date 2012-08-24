<?php
namespace Verifier;
require_once __DIR__.'/Item.php';
require_once __DIR__.'/Field.php';
require_once __DIR__.'/Each.php';
require_once __DIR__.'/Conditions.php';

/**
 * ����������� ������
 *
 * @author ������ ����������� (my.sokolovsky@gmail.com)
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
     * @param array | object $data ������ ��� �����������
     */
    public function __construct($data) {
        if (is_scalar($data)) {
            throw new DatatypeException("����������� �������� ������ � ������");
        }
        $this->_data = (object) $data;
    }

    /**
     * ��������� �������� ����� ����������� � �����.
     */
    public function setAsChanged() {
        $this->_wasChange = true;
    }

    /**
     * ������������� �������� ���� �� ���� $path.
     * @param string $label ����� ��� ���� (���� ��� ������ ������)
     * @param string $path  ���� � �������� ���� (���� ������, �� ��������� ����� �������� ��� ������)
     * @return Field
     */
    public function field($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Field($this, $value, $label);
        return $this->_items[$path];
    }


    /**
     * �������� ������ �� ���� $path
     * @param string $label ����� ��� ���� (���� ��� ������ ������)
     * @param string $path  ���� � �������� ���� (���� ������, �� ��������� ����� �������� ��� ������)
     * @return Each
     */
    public function each($label, $path = null) {
        $value = $this->_getDataByPath($path ?: $label);
        $this->_items[$path] = new Each($this, $value, $label);
        return $this->_items[$path];
    }
    /**
     * ���������� ��������.
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
     * ������� ���������� �����
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
                    throw new \Exception("����������� ���� `$path` ($pathItem) " .  var_export($value, true));
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

