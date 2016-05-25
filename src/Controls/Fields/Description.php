<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls\Fields;

final class Description extends BaseField
{
	protected function createSchemeField()
	{
		$form = $this->getForm();
		$form->addTextarea('content');
	}
}
