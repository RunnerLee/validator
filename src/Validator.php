<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-20 15:22
 */

namespace Runner\Validator;

use Closure;

/**
 * Class Validator.
 */
class Validator
{
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
    protected $messageTemplates = [];

    protected static $extensions = [];

    /**
     * Validator constructor.
     *
     * @param array $data
     * @param array $ruleGroups
     */
    public function __construct(array $data, array $ruleGroups)
    {
        $this->data = $data;
        $this->parseRules($ruleGroups);
        $this->messageTemplates = require __DIR__.'/message.php';
    }

    public static function addExtension($name, $callback, $isForce = false)
    {
        $name = self::formatRuleName($name);

        self::$extensions[$name] = $callback;

        $isForce && self::$forceRules[] = $name;
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
                        $this->messages[$field][$rule] = $this->buildFailMessage($rule, $field, $parameters);
                    }
                }
            } elseif ($forceRules = array_intersect(self::$forceRules, array_keys($rules))) {
                $value = null;
                foreach ($forceRules as $rule) {
                    if (!$this->runValidateRule($field, null, $rule, $rules[$rule])) {
                        $this->messages[$field][$rule] = $this->buildFailMessage($rule, $field, $rules[$rule]);
                    }
                }
            }
        }

        return !(bool) $this->messages;
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
                if (isset($map[$rule])) {
                    $rule = $map[$rule];
                } else {
                    $rule = $map[$rule] = self::formatRuleName($rule);
                }
                $this->ruleGroups[$field][$rule] = ('' === $parameters ? [] : explode(',', $parameters));
            }
        }
        unset($map);
    }

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
        if (array_key_exists($rule, self::$extensions)) {
            $callback = self::$extensions[$rule];
            if ($callback instanceof Closure) {
                $callback = $callback->bindTo($this);
            }

            return (bool) call_user_func($callback, $field, $value, $parameters);
        }

        return (bool) call_user_func([$this, "validate{$rule}"], $field, $value, $parameters);
    }

    /**
     * @param $rule
     * @param $field
     * @param array $parameters
     *
     * @return string
     */
    protected function buildFailMessage($rule, $field, array $parameters = [])
    {
        if (!isset($this->messageTemplates[$rule])) {
            return "{$field} field check failed";
        }
        array_unshift($parameters, "{$field} {$this->messageTemplates[$rule]}");

        try {
            return call_user_func_array('sprintf', $parameters);
        } catch (\Exception $e) {
            return "{$field} filed check failed";
        }
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateAccept($field, $value, array $parameters = [])
    {
        return in_array(strtolower($value), ['yes', 'on', '1', 1, true], true);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateNumeric($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT) || false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateInteger($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateFloat($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateSize($field, $value, array $parameters)
    {
        $size = filter_var($parameters[0], FILTER_VALIDATE_INT);
        false === $size && $size = filter_var($parameters[0], FILTER_VALIDATE_FLOAT);

        return $this->getSize($field, $value) === $size;
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateUrl($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateBoolean($field, $value, array $parameters = [])
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateConfirm($field, $value, array $parameters)
    {
        return $value === $this->data[$parameters[0]];
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateDate($field, $value, array $parameters = [])
    {
        return false !== strtotime($value);
    }

    /**
     * 邮箱地址
     *
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateEmail($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRequired($field, $value, array $parameters = [])
    {
        return !is_null($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRequiredWith($field, $value, array $parameters)
    {
        return !is_null($value) || !array_key_exists($parameters[0], $this->data);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRequiredWithout($field, $value, array $parameters)
    {
        return !is_null($value) || array_key_exists($parameters[0], $this->data);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRequiredIf($field, $value, array $parameters)
    {
        $otherField = array_shift($parameters);

        return !is_null($value) || (
            !array_key_exists($otherField, $this->data) || false === array_search($this->data[$otherField], $parameters)
        );
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRequiredUnless($field, $value, array $parameters)
    {
        $otherField = array_shift($parameters);

        return !is_null($value) || (
            !array_key_exists($otherField, $this->data) || false !== array_search($this->data[$otherField], $parameters)
        );
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateArray($field, $value, array $parameters = [])
    {
        return is_array($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameteres
     *
     * @return bool
     */
    protected function validateString($field, $value, array $parameteres = [])
    {
        return is_string($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateNullable($field, $value, array $parameters = [])
    {
        return true;
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateMin($field, $value, array $parameters)
    {
        return $this->getSize($field, $value) >= $parameters[0];
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateMax($field, $value, array $parameters)
    {
        return $this->getSize($field, $value) <= $parameters[0];
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRange($field, $value, array $parameters)
    {
        $size = $this->getSize($field, $value);
        if (!isset($parameters[0])) {
            return false;
        }
        if (isset($parameters[1])) {
            if ('' === $parameters[0]) {
                if ('' === $parameters[1]) {
                    return false;
                }

                return $size <= $parameters[1];
            }
            if ('' === $parameters[1]) {
                return $size >= $parameters[0];
            }

            return $size >= $parameters[0] && $size <= $parameters[1];
        }

        return '' === $parameters[0] ? false : ($size >= $parameters[0]);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateRegex($field, $value, array $parameters)
    {
        return (bool) preg_match($parameters[0], $value);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateIn($field, $value, array $parameters)
    {
        return in_array($value, $parameters, true);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateIp($field, $value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateDateFormat($field, $value, array $parameters)
    {
        return !(bool) date_parse_from_format($parameters[0], $value)['error_count'];
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateDateBefore($field, $value, array $parameters)
    {
        return strtotime($value) < strtotime($parameters[0]);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateDateAfter($field, $value, array $parameters)
    {
        return strtotime($value) > strtotime($parameters[0]);
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateJson($field, $value, array $parameters)
    {
        return is_array(json_decode($value, true));
    }

    /**
     * @param $field
     * @param $value
     * @param array $parameters
     *
     * @return bool
     */
    protected function validateDiff($field, $value, array $parameters)
    {
        $specifyField = array_shift($parameters);

        return array_key_exists($specifyField, $this->data) && $value !== $this->data[$specifyField];
    }

    /**
     * @param $field
     * @param $value
     *
     * @return int|mixed
     */
    protected function getSize($field, $value)
    {
        switch (true) {
            case isset($this->ruleGroups[$field]['String']) && is_string($value):
                return strlen($value);
            case is_array($value):
                return count($value);
            case false !== $temp = filter_var($value, FILTER_VALIDATE_INT):
                return $temp;
            case false !== $temp = filter_var($value, FILTER_VALIDATE_FLOAT):
                return $temp;
            default:
                return strlen($value);
        }
    }
}
