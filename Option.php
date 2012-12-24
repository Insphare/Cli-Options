<?php

class Cli_Options {

        /**
         * Required option flag.
         *
         * @var int
         */
        const OPTION_TYPE_REQUIRED = 1;

        /**
         * Optional option flag.
         *
         * @var int
         */
        const OPTION_TYPE_OPTIONAL = 2;

        /**
         * No value option flag.
         *
         * @var int
         */
        const OPTION_TYPE_NO_VALUE = 3;

        /**
         * Contains all given option types.
         * E.g. for validation of type.
         *
         * @var array
         */
        private $optionTypes = array(
                self::OPTION_TYPE_NO_VALUE => 'Optional',
                self::OPTION_TYPE_OPTIONAL => 'Optional',
                self::OPTION_TYPE_REQUIRED => 'Required',
        );

   		/**
         * Short options.
         * Key: Word
         *
         * @var Cli_Option[]
         */
        private $configShortOptions = array();

        /**
         * Long options.
         *
         * Key: Word
         * @var Cli_Option[]
         */
        private $configLongOptions = array();

        /**
         * Contains all given options.
         *
         * @var array
         */
        private $givenOptions = array();

        /**
         * File for help.
         *
         * @var string
         */
        private $file;

 		/**
         * @var array
         */
        private $cachedOptionForBetterPerformance = array();

        /**
         * @param $file
         */
        public function __construct($file) {

                if (strtolower(PHP_SAPI) != 'cli') {
                        throw new Exception('This script run not as cli!');
                }

                $this->file = basename($file);
                $this->addLongOption('help', self::OPTION_TYPE_NO_VALUE, 'Print Help', 'Print help with all options.');
        }

        /**
         * @param $key
         *
         * @return bool
         */
        protected function isSetOption($key) {
                return isset($this->givenOptions[$key]);
        }

        /**
         * @param $key
         *
         * @return null
         */
        public function getOptionValue($key) {
                if (false === $this->isSetOption($key)) {
                        return null;
                }

                return $this->givenOptions[$key];
        }

		/**
         * @param $optionType
         *
         * @return string
         *
         * @throws Exception
         */
        private function getSignByOptionType($optionType) {
                switch ($optionType) {
                        case self::OPTION_TYPE_REQUIRED:
                                $sign = ':';
                                break;

                        case self::OPTION_TYPE_OPTIONAL:
                                $sign = '::';
                                break;

                        case self::OPTION_TYPE_NO_VALUE:
                                $sign = '';
                                break;

                        default:
                                throw new Exception('Unkown option value.');
                }

                return $sign;
        }

        /**
         * @return string
         */
        private function getShortOptionsString() {
                $strShortOpts = '';
                foreach ($this->configShortOptions as $option => $config) {
                        $sign = $this->getSignByOptionType($config->getType());
                        $strShortOpts .= $option . $sign;
                }

                return $strShortOpts;
        }

		/**
         * @return array
         */
        private function getLongOptionsArray() {
                $arrLongOpts = array();
                foreach ($this->configLongOptions as $option => $config) {
                        $sign = $this->getSignByOptionType($config->getType());
                        $arrLongOpts[] = $option . $sign;
                }

                return $arrLongOpts;
        }

        /**
         * @param $type
         * @throws Exception
         */
        private function validateOptionType($type) {
                if (!isset($this->optionTypes[$type])) {
                        throw new Exception('Unknown option type: ' . $type);
                }
        }

		/**
         * @param $type
         * @return string
         */
        private function getOptionTypeAsString($type) {
                return $this->optionTypes[$type];
        }

        /**
         * Adds a short option.
         *
         * @param $key
         * @param $optionType
         * @param $shortDescription
         * @param $longDescription
         */
		public function addShortOption($key, $optionType, $shortDescription, $longDescription, $defaultValue = null) {
                $key = trim($key);

                if (empty($key)) {
                        throw new Exception('No key given!');
                }

                if (strlen($key) !== 1) {
                        throw new Exception('Your key can only have one char!');
                }

                if (isset($this->configShortOptions[$key])) {
                        throw new Exception('The short option key: ' . $key . ' is already in use!');
                }

                $this->validateOptionType($optionType);

                $option = new Cli_Option();
                $option->setKey($key);
                $option->setType($optionType);
                $option->setLongDescription($longDescription);
                $option->setShortDescription($shortDescription);
                $option->setDefaultValue($defaultValue);

                $this->configShortOptions[$key] = $option;
        }

        /**
         * Adds a long option.
         *
         * @param $key
         * @param $optionType
         * @param $shortExample
         * @param $longExample
         */
		public function addLongOption($key, $optionType, $shortDescription, $longDescription, $defaultValue = null) {
                $key = trim($key);

                if (empty($key)) {
                        throw new Exception('No key given!');
                }

                if (isset($this->configLongOptions[$key])) {
                        throw new Exception('The long option key: ' . $key . ' is already in use!');
                }

                $this->validateOptionType($optionType);

                $option = new Cli_Option();
                $option->setKey($key);
                $option->setType($optionType);
                $option->setLongDescription($longDescription);
                $option->setShortDescription($shortDescription);
                $option->setDefaultValue($defaultValue);

                $this->configLongOptions[$key] = $option;
        }

        /**
         * @return Cli_Option[]
         */
        private function getAllConfigOptions() {
                if (empty($this->cachedOptionForBetterPerformance)) {
                        $options = array();
                        $options += $this->configShortOptions;
                        $options += $this->configLongOptions;
                        $this->cachedOptionForBetterPerformance = $options;
                }

                return $this->cachedOptionForBetterPerformance;
        }

        /**
         * @param $key
         *
         * @return Cli_Option
         */
        private function getConfigByKey($key) {
                $configs = $this->getAllConfigOptions();
                return $configs[$key];
        }

		/**
         * Determine all given params.
         */
		public function parse() {
                $strShortOpts = $this->getShortOptionsString();
                $arrLongOpts = $this->getLongOptionsArray();
                $opts = getopt($strShortOpts, $arrLongOpts);

                foreach ($opts as $strOptionName => $strOptionValue) {
                        $config = $this->getConfigByKey($strOptionName);

                        /**
                         * Fallback: if the no_value option is set, this value should set to true.
                         */
                        if ($config->getType() === self::OPTION_TYPE_NO_VALUE) {
                                $strOptionValue = true;
                        }

                        $this->givenOptions[$strOptionName] = $strOptionValue;
                }

                if ($this->isSetOption('help')) {
                        $this->printHelp();
                }

                // validate required params
                $options = $this->getAllConfigOptions();
                foreach ($options as $key => $config) {
                        if ($config->getType() !== self::OPTION_TYPE_REQUIRED) {
                                // check there is not given options and have default values.
                                $optional = $config->getDefaultValue();

                                if (false === $this->isSetOption($key) && $optional != null) {
                                        $this->givenOptions[$key] = $optional;
                                }

                                continue;
                        }

                        $optionValue = $this->getOptionValue($key);
                        if (empty($optionValue)) {
                                $this->printHelp('Missing required param [' . $key . '].');
                        }
                }
        }

		/**
         * @param Cli_Option $config
         * @param string $prefix
         */
        private function getHelpLongLine(Cli_Option $config, $prefix = '-') {
                $temp = $prefix . "%s\t[%s]\t%s\t%s %s";

                $default = '';
                if (null !== $config->getDefaultValue()) {
                        $default = '[Default: ' . $config->getDefaultValue() . ']';
                }

                return sprintf($temp, $config->getKey(), $this->getOptionTypeAsString($config->getType()), $config->getShortDescription(), ucfirst($config->getLongDescription()), $default);
        }

		/**
         * @param null $text
         */
        public function printHelp($text = null) {
                $descriptionShort = array();
                $descriptionLong = array();
                $print = array();
                $print[] = '';

                if (null !== $text) {
                        $print[] = $text;
                        $print[] = '';
                }

                $opts = array();
                foreach ($this->configShortOptions as $key => $config) {
                        $value = $config->getShortDescription();
                        $opts[] = '-' . $key . '[' . $value . ']';
                        $descriptionShort[] = $this->getHelpLongLine($config);
                }

                foreach ($this->configLongOptions as $key => $config) {
                        if ($key !== 'help') {
                                $value = $config->getShortDescription();
                                $opts[] = '--' . $key . '="[' . $value . ']"';
                        }

                        $descriptionLong[] = $this->getHelpLongLine($config, '--');
                }

                $print[] = 'Usage: php ' . $this->file . ' ' . implode(' ', $opts);
                $print[] = '';

                if (count($descriptionShort)) {
                        $print[] = '';
                        $print[] = 'Short options:';
                        foreach ($descriptionShort as $desc) {
                                $print[] = $desc;
                        }
                }

                if (count($descriptionLong)) {
                        $print[] = '';
                        $print[] = 'Long options:';
                        foreach ($descriptionLong as $desc) {
                                $print[] = $desc;
                        }
                }

                $print[] = '';
                $print[] = '';

                echo implode(PHP_EOL, $print);
                die();
        }
}
