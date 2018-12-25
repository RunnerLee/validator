<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2018-12
 */

namespace Runner\Validator\Concerns;

use Exception;

trait MessagesAttributes
{
    protected $templates = [];

    protected function loadMessageTemplate($file, array $customMessage = [])
    {
        $this->templates = require $file;
        $this->templates = array_merge($this->templates, self::$extensionTemplates);

        foreach ($customMessage as $rule => $template) {
            $this->templates[self::formatRuleName($rule)] = $template;
        }
    }

    /**
     * @param string $rule
     * @param string $field
     * @param array  $parameters
     *
     * @return string
     */
    protected function buildMessage($rule, $field, array $parameters = [])
    {
        if (!isset($this->templates[$rule])) {
            return "{$field} field check failed";
        }
        array_unshift($parameters, "{$field} {$this->templates[$rule]}");

        try {
            return sprintf(...$parameters);
        } catch (Exception $e) {
            return "{$field} filed check failed";
        }
    }
}
