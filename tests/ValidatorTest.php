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
            'numeric_string' => '123456',
            'is_blocked' => 'no',
            'block_reason' => '123',
            'channel' => 'google',
            'diff_alpha' => 'hello',
            'diff_beta' => 'world',
            'json' => '{"a":"b"}',
            'ip' => '127.0.0.1',
            'in' => 'a',
            'float' => '1.44',
            'sub_arr' => [],
            'without_alpha' => 'a',
            'range_a' => '1.5',
            'range_b' => '1.5',
            'range_c' => '1.5',
            'range_e' => '1.5',
            'range_f' => '1.5',
            'range_g' => '3',
            'regex' => '123abc',
            'date_format' => '2018-01-01',
            'date_before' => '2018-01-01',
            'date_after' => '2018-01-01',
            'nullable' => null,
        ];
        $rules = [
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
            'numeric_string' => 'string|size:6',
            'block_reason' => 'string|required_with:is_blocked',
            'channel' => 'required|channel_range',
            'diff_beta' => 'diff:diff_alpha',
            'json' => 'json',
            'ip' => 'ip',
            'in' => 'in:a,b,c',
            'float' => 'required_unless:channel,baidu|float|size:1.44|min:1.44|max:1.44',
            'sub_arr.demo' => 'size:3',
            'without_alpha' => 'required_without:without_beta|required_if:channel,google',
            'nullable' => 'nullable',
            'range_a' => 'range:1,1.5',
            'range_b' => 'range:,1.5',
            'range_c' => 'range:1,',
            'range_g' => 'range:1',
            'regex' => 'regex:#\w+#',
            'date_format' => 'date_format:Y-m-d',
            'date_before' => 'date_before:2018-01-02',
            'date_after' => 'date_after:2017-12-31',
            'nullable' => 'nullable',
        ];

        $self = $this;

        Validator::addExtension('channel_range', function ($field, $value, array $parameters = []) use ($self) {
            $self->assertArrayHasKey($field, $this->data());

            return false !== array_search($value, ['google', 'bing']);
        });

        $validator = new Validator($data, $rules);

        $this->assertSame(true, $validator->validate());
        $this->assertSame($data, $validator->data());

        $data = [
            'range_a' => '1.4',
            'range_b' => '1.4',
            'channel_range' => 'baidu',
        ];

        $validator = new Validator(
            $data,
            [
                'do_not_has_field' => 'required|size:1',
                'range_a' => 'range',
                'range_b' => 'range:,',
                'channel_range' => 'channel_range',
            ]
        );

        $this->assertSame(false, $validator->validate());
        $this->assertSame($data, $validator->data());
        $this->assertSame([
            'do_not_has_field',
            'range_a',
            'range_b',
            'channel_range',
        ], $validator->fails());
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

        Validator::addExtension(
            'testing',
            function () {
                return false;
            },
            false,
            'is testing'
        );

        $validator = new Validator(
            [
                'a' => 'b',
            ],
            [
                'a' => 'testing',
            ]
        );

        $this->assertSame(false, $validator->validate());
        $this->assertSame(
            [
                'a' => [
                    'Testing' => 'a is testing',
                ],
            ],
            $validator->messages()
        );
    }

    public function testClosureBinding()
    {
        Validator::addExtension(
            'testing',
            function () {
                return Validator::class === get_class($this);
            }
        );

        $validator = new Validator(
            [
                'a' => 'b',
            ],
            [
                'a' => 'testing',
            ]
        );

        $this->assertSame(true, $validator->validate());
    }
}
