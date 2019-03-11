<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Caches;

use Nette\Http\Session;
use Nette\Http\SessionSection;

final class SessionCache implements Cache
{
    /** @var Session */
    private $session;

    /** @var SessionSection */
    private $section;


    /**
     * @param Session  $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }


    /**
     * @param  string  $namespace
     * @return Cache
     */
    public function derive(string $namespace): Cache
    {
        $this->section = $this->session->getSection($namespace);
        return $this;
    }


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setValues(iterable $values): void
    {
        $this->section->values = $values;
    }


    /**
     * @return iterable
     */
    public function getValues(): iterable
    {
        return $this->section->values ?? [];
    }


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setScheme(iterable $scheme): void
    {
        $this->section->scheme = $scheme;
    }


    /**
     * @return iterable
     */
    public function getScheme(): iterable
    {
        return $this->section->scheme ?? [];
    }


    /**
     * @param  int  $step
     * @return void
     */
    public function setStep(int $step): void
    {
        $this->section->step = $step;
    }


    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->section->step ?? 0;
    }
}
