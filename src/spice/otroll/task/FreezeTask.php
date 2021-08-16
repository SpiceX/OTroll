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

namespace spice\otroll\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;

class FreezeTask extends Task
{
    /** @var int[] $queue */
    private $queue = [];
    /** @var Player[] $players */
    private $players = [];

    /**
     * @param Player $player
     */
    public function addToQueue(Player $player): void
    {
        $this->queue[$player->getName()] = 10;
        $this->players[$player->getName()] = $player;
        $player->setImmobile();
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void
    {
        foreach ($this->queue as $name => $tick) {
            $player = $this->players[$name];

            if ($tick === 0) {
                $player->setImmobile(false);
                unset($this->queue[$player->getName()]);
                unset($this->players[$player->getName()]);
                continue;
            }

            $this->queue[$name]--;
        }
    }
}