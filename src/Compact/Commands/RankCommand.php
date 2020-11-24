<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RankCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("rank", "Use for set rank player");
		$this->plugin = $plugin;
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
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
		if(isset($args[0])){
			if(isset($args[1])){
				$player = $this->getServer()->getPlayer($args[0]);
				if($player instanceof \pocketmine\Player){
					if(isset($this->getPlugin()->configuration['Ranks'][$args[1]])){
						$name = $player->getName();
						$rank = $args[1];
						$this->getDBRanks()->query("UPDATE ranks SET rank = '$rank' WHERE player_name = '$name';");
						$player->sendMessage(TextFormat::GRAY."You received the rank ".$this->getPlugin()->configuration['Ranks'][$rank]);
					} else {
						$sender->sendMessage(TextFormat::RED."The rank does not exist!");
					}
				} else {
					$sender->sendMessage(TextFormat::RED."Player does not exist!");
				}
			} else {
				$sender->sendMessage(TextFormat::RED."Use /rank <player> <rank>");
			}
		} else {
			$sender->sendMessage(TextFormat::RED."Use /rank <player> <rank>");
		}
	}
}