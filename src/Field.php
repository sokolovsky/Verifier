<?php
namespace Verifier;

/**
 * Field for verification
 *
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
 */
class Field extends Item {

    public function __call($method, $args) {
        $command = $this->createCommand($method, $args);
        $this->processCondition($command, $this->getValue());
        return $this;
    }
}
