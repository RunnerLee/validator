# Validator

参考了 laravel 的验证规则语法, 同时又复制粘贴了 `vlucas/valitron` 的消息模板. 

无任何依赖, 在 FastD 中使用, 可以使用扩展包

[RunnerLee/validation](https://github.com/RunnerLee/validation)

## 使用
```

use Runner\Validator\Validator;

增加规则扩展
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



## 参考:
[https://github.com/vlucas/valitron](https://github.com/vlucas/valitron)

[https://github.com/illuminate/validation](https://github.com/illuminate/validation)