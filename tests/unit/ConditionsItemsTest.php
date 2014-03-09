<?php

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

class ConditionsItemsTest extends PHPUnit_Framework_TestCase {

    /**
     * @param string $name
     * @return ReflectionFunction
     */
    private function _getFunction($name) {
        $fName = '\Verifier\Conditions\\'.$name;
        $fun = new \ReflectionFunction($fName);
        return $fun;
    }

    public function testEqual() {
        $func = $this->_getFunction('equal');
        $this->assertFalse($func->invoke(3,1));
        $this->assertFalse($func->invoke('One', 'Two'));
        $this->assertTrue($func->invoke('One', 'One'));
    }

    public function testMore() {
        $func = $this->_getFunction('more');
        $this->assertTrue($func->invoke(3,1));
        $this->assertFalse($func->invoke(1,1));
        $this->assertFalse($func->invoke('One', 'One'));
    }

    public function testLess() {
        $func = $this->_getFunction('less');
        $this->assertFalse($func->invoke(3,1));
        $this->assertFalse($func->invoke(1,1));
        $this->assertFalse($func->invoke('One', 'One'));
        $this->assertTrue($func->invoke(1,3));
    }

    public function testNotEmpty() {
        $func = $this->_getFunction('notEmpty');
        $this->assertTrue($func->invoke(3));
        $this->assertFalse($func->invoke(0));
        $this->assertFalse($func->invoke(''));
        $this->assertFalse($func->invoke(null));
    }

    public function testMoreOrEqual() {
        $func = $this->_getFunction('moreOrEqual');
        $this->assertTrue($func->invoke(3,1));
        $this->assertTrue($func->invoke(1,1));
        $this->assertTrue($func->invoke('One', 'One'));
        $this->assertFalse($func->invoke(1,2));
    }

    public function testLessOrEqual() {
        $func = $this->_getFunction('lessOrEqual');
        $this->assertFalse($func->invoke(3,1));
        $this->assertTrue($func->invoke(1,1));
        $this->assertTrue($func->invoke('One', 'One'));
        $this->assertTrue($func->invoke(1,2));
    }

    public function testIn() {
        $func = $this->_getFunction('in');
        $this->assertFalse($func->invoke(3,array(1,2,5)));
        $this->assertTrue($func->invoke(1,array(1,2,3)));
        $this->assertTrue($func->invoke('One', array('One', 'Two')));
    }

    public function testNotIn() {
        $func = $this->_getFunction('notIn');
        $this->assertTrue($func->invoke(3,array(1,2,5)));
        $this->assertFalse($func->invoke(1,array(1,2,3)));
        $this->assertFalse($func->invoke('One', array('One', 'Two')));
    }

    public function testContains() {
        $func = $this->_getFunction('contains');
        $this->assertTrue($func->invoke('One in two', 'one'));
        $this->assertFalse($func->invoke('On in two', 'one'));
    }

    public function testRange() {
        $func = $this->_getFunction('range');
        $this->assertTrue($func->invoke(2, array(1,6)));
        $this->assertFalse($func->invoke(10, array(2,6)));
    }

    /**
     * @expectedException \Verifier\DatatypeException
     */
    public function testLengthMoreOrEqual() {
        $func = $this->_getFunction('lengthMoreOrEqual');
        $this->assertTrue($func->invoke('One', 3));
        $this->assertTrue($func->invoke('One', 2));
        $this->assertFalse($func->invoke('One', 6));
        $func->invoke(1121, 1); // Exception!
    }

    /**
     * @expectedException \Verifier\DatatypeException
     */
    public function testLengthLessOrEqual() {
        $func = $this->_getFunction('lengthLessOrEqual');
        $this->assertTrue($func->invoke('One', 5));
        $this->assertTrue($func->invoke('One', 3));
        $this->assertFalse($func->invoke('One', 1));
        $func->invoke(1121, 1); // Exception!
    }

    public function testNumeric() {
        $func = $this->_getFunction('numeric');
        $this->assertTrue($func->invoke(10));
        $this->assertTrue($func->invoke(10.2));
        $this->assertFalse($func->invoke('12One'));
        $this->assertTrue($func->invoke('10,2'));
        $this->assertFalse($func->invoke('12 One'));
        $this->assertTrue($func->invoke('12 000'));
    }

    public function testByFunction() {
        $func = $this->_getFunction('byFunction');
        $this->assertTrue($func->invoke(10, function ($v) {
            return $v == 10;
        }));
        $this->assertFalse($func->invoke(101, function ($v) {
            return $v == 10;
        }));
    }

    public function testByRegularExpression() {
        $func = $this->_getFunction('byRegularExpression');
        $this->assertTrue($func->invoke(10, '/\d/'));
        $this->assertFalse($func->invoke(101, '/\s/'));
    }

    public function testEmail() {
        $func = $this->_getFunction('email');
        $this->assertTrue($func->invoke('some@mail.com'));
        $this->assertFalse($func->invoke('someAmail.com'));
        $this->assertFalse($func->invoke('some@mail_com'));
    }

    public function testUrl() {
        $func = $this->_getFunction('url');
        $this->assertTrue($func->invoke('http://ya.ru'));
        $this->assertTrue($func->invoke('web://some/Amail.com'));
        $this->assertFalse($func->invoke('some_mail_com'));
    }
}
