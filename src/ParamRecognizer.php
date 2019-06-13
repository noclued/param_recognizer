<?php

namespace Noclue\ParamRecognizer;

class ParamRecognizer
{
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function __call($name, $arguments)
    {
        $key = lcfirst(str_replace("get", "", $name));

        return $this->getValueForKey($key);
    }

    private function getValueForKey(string $key) : ?string
    {
        $originalKey = $key;
        $key = sprintf("--%s=", $key);
        foreach ($this->params as $value) {
            if (false !== strpos($value, $key)) {
                return str_replace($key, '', $value);
            }
        }
        $key = sprintf("--%s=", $this->fromCamelCase($originalKey));
        foreach ($this->params as $value) {
            if (false !== strpos($value, $key)) {
                return str_replace($key, '', $value);
            }
        }

        return null;
    }

    private function fromCamelCase(string $input)
    {
         preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
         $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
