<?php


namespace Cms\Core\Di;


class Config
{
    private $config;

    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new \Exception(sprintf('Config file not found: %s', $file));
        }

        $this->config = parse_ini_file($file, true);
    }

    public function getByKey($key)
    {
        if (strpos($key, ".") !== false) {

            // Dot syntax
            $keyArray = explode(".", $key);
            if (count($keyArray) > 2) {
                throw new \Exception('Only one dot allowed per key!');
            }

            $keyPrimary   = $keyArray[0];
            $keySecondary = $keyArray[1];
            if (isset($this->config[$keyPrimary][$keySecondary])) {
                return $this->config[$keyPrimary][$keySecondary];
            } else {
                throw new \Exception(sprintf('Cannot get config value for key: %s', $key));
            }

        } else {

            // Single-key syntax
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                throw new \Exception(sprintf('Cannot get config value for key: %s', $key));
            }

        }
    }
}