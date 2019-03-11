<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use Nette\Forms\Container as Form;

interface Architect
{
	/**
	 * @return Architect
	 */
	public function getArchitect(): Architect;


	/**
	 * @return Form
	 */
	public function getForm(): Form;


	/**
	 * @return Section[]
	 */
	public function getSections(): iterable;


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable;


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void;
}
