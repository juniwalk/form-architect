<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use JuniWalk\FormArchitect\Caches\Cache;
use Nette\Application\UI;
use Nette\ComponentModel\IComponent;
use Nette\InvalidStateException;
use Nette\Forms\Container;
use Nette\Neon\Neon;
use Nette\Utils\Html;

abstract class AbstractArchitect extends Control implements Architect
{
	/** @var Cache */
	private $cache;

	/** @var string */
	private $id;

	/** @var string|null */
	private $templateFile;

	/** @var string */
	private $header;

	/** @var string */
	private $footer;

	/** @var bool */
	private $footerAllowed = true;

	/** @var string */
	private $layout = 'vertical';

	/** @var string */
	private $color = 'default';

	/** @var bool */
	private $hasAutosave = true;

	/** @var string[] */
	private $variables = [];

	/** @var string[] */
	private $layouts = [
		'vertical' => 'form-vertical',
		'horizontal' => 'form-horizontal',
		'inline' => 'form-inline',
	];

	/** @var string[] */
	private $colors = [
		'clear' => 'panel-clear',
		'default' => 'panel-default',
		'info' => 'panel-info',
		'primary' => 'panel-primary',
		'success' => 'panel-success',
		'warning' => 'panel-warning',
		'danger' => 'panel-danger',
	];

	/** @var callable[] */
	public $onBeforeRender = [];

	/** @var callable[] */
	public $onSchemeChange = [];

	/** @var callable[] */
	public $onSchemeSave = [];


	/**
	 * @param string  $id
	 * @param Cache  $cache
	 */
	public function __construct(string $id, Cache $cache)
	{
		$this->cache = $cache->derive('Architect.'.$id);
		$this->id = $id;

		$this->startup();
	}


	/**
	 * @param  string  $layout
	 * @return void
	 * @throws Exception
	 */
	public function handleLayout(string $layout): void
	{
		$this->setLayout($layout);

		$this->setScheme($this->getScheme());
		$this->onSchemeChange();
	}


	/**
	 * @param  string  $color
	 * @return void
	 * @throws Exception
	 */
	public function handleColor(string $color): void
	{
		$this->setColor($color);

		$this->setScheme($this->getScheme());
		$this->onSchemeChange();
	}


	/**
	 * @param  bool  $autosave
	 * @return void
	 */
	public function setAutosave(bool $autosave): void
	{
		$this->hasAutosave = $autosave;
	}


	/**
	 * @return bool
	 */
	public function hasAutosave(): bool
	{
		return $this->hasAutosave;
	}


	/**
	 * @param  string[]  $variables
	 * @param  bool  $append
	 * @return void
	 */
	public function setVariables(iterable $variables, bool $append = false): void
	{
		if ($append == true) {
			$variables = $variables + $this->variables;
		}

		$this->variables = $variables;
	}


	/**
	 * @param  string|null  $content
	 * @return string|null
	 */
	public function applyVariables(?string $content): ?string
	{
		foreach ($this->getVariables() as $key => $value) {
			$content = str_replace('('.$key.')', $value, $content);
		}

		return $content;
	}


	/**
	 * @return string[]
	 */
	public function getVariables(): iterable
	{
		return $this->variables + [
			'copy' => '&copy;',
			'year' => date('Y'),
		];
	}


	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}


	/**
	 * @param  string|null  $templateFile
	 * @return void
	 */
	public function setTemplateFile(?string $templateFile): void
	{
		$this->templateFile = $templateFile;
	}


	/**
	 * @return string
	 */
	public function getTemplateFile(): ?string
	{
		return $this->templateFile ?: $this->getDefaultTemplateFile();
	}


	/**
	 * @return string
	 */
	abstract public function getDefaultTemplateFile(): string;


	/**
	 * @param  string  $header
	 * @return void
	 */
	public function setHeader(string $header): void
	{
		$this->header = $header;
	}


	/**
	 * @return string|null
	 */
	public function getHeader(): ?string
	{
		return $this->header;
	}


	/**
	 * @param  string  $footer
	 * @return void
	 */
	public function setFooter(string $footer): void
	{
		$this->footer = $footer;
	}


	/**
	 * @return string|null
	 */
	public function getFooter(): ?string
	{
		return $this->footer;
	}


	/**
	 * @return Html|null
	 */
	public function getHeaderFormatted(): ?Html
	{
		$content = $this->applyVariables($this->header);
		return Html::el()->setHtml($content);
	}


	/**
	 * @return Html|null
	 */
	public function getFooterFormatted(): ?Html
	{
		$content = $this->applyVariables($this->footer);
		return Html::el()->setHtml($content);
	}


	/**
	 * @param  bool  $footerAllowed
	 * @return void
	 */
	public function setFooterAllowed(bool $footerAllowed): void
	{
		$this->footerAllowed = $footerAllowed;
	}


	/**
	 * @return bool
	 */
	public function isFooterAllowed(): bool
	{
		return $this->footerAllowed;
	}


	/**
	 * @param  string  $layout
	 * @return void
	 */
	public function setLayout(string $layout): void
	{
		if (!isset($this->layouts[$layout])) {
			throw new \Exception($layout);
		}

		$this->layout = $layout;
	}


	/**
	 * @return string
	 */
	public function getLayout(): string
	{
		return $this->layout;
	}


	/**
	 * @param  string  $layout
	 * @return bool
	 */
	public function isLayout(string $layout): bool
	{
		return $this->layout == $layout;
	}


	/**
	 * @param  string  $color
	 * @throws Exception
	 */
	public function setColor(string $color): void
	{
		if (!isset($this->colors[$color])) {
			throw new \Exception($color);
		}

		$this->color = $color;
	}


	/**
	 * @return string
	 */
	public function getColor(): string
	{
		return $this->color;
	}


	/**
	 * @param  string  $color
	 * @return bool
	 */
	public function isColor(string $color): bool
	{
		return $this->color == $color;
	}


	/**
	 * @param  string|null  $class
	 * @return Sections\Section
	 */
	public function addThankYouPage(?string $class): Sections\Section
	{
		return $this->addSection('thankYou', $class ?: Sections\ThankYouPage::class);
	}


	/**
	 * @return Sections\Section|null
	 * @throws Exception
	 */
	public function getThankYouPage(): ?Sections\Section
	{
		return $this->getComponent('thankYou', false);
	}


	/**
	 * @param  string|null  $name
	 * @param  string|null  $class
	 * @return Sections\Section
	 */
	public function addSection(?string $name, ?string $class): Sections\Section
	{
		$name = $name ?: $this->createName('section');
		$form = $this->getForm()->addContainer($name);

		if (is_null($class)) {
			$class = Sections\Page::class;
		}

		$this->addComponent($section = new $class($this, $form), $name);
		return $section;
	}


	/**
	 * @return Section[]
	 */
	public function getSections(): iterable
	{
		$sections = $this->getComponents(false, Sections\Section::class);
		$sections = iterator_to_array($sections);

		foreach ($sections as $key => $section) {
			if (!$section instanceof Sections\ThankYouPage) {
				continue;
			}

			unset($sections[$key]);
		}

		return $sections;
	}


	/**
	 * @param  string  $prefix
	 * @param  int  $length
	 * @return string
	 */
	public function createName(string $prefix, int $length = 15): string
	{
		return substr($prefix.sha1(uniqid('', true)), 0, $length);
	}


	/**
	 * @return void
	 */
	public function render(): void
	{
		$template = $this->getTemplate();
		$template->setTranslator($this->getTranslator());
		$template->setFile($this->getTemplateFile());
		$template->add('architect', $this->getArchitect());
		$template->add('layouts', $this->layouts);
		$template->add('layoutClass', $this->layouts[$this->layout]);
		$template->add('colors', $this->colors);
		$template->add('colorClass', $this->colors[$this->color]);
		$template->add('hasAutosave', $this->hasAutosave);

		$this->onBeforeRender($this, $template);

		$template->render();
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$values = $this->getForm()->getValues(true);

		$scheme = [];
		$scheme['header'] = $values['header'] ?? null;
		$scheme['footer'] = $values['footer'] ?? null;
		$scheme = $scheme + ['layout' => $this->layout];
		$scheme = $scheme + ['color' => $this->color];

		return array_filter($scheme);
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
		$this->header = $scheme['header'] ?? $this->header ?? null;
		$this->footer = $scheme['footer'] ?? $this->footer ?? null;

		if (isset($scheme['layout'])) {
			$this->setLayout($scheme['layout']);
		}

		if (isset($scheme['color'])) {
			$this->setColor($scheme['color']);
		}

		$this->getForm()->setValues([
			'header' => $this->header,
			'footer' => $this->footer,
		]);
	}


	/**
	 * @return string
	 */
	public function getNeon(): string
	{
		return Neon::encode($this->getScheme(), Neon::BLOCK);
	}


	/**
	 * @param  string  $neon
	 * @return void
	 */
	public function setNeon(string $neon): void
	{
		$this->setScheme(Neon::decode($neon));
	}


	/**
	 * @return Cache
	 */
	protected function getCache(): Cache
	{
		return $this->cache;
	}


	/**
	 * @return void
	 */
	abstract protected function startup(): void;


	/**
	 * @param  IComponent  $child
	 * @return null
	 * @throws InvalidStateException
	 */
	protected function validateChildComponent(IComponent $child)
	{
		if ($child instanceof Sections\Section || $child instanceof Container) {
			return;
		}

		throw new InvalidStateException;
	}


	/**
	 * @param  string  $name
	 * @return UI\Form
	 */
	protected function createComponentScheme(string $name): UI\Form
	{
		$form = new UI\Form;
		$form->setTranslator($this->getTranslator());

		$form->addText('header');
		$form->addText('footer');

		$form->addSubmit('autosave');
		$form->addSubmit('save');

		$form->onSuccess[] = function($form) {
			if ($form['autosave']->isSubmittedBy() && !$this->hasAutosave) {
				return;
			}

			$this->onSchemeSave($this);
		};

		return $form;
	}
}
