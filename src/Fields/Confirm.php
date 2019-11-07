<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\IControl as Input;

final class Confirm extends AbstractField implements InputProvider
{
	/** @var bool */
	private $isRequired = true;


	/**
	 * @return void
	 * @throws Exception
	 */
	public function handleRequired(): void
	{
		$this->setRequired(!$this->isRequired());

		$this->setScheme($this->getScheme());
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = parent::getScheme();
		$scheme = $scheme + ['isRequired' => $this->isRequired];

		return $scheme;
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		if (isset($scheme['isRequired'])) {
			$this->setRequired($scheme['isRequired']);
		}

		parent::setScheme($scheme);
	}


	/**
	 * @param  bool  $required
	 * @return void
	 */
	public function setRequired(bool $required): void
	{
		$this->isRequired = $required;
	}


	/**
	 * @return bool
	 */
	public function isRequired(): bool
	{
		return $this->isRequired;
	}


	/**
	 * @return bool
	 */
	public function isChoiceControl(): bool
	{
		return true;
	}


	/**
	 * @return bool
	 */
	public function hasToolbar(): bool
	{
		return true;
	}


    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return true;
    }


    /**
     * @return bool
     */
    public function isCloneable(): bool
    {
        return true;
    }


	/**
	 * @param  Form  $form
	 * @return Input|null
	 */
	public function createInput(Form $form): ?Input
	{
		$input = $form->addCheckbox($this->getName());
		$input->setRequired($this->getValue('isRequired'));

		return $input;
	}


	/**
	 * @return void
	 */
	protected function createSchemeField(): void
	{
		$form = $this->getForm();
		$form->addText('title');
		$form->addText('description');
		$form->addText('link');
	}
}
