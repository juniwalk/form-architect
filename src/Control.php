<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use Nette\Application\UI;
use Nette\Forms\Container as Form;
use Nette\Localization\ITranslator;
use Nette\Utils\Random;

abstract class Control extends UI\Control
{
	/** @var Architect */
	private $architect;

	/** @var ITranslator */
	private $translator;

	/** @var Form */
	private $form;


	/**
	 * @param Architect  $architect
	 * @param Form  $form
	 */
	public function __construct(Architect $architect, Form $form)
	{
		$this->architect = $architect;
		$this->form = $form;
	}


	/**
	 * @return Architect
	 */
	public function getArchitect(): Architect
	{
		if (!$this->architect && $this instanceof Architect) {
			return $this;
		}

		return $this->architect;
	}


	/**
	 * @param  ITranslator|null  $translator
	 * @return void
	 */
	public function setTranslator(?ITranslator $translator): void
	{
		$this->translator = $translator;
	}


	/**
	 * @return ITranslator|null
	 */
	public function getTranslator(): ?ITranslator
	{
		return $this->translator;
	}


	/**
	 * @return Form
	 */
	public function getForm(): Form
	{
		if ($form = $this->getComponent('scheme', false)) {
			return $form;
		}

		return $this->form;
	}


	/**
	 * @return iterable
	 */
	abstract public function getScheme(): iterable;


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	abstract public function setScheme(iterable $scheme = []): void;


	/**
	 * @param  string  $prefix
	 * @param  int  $length
	 * @return string
	 */
	public function createName(string $prefix, int $length = 12): string
	{
		$random = Random::generate($length);
		return substr($prefix.$random, 0, $length);
	}
}
