<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Sections;

use JuniWalk\FormArchitect\Architect;
use Nette\Forms\Container as Form;

final class ThankYouPage extends AbstractSection
{
    /** @var string */
    private $redirect;

    /** @var string */
    private $description;


    /**
     * @param Architect  $architect
     * @param Form  $form
     */
    public function __construct(Architect $architect, Form $form)
    {
        parent::__construct($architect, $form);
        $form->addText('redirect')->setType('url');
        $form->addTextArea('description');
    }


    /**
     * @return string|null
     */
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }


    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return trim($this->description, '<p></p>');
    }


    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return false;
    }


	/**
	 * @return bool
	 */
	public function isMergeAllowed(): bool
	{
        return false;
	}


	/**
	 * @return bool
	 */
	public function isDeleteAllowed(): bool
	{
        return true;
	}


	/**
	 * @return iterable
	 */
	public function getScheme(): iterable
	{
		$scheme = parent::getScheme();
		$scheme = $scheme + $this->getForm()->getValues(true);

		return $scheme;
	}


	/**
	 * @param  iterable  $scheme
	 * @return void
	 */
	public function setScheme(iterable $scheme = []): void
	{
        $this->redirect = $scheme['redirect'] ?? null;
        $this->description = $scheme['description'] ?? null;

        $this->getForm()->setValues($scheme, true);
		parent::setScheme($scheme);
	}


	/**
	 * @return void
	 */
	public function render(): void
	{
		$template = $this->createTemplate();
		$template->setFile(__DIR__.'/../templates/designer-thankyou.latte');
		$template->setTranslator($this->getArchitect()->getTranslator());
		$template->add('architect', $this->getArchitect());

		foreach ($this->getArchitect()->getTemplate()->getParameters() as $key => $value) {
			try {
				$template->add($key, $value);
			} catch (\Exception $e) { }
		}

		$template->render();
	}
}
