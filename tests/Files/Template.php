<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Tests\Files;

final class Template implements \Nette\Application\UI\ITemplate
{
	/** @var string */
	private $file;

	/** @var array */
	private $values = [];


	/**
	 * @param string  $key
	 * @param mixed   $value
	 */
	public function add($key, $value)
	{
		$this->values[$key] = $value;
	}


	/**
	 * @param  string  $key
	 * @return mixed|NULL
	 */
	public function get($key)
	{
		if (!isset($this->values[$key])) {
			return NULL;
		}

		return $this->values[$key];
	}


	public function render()
	{

	}


	/**
	 * @param string  $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}


	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}
}
