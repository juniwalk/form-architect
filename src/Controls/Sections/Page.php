<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Controls\Sections;

use JuniWalk\FormArchitect\Controls\Fields\Title;

final class Page extends BaseSection
{
	/**
	 * @return string
	 */
	public function getTitle()
	{
		$components = $this->getComponents();
		$title = reset($components);

		if (!$title instanceof Title || !($content = $title->getValue('content'))) {
			return $this->getName();
		}

		return $content;
	}
}
