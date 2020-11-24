<?php

namespace Compact\Scenarios;

use Compact\Loader;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;

class Cutclean implements Listener {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function getPlugin()
    {
        return $this->plugin;
    }
    
    public function onBreak(BlockBreakEvent $e)
    {
        if ($this->getPlugin()->configuration['Scenarios']['Cutclean'] == true) {
            if ($e->getBlock()->getId() == 15) {
                $e->setDrops([Item::get(265, 0, 1)]);
            } else
                if ($e->getBlock()->getId() == 14) {
                    $e->setDrops([Item::get(266, 0, 1)]);
                } else
                    if ($e->getBlock()->getId() == 56) {
                        $e->setDrops([Item::get(264, 0, 1)]);
                    } else
                        if ($e->getBlock()->getId() == 18) {
                            $e->setDrops([Item::get(260, 0, 1)]);
                        } else
                            if ($e->getBlock()->getId() == 16) {
                                $e->setDrops([Item::get(50, 0, 3)]);
                            }
        }
    }
}