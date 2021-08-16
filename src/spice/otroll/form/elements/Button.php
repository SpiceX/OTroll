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
namespace spice\otroll\form\elements;

class Button extends Element
{
	/** @var Image|null */
	private $image;
	/** @var string */
	private $type;

	/**
	 * @param string $text
	 * @param Image|null $image
	 */
	public function __construct(string $text, ?Image $image = null)
	{
		parent::__construct($text);
		$this->image = $image;
	}

	/**
	 * @return string|null
	 */
	public function getType(): ?string
	{
		return null;
	}

	/**
	 * @return bool
	 */
	public function hasImage(): bool
	{
		return $this->image !== null;
	}

	/**
	 * @return array
	 */
	public function serializeElementData(): array
	{
		$data = ["text" => $this->text];
		if ($this->hasImage()) {
			$data["image"] = $this->image;
		}
		return $data;
	}
}