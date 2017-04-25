<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-21 11:13
 */
use Runner\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $data = [
            'data'  => 'demo',
            'url'   => 'http://runnerlee.com',
            'array' => [
                'hello' => '1.1',
            ],
            'accept'         => 'on',
            'integer'        => '1',
            'boolean'        => 0,
            'string'         => 'hello world',
            'confirm'        => 'hello world',
            'date'           => '1995-09-06',
            'email'          => 'runnerleer@gmail.com',
            'numeric_string' => '123456',
            'is_blocked'     => 'no',
            'block_reason'   => '123',
            'channel'        => 'google',
        ];
        $rules = [
            'data'           => 'size:4',
            'url'            => 'url',
            'array'          => 'required|array',
            'array.hello'    => 'numeric',
            'accept'         => 'accept',
            'integer'        => 'integer',
            'boolean'        => 'boolean',
            'string'         => 'string',
            'confirm'        => 'confirm:string',
            'date'           => 'date',
            'email'          => 'email',
            'numeric_string' => 'string|size:6',
            'block_reason'   => 'string|required_with:is_blocked',
            'channel'        => 'required|channel_range',
        ];

        $self = $this;

        Validator::addExtension('channel_range', function ($field, $value, array $parameters = []) use ($self) {
            $self->assertArrayHasKey('data', $this->data());
            $self->assertArrayHasKey('url', $this->data());
            $self->assertArrayHasKey('array', $this->data());

            return false !== array_search($value, ['google', 'bing']);
        });

        $validator = new Validator($data, $rules);

        $this->assertSame(true, $validator->validate());
        $this->assertSame($data, $validator->data());
    }

    public function testMessage()
    {
        $data = [
            'data' => 'demo',
        ];
        $rules = [
            'data' => 'size:3',
        ];
        $validator = new Validator($data, $rules);

        $this->assertSame(false, $validator->validate());
        $this->assertSame("data 's size must be 3", $validator->messages()['data']['Size']);
    }
}
