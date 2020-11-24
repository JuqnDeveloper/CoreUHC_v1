<?php

namespace Compact\Database;

use Compact\Loader;

class TeamManager {

    public $plugin, $data;

    public function __construct(Loader $plugin, array $data)
    {
        $this->plugin = $plugin;
        $this->data = $data;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function getTeamInt(){
        return $this->data['team'];
    }

    public function getMaxSlots(){
        return $this->data['maxslots'];
    }

    public function getPlayers(){
        $players = [];
        foreach ($this->getPlugin()->getArena()->getPlayers() as $player){
            if($player->getTeamInt() == $this->getTeamInt()){
                $players[$player->getName()] = $player;
            }
        }
        return $players;
    }

    public function getPlayerEveryone(){
        $players = [];
        foreach ($this->getPlugin()->getArena()->getPlayers() as $player){
            if($player->getTeamInt() == $this->getTeamInt()){
                $players[$player->getName()] = $player;
            }
        }
        foreach ($this->getPlugin()->getArena()->getSpectators() as $player){
            if($player->getTeamInt() == $this->getTeamInt()){
                $players[$player->getName()] = $player;
            }
        }
        return $players;
    }

    public function isFull(){
        return count($this->getPlayers()) >= $this->getMaxSlots();
    }

    public function inTeam($player){
        $inteam = false;
        foreach ($this->getPlayers() as $teamPlayer){
            if($player == $teamPlayer->getName()){
                $inteam = true;
            }
        }
        return $inteam;
    }
}