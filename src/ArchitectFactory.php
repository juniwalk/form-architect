<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use JuniWalk\FormArchitect\Caches\Cache;
use Nette\Http\Session;
use Nette\Localization\ITranslator;

class ArchitectFactory
{
	/** @var Cache */
	private $cache;

	/** @var ITranslator */
	private $translator;


	/**
	 * @param Cache  $cache
	 * @param ITranslator|null  $translator
	 */
	public function __construct(Cache $cache, ITranslator $translator = null)
	{
		$this->translator = $translator ?: new Helpers\SimpleTranslator;
		$this->cache = $cache;
	}


	/**
	 * @param  string  $form
	 * @return Designer
	 */
	public function createDesigner(string $form): Designer
	{
		$designer = new Designer($form, clone $this->cache);
		$designer->setTranslator($this->translator);

		return $designer;
	}


	/**
	 * @param  string  $form
	 * @return Renderer
	 */
	public function createRenderer(string $form): Renderer
	{
		$renderer = new Renderer($form, clone $this->cache);
		$renderer->setTranslator($this->translator);

		return $renderer;
	}
}
