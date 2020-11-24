<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ScenariosCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("scenarios", "Use for view list scenarios on's");
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
		$list = TextFormat::GOLD."UHC SCENARIOS ACTIVATED!\n";
		foreach($this->getPlugin()->getScenarios() as $scenario => $active){
			if($this->getPlugin()->getScenario($scenario) == true){
				$list .= TextFormat::GOLD."- ".TextFormat::GRAY.$scenario."\n";
			}
		}
		$sender->sendMessage($list);
	}
}