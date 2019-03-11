<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Fields;

use Nette\Forms\Container as Form;
use Nette\Forms\IControl as Input;

final class Captcha extends AbstractField implements InputProvider
{
	/**
	 * @return bool
	 */
	public function isRequired(): bool
	{
		return true;
	}


    /**
     * @return bool
     */
    public function isSortable(): bool
    {
        return true;
    }


	/**
	 * @param  Form  $form
	 * @return Input|null
	 * @throws Exception
	 */
	public function createInput(Form $form): ?Input
	{
        $input = $form->addReCaptcha($this->getName())
            ->setRequired(true);

		return $input;
	}


	/**
	 * @return void
	 */
	protected function createSchemeField(): void
	{
		$form = $this->getForm();
        $form->addReCaptcha('captcha')
            ->setRequired(false);
	}
}
