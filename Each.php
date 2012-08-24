<?php
namespace Verifier;
/**
 * Работа со списком идентичных полей
 * @todo Дореализовать
 * создать набор функций в пространстве имен Conditions
 * в слачае множественных значений эталонных параметров, передавать их массивом
 * @author Максим Соколовский (my.sokolovsky@gmail.com)
 */
class Each extends Item {

    public function __call($method, $args) {
        $function = $this->getVerifyMethod($method);
        $message = array_pop($args);
        $referenceValue = array_shift($args);
        foreach ($this->getValue() as $itemValue) {
            if (!$function($itemValue, $referenceValue)) {
                return $this->proccessCondition(false, $message);
            }
        }
        return $this;
    }
}
