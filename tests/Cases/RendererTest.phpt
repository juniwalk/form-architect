<?php

/**
 * TEST: Renderer class of Form Architect.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

use JuniWalk\FormArchitect\Controls\Section;
use JuniWalk\FormArchitect\Renderer;
use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use JuniWalk\FormArchitect\Tests\Files\Template;
use nette\Utils\Html;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

final class RendererTest extends \Tester\TestCase
{
	/** @var Renderer */
	private $renderer;

	/**@var string[] */
	private $scheme = [];


	public function __construct()
	{
		$this->scheme = include __DIR__.'/../Data/scheme.php';
		$this->renderer = (new ArchitectFactory)
			->createRenderer(Random::generate(8));
	}


	/**
	 * @return Renderer
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}


	public function testTemplateFile()
	{
		$renderer = $this->getRenderer();

		Assert::same($renderer->getDefaultTemplateFile(), $renderer->getTemplateFile());

		$renderer->setTemplateFile(__FILE__);

		Assert::same(__FILE__, $renderer->getTemplateFile());
	}


	public function testScheme()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);
		$renderer->setDefaults([
			'page1' => ['question' => 'Answer 2'],
			'page2' => ['question' => 'Text answer'],
		]);

		$scheme = $renderer->getScheme();
		$renderer->onSchemeChange();

		Assert::type('array', $scheme);
		Assert::same($this->scheme, $scheme);
	}


	public function testFooterText()
	{
		$renderer = $this->getRenderer();
		$renderer->setFooterText(
			$string = 'Footer test text.'
		);

		$html = $renderer->getFooterText();

		Assert::type(Html::class, $html);
		Assert::same($string, $html->getText());
	}


	public function testPanelStyle()
	{
		$renderer = $this->getRenderer();
		$renderer->setPanelStyle($renderer::PANEL_CLEAR);

		Assert::same($renderer::PANEL_CLEAR, $renderer->getPanelStyle());
		Assert::exception(function () use ($renderer) {
			$renderer->setPanelStyle('error');
		},	'Exception');
	}


	public function testSectionTitle()
	{
		$renderer = $this->getRenderer();
		$renderer->setHasSectionTitle(FALSE);

		Assert::false($renderer->hasSectionTitle());
	}


	public function testSteps()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);

		Assert::true($renderer->hasSteps());
		Assert::true($renderer->isFirstStep());

		$renderer->handleNextStep();

		Assert::true($renderer->isLastStep());
		Assert::count(2, $renderer->getSteps());

		$renderer->handlePrevStep();

		Assert::same(1, $renderer->getLastStep());
		Assert::same(0, $renderer->getStep());
	}


	public function testRender()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);

		$renderer->onBeforeRender($renderer, $template = new Template);

		Assert::type(Section::class, $template->get('section'));
	}


	public function testFormForward()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);

		$form = $renderer->getComponent('form');
		$form->setSubmittedBy($form['_forward']);
		$form->fireEvents();

		Assert::true($renderer->isLastStep());
		Assert::same(1, $renderer->getStep());
	}


	public function testFormBack()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);
		$renderer->setStep(1);

		$form = $renderer->getComponent('form');
		$form->setSubmittedBy($form['_back']);
		$form->fireEvents();

		Assert::true($renderer->isFirstStep());
		Assert::same(0, $renderer->getStep());
	}


	public function testFormSubmit()
	{
		$renderer = $this->getRenderer();
		$renderer->setScheme($this->scheme);
		$renderer->onFormSubmit[] = function () {
			Assert::true(TRUE);
		};

		$form = $renderer->getComponent('form');
		$form->setSubmittedBy($form['_submit']);
		$form->fireEvents();
	}
}

(new RendererTest)->run();
