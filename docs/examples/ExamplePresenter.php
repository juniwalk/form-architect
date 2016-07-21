<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace App\Presenters;

use Nette\Utils\Random;

final class ExamplePresenter extends \Nette\Application\UI\Presenter
{
	/** @var \JuniWalk\FormArchitect\ArchitectFactory @inject */
	public $architectFactory;

	/** @var string|NULL @persistent */
	public $instance = NULL;


	public function actionDefault()
	{
		$this->instance || $this->instance = Random::generate(8);
	}


	/**
	 * @param  string  $name
	 * @return Designer
	 */
	protected function createComponentDesigner($name)
	{
		$designer = $this->architectFactory->createDesigner($this->instance);
		$designer->onSchemeSave[] = function ($designer) {
			// Save scheme somewhere or send fresh updates to Renderer
			$this['renderer']->setScheme($designer->getScheme());
			$this['renderer']->onSchemeChange();
		};

		return $designer;
	}


	/**
	 * @param  string  $name
	 * @return Renderer
	 */
	protected function createComponentRenderer($name)
	{
		$renderer = $this->architectFactory->createRenderer($this->instance);
		$renderer->onFormSubmit[] = function ($form, $data, $renderer) {
			// Process data from the Renderer
		};

		$renderer->setFooterText('Martin Procházka © '.date('Y'));
		$renderer->setScheme($this['designer']->getScheme());

		return $renderer;
	}
}
