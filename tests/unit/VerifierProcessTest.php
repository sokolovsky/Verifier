<?php

/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

class VerifierProcessTest extends PHPUnit_Framework_TestCase {

    public function testOne() {
        $v = new Verifier\Verifier(array(
            'one' => 1
        ));

        $field = $v->field('one');
        $field->equal(10);

        $this->assertFalse($v->isValid());
    }

    public function testSimpleFields() {
        $v = new Verifier\Verifier(array(
            'email' => 'some@mai.com',
            'int' => 10,
            'text' => 'some text'
        ));

        $v->field('email')->email('Field as email');
        $v->field('int')->more(9, 'Values need more than `10`');
        $v->field('text')->byRegularExpression('/^some/', 'Need first write `some`');

        $this->assertTrue($v->isValid());

    }

    public function testIteratorProcess() {
        $v = new \Verifier\Verifier(array(
            'set' => array(
                1,2,3,4
            )
        ));

        $v->each('set')->less(4, 'Less than 4');
        $this->assertFalse($v->isValid());
        $this->assertEquals(array(
            'set' => array('Less than 4')
        ), $v->getErrors());
    }
}
