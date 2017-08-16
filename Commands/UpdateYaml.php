<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateYaml extends Command
{
    protected function configure()
    {
        $this->setName('update');
        $this->addArgument('file');
        $this->addArgument('node-path');
        $this->addArgument('value');
        $this->addOption('inline', 'l', InputOption::VALUE_OPTIONAL, 'The level where you switch to inline YAML', 4);
        $this->addOption('indent', 'i', InputOption::VALUE_OPTIONAL,
            'The amount of spaces to use for indentation of nested nodes', 2);
        $this->addOption('value-type', 't', InputOption::VALUE_OPTIONAL,
            'The typecast for provided value (settype($type) or callable name of function used if exists)', 'mixed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $content = Yaml::parse(file_get_contents($file));
        $value = $input->getArgument('value');

        $cast = $this->getTypeCast($input->getOption('value-type'));

        $value = $cast($value);

        self::set($content, $input->getArgument('node-path'), $value);
        echo Yaml::dump($content, $input->getOption('inline'), $input->getOption('indent'));
    }

    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param string $type
     * @return callable
     */
    private function getTypeCast($type)
    {
        switch ($type) {
            case 'json':
                return function ($x) {
                    return json_decode($x, true);
                };
                break;
            case 'yaml':
            case 'yml':
                return function ($x) {
                    return Yaml::parse($x);
                };
                break;
            case 'mixed':
                return function ($x) {
                    return $x;
                };
                break;
            default:
                return function ($x) use ($type) {
                    settype($x, $type);

                    return $x;
                };
                break;
        }
    }
}
