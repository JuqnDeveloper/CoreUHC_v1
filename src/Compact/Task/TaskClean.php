<?php

namespace Compact\Task;

use Compact\Loader;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class TaskClean extends PluginTask {

    private $plugin;
    private $player;
    private $time = 20;

    public function __construct(Loader $owner, Player $player)
    {
        parent::__construct($owner);
        $this->plugin = $owner;
        $this->player = $player;
    }

    public function getServer(){
        return $this->plugin->getServer();
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getPlayer(){
        return $this->player;
    }

    public function onRun($currentTick)
    {
        $this->time--;
        if($this->time == 0){
            unset($this->getPlugin()->noclean[$this->getPlayer()->getName()]);
            unset($this->getPlugin()->taskclean[$this->getPlayer()->getName()]);
            $this->getServer()->getScheduler()->cancelTask($this->getTaskId());
            $this->getPlayer()->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."NoClean".TextFormat::GRAY."]".TextFormat::RED." The NoClean time is over");
        }
        if($this->time == 15 || $this->time == 10){
        	$this->getPlayer()->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."NoClean".TextFormat::GRAY."]".TextFormat::RED." NoClean end in ".$this->time." seconds");
        }
        if($this->time >= 1 and $this->time <= 5){
        	$this->getPlayer()->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."NoClean".TextFormat::GRAY."]".TextFormat::RED." NoClean end in ".$this->time." seconds");
        }
    }
}