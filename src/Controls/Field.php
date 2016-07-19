<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls;

interface Field
{
	/**
	 * @return string
	 */
	public function getClass();


	/**
	 * @return array
	 */
	public function getScheme();


	/**
	 * @param  array  $scheme
	 * @return NULL
	 */
	public function setScheme(array $scheme = []);


	/**
	 * @return bool
	 */
	public function hasToolbar();


	/**
	 * @return bool
	 */
	public function isCloneable();


	/**
	 * @return bool
	 */
	public function isSortable();


	/**
	 * @return bool
	 */
	public function hasUpwardMove();


	/**
	 * @return bool
	 */
	public function hasDownwardMove();
}
