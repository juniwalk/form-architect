<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls\Fields;

use JuniWalk\FormArchitect\Controls\InputProvider;
use Nette\Forms\Container;

final class Question extends BaseField implements InputProvider
{
	/** @var string */
	private $type = 'text';

	/** @var string[] */
	private $types = [
		'text' => 'form-architect.designer.field-type-text',
		'textarea' => 'form-architect.designer.field-type-textarea',
		NULL,	// Separator
		'radio' => 'form-architect.designer.field-type-radio',
		'checkbox' => 'form-architect.designer.field-type-checkbox',
		'select' => 'form-architect.designer.field-type-select',
	];

	/** @var string[] */
	private $icons = [
		'text' => 'fa-font',
		'textarea' => 'fa-paragraph',
		'radio' => 'fa-dot-circle-o',
		'checkbox' => 'fa-check-square',
		'select' => 'fa-chevron-circle-down',
	];


	/**
	 * @param string  $type
	 */
	public function handleType($type)
	{
		$scheme = $this->getScheme();
		$scheme['type'] = $type;

		$this->setScheme($scheme);
		$this->getArchitect()->onSchemeChange();
	}


	public function handleAddOption()
	{
		$scheme = $this->getScheme();
		$scheme['options'][] = ['option' => NULL];

		$this->setScheme($scheme);
		$this->getArchitect()->onSchemeChange();
	}


	public function handleDeleteOption($index)
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
	public function getType()
	{
		return $this->type;
	}


	/**
	 * @param  string  $type
	 * @throws Exception
	 */
	public function setType($type)
	{
		if (!isset($this->types[$type])) {
			throw new \Exception($type);
		}

		$this->type = $this->values['type'] = $type;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->types[$this->type];
	}


	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->getIconFor($this->type);
	}


	/**
	 * @param  string  $type
	 * @return string
	 */
	public function getIconFor($type)
	{
		return $this->icons[$type];
	}


	/**
	 * @return string[]
	 */
	public function getTypes()
	{
		return $this->types;
	}


	/**
	 * @return array
	 */
	public function getScheme()
	{
		$scheme = parent::getScheme();
		$scheme = $scheme + ['type' => $this->type];

		return $scheme;
	}


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = [])
	{
		if (isset($scheme['type'])) {
			$this->setType($scheme['type']);
		}

		return parent::setScheme($scheme);
	}


	/**
	 * @return bool
	 */
	public function isChoiceControl()
	{
		return in_array($this->type, ['radio', 'checkbox', 'select']);
	}


	/**
	 * @return bool
	 */
	public function isSelect()
	{
		return $this->type === 'select';
	}


	/**
	 * @param  Container  $form
	 * @return Nette\Forms\IControl
	 */
	public function createInput(Container $form)
	{
		$name = $this->getName();
		$items = [];

		if ($options = $this->getValue('options')) {
			foreach ($options as $key => $option) {
				$items[$key] = $option['option'];
			}
		}

		switch ($this->type) {
			default: case 'text':
				$input = $form->addText($name);
				break;

			case 'textarea':
				$input = $form->addTextArea($name);
				break;

			case 'radio':
				$input = $form->addRadioList($name, NULL, $items);
				break;

			case 'checkbox':
				$input = $form->addCheckboxList($name, NULL, $items);
				break;

			case 'select':
				if ($this->getValue('multiple')) {
					$input = $form->addMultiSelect($name)->setItems($items, FALSE);
				}

				if (!$this->getValue('multiple')) {
					$input = $form->addSelect($name)->setItems($items, FALSE);
				}

				break;
		}

		if ($this->getValue('isRequired')) {
			$input->setRequired(TRUE);
		}

		return $input;
	}


	protected function createSchemeField()
	{
		$form = $this->getForm();
		$form->addText('title');
		$form->addCheckbox('isRequired');

		if (!$this->isChoiceControl() || $this->isSelect()) {
			$form->addText('description');
		}

		if (!$this->isChoiceControl()) {
			return NULL;
		}

		$options = $form->addContainer('options');

		if ($this->isSelect()) {
			$form->addCheckbox('multiple');
		}

		if (!$this->isSelect()) {
			$form->addCheckbox('inline');
		}

		if (!isset($this->values['options'])) {
			return NULL;
		}

		for ($i = 0; $i < sizeof($this->values['options']); $i++) {
			$option = $options->addContainer($i);
			$option->addText('option');
		}
	}
}
