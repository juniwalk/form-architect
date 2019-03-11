<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\DI;

use JuniWalk\FormArchitect\ArchitectFactory;
use JuniWalk\FormArchitect\Caches;
use Nette\DI\CompilerExtension;

final class FormArchitectExtension extends CompilerExtension
{
	/** @var string[] */
	private $default = [
		'cache' => 'default',
	];

	/** @var string[] */
	private $cacheDrivers = [
		'default' => Caches\NetteCache::class,
		'session' => Caches\SessionCache::class,
	];


	public function loadConfiguration()
	{
        $config = $this->validateConfig($this->default, $this->getConfig());
		$cache = $this->cacheDrivers[$config['cache']];

		$this->getContainerBuilder()
			->addDefinition($this->prefix('cache'))
			->setClass($cache);

		$this->getContainerBuilder()
			->addDefinition($this->prefix('factory'))
			->setClass(ArchitectFactory::class);
	}
}
