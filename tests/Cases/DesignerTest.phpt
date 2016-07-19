<?php

/**
 * TEST: Designer class of Form Architect.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use JuniWalk\FormArchitect\Designer;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../bootstrap.php';

final class DesignerTest extends \Tester\TestCase
{
	/** @var Designer */
	private $designer;

	/**@var string[] */
	private $scheme = [];


	public function __construct()
	{
		$this->scheme = include __DIR__.'/../Data/scheme.php';
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


	public function testTemplateFile()
	{
		$designer = $this->getDesigner();

		Assert::same($designer->getDefaultTemplateFile(), $designer->getTemplateFile());

		$designer->setTemplateFile(__FILE__);

		Assert::same(__FILE__, $designer->getTemplateFile());
	}


	public function testScheme()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$scheme = $designer->getScheme();

		Assert::type('array', $scheme);
		Assert::same($this->scheme, $scheme);
	}


	public function testAddSection()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);
		$designer->handleAddSection();

		$scheme = $designer->getScheme();

		Assert::type('array', $scheme);
		Assert::count(3, $scheme);
	}


	public function testStartOver()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);
		$designer->handleStartOver();

		$scheme = $designer->getScheme();

		Assert::type('array', $scheme);
		Assert::count(0, $scheme);
	}
}

(new DesignerTest)->run();
