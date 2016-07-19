<?php

/**
 * TEST: Form Architect page section.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class PageTest extends \Tester\TestCase
{
	/** @var Designer */
	private $designer;

	/** @var string[] */
	private $scheme = [];


	public function __construct()
	{
		$this->scheme = include __DIR__.'/../../Data/scheme.php';
		$this->designer = (new ArchitectFactory)
			->createDesigner(Random::generate(8));
	}


	/**
	 * @return Designer
	 */
	public function getDesigner()
	{
		return $this->designer;
	}


	public function testTitle()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$section = $designer->getComponent('page1');
		Assert::same('Page 1', $section->getTitle());

		$section = $designer->getComponent('page2');
		Assert::same('page2', $section->getTitle());
	}
}

(new PageTest)->run();
