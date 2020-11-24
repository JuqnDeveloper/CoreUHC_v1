<?php

namespace Compact\Task;

use Compact\Loader;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class HealthBarTask extends PluginTask {

    private $plugin;

    public function __construct(Loader $owner)
    {
        parent::__construct($owner);
        $this->plugin = $owner;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }
    
    public function getDBRanks(){
    	return $this->getPlugin()->getDBRanks();
    }

    public function onRun($currentTick)
    {
        $arena = $this->getPlugin()->getArena();
        if(count($this->getPlugin()->getPlayers()) == 0) return;
        foreach ($this->getPlugin()->getPlayers() as $player) {
            if (!$player->isOnline()) return;
            $name = $player->getName();
            $sql = $this->getDBRanks()->query("SELECT * FROM ranks WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
            $rank = $sql['rank'];
            $format = null;
            if ($this->getPlugin()->running == false) {
                $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
            }
            if($this->getPlugin()->running == true){
                if ($arena->getStatus() == "whitelist" || $arena->getStatus() == "starting") {
                    $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                }
                if ($arena->getMode() == "MEEFFA") {
                    $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                }
                if ($arena->getMode() == "UHCFFA") {
                    $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                }
                if ($arena->getMode() == "SIMFFA") {
                    $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                }
                if($arena->getMode() == "MEETO2" || $arena->getMode() == "MEETO3" || $arena->getMode() == "MEETO4" || $arena->getMode() == "MEETO5" || $arena->getMode() == "UHCTO2" || $arena->getMode() == "UHCTO3" || $arena->getMode() == "UHCTO4" || $arena->getMode() == "UHCTO5" || $arena->getMode() == "SIMTO2" || $arena->getMode() == "SIMTO3" || $arena->getMode() == "SIMTO4" || $arena->getMode() == "SIMTO5"){
                    if ($arena->getStatus() == "grace" || $arena->getStatus() == "running"){
                    	if(!$player->isHost()){
                      	  $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . " [".TextFormat::BLUE.$player->getTeamInt() . TextFormat::GRAY. "]\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                   	 } else {
                   		 $player->getInstance()->setNameTag($this->getPlugin()->configuration['Ranks'][$rank] . TextFormat::GRAY . " " . $player->getName() . TextFormat::GRAY . "\n" . TextFormat::GOLD . $player->getInstance()->getHealth() . " HP" . TextFormat::GRAY . "  \n \n \n \n \n" . TextFormat::GOLD . "@CompactUHC\n\n\n" . TextFormat::GRAY . "K: " . $player->getKills());
                    	}
                    }
                }
            }
        }
    }
}