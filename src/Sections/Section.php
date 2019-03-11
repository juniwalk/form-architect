<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Sections;

interface Section
{
	/**
	 * @return Fields\Field[]
	 */
	public function getFields(): iterable;


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
    public function isSortable(): bool;


	/**
	 * @return bool
	 */
	public function isMergeAllowed(): bool;


	/**
	 * @return bool
	 */
	public function isDeleteAllowed(): bool;
}
