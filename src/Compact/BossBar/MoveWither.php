<?php

namespace Compact\BossBar;

use pocketmine\scheduler\PluginTask;
use pocketmine\network\protocol\MoveEntityPacket;
use Compact\Loader;

class MoveWither extends PluginTask {
  
    public $plugin;
  
    public function __construct(Loader $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }
  
    public function onRun($tick){
      
        $pk = new MoveEntityPacket();
        $pk->eid = 9272;
        $pk->yaw = 0;
        $pk->headYaw = 0;
        $pk->pitch = 0;
        foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p){
            $pk->x = $p->x;
            $pk->y = $p->y - 28;
            $pk->z = $p->z;
            $p->dataPacket($pk);
        }
    }
}