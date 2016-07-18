<?php

/**
 * TEST: Form Architect base field.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

use JuniWalk\FormArchitect\Controls\Field;
use JuniWalk\FormArchitect\Designer;
use JuniWalk\FormArchitect\Tests\Files\ArchitectFactory;
use Nette\Utils\Random;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class FieldTest extends \Tester\TestCase
{
	/** @var Designer */
	private $designer;

	/**@var string[] */
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


	public function testGeneric()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');

		Assert::type(Field::class, $field);
		Assert::same('question', $field->getClass());
		Assert::same(NULL, $field->getValue('unknown'));

		Assert::true($field->hasToolbar());
		Assert::true($field->isSortable());

		Assert::same('form-architect.designer.field-type-select', $field->getTitle());
	}


	public function testIcon()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');

		Assert::same('fa-chevron-circle-down', $field->getIcon());
		Assert::same('fa-font', $field->getIconFor('text'));
	}


	public function testClone()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');
		$field->handleClone();

		$fieldName = array_keys($designer->getScheme()['page1']);
		$fieldName = end($fieldName);

		$cloned = $designer->getComponent('page1-'.$fieldName);

		Assert::type(get_class($field), $cloned);
	}


	public function testRemove()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');
		$field->handleRemove();

		Assert::exception(function () use ($designer) {
			$designer->getComponent('page1-question');

		}, 'Nette\InvalidArgumentException');
	}


	public function testMoveUp()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');

		Assert::true($field->hasUpwardMove());
		$field->handleMoveUp();
		Assert::false($field->hasUpwardMove());
	}


	public function testMoveDown()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$field = $designer->getComponent('page1-question');

		Assert::true($field->hasDownwardMove());
		$field->handleMoveDown();
		Assert::false($field->hasDownwardMove());
	}
}

(new FieldTest)->run();
