<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use JuniWalk\FormArchitect\Caches\Cache;
use Nette\Localization\ITranslator;

class ArchitectFactory
{
	/** @var Cache */
	private $cache;

	/** @var string */
	private $uploadDir;

	/** @var ITranslator */
	private $translator;


	/**
	 * @param string|null  $uploadDir
	 * @param Cache  $cache
	 * @param ITranslator|null  $translator
	 */
	public function __construct(?string $uploadDir, Cache $cache, ITranslator $translator = null)
	{
		$this->translator = $translator ?: new Helpers\SimpleTranslator;
		$this->uploadDir = $uploadDir;
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
		$renderer->setUploadDir($this->uploadDir);

		return $renderer;
	}
}
