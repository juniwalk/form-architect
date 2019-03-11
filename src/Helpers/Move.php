<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Helpers;

/**
 * @author dalethedeveloper
 * @link https://gist.github.com/dalethedeveloper/966848
 */
final class Move
{
	/**
	 * @param  iterable  $items
	 * @param  int  $index
	 * @return iterable
	 */
	public static function up(iterable $items, int $index): iterable
	{
		if ($index == 0 || $index > count($items)) {
			return $items;
		}

		$result = array_slice($items, 0, $index - 1, true);
		$result[] = $items[$index];
		$result[] = $items[$index - 1];
		$result += array_slice($items, $index + 1, count($items), true);

		return $result;
	}


	/**
	 * @param  iterable  $items
	 * @param  int  $index
	 * @return iterable
	 */
	public static function down(iterable $items, int $index): iterable
	{
		if (count($items) - 1 < $index) {
			return $items;
		}

		$result = array_slice($items, 0, $index, true);
		$result[] = $items[$index + 1];
		$result[] = $items[$index];
		$result += array_slice($items, $index + 2, count($items), true);

		return $result;
	}
}
