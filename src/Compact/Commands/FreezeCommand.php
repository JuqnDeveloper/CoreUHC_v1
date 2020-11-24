<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FreezeCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("freeze", "Use for freezing player");
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
			$player = $this->getServer()->getPlayer($args[0]);
			if($player instanceof \pocketmine\Player){
				if(!in_array($player->getName(), $this->getPlugin()->freeze)){
					$this->getPlugin()->freeze[$player->getName()] = $player->getName();
					$player->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Freeze".TextFormat::GRAY."] ".TextFormat::GRAY."You were frozen by ".TextFormat::BLUE.$sender->getName());
					$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Freeze".TextFormat::GRAY."] ".TextFormat::GRAY."You froze ".TextFormat::BLUE.$player->getName());
					return true;
				} else {
					unset($this->getPlugin()->freeze[$player->getName()]);
					$player->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Freeze".TextFormat::GRAY."] ".TextFormat::GRAY."You were thawed by ".TextFormat::BLUE.$sender->getName());
					$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Freeze".TextFormat::GRAY."] ".TextFormat::GRAY."You have thawed ".TextFormat::BLUE.$player->getName());
					return true;
				}
			} else {
				$sender->sendMessage(TextFormat::RED."Player not found");
			}
		} else {
			$sender->sendMessage(TextFormat::RED."Use /freeze <name>");
		}
	}
}