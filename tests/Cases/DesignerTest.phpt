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

namespace JuniWalk\FormArchitect\Tests\Cases;

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
	private $scheme = [
		'mySection' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Sections\Page',
			'sectionTitle' => [
				'class' => 'JuniWalk\FormArchitect\Controls\Fields\Title',
				'content' => 'Form title',
			],
		],
	];


	public function __construct()
	{
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


	public function testScheme()
	{
		$designer = $this->getDesigner();
		$designer->setScheme($this->scheme);

		$scheme = $designer->getScheme();

		Assert::type('array', $scheme);
		Assert::same($this->scheme, $scheme);
	}
}

(new DesignerTest)->run();
