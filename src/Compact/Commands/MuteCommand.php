<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MuteCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("mute", "Use for mute player");
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
				if(!in_array($player->getName(), $this->getPlugin()->mute)){
					$this->getPlugin()->mute[$player->getName()] = $player->getName();
					$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Mute".TextFormat::GRAY."] ".TextFormat::YELLOW."You have mutated ".$player->getName());
					$player->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Mute".TextFormat::GRAY."] ".TextFormat::YELLOW."You were mutated");
					return true;
				} else {
					unset($this->getPlugin()->mute[$player->getName()]);
					$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Mute".TextFormat::GRAY."] ".TextFormat::YELLOW."You have commuted ".$player->getName());
					$player->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Mute".TextFormat::GRAY."] ".TextFormat::YELLOW."You were unmuted");
					return true;
				}
			} else {
				$sender->sendMessage(TextFormat::RED."Player not found");
			}
		} else {
			$sender->sendMessage(TextFormat::RED."Use /mute <name>");
		}
		return true;
	}
}