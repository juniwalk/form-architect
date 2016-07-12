<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use JuniWalk\FormArchitect\Controls\InputProvider;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

/**
 * @property Itranslator $translator
 * @method void onFormSubmit(Form $form, array $data, Renderer $renderer)
 * @method void onFormStep(Form $form, array $data, Renderer $renderer)
 */
final class Renderer extends BaseArchitect
{
	/** @var string */
	const PANEL_CLEAR = 'panel-clear';
	const PANEL_DANGER = 'panel-danger';
	const PANEL_DEFAULT = 'panel-default';
	const PANEL_INFO = 'panel-info';
	const PANEL_PRIMARY = 'panel-primary';
	const PANEL_WARNING = 'panel-warning';


	/** @var bool */
	private $hasSectionTitle = TRUE;

	/** @var string */
	private $panelStyle = self::PANEL_PRIMARY;

	/** @var string|Html */
	private $footerText = NULL;

	/** @var string[] */
	private $steps = [];

	/** @var callable[] */
	public $onFormSubmit = [];

	/** @var callable[] */
	public $onFormStep = [];


	public function handleNextStep()
	{
		$this->setStep($this->getStep() + 1);
		$this->redrawControl('section');
	}


	public function handlePrevStep()
	{
		$this->setStep($this->getStep() - 1);
		$this->redrawControl('section');
	}


	/**
	 * @param string|Html|NULL  $footerText
	 */
	public function setFooterText($footerText = NULL)
	{
		if (!$footerText instanceof Html) {
			$footerText = Html::el(NULL, $footerText);
		}

		$this->footerText = $footerText;
	}


	/**
	 * @return Html|NULL
	 */
	public function getFooterText()
	{
		return $this->footerText;
	}


	/**
	 * @param  string  $panelStyle
	 * @throws Exception
	 */
	public function setPanelStyle($panelStyle = self::PANEL_PRIMARY)
	{
		static $styles = [
			self::PANEL_CLEAR,
			self::PANEL_DANGER,
			self::PANEL_DEFAULT,
			self::PANEL_INFO,
			self::PANEL_PRIMARY,
			self::PANEL_WARNING,
		];

		if (!in_array($panelStyle, $styles)) {
			throw new \Exception;
		}

		$this->panelStyle = $panelStyle;
	}


	/**
	 * @return string
	 */
	public function getPanelStyle()
	{
		return $this->panelStyle;
	}


	/**
	 * @param bool  $hasSectionTitle
	 */
	public function setHasSectionTitle($hasSectionTitle = TRUE)
	{
		$this->hasSectionTitle = (bool) $hasSectionTitle;
	}


	/**
	 * @return bool
	 */
	public function hasSectionTitle()
	{
		return $this->hasSectionTitle;
	}


	/**
	 * @param array  $scheme
	 */
	public function setScheme(array $scheme = [])
	{
		$this->getCache()->scheme = $scheme;
		$this->steps = array_keys($scheme);
	}


	/**
	 * @return array
	 */
	public function getScheme()
	{
		return $this->getCache()->scheme;
	}


	/**
	 * @param  array  $defaults
	 */
	public function setDefaults(array $defaults = [])
	{
		$this->getCache()->data = $defaults;
	}


	/**
	 * @param int  $step
	 */
	public function setStep($step)
	{
		$this->getCache()->step = min(max($step, 0), $this->getLastStep());
	}


	/**
	 * @return int
	 */
	public function getStep()
	{
		$step = $this->getCache()->step;

		if (!$step || $step > $this->getLastStep()) {
			return 0;
		}

		return max($step, 0);
	}


	/**
	 * @return int
	 */
	public function getLastStep()
	{
		return sizeof($this->steps) - 1;
	}


	/**
	 * @return string[]
	 */
	public function getSteps()
	{
		return $this->steps;
	}


	/**
	 * @return bool
	 */
	public function hasSteps()
	{
		return sizeof($this->steps) > 1;
	}


	/**
	 * @return bool
	 */
	public function isFirstStep()
	{
		return $this->getStep() == 0;
	}


	/**
	 * @return bool
	 */
	public function isLastStep()
	{
		return $this->getStep() == $this->getLastStep();
	}


	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile(__DIR__.'/templates/renderer.latte');
		$template->setTranslator($this->translator);

		$template->add('footerText', $this->footerText);
		$template->add('panelStyle', $this->panelStyle);

		foreach ($this->getComponents() as $component) {
			$this->removeComponent($component);
		}

		$form = $this->getComponent('form', TRUE);
		$sectionToRender = $this->getSection();

		foreach ($this->getScheme() as $key => $values) {
			if ($key !== $sectionToRender) {
				continue;
			}

			$section = $this->addSection($key);
			$section->setScheme($values);

			$page = $form->addContainer($section->getName());

			foreach ($section->getFields() as $field) {
				if (!$field instanceof InputProvider) {
					continue;
				}

				$field->createInput($page);
			}

			$template->add('section', $section);
		}

		$form->setDefaults($this->getCache()->data ?: []);
		$template->render();
	}


	/**
	 * @return string|NULL
	 */
	protected function getSection()
	{
		if (empty($this->steps)) {
			return NULL;
		}

		return $this->steps[$this->getStep()];
	}


	protected function startup()
	{
		$this->onSchemeChange[] = function () {
			$this->redrawControl('section');
			$this->clearCache();
		};

		$this->onFormStep[] = function () {
			$this->redrawControl('section');
		};
	}


	/**
	 * @param  string  $name
	 * @return Form
	 */
	protected function createComponentForm($name)
	{
		$form = new Form($this, $name);
		$form->setTranslator($this->translator);
		$form->onSuccess[] = function ($form) {
			$values = array_diff_key($form->getHttpData(), [
				'_do' => TRUE,
				'_submit' => TRUE,
				'_forward' => TRUE,
				'_back' => TRUE,
			]);

			$this->onFormStep($form, $values, $this);

			if ($form['_back']->isSubmittedBy()) {
				return $this->setStep($this->getStep() - 1);
			}

			$values = $this->mergeValues($values);

			if ($form['_forward']->isSubmittedBy()) {
				return $this->setStep($this->getStep() + 1);
			}

			$this->clearCache();
			$this->onFormSubmit($form, $values, $this);
		};

		$form->addSubmit('_back')->setValidationScope(FALSE);
		$form->addSubmit('_forward');
		$form->addSubmit('_submit');

		return $form;
	}


	private function clearCache()
	{
		$this->getCache()->data = NULL;
		$this->getCache()->step = NULL;
	}


	/**
	 * @param  array  $values
	 * @return array
	 */
	private function mergeValues(array $values = [])
	{
		return $this->getCache()->data = array_merge((array) $this->getCache()->data, $values);
	}
}
