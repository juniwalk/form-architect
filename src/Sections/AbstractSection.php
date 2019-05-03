<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Sections;

use JuniWalk\FormArchitect\Control;
use JuniWalk\FormArchitect\Fields;
use JuniWalk\FormArchitect\Helpers\Move;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;

abstract class AbstractSection extends Control implements Section
{
	/**
	 * @return void
	 * @throws Exception
	 */
	public function handleMoveUp(): void
	{
		if (!$this->hasUpwardMove()) {
			throw new \Exception('Unable to move up');
		}

		$architect = $this->getArchitect();
		$sections = [];
		$index = null;

		foreach ($architect->getSections() as $key => $section) {
			if ($this->getName() === $key) {
				$index = sizeof($sections);
			}

			$architect->removeComponent($section);
			$sections[] = $section;
		}

		$sections = Move::up($sections, $index);

		foreach ($sections as $section) {
			$architect->addComponent($section, $section->getName());
		}

		$architect->onSchemeChange();
	}


	/**
	 * @return void
	 * @throws Exception
	 */
	public function handleMoveDown(): void
	{
		if (!$this->hasDownwardMove()) {
			throw new \Exception('Unable to move up');
		}

		$architect = $this->getArchitect();
		$sections = [];
		$index = null;

		foreach ($architect->getSections() as $key => $section) {
			if ($this->getName() === $key) {
				$index = sizeof($sections);
			}

			$architect->removeComponent($section);
			$sections[] = $section;
		}

		$sections = Move::down($sections, $index);

		foreach ($sections as $section) {
			$architect->addComponent($section, $section->getName());
		}

		$architect->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleAddQuestion(): void
	{
		$this->addQuestion(null);
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleAddCaptcha(): void
	{
		$this->getComponent('captcha', false) || $this->addCaptcha();
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleAddDescription(): void
	{
		$this->addDescription(null);
		$this->getArchitect()->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleClone(): void
	{
		$architect = $this->getArchitect();
		$section = $architect->addSection();

		foreach ($this->getFields() as $field) {
			$section->addField(null, get_class($field))
				->setScheme($field->getScheme());
		}

		$architect->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleMerge(): void
	{
		$architect = $this->getArchitect();

		foreach ($architect->getSections() as $section) {
			if ($this === $section) {
				break;
			}

			$target = $section;
		}

		if (!isset($target)) {
			return;
		}

		foreach ($this->getFields() as $field) {
			if (!$field->isCloneable()) {
				continue;
			}

			$target->addField(null, get_class($field))
				->setScheme($field->getScheme());
		}

		$architect->removeComponent($this);
		$architect->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleRemove(): void
	{
		$architect = $this->getArchitect();
		$architect->removeComponent($this);
		$architect->onSchemeChange();
	}


	/**
	 * @param  string|null  $name
	 * @param  string|null  $class
	 * @return Fields\Field
	 * @throws Exception
	 */
	public function addField(?string $name, ?string $class): Fields\Field
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
	 * @param  string|null  $name
	 * @return Fields\Question
	 * @throws Exception
	 */
	public function addQuestion(?string $name): Fields\Question
	{
		return $this->addField($name, Fields\Question::class);
	}


	/**
	 * @return Fields\Captcha
	 * @throws Exception
	 */
	public function addCaptcha(): Fields\Captcha
	{
		return $this->addField('captcha', Fields\Captcha::class);
	}


	/**
	 * @param  string|null  $name
	 * @return Fields\Description
	 * @throws Exception
	 */
	public function addDescription(?string $name): Fields\Description
	{
		return $this->addField($name, Fields\Description::class);
	}


	/**
	 * @return Fields\Field[]
	 */
	public function getFields(): iterable
	{
		$fields = $this->getComponents(false, Fields\Field::class);
		return iterator_to_array($fields);
	}


	/**
	 * @return Fields\Field[]
	 */
	public function getInputs(): iterable
	{
		$fields = $this->getComponents(false, Fields\InputProvider::class);
		return iterator_to_array($fields);
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = ['class' => static::class];

		foreach ($this->getFields() as $key => $field) {
			$values = $field->getScheme();
			$name = $values['name'] ?? $key;

			$scheme['fields'][$name] = $values;
		}

		return $scheme;
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		foreach ($this->getComponents() as $component) {
			$this->removeComponent($component);
		}

		foreach ($scheme['fields'] ?? [] as $key => $values) {
			$field = $this->getComponent($key, false) ?? $this->addField($key, $values['class']);

			$values['name'] = $field->getName();

			$field->setScheme($values);
		}
	}


	/**
	 * @return bool
	 */
	public function hasUpwardMove(): bool
	{
		if (!$this->isSortable()) {
			return false;
		}

		$sections = $this->getArchitect()->getSections();

		foreach ($sections as $key => $section) {
			if (!$section->isSortable()) {
				unset($section[$key]);
			}
		}

		return $this !== current($sections);
	}


	/**
	 * @return bool
	 */
	public function hasDownwardMove(): bool
	{
		if (!$this->isSortable()) {
			return false;
		}

		$sections = $this->getArchitect()->getSections();

		foreach ($sections as $key => $section) {
			if (!$section->isSortable()) {
				unset($section[$key]);
			}
		}

		return $this !== end($sections);
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
	public function isMergeAllowed(): bool
	{
		$sections = $this->getArchitect()->getSections();
		return reset($sections)->getName() !== $this->getName();
	}


	/**
	 * @return bool
	 */
	public function isDeleteAllowed(): bool
	{
		$sections = $this->getArchitect()->getSections();
		return count($sections) > 1;
	}


	/**
	 * @return void
	 */
	public function render(): void
	{
		$template = $this->createTemplate();
		$template->setFile(__DIR__.'/../templates/designer-section.latte');
		$template->setTranslator($this->getArchitect()->getTranslator());
		$template->add('architect', $this->getArchitect());

		foreach ($this->getArchitect()->getTemplate()->getParameters() as $key => $value) {
			try {
				$template->add($key, $value);
			} catch (\Exception $e) { }
		}

		$template->render();
	}


	/**
	 * @param  IComponent  $child
 	 * @return void
	 * @throws InvalidStateException
	 */
	protected function validateChildComponent(IComponent $child)
	{
		if ($child instanceof Fields\Field) {
			return;
		}

		throw new InvalidStateException;
	}
}
