<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls;

/**
 * @method onSchemeChange
 */
interface Architect
{
	/**
	 * @return Architect
	 */
	public function getArchitect();


	/**
	 * @return Container
	 */
	public function getForm();


	/**
	 * @return Section[]
	 */
	public function getSections();


	/**
	 * @return array
	 */
	public function getScheme();


	/**
	 * @param  array  $scheme
	 * @return NULL
	 */
	public function setScheme(array $scheme = []);
}
