# Validator

参考了 Laravel 的验证规则语法, 同时又复制粘贴了 `vlucas/valitron` 的消息模板. 

无任何依赖, 在 FastD 中使用, 可以使用扩展包

[RunnerLee/validation](https://github.com/RunnerLee/validation)

## 使用
```php
<?php
use Runner\Validator\Validator;

// 增加规则扩展
Validator::addExtension(
    'channel',
    function ($field, $value, array $parameters = []) {
        return false !== array_search($value, ['google', 'bing']);
    },
    false
);

$data = [
    'blog' => 'https://github.com/RunnerLee',
];
$rule = [
    'blog' => 'required|url',
];
$validator = new Validator($data, $rule);

var_dump($validator->validate()); // output: true

// 获取错误消息
$validator->messages();
```

## 内置验证规则
* `accept`：字段的取值范围必须是：yes、on、'1'、1、true 中的一个
* `numeric`：字段的值必须是数字（包括整形和浮点数）
* `integer`：字段的值必须是整形
* `float`：字段的值必须是浮点型
* `size`：字段的值必须为指定长度
* `url`：字段的值必须是合法的 url 类型
* `boolean`：字段的值必须是布尔类型
* `confirm`：字段的值必须与指定值相等
* `date`：字段的值必须是一个合法的日期
* `email`：字段的值必须是一个合法的 email
* `required`：必须包含该字段
* `requiredWith`：如果指定的字段中的任意一个有值且不为空，则此字段为必填
* `requiredWithout`：如果缺少任意一个指定的字段，则此字段为必填
* `requiredIf`：如果指定的其它字段（anotherfield）等于任何一个 value 时，此字段为必填
* `array`：字段的值必须是数组类型
* `string`：字段的值必须是字符串类型
* `min`：字段的值必须大于等于指定值
* `max`：字段的值必须小于等于指定值
* `range`：字段的值长度必须指定范围内，包含 min 和 max。
* `regex`：字段的值必须匹配指定的正则表达式
* `in`：字段的值必须在指定的数组内
* `ip`：字段的值必须是一个合法的 ip
* `dateFormat`：字段的值必须是一个合法的 date format 格式。参考：http://php.net/manual/en/datetime.createfromformat.php
* `dateBefore`：字段的值必须小于给定时间。给定时间值非时间戳格式，而是 date/time 字符串，如：10 September 2000
* `dateAfter`：字段的值必须大于给定时间。时间值格式同上。
* `json`：字段的值必须是一个合法的 JSON 格式

## Note

#### 关于空字符串
如果传入一个数组:

```php
$data = [
    'foo' => '',
];
```

同时规则设置为:
```php
$rules= [
    'foo' => 'string',
];
```

此时校验结果会是通过的. 也就是说 `validator` 不会把 `''` 当作 `null` 处理.


## 参考
[https://github.com/vlucas/valitron](https://github.com/vlucas/valitron)
[https://github.com/illuminate/validation](https://github.com/illuminate/validation)
