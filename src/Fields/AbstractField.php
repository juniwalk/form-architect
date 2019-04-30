<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use JuniWalk\FormArchitect\Control;
use JuniWalk\FormArchitect\Helpers\Move;

abstract class AbstractField extends Control implements Field
{
	/** @var bool */
	private $hasLabel = true;

	/** @var string[] */
	protected $values = [];


	/**
	 * @return void
	 * @throws Exception
	 */
	public function handleLabel(): void
	{
		$this->setLabel(!$this->hasLabel());

		$this->setScheme($this->getScheme());
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleRemove(): void
	{
		$section = $this->getParent();
		$section->removeComponent($this);

		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleClone(): void
	{
		$section = $this->getParent();
		$section->addField(null, get_class($this))
			->setScheme($this->getScheme());

		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 * @throws Exception
	 */
	public function handleMoveUp(): void
	{
		if (!$this->hasUpwardMove()) {
			throw new \Exception('Unable to move up');
		}

		$section = $this->getParent();
		$fields = [];
		$index = null;

		foreach ($section->getInputs() as $key => $field) {
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
	 * @return void
	 * @throws Exception
	 */
	public function handleMoveDown(): void
	{
		if (!$this->hasDownwardMove()) {
			throw new \Exception('Unable to move down');
		}

		$section = $this->getParent();
		$fields = [];
		$index = null;

		foreach ($section->getInputs() as $key => $field) {
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


	/**
	 * @return string
	 */
	public function getClass(): string
	{
		$parts = explode('\\', static::class);
		return strtolower(array_pop($parts));
	}


	/**
	 * @param  string  $key
	 * @return mixed
	 */
	public function getValue(string $key)
	{
		return $this->values[$key] ?? null;
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = array_merge(
			['class' => static::class],
			['hasLabel' => $this->hasLabel],
			$this->getForm()->getValues(true)
		);

		return $scheme;
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		$this->values = $scheme;
		$form = $this->getForm();

		if (isset($scheme['hasLabel'])) {
			$this->setLabel($scheme['hasLabel']);
		}

		foreach ($form->getComponents() as $control) {
			$form->removeComponent($control);
		}

		$this->createSchemeField();
		$form->setValues($scheme);
	}


	/**
	 * @return bool
	 */
	public function hasUpwardMove(): bool
	{
		if (!$this->isSortable()) {
			return false;
		}

		$fields = $this->getParent()->getInputs();

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
	public function hasDownwardMove(): bool
	{
		if (!$this->isSortable()) {
			return false;
		}

		$fields = $this->getParent()->getInputs();

		foreach ($fields as $key => $field) {
			if (!$field->isSortable()) {
				unset($fields[$key]);
			}
		}

		return $this !== end($fields);
	}


	/**
	 * @param  bool  $label
	 * @return void
	 */
	public function setLabel(bool $label): void
	{
		$this->hasLabel = $label;
	}


	/**
	 * @return bool
	 */
	public function hasLabel(): bool
	{
		if ($this->getArchitect()->isLayout('inline')) {
			return false;
		}

		return $this->hasLabel;
	}


	/**
	 * @return bool
	 */
	public function hasToolbar(): bool
	{
		return false;
	}


    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return false;
    }


    /**
     * @return bool
     */
    public function isCloneable(): bool
    {
        return false;
    }


	/**
	 * @return void
	 */
	public function render(): void
	{
		$architect = $this->getArchitect();

		if (!iterator_to_array($this->getForm()->getComponents())) {
			$this->setScheme([]);
		}

		$template = $this->getTemplate();
		$template->setFile(__DIR__.'/../templates/designer-field.latte');
		$template->setTranslator($architect->getTranslator());
		$template->add('architect', $architect);
		$template->render();
	}


	/**
	 * @return void
	 */
	abstract protected function createSchemeField(): void;
}
