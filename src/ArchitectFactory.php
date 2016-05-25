<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use Nette\Http\Session;
use Nette\Localization\ITranslator;

class ArchitectFactory
{
	/** @var Session */
	private $session;

	/** @var ITranslator */
	private $translator;


	/**
	 * @param Session           $session
	 * @param ITranslator|NULL  $translator
	 */
	public function __construct(Session $session, ITranslator $translator = NULL)
	{
		$this->translator = $translator ?: new Helpers\SimpleTranslator;
		$this->session = $session;
	}


	/**
	 * @param  string  $identifier
	 * @return Designer
	 */
	public function createDesigner($identifier)
	{
		$designer = new Designer($identifier, $this->session);
		$designer->setTranslator($this->translator);

		return $designer;
	}


	/**
	 * @param  string  $identifier
	 * @return Renderer
	 */
	public function createRenderer($identifier)
	{
		$renderer = new Renderer($identifier, $this->session);
		$renderer->setTranslator($this->translator);

		return $renderer;
	}
}
