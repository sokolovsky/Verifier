<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace Verifier;


class ConditionCommand {
    private
        $_fun,
        $_message,
        $_referenceValue;

    public function __construct($fun, $args) {
        $this->_fun = new \ReflectionFunction($fun);
        if ($this->_fun->getNumberOfParameters() > count($args)) {
            $this->_message = '';
        } else {
            $this->_message = array_pop($args);
        }
        $this->_referenceValue = array_shift($args);
    }

    /**
     * @param int $num
     * @return mixed|null
     */
    public function getReferenceValue($num = 0) {
        return $this->_referenceValue;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setReferenceValue($value) {
        $this->_referenceValue = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->_message;
    }

    /**
     * @param $value
     * @return bool
     */
    public function execute($value) {
        return $this->_fun->invoke($value, $this->_referenceValue);
    }
}