<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-24 17:40
 */

use Runner\Validator\Validator;

class BaseRuleTest extends \PHPUnit_Framework_TestCase
{

    protected $validator;

    public function setUp()
    {
        $this->validator = new class(['password' => '123456', 'password_confirm' => '123456'], []) extends Validator {
            public function callValidateRule()
            {
                $parameters = func_get_args();
                $funcName = array_shift($parameters);
                $value = array_shift($parameters);

                return call_user_func([$this, $funcName], $value, $parameters);
            }
        };
    }

    public function testSize()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateSize', 'a', 1));
        $this->assertSame(false, $this->validator->callValidateRule('validateSize', 'a', 2));
    }

    public function testAccept()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateAccept', 'yes'));
        $this->assertSame(true, $this->validator->callValidateRule('validateAccept', 'no'));
    }

    public function testNumeric()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateNumeric', '1'));
        $this->assertSame(true, $this->validator->callValidateRule('validateNumeric', '1.1'));
        $this->assertSame(false, $this->validator->callValidateRule('validateNumeric', 'runnerlee'));
    }

    public function testInteger()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateInteger', '1'));
        $this->assertSame(false, $this->validator->callValidateRule('validateInteger', '1.1'));
    }

    public function testFloat()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateFloat', '1.1'));
        $this->assertSame(false, $this->validator->callValidateRule('validateFloat', '1.1.1'));
    }

    public function testUrl()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateUrl', 'https://github.com'));
        $this->assertSame(false, $this->validator->callValidateRule('validateUrl', 'github.com'));
    }

    public function testBoolean()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateBoolean', '1'));
        $this->assertSame(true, $this->validator->callValidateRule('validateBoolean', '0'));
        $this->assertSame(false, $this->validator->callValidateRule('validateBoolean', 'runnerlee'));
    }

    public function testConfirm()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateConfirm', '123456', 'password_confirm'));
        $this->assertSame(false, $this->validator->callValidateRule('validateConfirm', '12345', 'password_confirm'));
    }

    public function testRequired()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateRequired', 'runnerlee'));
        $this->assertSame(false, $this->validator->callValidateRule('validateRequired', null));
    }

    public function testDate()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateDate', '1995-09-06'));
        $this->assertSame(false, $this->validator->callValidateRule('validateDate', 'runnerlee'));
    }





}
