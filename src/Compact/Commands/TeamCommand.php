<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TeamCommand extends Command
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("tc", "Use for team chat");
        $this->plugin = $plugin;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getServer()
    {
        return $this->getPlugin()->getServer();
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if (isset($args[0])) {
        	if(count($this->getPlugin()->data['teams']) > 0){
        	    if (!isset($this->getPlugin()->data['spectators'][$sender->getName()])) {
    	            if (isset($this->getPlugin()->data['players'][$sender->getName()])) {
    	                $player = $this->getPlugin()->getPlayer($sender->getName());
  	                  if (!$player->isHost()) {
   	                     foreach ($player->getTeam()->getPlayerEveryone() as $players) {
  	                          $players->getInstance()->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Team" . TextFormat::GRAY . "] " . $sender->getName() . " : " . TextFormat::WHITE . implode(" ", $args));
							}
                        }
                    }
                }
            }
        }
    }
}