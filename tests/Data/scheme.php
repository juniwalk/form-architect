<?php

/**
 * @author    Martin Procházka <juniwalk@outlook.cz>
 * @package   FormArchitect
 * @link      https://github.com/juniwalk/form-architect
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

return [
	'page1' => [
		'class' => 'JuniWalk\FormArchitect\Controls\Sections\Page',
		'title' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Fields\Title',
			'content' => 'Page 1',
		],
		'description' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Fields\Description',
			'content' => '<p>This is a <b>description</b>. :)<br></p>',
		],
		'question' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Fields\Question',
			'title' => 'Question',
			'isRequired' => TRUE,
			'description' => 'Question description',
			'options' => [
				['option' => 'Answer 1'],
				['option' => 'Answer 2'],
				['option' => 'Answer 3'],
			],
			'multiple' => FALSE,
			'type' => 'select',
		],
		'choice' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Fields\Question',
			'title' => 'Gender',
			'isRequired' => FALSE,
			'options' => [
				['option' => 'Male'],
				['option' => 'Female'],
			],
			'inline' => TRUE,
			'type' => 'radio',
		],
	],
	'page2' => [
		'class' => 'JuniWalk\FormArchitect\Controls\Sections\Page',
		'question' => [
			'class' => 'JuniWalk\FormArchitect\Controls\Fields\Question',
			'title' => 'Question',
			'isRequired' => FALSE,
			'description' => 'Question description',
			'type' => 'text',
		],
	],
];
