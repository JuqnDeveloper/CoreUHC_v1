<?php

namespace Compact\Arena;

use Compact\Loader;
use pocketmine\utils\TextFormat;

class Arena {

    public $plugin, $data;
    
    public $timeRunning;
    public $arenaName = null;
    public $arenaActive = false;

    public function __construct(Loader $plugin, array $data)
    {
        $this->plugin = $plugin;
        $this->data = $data;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getData(){
        return $this->data;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function getTeams(){
        return $this->getPlugin()->teamsAvailable;
    }

    public function getTeamsAlive(){
        $teams = [];
        foreach ($this->getTeams() as $teamInt){
        	$teamInt = (int) $teamInt;
            $team = $this->getPlugin()->getTeam($teamInt);
            if(count($team->getPlayers()) != 0){
                $teams[] = $team;
            }
        }
        return $teams;
    }

    public function getOtherTeams($team){
        foreach ($this->getTeamsAlive() as $otherTeam){
            if($otherTeam->getTeamInt() == $team){
                continue;
            }
            return $otherTeam;
        }
        return null;
    }

    public function getPlayers(){
        $players = [];
        foreach ($this->getPlugin()->getPlayers() as $player){
            $players[] = $player;
        }
        return $players;
    }

    public function getSpectators(){
        $spectators = [];
        foreach ($this->getPlugin()->getSpectators() as $spectator){
            $spectators[] = $spectator;
        }
        return $spectators;
    }

    public function getPlayersEveryone(){
        $players = [];
        foreach ($this->getPlayers() as $player){
            $players[$player->getName()] = $player;
        }
        foreach ($this->getSpectators() as $player){
            $players[$player->getName()] = $player;
        }
        return $players;
    }

    public function getName(){
        return $this->data['name'];
    }

    public function getGraceTime(){
	    return $this->data['GraceTime'];
	}

    public function getGameTime(){
        return $this->data['GameTime'];
    }

    public function getStartTime(){
        return $this->data['StartTime'];
    }

    public function getWhitelistTime(){
        return $this->data['WhitelistTime'];
    }

    public function getTpallTime(){
        return $this->data['TpallTime'];
    }

    public function getMode(){
        return $this->data['mode'];
    }
    
    public function getTimeRunning(){
    	return $this->timeRunning;
    }
    
    public function setTimeRunning($time){
    	$this->timeRunning = (int) $time;
    }
    
    public function getLevelArena(){
    	return $this->arenaName;
    }
    
    public function setLevelName($arena){
    	$this->arenaName = $arena;
    }
    
    public function getArenaActive(){
    	return $this->arenaActive;
    }
    
    public function setArenaActive($type){
    	$this->arenaActive = $type;
    }
    
    public function setName($name){
        $this->data['name'] = $name;
    }

    public function setGraceTime($time){
        $this->data['GraceTime'] = (int) $time;
    }

    public function setGameTime($time){
        $this->data['GameTime'] = (int) $time;
    }

    public function setStartTime($time){
        $this->data['StartTime'] = (int) $time;
    }

    public function setWhitelistTime($time){
        $this->data['WhitelistTime'] = (int) $time;
    }

    public function setTpallTime($time){
        $this->data['TpallTime'] = (int) $time;
    }

    public function setMode($value){
        $this->data['mode'] = $value;
    }
    
    public function getStatus(){
    	return $this->data['status'];
    }
    
    public function setStatus($status){
    	$this->data['status'] = $status;
    }
    
    public function setTeam(){
		foreach($this->getPlayersEveryone() as $player){
			if(!$player->isHost()){
				if($player->getTeamInt() == 0){
					$teamInt = array_rand($this->getPlugin()->teamsAvailable);
					if($teamInt !== 0){
						$team = $this->getPlugin()->getTeam($teamInt);
						if(!$team->isFull()){
							$player->data["team"] = $team->getTeamInt();
							$player->getInstance()->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Team".TextFormat::GRAY."] ".TextFormat::GRAY."You have joined team ".TextFormat::YELLOW.$team->getTeamInt());
						}
					}
				}
			}
		}
	}
}