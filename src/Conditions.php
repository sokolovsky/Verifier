<?php
namespace Verifier\Conditions;

/**
 * Methods of test conditions
 */

function equal($verifiedValue, $referenceValue) {
    return $verifiedValue == $referenceValue;
}

function more ($verifiedValue, $referenceValue) {
    return $verifiedValue > $referenceValue;
}

function less ($verifiedValue, $referenceValue) {
    return $verifiedValue < $referenceValue;
}

function notEmpty($verifiedValue) {
    return !empty($verifiedValue);
}

function moreOrEqual ($verifiedValue, $referenceValue) {
    return $verifiedValue >= $referenceValue;
}

function lessOrEqual ($verifiedValue, $referenceValue) {
    return $verifiedValue <= $referenceValue;
}

function in($verifiedValue, array $list) {
    return in_array($verifiedValue, $list);
}

function notIn($verifiedValue, array $list) {
    return !in_array($verifiedValue, $list);
}

function range($verifiedValue, array $range) {
    return $verifiedValue >= $range[0] && $verifiedValue <= $range[1];
}

function lengthMoreOrEqual($verifiedValue, $lenght) {
    if (!is_string($verifiedValue)) {
        throw new \Verifier\DatatypeException;
    }
    return strlen($verifiedValue) >= $lenght;
}

function lengthLessOrEqual($verifiedValue, $lenght) {
    if (!is_string($verifiedValue)) {
        throw new \Verifier\DatatypeException;
    }
    return strlen($verifiedValue) <= $lenght;
}

function numeric($verifiedValue) {
    return is_numeric($verifiedValue);
}

function byFunction($verifiedValue, $function) {
    return (bool) $function($verifiedValue);
}

function byRegularExpression($verifiedValue, $pattern) {
    return preg_match($pattern, $verifiedValue);
}

function email($verifiedValue) {
    return filter_var($verifiedValue, FILTER_VALIDATE_EMAIL) !== false;
}

function url($verifiedValue) {
    return filter_var($verifiedValue, FILTER_VALIDATE_URL) !== false;
}
