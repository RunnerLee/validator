# Changelog For runner/validator

## [1.0.0] - 2018-12-25

### Added
- 增加可引入自定义语言包
- 增加自定义错误消息

### Changed
- 拆分 validate 及消息处理为 trait
- extend 的 callback 类作用域取消绑定到 Validator 示例. validate 函数参数增加传入 Validator 实例
- validate 从 protected 改为 publish