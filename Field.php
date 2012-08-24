<?php
namespace Verifier;

/**
 * Описание Field
 *
 * @author Максим Соколовский (my.sokolovsky@gmail.com)
 */
class Field extends Item {

    public function __call($method, $args) {
        $function = $this->getVerifyMethod($method);
        $message = array_pop($args);
        $referenceValue = array_shift($args);
        return $this->proccessCondition($function($this->getValue(), $referenceValue), $message);
    }
}
