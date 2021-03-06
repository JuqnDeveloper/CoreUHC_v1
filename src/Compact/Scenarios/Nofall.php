<?php

namespace Compact\Scenarios;

use Compact\Loader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class Nofall implements Listener
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
        if ($this->getPlugin()->configuration['Scenarios']['Nofall'] == true) {
            if ($event->getEntity() instanceof Player) {
                if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
                    $event->setCancelled();
                }
            }
        }
    }
}