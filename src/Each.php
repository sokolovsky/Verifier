<?php
namespace Verifier;
/**
 * Working with a list of identical fields.
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
 */
class Each extends Item {

    public function __call($method, $args) {
        $function = $this->getVerifyMethod($method);
        $message = array_pop($args);
        $referenceValue = $this->getReferenceValue($args);
        foreach ($this->getValue() as $itemValue) {
            if (!$function($itemValue, $referenceValue)) {
                return $this->processCondition(false, $message);
            }
        }
        return $this;
    }
}
