<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

/**
 * @property Itranslator $translator
 * @method void onBeforeRender(Designer $designer, $template)
 * @method void onSchemeChange()
 * @method void onSchemeSave()
 */
final class Designer extends BaseArchitect
{
	public function handleAddSection()
	{
		$title = $this->addSection()->addTitle();

		$title->setScheme();
		$this->onSchemeChange();
	}


	public function handleStartOver()
	{
		$this->setScheme();
		$this->onSchemeChange();
	}


	/**
	 * @return array
	 */
	public function getScheme()
	{
		$scheme = [];

		foreach ($this->getSections() as $key => $section) {
			$scheme[$key] = $section->getScheme();
		}

		return $scheme;
	}


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = [])
	{
		foreach ($this->getComponents() as $component) {
			$this->removeComponent($component);
		}

		unset($scheme['class']);

		foreach ($scheme as $key => $values) {
			$section = $this->addSection($key);
			$section->setScheme($values);
		}
	}


	/**
	 * @return string
	 */
	public function getDefaultTemplateFile()
	{
		return __DIR__.'/templates/designer.latte';
	}


	protected function startup()
	{
		$this->schemeLoad();

		$this->onSchemeChange[] = function () {
			$this->redrawControl('sections');
			$this->onSchemeSave($this);
		};

		$this->onSchemeSave[] = function () {
			$this->isControlInvalid() || $this->redrawControl('empty');
			$this->schemeSave();
		};
	}


	private function schemeLoad()
	{
		$this->setScheme($this->getCache()->scheme ?: []);
	}


	private function schemeSave()
	{
 		$this->getCache()->scheme = $this->getScheme();
	}
}
