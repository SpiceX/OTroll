<?php
/*
 *   ___ _____          _ _
 *  / _ \_   _| __ ___ | | |
 * | | | || || '__/ _ \| | |
 * | |_| || || | | (_) | | |
 * \___/ |_||_|  \___/|_|_|
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Spice
 * @link https://github.com/SpiceX/OTroll
 *
*/
declare(strict_types=1);

namespace spice\otroll;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as T;
use spice\otroll\command\TrollCommand;
use spice\otroll\troll\TrollManager;

class OTroll extends PluginBase
{
    public const PREFIX = T::DARK_GREEN . "[" . T::GREEN . "OTroll" . T::DARK_GREEN . "] ";

    /** @var TrollManager|null */
    private $trollManager;

    public function onEnable(): void
    {
        $this->trollManager = new TrollManager($this);
        $this->getServer()->getCommandMap()->register("OTroll", new TrollCommand($this));
    }

    /**
     * @return TrollManager
     */
    public function getTrollManager(): TrollManager
    {
        return $this->trollManager;
    }

}