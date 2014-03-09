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
            $this->processCondition($command, $itemValue);
        }
        return $this;
    }
}
