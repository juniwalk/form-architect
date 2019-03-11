<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

interface Field
{
	/**
	 * @return string
	 */
	public function getClass(): string;


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable;


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void;


	/**
	 * @return bool
	 */
	public function hasToolbar(): bool;
}
