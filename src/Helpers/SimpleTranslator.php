<?php declare(strict_types=1);

/**
 * @copyright Martin Procházka (c) 2016
 * @license   MIT License
 */

namespace JuniWalk\FormArchitect\Helpers;

use Nette\Localization\ITranslator;
use Nette\Neon\Neon;
use Nette\Utils\Html;

final class SimpleTranslator implements ITranslator
{
	/** @var string[] */
	private $messages = [];


	public function __construct()
	{
		$neon = __DIR__.'/../../dist/locale/form-architect.cs.neon';
		$neon = file_get_contents($neon);
		$this->messages = Neon::decode($neon);
	}


	/**
	 * @param  string  $message
	 * @param  int|NULL  $count
	 * @return string
	 */
	public function translate($message, $count = null)
	{
		if ($message instanceof Html) {
			$message = $message->getText();
		}

		return $this->getText($message) ?? $message;
	}


	/**
	 * @param  string  $message
	 * @return string|null
	 */
	private function getText(string $message): ?string
	{
		$keys = explode('.', $message);
		$source = $this->messages;
		array_shift($keys);

		foreach ($keys as $key) {
			$source = $source[$key] ?? null;
		}

		if (is_array($source)) {
			return null;
		}

		return $source;
	}
}
