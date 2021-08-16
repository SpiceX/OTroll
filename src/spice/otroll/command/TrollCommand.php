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
namespace spice\otroll\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;
use spice\otroll\OTroll;

class TrollCommand extends Command implements PluginIdentifiableCommand
{

    /** @var OTroll */
    private $plugin;

    /**
     * @param OTroll $plugin
     */
    public function __construct(OTroll $plugin)
    {
        parent::__construct("troll", "troll command", "/troll", ['trl']);
        $this->setPermission("otroll.cmd");
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender) || !$sender instanceof Player) {
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage(TF::RED . "Usage: /troll <player: str>");
            return;
        }
        $player = $this->plugin->getServer()->getPlayer($args[0]);
        if (!$player instanceof Player || !$player->isOnline()) {
            $sender->sendMessage(TF::RED . "Player $args[0] was not found or is not online!");
            return;
        }
        $sender->sendForm($this->plugin->getTrollManager()->getTrollMenu($player));
    }


    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}