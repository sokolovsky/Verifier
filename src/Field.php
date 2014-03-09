<?php
namespace Verifier;

/**
 * Field for verification
 *
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
 */
class Field extends Item {

    public function __call($method, $args) {
        $function = $this->getVerifyMethod($method);
        $message = array_pop($args);
        $referenceValue = $this->getReferenceValue($args);
        return $this->processCondition($function($this->getValue(), $referenceValue), $message);
    }
}
