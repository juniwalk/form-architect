<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\IControl as Input;

final class Description extends AbstractField implements InputProvider
{
	/** @var bool */
	protected $hasLabel = false;

	/**
	 * @return bool
	 */
	public function isSortable(): bool
	{
		return true;
	}


	/**
	 * @param  bool  $label
	 * @return void
	 */
	public function setLabel(bool $label): void
	{
	}


	/**
	 * @param  Form  $form
	 * @return Input|null
	 */
	public function createInput(Form $form): ?Input
	{
		return null;
	}


	/**
	 * @return void
	 */
	protected function createSchemeField(): void
	{
		$form = $this->getForm();
		$form->addTextarea('content');
	}
}
