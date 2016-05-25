<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Helpers;

use Nette\Utils\Html;

final class SimpleTranslator implements \Nette\Localization\ITranslator
{
	/** @var string[] */
	private $messages = [
		'form-architect.renderer.steps-back' => 'Zpět',
		'form-architect.renderer.steps-next' => 'Další',
		'form-architect.renderer.steps-submit' => 'Odeslat',

		'form-architect.designer.scheme-save' => 'Uložit',
		'form-architect.designer.scheme-clear' => 'Zahodit vše',

		'form-architect.designer.section' => 'Stránka',
		'form-architect.designer.section-add' => 'Přidat stránku',
		'form-architect.designer.section-name' => 'Název stránky',
		'form-architect.designer.section-question' => 'Přidat otázku',
		'form-architect.designer.section-description' => 'Přidat popis',

		'form-architect.designer.field-title' => 'Otázka a typ odpovědi',
		'form-architect.designer.field-description' => 'Popis',
		'form-architect.designer.field-required' => 'Nastavit jako povinnou otázku.',
		'form-architect.designer.field-type-text' => 'Text',
		'form-architect.designer.field-type-textarea' => 'TextArea',
		'form-architect.designer.field-type-radio' => 'Radio',
		'form-architect.designer.field-type-checkbox' => 'Checkbox',
		'form-architect.designer.field-type-select' => 'Select',

		'form-architect.designer.field-option' => 'Odpověď',
		'form-architect.designer.field-option-add' => 'Přidat odpověď',
		'form-architect.designer.field-option-delete' => 'Odebrat odpověď',
		'form-architect.designer.field-option-multiple' => 'Umožnit více odpovědí.',
		'form-architect.designer.field-option-inline' => 'Odpovědi zobrazit do řádku.',
	];


	/**
	 * @param  string    $message
	 * @param  int|NULL  $count
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		if ($message instanceof Html) {
			$message = $message->getText();
		}

		if (!$message || !isset($this->messages[$message])) {
			return $message;
		}

		return $this->messages[$message];
	}
}
