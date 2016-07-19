<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls\Fields;

use JuniWalk\FormArchitect\Controls\Control;
use JuniWalk\FormArchitect\Controls\Field;
use JuniWalk\FormArchitect\Designer;
use JuniWalk\FormArchitect\Helpers\Move;
use Nette\Forms\Container;

abstract class BaseField extends Control implements Field
{
	/** @var string[] */
	protected $values = [];


	/**
	 * @throws Exception
	 */
	public function handleMoveUp()
	{
		if (!$this->hasUpwardMove()) {
			throw new \Exception('Unable to move up');
		}

		$section = $this->getParent();

		$fields = [];
		$index = NULL;

		foreach ($section->getFields() as $key => $field) {
			if ($this->getName() === $key) {
				$index = sizeof($fields);
			}

			$section->removeComponent($field);
			$fields[] = $field;
		}

		$fields = Move::up($fields, $index);

		foreach ($fields as $field) {
			$section->addComponent($field, $field->getName());
		}

		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @throws Exception
	 */
	public function handleMoveDown()
	{
		if (!$this->hasDownwardMove()) {
			throw new \Exception('Unable to move down');
		}

		$section = $this->getParent();

		$fields = [];
		$index = NULL;

		foreach ($section->getFields() as $key => $field) {
			if ($this->getName() === $key) {
				$index = sizeof($fields);
			}

			$section->removeComponent($field);
			$fields[] = $field;
		}

		$fields = Move::down($fields, $index);

		foreach ($fields as $field) {
			$section->addComponent($field, $field->getName());
		}

		$this->getArchitect()->onSchemeChange();
	}


	public function handleClone()
	{
		$section = $this->getParent();
		$section->addField(NULL, get_class($this))
			->setScheme($this->getScheme());

		$this->getArchitect()->onSchemeChange();
	}


	public function handleRemove()
	{
		$section = $this->getParent();
		$section->removeComponent($this);

		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return string
	 */
	public function getClass()
	{
		$parts = explode('\\', static::class);
		return strtolower(array_pop($parts));
	}


	/**
	 * @param  string  $key
	 * @return string|NULL
	 */
	public function getValue($key)
	{
		if (!isset($this->values[$key])) {
			return NULL;
		}

		return $this->values[$key];
	}


	/**
	 * @return array
	 */
	public function getScheme()
	{
		return array_merge(
			['class' => static::class],
			$this->getForm()->getValues(TRUE)
		);
	}


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = [])
	{
		unset($scheme['class']);
		$this->values = $scheme;

		if (!$this->getArchitect() instanceof Designer) {
			return NULL;
		}

		$form = $this->getForm();

		foreach ($form->getComponents() as $control) {
			$form->removeComponent($control);
		}

		$this->createSchemeField();

		$form->setValues($this->values, TRUE);
	}


	/**
	 * @return bool
	 */
	public function hasToolbar()
	{
		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isCloneable()
	{
		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isSortable()
	{
		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function hasUpwardMove()
	{
		if (!$this->isSortable()) {
			return FALSE;
		}

		$fields = $this->getParent()->getFields();

		foreach ($fields as $key => $field) {
			if (!$field->isSortable()) {
				unset($fields[$key]);
			}
		}

		return $this !== current($fields);
	}


	/**
	 * @return bool
	 */
	public function hasDownwardMove()
	{
		if (!$this->isSortable()) {
			return FALSE;
		}

		$fields = $this->getParent()->getFields();

		foreach ($fields as $key => $field) {
			if (!$field->isSortable()) {
				unset($fields[$key]);
			}
		}

		return $this !== end($fields);
	}


	public function render()
	{
		if (!iterator_to_array($this->getForm()->getComponents())) {
			$this->setScheme([]);
		}

		$template = $this->getTemplate();
		$template->setFile(__DIR__.'/../../templates/designer-field.latte');
		$template->setTranslator($this->getArchitect()->getTranslator());
		$template->render();
	}


	/**
	 * @return NULL
	 */
	abstract protected function createSchemeField();
}
