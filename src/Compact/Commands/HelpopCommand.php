<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HelpopCommand extends Command {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct("helpop", "Use for send message for staff");
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args){
		if(isset($args[0])){
			$message = implode(" ", $args);
			$sender->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Helpop".TextFormat::GRAY."] ".TextFormat::RED."Your message was sent to the staff");
			foreach($this->getPlugin()->getArena()->getPlayersEveryone() as $player){
				if($player->isHost()){
					if(!$player->isOnline()) return;
					$player->getInstance()->sendMessage(TextFormat::GRAY."[".TextFormat::GOLD."Helpop".TextFormat::GRAY."] ".$sender->getName()." ".TextFormat::RED.$message);
				}
			}
		} else {
		    $sender->sendMessage(TextFormat::RED."Use /helpop <message>");
        }
	}
}