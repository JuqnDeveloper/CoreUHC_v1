<?php

namespace Compact\Commands;

use Compact\AutoTweet\APITwitter;
use Compact\Loader;
use Compact\Database\PlayerStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WinnerCommand extends Command
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("winner", "Use for winner player");
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
    
    public function getPlayers(){
    	//$arena = $this->getPlugin()->getArena();
    	$players = [];
    	foreach($this->getPlugin()->getPlayers() as $player){
    		if(!$player->isHost()){
    			if($player->isOnline()){
    				$players[] = $player;
    			}
    		}
    	}
    	return $players;
    }

    /**public function getPlayers()
    {
        $arena = $this->getPlugin()->getArena();
        $players = [];
        foreach ($arena->getPlayers() as $player) {
            if (!$player->isHost()) {
                if ($player->isOnline()) {
                    $players[] = $player;
                }
            }
        }
        return $player;
    }**/

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
    	$arena = $this->getPlugin()->getArena();
        if (!$sender->isOp()) return;
        if ($this->getPlugin()->running == true) {
            if (count($this->getPlayers()) == 1) {
                $players = null;
                foreach ($this->getPlayers() as $player) {
                    $stats = new PlayerStats($this->getPlugin());
                    $stats->addWin($player->getName());
                    $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Winner, Winner Chicken Dinner!");
                    $this->getPlugin()->winnerTask($player->getInstance());
                    $players = $player;
                }
                $this->getServer()->broadcastMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Congratulate " . TextFormat::BLUE . $players->getName(). TextFormat::GOLD . " for winning the UHC / MEETUP");
                $twitter = new APITwitter($this->getPlugin());
                $twitter->publicWinner($players->getName(), $players->getKills());
            }
        }
    }
}