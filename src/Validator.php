<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-20 15:22
 */
namespace Runner\Validator;

/**
 * Class Validator
 * @package Runner\Validation
 */
class Validator
{

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $ruleGroups;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var array
     */
    protected $forceRules = ['required'];

    /**
     * Validator constructor.
     * @param array $data
     * @param array $ruleGroups
     */
    public function __construct(array $data, array $ruleGroups)
    {
        $this->data = $data;
        $this->parseRules($ruleGroups);
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->ruleGroups as $field => $rules) {
            if ($this->hasField($field)) {
                foreach ($rules as $rule => $parameters) {
                    if (!$this->runValidateRule($field, $rule, $parameters)) {
                        $this->messages[$field][] = $rule;
                    }
                }
            } elseif ($forceRules = array_intersect($this->forceRules, array_keys($rules))) {
                $this->messages[$field] = $forceRules;
            }
        }

        return !(bool)$this->messages;
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
        foreach ($ruleGroups as $field => $rules) {
            foreach (explode('|', $rules) as $rule) {
                false === strpos(':', $rule) && $rule .= ':';
                list($rule, $parameters) = explode(':', $rule);
                $this->ruleGroups[$field][$rule] = explode(',', $parameters);
            }
        }
    }

    /**
     * @param string $field
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
     * @param $rule
     * @param array $parameters
     * @return bool
     */
    protected function runValidateRule($field, $rule, array $parameters = [])
    {
        return (bool)call_user_func([$this, 'validate' . ucfirst($rule)], $this->getField($field), $parameters);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateAccept($value, array $parameters = [])
    {
        return in_array(strtolower($value), ['yes', 'on', '1', 1, true]);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateNumeric($value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT) || false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateInteger($value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateFloat($value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateSize($value, array $parameters)
    {
        switch (true) {
            case is_array($value):
                $value = count($value);
                break;
            case false !== $temp = filter_var($value, FILTER_VALIDATE_INT):
                $value = $temp;
                break;
            default:
                $value = strlen($value);
        }

        return $value === intval($parameters[0]);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateUrl($value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateBoolean($value, array $parameters = [])
    {
        return in_array($value, [true, false, 0, 1, '0', '1']);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateConfirm($value, array $parameters)
    {
        return $value === $this->data[$parameters[0]];
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateDate($value, array $parameters = [])
    {
        return false !== strtotime($value);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateEmail($value, array $parameters = [])
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateRequired($value, array $parameters = [])
    {
        return !is_null($value);
    }

    /**
     * @param $value
     * @param array $parameters
     * @return bool
     */
    protected function validateArray($value, array $parameters = [])
    {
        return is_array($value);
    }

    protected function validateString($value, array $parameteres = [])
    {
        return is_string($value);
    }
}
