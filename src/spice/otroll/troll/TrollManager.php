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

namespace spice\otroll\troll;

use pocketmine\block\BlockIds;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use ReflectionClass;
use spice\otroll\form\elements\Button;
use spice\otroll\form\types\MenuForm;
use spice\otroll\OTroll;
use spice\otroll\task\FreezeTask;
use spice\otroll\task\TimeoutTask;

class TrollManager
{

    /** @var OTroll|null */
    private $plugin;

    /** @var TimeoutTask */
    private $timeoutTask;

    /** @var FreezeTask */
    private $freezeTask;

    /**
     * @param OTroll $plugin
     */
    public function __construct(OTroll $plugin)
    {
        $this->plugin = $plugin;
        $plugin->getScheduler()->scheduleRepeatingTask($this->timeoutTask = new TimeoutTask(), 20);
        $plugin->getScheduler()->scheduleRepeatingTask($this->freezeTask = new FreezeTask(), 20);
    }

    /**
     * @param Player $trolled
     * @return MenuForm
     */
    public function getTrollMenu(Player $trolled): MenuForm
    {
        return new MenuForm(
            TF::YELLOW . "Trolling {$trolled->getName()}", TF::GRAY . "Select a troll", [
            new Button("Burn"), new Button("Fake Timeout"),
            new Button("Drop"), new Button("FakeOP"),
            new Button("Lightning Strike"), new Button("Scare"),
            new Button("Fake Demo"), new Button("Look Random"),
            new Button("Freeze"), new Button("Launch"),
            new Button("Hunger"), new Button("All Effects"),
            new Button("Fake Ban"), new Button("Anticheat Fake"),
            new Button("Explode"), new Button("Kill"), new Button("Shuffle Inventory"),
            new Button("Damage"), new Button("Drop All Items"),
        ],
            function (Player $player, Button $selected) use ($trolled): void {
                if (!$trolled instanceof Player || !$trolled->isOnline()) {
                    return;
                }
                switch ($selected->getValue()) {
                    case 0:
                        $trolled->setOnFire(10);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} is now in fire for 10 seconds!");
                        break;
                    case 1:
                        $this->timeoutTask->addToQueue($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} has now a countdown!");
                        break;
                    case 2:
                        $trolled->getLevel()->dropItem($trolled->asVector3(), $trolled->getInventory()->getItemInHand(), $trolled->getDirectionVector());
                        $trolled->getInventory()->setItemInHand(Item::get(BlockIds::AIR));
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} has dropped the item in hand!");
                        break;
                    case 3:
                        $trolled->sendMessage(TF::GRAY . "You are now op!");
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} has been trolled with fake op!");
                        break;
                    case 4:
                        $this->addLightningBolt($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} has been struck by lightning.!");
                        break;
                    case 5:
                        $this->addElderScare($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} scared with elder guardian curse!");
                        break;
                    case 6:
                        $this->addFakeDemo($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "Fake demo was sent to player!");
                        break;
                    case 7:
                        $trolled->teleport($trolled->asVector3(), mt_rand(0, 180), mt_rand(0, 180));
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "Player rotation changed!");
                        break;
                    case 8:
                        $this->freezeTask->addToQueue($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} freezed for 10 seconds!");
                        break;
                    case 9:
                        $trolled->getLevel()->addParticle(new HugeExplodeParticle($trolled->asVector3()));
                        $trolled->setMotion(new Vector3($trolled->getDirectionVector()->x * 2, 2, $trolled->getDirectionVector()->z * 2));
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} launched to the moon!");
                        break;
                    case 10:
                        $trolled->setFood(1.0);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} hunger is now 1!");
                        break;
                    case 11:
                        foreach ($this->getAllEffects() as $effect) {
                            $trolled->addEffect(new EffectInstance($effect, 20 * 10));
                        }
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} now has all effects!");
                        break;
                    case 12:
                        $trolled->kick(TF::RED . "You have been banned forever.", false);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} fake banned lol!");
                        break;
                    case 13:
                        $trolled->sendMessage(TF::RED . "[AntiCheat] we have detected some cheating in your game, please close the programs or you will be banned.");
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} warned for fake hacks lmao");
                        break;
                    case 14:
                        $nbt = Entity::createBaseNBT($trolled->asPosition());
                        $entity = Entity::createEntity("PrimedTNT", $trolled->getLevel(), $nbt);
                        $entity->spawnToAll();
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} will explode, now...");
                        break;
                    case 15:
                        $trolled->kill();
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . ":trollface:");
                        break;
                    case 16:
                        $this->shuffleInv($trolled);
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} inventory shuffled");
                        break;
                    case 17:
                        $trolled->attack(new EntityDamageEvent($trolled, EntityDamageEvent::CAUSE_SUICIDE, 1));
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} has been hit");
                        break;
                    case 18:
                        foreach ($trolled->getInventory()->getContents() as $item) {
                            $trolled->getLevel()->dropItem($trolled->asVector3(), $item, $trolled->getDirectionVector());
                        }
                        $trolled->getInventory()->clearAll();
                        $player->sendMessage(OTroll::PREFIX . TF::GREEN . "{$trolled->getName()} lost the inventory");
                }
            }
        );
    }

    /**
     * @param Player $player
     */
    public function shuffleInv(Player $player): void
    {
        $inv = $player->getInventory();
        $contents = $inv->getContents(true);
        shuffle($contents);
        $inv->setContents($contents);
    }

    /**
     * @param Player $player
     */
    public function addLightningBolt(Player $player): void
    {
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $pk->entityUniqueId = Entity::$entityCount++;
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[EntityIds::LIGHTNING_BOLT];
        $pk->position = $player->asVector3();
        $pk->motion = $player->asVector3();
        $this->plugin->getServer()->broadcastPacket($this->plugin->getServer()->getOnlinePlayers(), $pk);
    }

    /**
     * @param Player $player
     */
    public function addElderScare(Player $player): void
    {
        $player->broadcastEntityEvent(ActorEventPacket::ELDER_GUARDIAN_CURSE, 0, [$player]);
    }

    /**
     * @param Player $player
     */
    public function addFakeDemo(Player $player): void
    {
        for ($i = 0; $i < 20; $i++) {
            $player->sendMessage(TF::RED . "Your demo test has finished, please buy rank on the server.");
            $player->sendPopup(TF::RED . "Your demo test has finished, please buy rank on the server.");
        }
        $player->sendTitle(TF::RED . "Your demo test has finished", TF::RED . "please buy rank on the server");
    }

    /**
     * @return array
     */
    public function getAllEffects(): array
    {
        $reflection = new ReflectionClass(Effect::class);
        $property = $reflection->getProperty("effects");
        $property->setAccessible(true);
        return $property->getValue();
    }

}