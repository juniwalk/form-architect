<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

final class Designer extends AbstractArchitect
{
	/**
	 * @return void
	 */
	public function handleAddSection(): void
	{
		$this->addSection(null, Sections\Page::class);
		$this->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleAddThankYouPage(): void
	{
		$this->getThankYouPage() || $this->addThankYouPage(Sections\ThankYouPage::class);
		$this->onSchemeChange();
	}


	/**
	 * @return void
	 */
	public function handleStartOver(): void
	{
		$this->setScheme();
		$this->onSchemeChange();
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = parent::getScheme();

		foreach ($this->getSections() as $key => $section) {
			$scheme['sections'][$key] = $section->getScheme();
		}

		if ($thankYou = $this->getThankYouPage()) {
			$scheme['thankYou'] = $thankYou->getScheme();
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

		foreach ($scheme['sections'] ?? [] as $name => $section) {
			$this->addSection($name, $section['class'])->setScheme($section);
		}

		if ($thankYou = $scheme['thankYou'] ?? []) {
			$this->addThankYouPage($thankYou['class'])->setScheme($thankYou);
		}

		parent::setScheme($scheme);
	}


	/**
	 * @return string
	 */
	public function getDefaultTemplateFile(): string
	{
		return __DIR__.'/templates/designer.latte';
	}


	/**
	 * @return void
	 */
	protected function startup(): void
	{
		$this->schemeLoad();

		$this->onSchemeChange[] = function() {
			$this->redrawControl('sections');
			$this->redrawControl('thankYou');
			$this->onSchemeSave($this);
		};

		$this->onSchemeSave[] = function() {
			$this->schemeSave();
			$this->schemeLoad();
			$this->redrawControl('architect');
		};
	}


	/**
	 * @return void
	 */
	private function schemeLoad(): void
	{
		$this->setScheme($this->getCache()->getScheme());
	}


	/**
	 * @return void
	 */
	private function schemeSave(): void
	{
		$this->getCache()->setScheme($this->getScheme());
	}
}
