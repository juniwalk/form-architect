<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls\Sections;

use JuniWalk\FormArchitect\Controls\Control;
use JuniWalk\FormArchitect\Controls\Field;
use JuniWalk\FormArchitect\Controls\Fields\Description;
use JuniWalk\FormArchitect\Controls\Fields\Question;
use JuniWalk\FormArchitect\Controls\Section;
use Nette\ComponentModel\IComponent;

abstract class BaseSection extends Control implements Section
{
	public function handleAddQuestion()
	{
		$this->addQuestion();
		$this->getArchitect()->onSchemeChange();
	}


	public function handleAddDescription()
	{
		$this->addDescription();
		$this->getArchitect()->onSchemeChange();
	}


	public function handleClone()
	{
		$architect = $this->getArchitect();
		$section = $architect->addSection();

		foreach ($this->getFields() as $field) {
			$section->addField(NULL, get_class($field))
				->setScheme($field->getScheme());
		}

		$architect->onSchemeChange();
	}


	public function handleMerge()
	{
		$architect = $this->getArchitect();

		foreach ($architect->getSections() as $section) {
			if ($this === $section) {
				break;
			}

			$target = $section;
		}

		if (!isset($target)) {
			return NULL;
		}

		foreach ($this->getFields() as $field) {
			if (!$field->isCloneable()) {
				continue;
			}

			$target->addField(NULL, get_class($field))
				->setScheme($field->getScheme());
		}

		$architect->removeComponent($this);
		$architect->onSchemeChange();
	}


	public function handleRemove()
	{
		$architect = $this->getArchitect();
		$architect->removeComponent($this);
		$architect->onSchemeChange();
	}


	/**
	 * @param  string|NULL  $name
	 * @return Field
	 * @throws Exception
	 */
	public function addField($name = NULL, $class = NULL)
	{
		$architect = $this->getArchitect();
		$name = $name ?: $architect->createName('field');
		$form = $this->getForm()->addContainer($name);

		if (is_null($class)) {
			throw new \Exception;
		}

		$this->addComponent($field = new $class($architect, $form), $name);
		return $field;
	}


	/**
	 * @param  string|NULL  $name
	 * @return Question
	 * @throws Exception
	 */
	public function addQuestion($name = NULL)
	{
		return $this->addField($name, Question::class);
	}


	/**
	 * @param  string|NULL  $name
	 * @return Description
	 * @throws Exception
	 */
	public function addDescription($name = NULL)
	{
		return $this->addField($name, Description::class);
	}


	/**
	 * @return Field[]
	 */
	public function getFields()
	{
		$fields = $this->getComponents(FALSE, Field::class);
		return iterator_to_array($fields);
	}


	/**
	 * @return array
	 */
	public function getScheme()
	{
		$scheme = ['class' => static::class];

		foreach ($this->getFields() as $key => $field) {
			$scheme[$key] = $field->getScheme();
		}

		return $scheme;
	}


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = [])
	{
		unset($scheme['class']);

		foreach ($this->getComponents() as $component) {
			$this->removeComponent($component);
		}

		foreach ($scheme as $key => $values) {
			$field = $this->addField($key, $values['class']);
			$field->setScheme($values);
		}
	}


	/**
	 * @return bool
	 */
	public function isMergeAllowed()
	{
		$sections = $this->getArchitect()->getSections();
		return reset($sections)->getName() !== $this->getName();
	}


	/**
	 * @return bool
	 */
	public function isDeleteAllowed()
	{
		$sections = $this->getArchitect()->getSections();
		return count($sections) > 1;
	}


	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile(__DIR__.'/../../templates/designer-section.latte');
		$template->setTranslator($this->getArchitect()->getTranslator());
		$template->render();
	}


	/**
	 * @param  IComponent  $child
	 * @throws Nette\InvalidStateException
	 */
	protected function validateChildComponent(IComponent $child)
	{
		if ($child instanceof Field) {
			return NULL;
		}

		throw new \Nette\InvalidStateException;
	}
}
