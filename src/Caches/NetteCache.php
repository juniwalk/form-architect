<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Caches;

use Nette\Caching;

final class NetteCache implements Cache
{
    /** @var Caching\IStorage */
    private $storage;

    /** @var Caching\Cache */
	private $cache;

	/** @var string[] */
	private $meta = [
		Caching\Cache::EXPIRE => '20 minutes',
		Caching\Cache::SLIDING => true,
		Caching\Cache::TAGS => ['form-architect'],
		Caching\Cache::PRIORITY => 50,
	];


    /**
     * @param Caching\IStorage  $storage
     */
    public function __construct(Caching\IStorage $storage)
    {
        $this->storage = $storage;
    }


    /**
     * @param  string  $namespace
     * @return Cache
     */
    public function derive(string $namespace): Cache
    {
        $this->cache = new Caching\Cache($this->storage, $namespace);
        return $this;
    }


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setValues(iterable $values): void
    {
        $this->cache->save('values', $values, $this->meta);
    }


    /**
     * @return iterable
     */
    public function getValues(): iterable
    {
        return $this->cache->load('values') ?? [];
    }


    /**
     * @param  iterable  $scheme
     * @return void
     */
    public function setScheme(iterable $scheme): void
    {
        $this->cache->save('scheme', $scheme, $this->meta);
    }


    /**
     * @return iterable
     */
    public function getScheme(): iterable
    {
        return $this->cache->load('scheme') ?? [];
    }


    /**
     * @param  int  $step
     * @return void
     */
    public function setStep(int $step): void
    {
        $this->cache->save('step', $step, $this->meta);
    }


    /**
     * @return int
     */
    public function getStep(): int
    {
        return $this->cache->load('step') ?? 0;
    }
}
