<?php
namespace Verifier;
/**
 * ������ �� ������� ���������� �����
 * @todo �������������
 * ������� ����� ������� � ������������ ���� Conditions
 * � ������ ������������� �������� ��������� ����������, ���������� �� ��������
 * @author ������ ����������� (my.sokolovsky@gmail.com)
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
