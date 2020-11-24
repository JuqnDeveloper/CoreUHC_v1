<?php

namespace Compact\AutoTweet;

use Compact\Loader;
use pocketmine\utils\Config;

class APITwitter {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function getScenarios(){
		$scenarios = array();
		foreach($this->plugin->configuration['Scenarios'] as $scenario => $active){
			if($this->plugin->configuration['Scenarios'][$scenario] == true){
				array_push($scenarios, $scenario);
			}
		}
		$text = implode(", ", $scenarios);
		return $text;
	}

	public function getHosts(){
	    $hosters = array();
	    foreach ($this->plugin->getArena()->getPlayers() as $players){
	        if($players->isHost()){
	            array_push($hosters, $players->getName());
            }
        }
	    $text = implode(" / ", $hosters);
	    return $text;
    }
	
	public function publicTweetStartUHC($type, $typeMode, $whitelist){
        $config = new Config($this->plugin->getDataFolder()."Config.yml", Config::YAML);
        $config->set("HostInt", $config->get("HostInt") + 1);
        $config->save();
		$this->plugin->publicTweetStartUHC($type, $typeMode, $whitelist, $this->getScenarios(), $this->getHosts());
	}

	public function publicWinner($player, $kills){
	    $this->plugin->publicWinner($player, $kills);
    }

	public function publicWinnerTeam($players){
	    $this->plugin->publicWinnerTeam($players);
    }
}