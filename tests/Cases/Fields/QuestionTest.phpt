<?php

/**
 * TEST: Form Architect question field.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

use JuniWalk\FormArchitect\Controls\Fields\Question;
use JuniWalk\FormArchitect\Designer;
use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class QuestionTest extends \Tester\TestCase
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


	public function testType()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');

		Assert::type(Question::class, $field);
		Assert::type('array', $field->getTypes());
		Assert::exception(function () use ($field) {
			$field->setType('unknown');

		}, 'Exception');
	}


	public function testAddOption()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');
		$field->handleAddOption();

		Assert::count(4, $options = $field->getValue('options'));
		Assert::null($options[3]['option']);
	}


	public function testDeleteOption()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');
		$field->handleDeleteOption(1);

		Assert::count(2, $options = $field->getValue('options'));
		Assert::same('Answer 3', $options[1]['option']);
	}


	/**
	 * @param string  $type
	 * @param bool    $check1  If the field is choice control
	 * @param bool    $check2  If the field is a select
	 * @dataProvider ../../Data/types.php
	 */
	public function testQuestion($type, $check1, $check2)
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page2-question');
		$field->handleType($type);

		Assert::same($type, $field->getType());
		Assert::same($check1, $field->isChoiceControl());
		Assert::same($check2, $field->isSelect());
	}
}

(new QuestionTest)->run();
