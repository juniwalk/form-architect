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

use JuniWalk\FormArchitect\Helpers\SimpleTranslator;
use Nette\Utils\Html;
use Tester\Assert;

require __DIR__.'/../../bootstrap.php';

final class SimpleTranslatorTest extends \Tester\TestCase
{
	public function testTranslate()
	{
		Assert::same('Stránka', (new SimpleTranslator)->translate('form-architect.designer.section'));
	}


	public function testNotFound()
	{
		Assert::same('Stránka', (new SimpleTranslator)->translate(Html::el(NULL, 'Stránka')));
	}
}

(new SimpleTranslatorTest)->run();
