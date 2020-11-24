<?php

namespace Compact\Scenarios;

use Compact\Loader;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class Xray implements Listener {

    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        $id = $event->getBlock()->getId();
        if($this->getPlugin()->configuration['Scenarios']['Xray'] == true){
            if($id == 14){
                foreach ($this->getPlugin()->getArena()->getPlayersEveryone() as $players){
                    if($players->isHost()){
                        $players->getInstance()->sendMessage(TextFormat::GREEN." ".$player->getName().TextFormat::GRAY." +1 Gold");
                    }
                }
            }

            if($id == 56){
                foreach ($this->getPlugin()->getArena()->getPlayersEveryone() as $players){
                    if($players->isHost()){
                        $players->getInstance()->sendMessage(TextFormat::GREEN." ".$player->getName().TextFormat::GRAY." +1 Diamond");
                    }
                }
            }
        }
    }
}