<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls;

use Nette\Forms\Container;
use Nette\Localization\ITranslator;

abstract class Control extends \Nette\Application\UI\Control
{
	/** @var Architect */
	private $architect;

	/** @var ITranslator */
	private $translator;

	/** @var Container */
	private $form;


	/**
	 * @param Architect  $architect
	 * @param Container  $form
	 */
	public function __construct(Architect $architect, Container $form)
	{
		$this->architect = $architect;
		$this->form = $form;
	}


	/**
	 * @return Architect
	 */
	public function getArchitect()
	{
		if (!$this->architect && $this instanceof Architect) {
			return $this;
		}

		return $this->architect;
	}


	/**
	 * @param ITranslator|NULL  $translator
	 */
	public function setTranslator(ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}


	/**
	 * @return ITranslator|NULL
	 */
	public function getTranslator()
	{
		return $this->translator;
	}


	/**
	 * @return Container
	 */
	public function getForm()
	{
		if ($form = $this->getComponent('scheme', FALSE)) {
			return $form;
		}

		return $this->form;
	}


	/**
	 * @return array
	 */
	abstract public function getScheme();


	/**
	 * @param  array  $scheme
	 * @return NULL
	 */
	abstract public function setScheme(array $scheme = []);
}
