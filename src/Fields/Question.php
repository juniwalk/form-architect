<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\IControl as Input;

final class Question extends AbstractField implements InputProvider
{
	/** @var string */
	private $type = 'text';

	/** @var bool */
	private $isRequired = false;

	/** @var string[] */
	private $types = [
		'text',
		'email',
		'url',
		'password',
		'textarea',
		null, // Separator
		'radio',
		'checkbox',
		'select',
	];


	/**
	 * @param  string  $type
	 * @return void
	 * @throws Exception
	 */
	public function handleType(string $type): void
	{
		$this->setType($type);

		$this->setScheme($this->getScheme());
		$this->getArchitect()->onSchemeChange();
	}


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
	 * @return void
	 * @throws Exception
	 */
	public function handleAddOption(): void
	{
		$scheme = $this->getScheme();
		$scheme['options'][] = ['option' => null];

		$this->setScheme($scheme);
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @param  int  $index
	 * @return void
	 * @throws Exception
	 */
	public function handleDeleteOption(int $index): void
	{
		$scheme = $this->getScheme();

		unset($scheme['options'][$index]);
		$scheme['options'] = array_values($scheme['options']);

		$this->setScheme($scheme);
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}


	/**
	 * @param  string  $type
	 * @throws Exception
	 */
	public function setType(string $type): void
	{
		if (!$type || !in_array($type, $this->types)) {
			throw new \Exception($type);
		}

		$this->type = $this->values['type'] = $type;
	}


	/**
	 * @return string[]
	 */
	public function getTypes(): iterable
	{
		return $this->types;
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = parent::getScheme();
		$scheme = $scheme + ['isRequired' => $this->isRequired];
		$scheme = $scheme + ['type' => $this->type];

		return $scheme;
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		if (isset($scheme['type'])) {
			$this->setType($scheme['type']);
		}

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
		return in_array($this->type, ['radio', 'checkbox', 'select']);
	}


	/**
	 * @return bool
	 */
	public function isSelect(): bool
	{
		return $this->type === 'select';
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
	 * @throws Exception
	 */
	public function createInput(Form $form): ?Input
	{
		$name = $this->getName();
		$items = [];

		if ($options = (array) $this->getValue('options')) {
			foreach ($options as $key => $option) {
				$items[$key] = $option['option'];
			}
		}

		switch ($this->type) {
			case 'text':
				$input = $form->addText($name);
				break;

			case 'password':
				$input = $form->addText($name)
					->addRule(\Nette\Forms\Form::MIN_LENGTH, 'nette.user.password-length', 6);
				break;

			case 'email':
				$input = $form->addText($name)
					->addRule(\Nette\Forms\Form::EMAIL, 'nette.user.email-invalid');
				break;

			case 'textarea':
				$input = $form->addTextArea($name);
				break;

			case 'radio':
				$input = $form->addRadioList($name, null, $items);
				break;

			case 'checkbox':
				$input = $form->addCheckboxList($name, null, $items);
				break;

			case 'select':
				if ($this->getValue('multiple')) {
					$input = $form->addMultiSelect($name);
				}

				if (!$this->getValue('multiple')) {
					$input = $form->addSelect($name);
				}

				$input->setItems($items, false);
				break;

			default: throw new \Exception($this->type);
		}

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
		$form->addText('name');

		if (!$this->isChoiceControl() || $this->isSelect()) {
			$form->addText('description');
		}

		if (!$this->isChoiceControl()) {
			return;
		}

		$options = $form->addContainer('options');

		if ($this->isSelect()) {
			$form->addCheckbox('multiple');
		}

		if (!$this->isSelect()) {
			$form->addCheckbox('inline');
		}

		if (!isset($this->values['options'])) {
			return;
		}

		$optionsCount = count($this->values['options']);

		for ($i = 0; $i < $optionsCount; $i++) {
			$option = $options->addContainer($i);
			$option->addText('option');
		}
	}
}
