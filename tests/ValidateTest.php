<?php

use Runner\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidateTest extends TestCase
{
    public function testSize()
    {
        $validator = new Validator([
            'str1' => 'a',
            'arr1' => [1],
            'int1' => 1,
        ], [
            'str1' => 'size:1',
            'arr1' => 'size:1',
            'int1' => 'size:1',
        ]);

        $this->assertTrue($validator->validate());
    }
}