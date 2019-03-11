<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Caches;

interface Cache
{
    /**
     * @param  string  $namespace
     * @return static
     */
    public function derive(string $namespace): Cache;


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setValues(iterable $values): void;


    /**
     * @return iterable
     */
    public function getValues(): iterable;


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setScheme(iterable $scheme): void;


    /**
     * @return iterable
     */
    public function getScheme(): iterable;


    /**
     * @param  int  $step
     * @return void
     */
    public function setStep(int $step): void;


    /**
     * @return int
     */
    public function getStep(): int;
}
