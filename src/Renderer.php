<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use Nette\Application\UI;
use Nette\Forms\Container;
use Nette\Utils\ArrayHash;

final class Renderer extends AbstractArchitect
{
	/** @var bool */
	private $isReadOnly = false;

	/** @var string[] */
	private $steps = [];

	/** @var callable[] */
	public $onFormSubmit = [];

	/** @var callable[] */
	public $onFormStep = [];


	/**
	 * @return void
	 */
	public function handleFirstStep(): void
	{
		$this->clearCache();
		$this->redrawControl('section');
	}


	/**
	 * @return void
	 */
	public function handleNextStep(): void
	{
		$this->setStep($this->getStep() + 1);
		$this->redrawControl('section');
	}


	/**
	 * @return void
	 */
	public function handlePrevStep(): void
	{
		$this->setStep($this->getStep() - 1);
		$this->redrawControl('section');
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		parent::setScheme($scheme);

		$this->steps = array_keys($scheme['sections'] ?? []);

		if (isset($scheme['thankYou'])) {
			$this->steps[] = 'thankYou';
		}

		$this->getCache()->setScheme($scheme);
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		return $this->getCache()->getScheme();
	}


	/**
	 * @param  iterable  $values
	 * @return void
	 */
	public function setValues(iterable $values = []): void
	{
		$this->getCache()->setValues($values);
	}


	/**
	 * @return iterable
	 */
	public function getValues(): iterable
	{
		return (array) $this->getCache()->getValues();
	}


	/**
	 * @param  bool  $readOnly
	 * @return void
	 */
	public function setReadOnly(bool $readOnly): void
	{
		$this->isReadOnly = $readOnly;
	}


	/**
	 * @return bool
	 */
	public function isReadOnly(): bool
	{
		return $this->isReadOnly;
	}


	/**
	 * @param  int  $step
	 * @return void
	 */
	public function setStep(int $step): void
	{
		$step = min(max($step, 0), $this->getLastStep());
		$this->getCache()->setStep($step);
	}


	/**
	 * @return int
	 */
	public function getStep(): int
	{
		$step = $this->getCache()->getStep();

		if (!$step || $step > $this->getLastStep()) {
			return 0;
		}

		return max($step, 0);
	}


	/**
	 * @return int
	 */
	public function getLastStep(): int
	{
		return sizeof($this->steps) - 1;
	}


	/**
	 * @return string[]
	 */
	public function getSteps(): iterable
	{
		return $this->steps;
	}


	/**
	 * @return bool
	 */
	public function hasSteps(): bool
	{
		return sizeof($this->steps) > 1;
	}


	/**
	 * @return bool
	 */
	public function isFirstStep(): bool
	{
		return $this->getStep() == 0;
	}


	/**
	 * @return bool
	 */
	public function isLastStep(): bool
	{
		return $this->getStep() == $this->getLastStep();
	}


	/**
	 * @return bool
	 */
	public function showSubmit(): bool
	{
		$lastStep = $this->getLastStep();

		if ($this->hasThankYouPage()) {
			$lastStep = $lastStep - 1;
		}

		return $lastStep == $this->getStep();
	}


	/**
	 * @return bool
	 */
	public function hasThankYouPage(): bool
	{
		return in_array('thankYou', $this->steps);
	}


	/**
	 * @return string
	 */
	public function getDefaultTemplateFile(): string
	{
		return __DIR__.'/templates/renderer.latte';
	}


	/**
	 * @return string|null
	 */
	protected function getSection(): ?string
	{
		if (empty($this->steps)) {
			return null;
		}

		return $this->steps[$this->getStep()];
	}


	/**
	 * @return void
	 */
	protected function startup(): void
	{
		$this->onBeforeRender[] = function(self $self, UI\ITemplate $template) {
			$this->onBeforeRender($template);
		};

		$this->onSchemeChange[] = function() {
			$this->redrawControl('section');
			$this->clearCache();
		};

		$this->onFormStep[] = function() {
			$this->redrawControl('section');
		};

		$this->onFormSubmit[] = function() {
			$this->flashMessage('submit-success', 'success');
		};
	}


	/**
	 * @param  string  $name
	 * @return UI\Form
	 */
	protected function createComponentForm(string $name): UI\Form
	{
		$form = new UI\Form;
		$form->setTranslator($this->getTranslator());
		$form->addSubmit('_back')->setValidationScope(false);
		$form->addSubmit('_forward');
		$form->addSubmit('_submit');
		$form->onSuccess[] = function (UI\Form $form) {
			$step = $this->onFormSuccess($form);
			$this->setStep($step);
		};

		return $form;
	}


	/**
	 * @param  UI\Form  $form
	 * @return int
	 */
	private function onFormSuccess(UI\Form $form): int
	{
		$values = array_diff_key($form->getHttpData(), [
			'_do' => null,
			'_submit' => null,
			'_forward' => null,
			'_back' => null,
		]);

		$this->onFormStep($form, $values, $this);

		if ($form['_back']->isSubmittedBy()) {
			return $this->getStep() - 1;
		}

		$values = $values + $this->getValues();
		$this->setValues($values);

		if ($form['_forward']->isSubmittedBy()) {
			return $this->getStep() + 1;
		}

		$this->clearCache();
		$this->onFormSubmit($form, $values, $this);

		if (!$this->hasThankYouPage()) {
			return 0;
		}

		return $this->getLastStep();
	}


	/**
	 * @param  UI\ITemplate  $template
	 * @return void
	 */
	private function onBeforeRender(UI\ITemplate $template): void
	{
		$form = $this->getComponent('form', true);
		$scheme = $this->getScheme();

		switch ($name = $this->getSection()) {
			case 'thankYou':
				$class = $scheme[$name]['class'] ?? Sections\Page::class;
				$section = $this->addThankYouPage(Sections\ThankYouPage::class);
				$section->setScheme($scheme[$name] ?? []);

				$template->setFile(__DIR__.'/templates/renderer-thankyou.latte');
				break;

			default:
				$class = $scheme['sections'][$name]['class'] ?? Sections\Page::class;
				$section = $this->addSection($name, $class);
				$section->setScheme($scheme['sections'][$name] ?? []);

				$container = $form->addContainer($section->getName());
				break;
		}

		foreach ($section->getInputs() as $field) {
			$input = $field->createInput($container);

			if ($this->isReadOnly) {
				$input->setDisabled();
			}
		}

		$form->setValues($this->getValues());
		$template->add('section', $section);
	}


	/**
	 * @return void
	 */
	private function clearCache(): void
	{
		$this->setValues([]);
		$this->setStep(0);
	}
}
