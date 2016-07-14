<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Container;
use Nette\Http\Session;

/**
 * @property Itranslator $translator
 * @method void onBeforeRender(BaseArchitect $architect, $template)
 * @method void onSchemeChange()
 * @method void onSchemeSave()
 */
abstract class BaseArchitect extends Controls\Control implements Controls\Architect
{
	/** @var \Nette\Http\SessionSection */
	private $cache;

	/** @var string */
	private $identifier;

	/** @var string|NULL */
	private $templateFile;

	/** @var callable[] */
	public $onBeforeRender = [];

	/** @var callable[] */
	public $onSchemeChange = [];

	/** @var callable[] */
	public $onSchemeSave = [];


	/**
	 * @param string   $identifier
	 * @param Session  $session
	 */
	public function __construct($identifier, Session $session)
	{
		$this->cache = $session->getSection('Architect.'.$this->getClass().'.'.$identifier);
		$this->identifier = $identifier;
		$this->startup();
	}


	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}


	/**
	 * @param string|NULL  $templateFile
	 */
	public function setTemplateFile($templateFile = NULL)
	{
		$this->templateFile = $templateFile;
	}


	/**
	 * @return string
	 */
	public function getTemplateFile()
	{
		return $this->templateFile ?: $this->getDefaultTemplateFile();
	}


	/**
	 * @return string
	 */
	abstract public function getDefaultTemplateFile();


	/**
	 * @param  string|NULL  $name
	 * @return Section
	 */
	public function addSection($name = NULL)
	{
		$name = $name ?: $this->createName('section');
		$container = $this->getForm()->addContainer($name);

		$this->addComponent($section = new Controls\Sections\Page($this, $container), $name);
		return $section;
	}


	/**
	 * @return Section[]
	 */
	public function getSections()
	{
		$sections = $this->getComponents(FALSE, Controls\Section::class);
		return iterator_to_array($sections);
	}


	/**
	 * @return string
	 */
	public function getClass()
	{
		$class = explode('\\', static::class);
		return end($class);
	}


	/**
	 * @param  string  $prefix
	 * @param  int     $length
	 * @return string
	 */
	public function createName($prefix, $length = 15)
	{
		return substr($prefix.sha1(uniqid('', TRUE)), 0, $length);
	}


	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFile());
		$template->setTranslator($this->translator);

		$this->onBeforeRender($this, $template);

		$template->render();
	}


	/**
	 * @return \Nette\Http\SessionSection
	 */
	protected function getCache()
	{
		return $this->cache;
	}


	/**
	 * @return NULL
	 */
	abstract protected function startup();


	/**
	 * @param  IComponent  $child
	 * @throws \Nette\InvalidStateException
	 */
	protected function validateChildComponent(IComponent $child)
	{
		if ($child instanceof Controls\Section || $child instanceof Container) {
			return NULL;
		}

		throw new \Nette\InvalidStateException;
	}


	/**
	 * @param  string  $name
	 * @return Form
	 */
	protected function createComponentScheme($name)
	{
		$form = new Form($this, $name);
		$form->setTranslator($this->translator);
		$form->onSuccess[] = function () {
			$this->onSchemeSave();
		};

		$form->addSubmit('save');

		return $form;
	}
}
