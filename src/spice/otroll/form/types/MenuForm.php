<?php
/*
 *      _____                _____
 *     | ____|__ _ ___ _   _|  ___|__  _ __ _ __ ___  ___
 *    |  _| / _` / __| | | | |_ / _ \| '__| '_ ` _ \/ __|
 *   | |__| (_| \__ \ |_| |  _| (_) | |  | | | | | \__ \
 *  |_____\__,_|___/\__, |_|  \___/|_|  |_| |_| |_|___/
 *                |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Frago9876543210
 * @link https://github.com/Frago9876543210/EasyForms
 *
*/

declare(strict_types=1);
namespace spice\otroll\form\types;

use Closure;
use spice\otroll\form\elements\Button;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use function array_map;
use function array_merge;
use function is_int;
use function is_string;

class MenuForm extends Form
{
	/** @var string */
	private $content;
	/** @var Button[] */
	private $buttons = [];

	public function __construct(string $title, string $content = "", array $buttons = [], ?Closure $onSubmit = null, ?Closure $onClose = null)
	{
		parent::__construct($title);
		$this->content = $content;
		$this->append(...$buttons);
		if ($onSubmit !== null) {
			$this->onSubmit($onSubmit);
		}
		if ($onClose !== null) {
			$this->onClose($onClose);
		}
	}

	/**
	 * @return string
	 */
	protected function getType(): string
	{
		return self::TYPE_MENU;
	}

	/**
	 * @return callable
	 */
	protected function getOnSubmitCallableSignature(): callable
	{
		return function (Player $player, Button $selected): void {
		};
	}

	/**
	 * @return array
	 */
	protected function serializeFormData(): array
	{
		return [
			"buttons" => $this->buttons,
			"content" => $this->content
		];
	}

	/**
	 * @param Button|string ...$buttons
	 * @return self
	 */
	public function append(...$buttons): self
	{
		if (isset($buttons[0])) {
			if (is_string($buttons[0])) {
				$buttons = array_map(function (string $text): Button {
					return new Button($text);
				}, $buttons);
			} else {
				(function (Button ...$_) {
				})(...$buttons);
			}
		}
		$this->buttons = array_merge($this->buttons, $buttons);
		return $this;
	}

	final public function handleResponse(Player $player, $data): void
	{
		if ($data === null) {
			if ($this->onClose !== null) {
				($this->onClose)($player);
			}
		} elseif (is_int($data)) {
			if (!isset($this->buttons[$data])) {
				throw new FormValidationException("Button with index $data does not exist");
			}
			if ($this->onSubmit !== null) {
				$button = $this->buttons[$data];
				$button->setValue($data);
				($this->onSubmit)($player, $button);
			}
		} else {
			throw new FormValidationException("Expected int or null, got " . gettype($data));
		}
	}
}