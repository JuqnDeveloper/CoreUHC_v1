<?php

namespace Compact\Scenarios;

use Compact\Loader;
use Compact\Task\TaskClean;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\utils\TextFormat;

class NoClean implements Listener
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getServer()
    {
        return $this->plugin->getServer();
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function onDeath(PlayerDeathEvent $e)
    {
        $p = $e->getPlayer();
        $cause = $p->getLastDamageCause();
        if ($this->getPlugin()->configuration['Scenarios']['Noclean'] == true) {
            if ($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                if ($killer instanceof Player) {
                    $this->getPlugin()->noclean[$killer->getName()] = $killer->getName();
                    $this->getPlugin()->taskclean[$killer->getName()] = $this->getServer()->getScheduler()->scheduleRepeatingTask(new TaskClean($this->getPlugin(), $killer), 20)->getTaskId();
                    $killer->sendMessage(TextFormat::RED . "NoClean activated!. Duration 20s");
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $e)
    {
        if ($this->getPlugin()->configuration['Scenarios']['Noclean'] == true) {
            if ($e instanceof EntityDamageByEntityEvent) {
                $damager = $e->getDamager();
                $entity = $e->getEntity();
                if ($damager instanceof Player and $entity instanceof Player) {
                    if (in_array($entity->getName(), $this->getPlugin()->noclean) and !in_array($damager->getName(), $this->getPlugin()->noclean)) {
                        $e->setCancelled();
                    }
                    if (!in_array($entity->getName(), $this->getPlugin()->noclean) and in_array($damager->getName(), $this->getPlugin()->noclean)) {
                        unset($this->getPlugin()->noclean[$damager->getName()]);
                        $this->getServer()->getScheduler()->cancelTask($this->getPlugin()->taskclean[$damager->getName()]);
                        unset($this->getPlugin()->taskclean[$damager->getName()]);
                        $damager->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."NoClean".TextFormat::GRAY."]".TextFormat::RED." You hit while I was with the NoClean, your time is over!");
                    }
                    if (in_array($entity->getName(), $this->getPlugin()->noclean) and in_array($damager->getName(), $this->getPlugin()->noclean)) {
                        $e->setCancelled();
                    }
                }
            }
        }
    }
}