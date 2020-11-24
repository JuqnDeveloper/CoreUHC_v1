<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ScenarioCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("scenario", "Use for add/rem scenario UHC");
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
		if(!$sender->isOp()) return;
		if(isset($args[0])){
			switch($args[0]){
				case "add":
					if(isset($args[1])){
						if(isset($this->getPlugin()->configuration['Scenarios'][$args[1]])){
							if($scenario = $this->getPlugin()->getScenario($args[1]) == false){
								$this->getPlugin()->configuration['Scenarios'][$args[1]] = true;
								$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Scenarios".TextFormat::GRAY."] Scenario ".TextFormat::YELLOW.$args[1].TextFormat::GRAY." is activated!");
							} else {
								$sender->sendMessage(TextFormat::RED."The scenario has already been activated!");
							}
						} else {
							$sender->sendMessage(TextFormat::RED."The scenario does not exist!");
						}
					} else {
						$sender->sendMessage(TextFormat::RED."Use /scenario remove <scenario>");
					}
					break;
				case "remove":
				case "rem":
					if(isset($args[1])){
						if(in_array($args[1], $this->getPlugin()->getScenarios())){
							if($scenario = $this->getPlugin()->getScenario($args[1]) == true){
								$scenario = false;
								$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Scenarios".TextFormat::GRAY."] Scenario ".TextFormat::YELLOW.$args[1].TextFormat::GRAY." is deactivated!");
							} else {
								$sender->sendMessage(TextFormat::RED."The scenario has already been deactivated!");
							}
						} else {
							$sender->sendMessage(TextFormat::RED."The scenario does not exist!");
						}
					} else {
						$sender->sendMessage(TextFormat::RED."Use /scenario remove <scenario>");
					}
					break;
				case "list":
					$message = TextFormat::GOLD."Scenario List\n";
					foreach($this->getPlugin()->getScenarios() as $scenario => $value){
						$message .= TextFormat::GOLD."- ".TextFormat::GRAY.$scenario."\n";
					}
					$sender->sendMessage($message);
					break;
			}
		}
	}
}