<?php

namespace Compact\Database;

use Compact\Loader;

class PlayerManager {

    public $plugin, $data;

    public function __construct(Loader $plugin, array  $data)
    {
        $this->plugin = $plugin;
        $this->data = $data;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function getName(){
        return $this->data['name'];
    }

    public function getTeamInt(){
        return $this->data['team'];
    }
    
    public function getTeam(){
    	return $this->getPlugin()->getTeam($this->getTeamInt());
    }

    public function getInstance(){
        return $this->getServer()->getPlayer($this->getName());
    }

    public function isOnline(){
        return $this->getInstance() instanceof \pocketmine\Player;
    }

    public function isHost(){
        return $this->data['host'];
    }
    
    public function setHost(){
    	$this->data['host'] = true;
    }

    public function getKills(){
        return $this->data['kills'];
    }
    
    public function inTeamDamager($teamInt){
    	$value = false;
    	if($teamInt == $this->getTeamInt()){
    		$value = true;
    	}
    	return $value;
    }

    public function getTeamKills(){
        $kills = 0;
        $team = $this->getPlugin()->getTeam($this->getTeamInt());
        foreach ($team->getPlayerEveryone() as $player){
           $kills = $kills + $player->getKills();
        }
        return $kills;
    }

    public function addKill(){
        $this->data['kills'] = $this->getKills() + 1;
    }

    public function setSpectator(){
    	$data = [
    		'name' => $this->getName(),
    		'team' => $this->getTeamInt(),
    		'host' => $this->isHost(),
    		'kills' => $this->getKills()
    	];
    	$this->getPlugin()->addSpectator($this->getName(), $data);
	}
}