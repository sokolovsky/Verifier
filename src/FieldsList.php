<?php
namespace Verifier;
/**
 * Working with a list of identical fields.
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
 */
class FieldsList extends Item {

    public function __call($method, $args) {
        $command = $this->createCommand($method, $args);
        foreach ($this->getValue() as $itemValue) {
            if (!$this->processCondition($command, $itemValue)) {
                break;
            }
        }
        return $this;
    }
}
