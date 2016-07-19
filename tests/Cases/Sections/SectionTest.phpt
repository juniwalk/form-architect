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

use JuniWalk\FormArchitect\Controls\Fields\Description;
use JuniWalk\FormArchitect\Controls\Fields\Question;
use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class SectionTest extends \Tester\TestCase
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


	public function testAddQuestion()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$page2 = $designer->getComponent('page2');
		$page2->handleAddQuestion();

		Assert::count(2, $fields = $page2->getFields());
		Assert::type(Question::class, end($fields));
	}


	public function testAddDescription()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$page2 = $designer->getComponent('page2');
		$page2->handleAddDescription();

		Assert::count(2, $fields = $page2->getFields());
		Assert::type(Description::class, end($fields));
	}


	public function testClone()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$designer->getComponent('page1')->handleClone();

		Assert::count(3, $designer->getSections());
	}


	public function testRemove()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$page2 = $designer->getComponent('page2');
		$page2->handleRemove();

		Assert::count(1, $designer->getSections());

		$page1 = $designer->getComponent('page1');

		Assert::false($page1->isDeleteAllowed());
	}


	public function testMerge()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$page1 = $designer->getComponent('page1');
		Assert::false($page1->isMergeAllowed());

		$page2 = $designer->getComponent('page2');
		Assert::true($page2->isMergeAllowed());

		$page2->addTitle();
		$page2->handleMerge();

		$sections = $designer->getSections();
		Assert::count(1, $sections);
	}
}

(new SectionTest)->run();
