<?php

class Cli_Option {

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $longDescription;

	/**
	 * @var string
	 */
	private $shortDescription;

	/**
	 * @var string
	 */
	private $defaultValue;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @param $defaultValue
	 */
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}

	/**
	 * @return string
	 */
	public function getDefaultValue() {
		return $this->defaultValue;
	}

	/**
	 * @param $longDescription
	 */
	public function setLongDescription($longDescription) {
		$this->longDescription = $longDescription;
	}

	/**
	 * @return string
	 */
	public function getLongDescription() {
		return $this->longDescription;
	}

	/**
	 * @param $shortDescription
	 */
	public function setShortDescription($shortDescription) {
		$this->shortDescription = strtoupper($shortDescription);
	}

	/**
	 * @return string
	 */
	public function getShortDescription() {
		return $this->shortDescription;
	}

	/**
	 * @param $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key) {
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}
}
