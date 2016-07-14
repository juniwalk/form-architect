<?php

/**
 * TEST: Helper for moving the fields.
 * @testCase
 *
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Tests\Cases;

use JuniWalk\FormArchitect\Helpers\Move;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class MoveTest extends \Tester\TestCase
{
	/** @var string[] */
	private $values = [
		'Field 1',
		'Field 2',
		'Field 3',
	];


	/**
	 * @return string[]
	 */
	public function getValues()
	{
		return $this->values;
	}


	public function testMoveUp()
	{
		$values = $this->getValues();

		Assert::same('Field 1', $values[0]);
		$values = Move::up($values, 1);
		Assert::same('Field 2', $values[0]);
	}


	public function testMoveDown()
	{
		$values = $this->getValues();

		Assert::same('Field 3', $values[2]);
		$values = Move::down($values, 1);
		Assert::same('Field 2', $values[2]);
	}


	public function testOutOfReach()
	{
		$values = $this->getValues();
		$values = Move::down($values, 5);
		$values = Move::up($values, 5);

		Assert::same($values, $this->getValues());
	}
}

(new MoveTest)->run();
