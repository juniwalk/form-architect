<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\DI;

final class FormArchitectExtension extends \Nette\DI\CompilerExtension
{
	public function loadConfiguration()
	{
		$this->getContainerBuilder()
			->addDefinition($this->prefix('factory'))
			->setClass('\JuniWalk\FormArchitect\ArchitectFactory');
	}
}
