<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Helpers;

final class Move
{
	/**
	 * @param  array  $holder
	 * @param  int    $index
	 * @return array
	 * @link https://gist.github.com/dalethedeveloper/966848 Source of the code
	 */
	public static function up(array $holder, $index)
	{
		if ($index == 0 || $index > count($holder)) {
			return $holder;
		}

		$result = array_slice($holder, 0, $index - 1, TRUE);
		$result[] = $holder[$index];
		$result[] = $holder[$index - 1];
		$result += array_slice($holder, $index + 1, count($holder), TRUE);

		return $result;
	}


	/**
	 * @param  array  $holder
	 * @param  int    $index
	 * @return array
	 * @link https://gist.github.com/dalethedeveloper/966848 Source of the code
	 */
	public static function down(array $holder, $index)
	{
		if (count($holder) - 1 < $index) {
			return $holder;
		}

		$result = array_slice($holder, 0, $index, TRUE);
		$result[] = $holder[$index + 1];
		$result[] = $holder[$index];
		$result += array_slice($holder, $index + 2, count($holder), TRUE);

		return $result;
	}
}
