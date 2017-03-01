<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 17-2-27 09:59
 */
class ValidatorTester extends \Runner\Validator\Validator
{
    public function callValidateRule()
    {
        $parameters = func_get_args();
        $funcName = array_shift($parameters);
        $field = array_shift($parameters);
        $value = array_shift($parameters);

        return call_user_func([$this, $funcName], $field, $value, $parameters);
    }
}
