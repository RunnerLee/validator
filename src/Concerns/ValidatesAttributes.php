<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2018-12
 */

namespace Runner\Validator\Concerns;

use Runner\Validator\Validator;

trait ValidatesAttributes
{
    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateAccept($field, $value, array $parameters, Validator $validator)
    {
        return in_array(strtolower($value), ['yes', 'on', '1', 1, true], true);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateNumeric($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT) || false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateInteger($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateFloat($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_FLOAT);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateSize($field, $value, array $parameters, Validator $validator)
    {
        $size = filter_var($parameters[0], FILTER_VALIDATE_INT);
        false === $size && $size = filter_var($parameters[0], FILTER_VALIDATE_FLOAT);

        return $this->getSize($field, $value) === $size;
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateUrl($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_URL);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateBoolean($field, $value, array $parameters, Validator $validator)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateConfirm($field, $value, array $parameters, Validator $validator)
    {
        return $value === $this->data[$parameters[0]];
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateDate($field, $value, array $parameters, Validator $validator)
    {
        return false !== strtotime($value);
    }

    /**
     * 邮箱地址
     *
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateEmail($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRequired($field, $value, array $parameters, Validator $validator)
    {
        return !is_null($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRequiredWith($field, $value, array $parameters, Validator $validator)
    {
        return !is_null($value) || !array_key_exists($parameters[0], $this->data);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRequiredWithout($field, $value, array $parameters, Validator $validator)
    {
        return !is_null($value) || array_key_exists($parameters[0], $this->data);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRequiredIf($field, $value, array $parameters, Validator $validator)
    {
        $otherField = array_shift($parameters);

        return !is_null($value) || (
                !array_key_exists($otherField, $this->data) || false === array_search($this->data[$otherField], $parameters)
            );
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRequiredUnless($field, $value, array $parameters, Validator $validator)
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
    public function validateArray($field, $value, array $parameters, Validator $validator)
    {
        return is_array($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateString($field, $value, array $parameters, Validator $validator)
    {
        return is_string($value);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateNullable($field, $value, array $parameters, Validator $validator)
    {
        return true;
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateMin($field, $value, array $parameters, Validator $validator)
    {
        return $this->getSize($field, $value) >= $parameters[0];
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateMax($field, $value, array $parameters, Validator $validator)
    {
        return $this->getSize($field, $value) <= $parameters[0];
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRange($field, $value, array $parameters, Validator $validator)
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
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateRegex($field, $value, array $parameters, Validator $validator)
    {
        return (bool) preg_match($parameters[0], $value);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateIn($field, $value, array $parameters, Validator $validator)
    {
        return in_array($value, $parameters, true);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateIp($field, $value, array $parameters, Validator $validator)
    {
        return false !== filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateDateFormat($field, $value, array $parameters, Validator $validator)
    {
        return !(bool) date_parse_from_format($parameters[0], $value)['error_count'];
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateDateBefore($field, $value, array $parameters, Validator $validator)
    {
        return strtotime($value) < strtotime($parameters[0]);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateDateAfter($field, $value, array $parameters, Validator $validator)
    {
        return strtotime($value) > strtotime($parameters[0]);
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateJson($field, $value, array $parameters, Validator $validator)
    {
        return is_array(json_decode($value, true));
    }

    /**
     * @param $field
     * @param $value
     * @param array     $parameters
     * @param Validator $validator
     *
     * @return bool
     */
    public function validateDiff($field, $value, array $parameters, Validator $validator)
    {
        $specifyField = array_shift($parameters);

        return array_key_exists($specifyField, $this->data) && $value !== $this->data[$specifyField];
    }

    /**
     * @param $field
     * @param $value
     *
     * @return int|float
     */
    public function getSize($field, $value)
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
                return mb_strlen($value);
        }
    }
}
