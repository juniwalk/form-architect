<?php

/**
 * TEST: Form Architect factory.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Tests\Cases;

use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use JuniWalk\FormArchitect\Designer;
use JuniWalk\FormArchitect\Renderer;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

final class ArchitectFactoryTest extends \Tester\TestCase
{
	/** @var ArchitectFactory */
	private $factory;


	public function __construct()
	{
		$this->factory = new ArchitectFactory;
	}


	public function testDesigner()
	{
		$designer = $this->factory->createDesigner(
			$identifier = Random::generate(8)
		);

		Assert::same($identifier, $designer->getIdentifier());
		Assert::type(Designer::class, $designer);
	}


	public function testRenderer()
	{
		$renderer = $this->factory->createRenderer(
			$identifier = Random::generate(8)
		);

		Assert::same($identifier, $renderer->getIdentifier());
		Assert::type(Renderer::class, $renderer);
	}
}

(new ArchitectFactoryTest)->run();
