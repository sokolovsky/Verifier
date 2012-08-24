<?php
namespace Verifier;
/**
 * Описание Item
 *
 * @method Verifier\Item more(scalar $value, string $message)
 * @method Verifier\Item less(scalar $value, string $message)
 * @method Verifier\Item moreOrEqual(scalar $value, string $message)
 * @method Verifier\Item lessOrEqual(scalar $value, string $message)
 * @method Verifier\Item lengthMoreOrEqual(integer $lenght, string $message)
 * @method Verifier\Item lengthLessOrEqual(integer $lenght, string $message)
 * @method Verifier\Item equal(scalar $value, string $message)
 * @method Verifier\Item in(array $list, string $message)
 * @method Verifier\Item notIn(array $list, string $message)
 * @method Verifier\Item range(array $range, string $message)
 * @method Verifier\Item byFunction(callback $function, string $message)
 * @method Verifier\Item byRegularExpression(string $pattern, string $message)
 * @method Verifier\Item numeric(string $message)
 * @method Verifier\Item notEmpty(string $message)
 * @method Verifier\Item email(string $message)
 * @method Verifier\Item url(string $message)
 *
 * @author Максим Соколовский (my.sokolovsky@gmail.com)
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
     * @param mixed $value значение для верификации
     * @param string $label метка поля (значения) верификации
     */
    public function __construct(Verifier $verifier, $value, $label) {
        $this->_verifier = $verifier;
        $this->_value = $value;
        $this->_label = (string)$label;
    }

    protected function getVerifyMethod($name) {
        $fName = __NAMESPACE__.'\Conditions\\'.$name;
        if (!function_exists($fName)) {
            throw new ErrorCodeException("Проверочного метода {$name} не существует");
        }
        return $fName;
    }

    /**
     * Если на текущий момент проверка пункта уже является ошибочны, остальная проверка игнорируется
     * @return \Verifier\Item
     */
    public function ifValid() {
        if ($this->hasErrors()) {
            $this->_notExecute = true;
        }
        return $this;
    }

    /**
     * Получение метки значения верификации
     * @return string
     */
    public function getLabel() {
        return $this->_label;
    }

    private function _addError($message) {
        $this->_errors[] = (string) $message;
    }

    /**
     * Получение списка ошибок
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }


    /**
     * Признак наличия ошибок
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->_errors);
    }

    protected function proccessCondition($condition, $message) {
        if (!$this->_notExecute) {
            $this->_verifier->setAsChanged();
            !(bool) $condition && $this->_addError($message);
        }
        return $this;
    }

    protected function getValue() {
        return $this->_value;
    }
}
