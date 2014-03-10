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

    public function testUsePath() {
        $v = new Verifier\Verifier(array(
            'one' => array(
                'value' => 10
            )
        ));

        $field = $v->field('one.value');
        $field->equal(10);

        $this->assertTrue($v->isValid());
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
                1,2,3,4,5
            )
        ));

        $v->each('set')->less(4, 'Less than 4');
        $this->assertFalse($v->isValid());
        $this->assertEquals(array(
            'set' => array('Less than 4')
        ), $v->getErrors());
    }

    public function testIfValidProcess() {
        $v = new \Verifier\Verifier(array(
            'data' => ''
        ));

        $v->field('data')
            ->notEmpty('Empty data')
            ->ifValid() // tear process if not valid field
            ->url('data use as url');

        $this->assertFalse($v->isValid());

        $this->assertEquals(array(
            'data' => array(
                'Empty data'
            )
        ), $v->getErrors());

        $v
            ->refresh()
            ->field('data')
            ->notEmpty('Empty data')
            ->url('data use as url');
        $this->assertEquals(array(
            'data' => array(
                'Empty data',
                'data use as url'
            )
        ), $v->getErrors());
    }

    public function testUseDependencyByFields() {
        $v = new \Verifier\Verifier(array(
            'one' => 1,
            'two' => 2,
            'three' => 3
        ));

        $v->useDependency();
        $v->field('one')->more('three', 'one more than three');
        $v->field('three')->more('two')->more('one');

        $this->assertFalse($v->isValid());

        $this->assertEquals(array(
            'one' => array('one more than three')
        ), $v->getErrors());
    }
}
