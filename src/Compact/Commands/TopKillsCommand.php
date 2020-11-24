<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TopKillsCommand extends Command {
	
	private $plugin;
	private $kills = [];
	
	public function __construct(Loader $plugin){
		parent::__construct("topkills", "Use view topkills");
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}

	public function getKills(){
	    foreach ($this->getPlugin()->getArena()->getPlayersEveryone() as $player){
	        $this->kills[$player->getName()] = $player->getKills();
        }
	    return $this->kills;
    }
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
	    $kills = $this->getKills();
	    arsort($kills);
	    $sender->sendMessage(TextFormat::GRAY."TOP ".TextFormat::GOLD."10 ".TextFormat::GRAY."KILLS");
	    for ($i = 0; $i < 10; $i++){
	        $kill = array_values($kills);
	        $player = array_keys($kills);
	        $pos = $i + 1;
	        $sender->sendMessage(TextFormat::GRAY.$pos." ".TextFormat::GOLD.$player[$i]." ".TextFormat::GRAY."- ".TextFormat::BLUE.$kill[$i]);
        }
		/**foreach($this->getPlugin()->getArena()->getPlayersEveryone() as $player){
			$array = [];
			$array[$player->getName()] = $player;
			if(empty($array)){
				return true;
			}
			$top = [];
			arsort($array);
			$sender->sendMessage(TextFormat::GRAY."TOP ".TextFormat::GOLD."10 ".TextFormat::GRAY."KILLS");
			for($i = 0; $i < (count($array) >= 10 ? 10 : count($array)); $i++){
				$pos = $i+1;
				$player = array_keys($array);
				$kills = array_values($array);
				$sender->sendMessage(TextFormat::GRAY.$pos." ".TextFormat::GOLD.$player[$i]." ".TextFormat::GRAY."- ".TextFormat::BLUE.$kills[$i]);
			}
		}**/
	}
}