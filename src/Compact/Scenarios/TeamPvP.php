<?php

namespace Compact\Scenarios;

use Compact\Loader;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class TeamPvP implements Listener
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
        if ($this->getPlugin()->configuration['Scenarios']['TeamPvP'] == true) {
            $entity = $this->getPlugin()->getPlayer($event->getEntity()->getName());
            if($event instanceof EntityDamageByEntityEvent){
            	$damager = $this->getPlugin()->getPlayer($event->getDamager()->getName());
            	if($entity->inTeamDamager($damager->getTeamInt())){
            		$event->setCancelled();
            	}
            }
        }
    }
}