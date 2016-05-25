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

use JuniWalk\FormArchitect\ArchitectFactory;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

final class ArchitectFactoryTest extends \Tester\TestCase
{
	public function testDesigner()
	{
		Assert::true(TRUE);
	}


	public function testRenderer()
	{
		Assert::true(TRUE);
	}
}

(new ArchitectFactoryTest)->run();
