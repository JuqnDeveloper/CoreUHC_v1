<?php

namespace Compact\Commands;

use Compact\Loader;
use Compact\Database\PlayerStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StatsCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("stats", "Use for view stats");
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
		$stats = new PlayerStats($this->getPlugin());
		$sender->sendMessage(TextFormat::BLUE."CompactUHCs Stats\n".
	    TextFormat::BLUE."- Player: ".TextFormat::GOLD.$sender->getName()."\n".
	    TextFormat::BLUE."- UHCs: ".TextFormat::GOLD.$stats->getUHC($sender->getName())."\n".
  	  TextFormat::BLUE."- Wins: ".TextFormat::GOLD.$stats->getWin($sender->getName())."\n".
  	  TextFormat::BLUE."- WinsTeam: ".TextFormat::GOLD.$stats->getWinTeam($sender->getName())."\n".
	    TextFormat::BLUE."- Kills: ".TextFormat::GOLD.$stats->getKills($sender->getName())."\n".
	    TextFormat::BLUE."- Diamonds: ".TextFormat::GOLD.$stats->getDiamond($sender->getName())."\n".
 	   TextFormat::BLUE."- Gold: ".TextFormat::GOLD.$stats->getGold($sender->getName())."\n".
 	   TextFormat::BLUE."- Iron: ".TextFormat::GOLD.$stats->getIron($sender->getName()));
	}
}