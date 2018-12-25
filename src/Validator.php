<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-20 15:22
 */

namespace Runner\Validator;

use Runner\Validator\Concerns\MessagesAttributes;
use Runner\Validator\Concerns\ValidatesAttributes;

/**
 * Class Validator.
 */
class Validator
{
    use ValidatesAttributes, MessagesAttributes;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $ruleGroups = [];

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected static $forceRules = ['Required', 'RequiredIf', 'RequiredWith', 'RequiredUnless', 'RequiredWithout'];

    /**
     * @var array
     */
    protected static $extensions = [];

    /**
     * @var array
     */
    protected static $extensionTemplates = [];

    /**
     * Validator constructor.
     *
     * @param array  $data
     * @param array  $rules
     * @param array  $customMessages
     * @param string $file
     */
    public function __construct(array $data, array $rules, array $customMessages = [], $file = __DIR__.'/messages/en.php')
    {
        $this->data = $data;
        $this->parseRules($rules);
        $this->loadMessageTemplate($file, $customMessages);
    }

    /**
     * @param $name
     * @param $callback
     * @param bool   $isForce
     * @param string $message
     */
    public static function addExtension($name, $callback, $isForce = false, $message = null)
    {
        $name = self::formatRuleName($name);

        self::$extensions[$name] = $callback;

        $isForce && self::$forceRules[] = $name;

        !empty($message) && (static::$extensionTemplates[$name] = $message);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->ruleGroups as $field => $rules) {
            if ($this->hasField($field)) {
                $value = $this->getField($field);
                foreach ($rules as $rule => $parameters) {
                    if (!$this->runValidateRule($field, $value, $rule, $parameters)) {
                        $this->messages[$field][$rule] = $this->buildMessage($rule, $field, $parameters);
                    }
                }
            } elseif ($forceRules = array_intersect(self::$forceRules, array_keys($rules))) {
                $value = null;
                foreach ($forceRules as $rule) {
                    if (!$this->runValidateRule($field, null, $rule, $rules[$rule])) {
                        $this->messages[$field][$rule] = $this->buildMessage($rule, $field, $rules[$rule]);
                    }
                }
            }
        }

        return 0 === count($this->messages);
    }

    /**
     * @return array
     */
    public function fails()
    {
        return array_keys($this->messages);
    }

    /**
     * @return array
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @param array $ruleGroups
     */
    protected function parseRules(array $ruleGroups)
    {
        $map = [];
        foreach ($ruleGroups as $field => $rules) {
            foreach (explode('|', $rules) as $rule) {
                list($rule, $parameters) = explode(':', (false === strpos($rule, ':') ? ($rule.':') : $rule), 2);
                !isset($map[$rule]) && $map[$rule] = self::formatRuleName($rule);
                $rule = $map[$rule];
                $this->ruleGroups[$field][$rule] = ('' === $parameters ? [] : explode(',', $parameters));
            }
        }
        unset($map);
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected static function formatRuleName($name)
    {
        return implode(
            '',
            array_map(
                function ($value) {
                    return ucfirst($value);
                },
                explode('_', $name)
            )
        );
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    protected function hasField($field)
    {
        $field = explode('.', $field);
        $item = array_shift($field);
        if (!array_key_exists($item, $this->data)) {
            return false;
        }
        $value = $this->data[$item];

        foreach ($field as $item) {
            if (!array_key_exists($item, $value)) {
                return false;
            }
            $value = $value[$item];
        }

        return true;
    }

    /**
     * @param string $field
     *
     * @return mixed
     */
    protected function getField($field)
    {
        $field = explode('.', $field);
        $item = array_shift($field);
        $value = $this->data[$item];
        foreach ($field as $item) {
            $value = $value[$item];
        }

        return $value;
    }

    /**
     * @param $field
     * @param $value
     * @param $rule
     * @param array $parameters
     *
     * @return bool
     */
    protected function runValidateRule($field, $value, $rule, array $parameters = [])
    {
        $callback = array_key_exists($rule, self::$extensions) ? self::$extensions[$rule] : [$this, "validate{$rule}"];

        return (bool) call_user_func($callback, $field, $value, $parameters, $this);
    }
}
