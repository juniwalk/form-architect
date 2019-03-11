<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\IControl as Input;

interface InputProvider
{
	/**
	 * @param  Form  $form
	 * @return Input|null
	 */
	public function createInput(Form $form): ?Input;
}
