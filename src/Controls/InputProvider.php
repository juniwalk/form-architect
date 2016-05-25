<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls;

use Nette\Forms\Container;

interface InputProvider
{
	/**
	 * @param  Container  $form
	 * @return Nette\Forms\IControl
	 */
	public function createInput(Container $form);
}
