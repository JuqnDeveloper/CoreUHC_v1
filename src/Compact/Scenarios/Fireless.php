<?php

namespace Compact\Scenarios;

use Compact\Loader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class Fireless implements Listener
{
	
    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function onDamage(EntityDamageEvent $event)
    {
        if ($this->getPlugin()->configuration['Scenarios']['Fireless'] == true) {
            $entity = $event->getEntity();
            if ($entity instanceof Player) {
                if ($event->getCause() === EntityDamageEvent::CAUSE_FIRE or $event->getCause() === EntityDamageEvent::CAUSE_FIRE_TICK or $event->getCause() === EntityDamageEvent::CAUSE_LAVA) {
                    $event->setCancelled();
                    $entity->extinguish();
                }
            }
        }
    }
}