<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\Form as Rule;
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
		'textarea',
		null, // Separator
		'file',
		'url',
		'datetime',
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
		$name = $this->createName('option');

		$scheme = $this->getScheme();
		$scheme['options'][$name] = null;

		$this->setScheme($scheme);
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @param  int  $index
	 * @return void
	 * @throws Exception
	 */
	public function handleDeleteOption(string $index): void
	{
		$scheme = $this->getScheme();

		$key = array_keys($scheme['options'])[$index];
		unset($scheme['options'][$key]);

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

		if ($options = $scheme['options'] ?? []) {
			foreach ($options as $key => $value) {
				$name = $value['key'] ?: $this->createName('option');

				$options[$name] = $value['option'];
				unset($options[$key]);
			}
			
			$scheme['options'] = $options;
		}

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

		if ($options = $scheme['options'] ?? []) {
			$scheme['options'] = [];

			foreach ($options as $key => $value) {
				$scheme['options'][] = [
					'key' => $key,
					'option' => $value,
				];
			}
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
			case 'url':
				$input = $form->addText($name);
				break;

			case 'file':
				$input = $form->addUpload($name)
					->addRule(Rule::MAX_FILE_SIZE, 'form-architect.flash.max-file-size', 2097152);
				break;

			case 'email':
				$input = $form->addText($name)
					->addRule(Rule::EMAIL, 'form-architect.flash.email-invalid');
				break;

			case 'datetime':
				$input = $form->addDateTime($name)->setFormat('Y-m-d H:i');
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

		$input->setRequired(false);

		if ($this->getValue('isRequired')) {
			$input->setRequired($this->getValue('title').' is required.');
		}

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
			$option->addText('key');
		}
	}
}
