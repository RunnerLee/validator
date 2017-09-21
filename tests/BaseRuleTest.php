<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-24 17:40
 */
require __DIR__.'/ValidatorTester.php';

class BaseRuleTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new ValidatorTester(['password' => '123456', 'password_confirm' => '123456'], []);
    }

    public function testSize()
    {
        $this->assertSame(true, $this->validator->callValidateRule('validateSize', '', 'a', 1));
        $this->assertSame(false, $this->validator->callValidateRule('validateSize', '', 'a', 2));
    }

//
//
//    public function testAccept()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateAccept', 'yes'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateAccept', 'no'));
//    }
//
//    public function testNumeric()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateNumeric', '1'));
//        $this->assertSame(true, $this->validator->callValidateRule('validateNumeric', '1.1'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateNumeric', 'runnerlee'));
//    }
//
//    public function testInteger()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateInteger', '1'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateInteger', '1.1'));
//    }
//
//    public function testFloat()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateFloat', '1.1'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateFloat', '1.1.1'));
//    }
//
//    public function testUrl()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateUrl', 'https://github.com'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateUrl', 'github.com'));
//    }
//
//    public function testBoolean()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateBoolean', '1'));
//        $this->assertSame(true, $this->validator->callValidateRule('validateBoolean', '0'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateBoolean', 'runnerlee'));
//    }
//
//    public function testConfirm()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateConfirm', '123456', 'password_confirm'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateConfirm', '12345', 'password_confirm'));
//    }
//
//    public function testRequired()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateRequired', 'runnerlee'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRequired', null));
//    }
//
//    public function testDate()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateDate', '1995-09-06'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateDate', 'runnerlee'));
//    }
//
//    public function testEmail()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateEmail', 'runnerleer@gmail.com'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateEmail', 'runnerleer@gmail'));
//    }
//
//    public function testArray()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateArray', []));
//        $this->assertSame(false, $this->validator->callValidateRule('validateArray', 'runnerleer@gmail.com'));
//    }
//
//    public function testNullable()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateNullable', null));
//        $this->assertSame(true, $this->validator->callValidateRule('validateNullable', 'runnerlee'));
//    }
//
//    public function testMin()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateMin', 'string', 6));
//        $this->assertSame(true, $this->validator->callValidateRule('validateMin', '6', 6));
//        $this->assertSame(true, $this->validator->callValidateRule('validateMin', range(0, 5), 6));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMin', 'string', 7));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMin', '6', 7));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMin', range(0, 5), 7));
//    }
//
//    public function testMax()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateMax', 'string', 6));
//        $this->assertSame(true, $this->validator->callValidateRule('validateMax', '6', 6));
//        $this->assertSame(true, $this->validator->callValidateRule('validateMax', range(0, 5), 6));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMax', 'string', 5));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMax', '6', 5));
//        $this->assertSame(false, $this->validator->callValidateRule('validateMax', range(0, 5), 5));
//    }
//
//    public function testRange()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateRange', 6, 6));
//        $this->assertSame(true, $this->validator->callValidateRule('validateRange', 6, 6, ''));
//        $this->assertSame(true, $this->validator->callValidateRule('validateRange', 6, 6, 7));
//        $this->assertSame(true, $this->validator->callValidateRule('validateRange', 6, '', 7));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRange', 6));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRange', 6, 7));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRange', 6, '', 5));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRange', 6, 3, 4));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRange', 6, '', 4));
//    }
//
//    public function testRegex()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateRegex', '123456', '\d+'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateRegex', '123456', '\s+'));
//    }
//
//    public function testIn()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateIn', 'hello', 'hello', 'world'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateIn', 'hack', 'hello', 'world'));
//    }
//
//    public function testIp()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateIp', '114.114.114.114'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateIp', '114.114.114'));
//    }
//
//    public function testDateFormat()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateDateFormat', '1995-09-06', 'Y-m-d'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateDateFormat', '1995-09-06', 'H:i:s'));
//    }
//
//    public function testJson()
//    {
//        $this->assertSame(true, $this->validator->callValidateRule('validateJson', '{}'));
//        $this->assertSame(false, $this->validator->callValidateRule('validateJson', 'runnerlee'));
//    }
}
