<?php

namespace Lighthouse\CoreBundle\Test;

use DomainException;

class JsonPath
{
    const DELIMITER = '.';

    /**
     * @var mixed|array
     */
    protected $json;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param mixed $json
     * @param string $path
     */
    public function __construct($json, $path)
    {
        $this->json = $json;
        $this->path = $path;
    }

    /**
     * @param bool $suppressNotFound
     * @return array|mixed
     */
    public function getValues($suppressNotFound = false)
    {
        $result = $this->path($this->json, $this->path, $suppressNotFound);
        if (false === strpos($this->path, '*')) {
            $result = array($result);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $values = $this->getValues();
        $value = reset($values);
        return $value;
    }

    /**
     * @param array $array
     * @param string $path
     * @param bool $suppressNotFound
     * @throws \DomainException
     * @throws \Exception
     * @return array|mixed
     */
    protected function path(array $array, $path, $suppressNotFound = false)
    {
        $delimiter = self::DELIMITER;
        // Remove starting delimiters and spaces
        $path = ltrim($path, "{$delimiter} ");

        // Remove ending delimiters, spaces, and wildcards
        $path = rtrim($path, "{$delimiter} *");

        // Split the keys by delimiter
        $keys = explode($delimiter, $path);

        do {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                // Make the key an integer
                $key = (int)$key;
            }

            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        // Dig down into the next part of the path
                        $array = $array[$key];
                    } else {
                        // Unable to dig deeper
                        break;
                    }
                } else {
                    // Found the path requested
                    return $array[$key];
                }
            } elseif ($key === '*') {
                // Handle wildcards

                $values = array();
                foreach ($array as $arr) {
                    try {
                        $values[] = $this->path($arr, implode($delimiter, $keys));
                    } catch (\DomainException $e) {
                        if (!$suppressNotFound) {
                            throw $e;
                        }
                    }
                }

                if ($values) {
                    // Found the values requested
                    return $values;
                } else {
                    // Unable to dig deeper
                    break;
                }
            } else {
                // Unable to dig deeper
                break;
            }
        } while ($keys);

        throw new DomainException(sprintf("Json path '%s' not found", $this->path));
    }

    /**
     * @return bool
     */
    public function isFound()
    {
        try {
            $this->getValues();
            return true;
        } catch (DomainException $e) {
            return false;
        }
    }

    /**
     * return int
     */
    public function count()
    {
        try {
            $value = $this->getValues();
            if (is_array($value)) {
                $count = count($value);
            } else {
                $count = 1;
            }
        } catch (DomainException $e) {
            $count = 0;
        }

        return $count;
    }
}
