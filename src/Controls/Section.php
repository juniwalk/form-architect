<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls;

interface Section
{
	/**
	 * @return Field[]
	 */
	public function getFields();


	/**
	 * @return array
	 */
	public function getScheme();


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = []);


	/**
	 * @return bool
	 */
	public function isMergeAllowed();


	/**
	 * @return bool
	 */
	public function isDeleteAllowed();
}
