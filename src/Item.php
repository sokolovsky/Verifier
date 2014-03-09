<?php
namespace Verifier;
/**
 * Item (field or list of the same fields verification)
 *
 * @method \Verifier\Item more(\scalar $value, \string $message = "")
 * @method \Verifier\Item less(\scalar $value, \string $message = "")
 * @method \Verifier\Item moreOrEqual(\scalar $value, \string $message = "")
 * @method \Verifier\Item lessOrEqual(scalar $value, \string $message = "")
 * @method \Verifier\Item lengthMoreOrEqual(\integer $len, \string $message = "")
 * @method \Verifier\Item lengthLessOrEqual(\integer $len, \string $message = "")
 * @method \Verifier\Item equal(\scalar $value, \string $message = "")
 * @method \Verifier\Item in(array $list, \string $message = "")
 * @method \Verifier\Item notIn(array $list, \string $message = "")
 * @method \Verifier\Item range(array $range, \string $message = "")
 * @method \Verifier\Item byFunction(callback $function, \string $message = "")
 * @method \Verifier\Item byRegularExpression(\string $pattern, \string $message = "")
 * @method \Verifier\Item numeric(\string $message = "")
 * @method \Verifier\Item notEmpty(\string $message = "")
 * @method \Verifier\Item email(\string $message = "")
 * @method \Verifier\Item url(\string $message = "")
 *
 * @author Maxim Sokolovsky (my.sokolovsky@gmail.com)
 */
abstract class Item {

    private $_label;
    private $_errors;
    private $_notExecute = false;
    /**
     * @var Verifier
     */
    private $_verifier;

    private $_value;

    /**
     * @param Verifier $verifier
     * @param mixed $value Value for verification
     * @param string $label Label field (value) verification
     */
    public function __construct(Verifier $verifier, $value, $label) {
        $this->_verifier = $verifier;
        $this->_value = $value;
        $this->_label = (string)$label;
    }

    /**
     * If you currently checking the item is already wrong, check the rest ignored.
     * @return \Verifier\Item
     */
    public function ifValid() {
        if ($this->hasErrors()) {
            $this->_notExecute = true;
        }
        return $this;
    }

    /**
     * Getting value labels verification.
     * @return string
     */
    public function getLabel() {
        return $this->_label;
    }

    private function _addError($message) {
        $this->_errors[] = (string) $message;
    }

    /**
     * Getting a list of errors.
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }


    /**
     * Sign for errors.
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->_errors);
    }

    protected function processCondition(ConditionCommand $command, $value) {
        if (!$this->_notExecute) {
            $this->_verifier->setAsChanged();
            $refValue = $command->getReferenceValue();
            if ($this->_verifier->isUseDependency() && $this->_verifier->hasItem($refValue)) {
                $dependsRefValue = $this->_verifier->field($refValue)->getValue();
                $command->setReferenceValue($dependsRefValue);
            }
            !$command->execute($value) && $this->_addError($command->getMessage());
        }
        return $this;
    }

    protected function getValue() {
        return $this->_value;
    }

    protected function createCommand($function, $args) {
        is_string($function) && $function = '\\'.__NAMESPACE__.'\Conditions\\'.$function;

        return new ConditionCommand($function, $args);
    }
}
