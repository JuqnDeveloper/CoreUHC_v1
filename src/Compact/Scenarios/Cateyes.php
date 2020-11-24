<?php

namespace Compact\Scenarios;

use Compact\Loader;

use pocketmine\event\Listener;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;

class Cateyes implements Listener {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function getPlugin()
    {
        return $this->plugin;
    }
    
    public function onBreak(BlockBreakEvent $event)
    {
        if ($this->getPlugin()->configuration['Scenarios']['Cateyes'] == true) {
            $player = $event->getPlayer();
            if ($event->getBlock()->getId() == 17) {
                $player->addEffect(Effect::getEffect(Effect::NIGHT_VISION)->setDuration(1000000)->setVisible(false));
            }
        }
    }
}