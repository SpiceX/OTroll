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

use JsonSerializable;

class Image implements JsonSerializable
{
	public const TYPE_URL = "url";
	public const TYPE_PATH = "path";

	/** @var string */
	private $type;
	/** @var string */
	private $data;

	/**
	 * @param string $data
	 * @param string $type
	 */
	public function __construct(string $data, string $type = self::TYPE_URL)
	{
		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getData(): string
	{
		return $this->data;
	}

	public function jsonSerialize(): array
	{
		return [
			"type" => $this->type,
			"data" => $this->data
		];
	}
}