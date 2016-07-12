<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Tests\Files;

use JuniWalk\FormArchitect\ArchitectFactory as Factory;
use Nette\Http\RequestFactory;
use Nette\Http\Response;
use Nette\Http\Session;

final class ArchitectFactory
{
	/** @var Factory */
	private $factory;


	public function __construct()
	{
		$request = (new RequestFactory)->createHttpRequest();
		$response = new Response;

		$this->factory = new Factory(new Session($request, $response), NULL);
	}


	/**
	 * @param  string  $identifier
	 * @return Designer
	 */
	public function createDesigner($identifier)
	{
		return $this->factory->createDesigner($identifier);
	}


	/**
	 * @param  string  $identifier
	 * @return Renderer
	 */
	public function createRenderer($identifier)
	{
		return $this->factory->createRenderer($identifier);
	}
}
