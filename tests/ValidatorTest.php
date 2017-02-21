<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-21 11:13
 */


use Runner\Validator\Validator;


class ValidatorTest extends PHPUnit_Framework_TestCase
{

    public function testValidator()
    {
        $validator = new Validator(
            [
                'data' => 'demo',
                'url' => 'http://runnerlee.com',
                'array' => [
                    'hello' => '1.1',
                ],
                'accept' => 'on',
                'integer' => '1',
                'boolean' => 0,
                'string' => 'hello world',
                'confirm' => 'hello world',
                'date' => '1995-09-06',
                'email' => 'runnerleer@gmail.com',
            ],
            [
                'data' => 'size:4',
                'url' => 'url',
                'array' => 'required|array',
                'array.hello' => 'numeric',
                'accept' => 'accept',
                'integer' => 'integer',
                'boolean' => 'boolean',
                'string' => 'string',
                'confirm' => 'confirm:string',
                'date' => 'date',
                'email' => 'email',
            ]
        );

        $this->assertSame(true, $validator->validate());
    }

}
