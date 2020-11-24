<?php

namespace Compact\Commands;

use Compact\AutoTweet\APITwitter;
use Compact\Loader;
use Compact\Database\PlayerStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WinnerTeamCommand extends Command
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("winnerteam", "Use for winner player");
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

    public function getTeamsAlive()
    {
        $teamInt = [];
        $arena = $this->getPlugin()->getArena();
        foreach ($arena->getTeams() as $team) {
            $team = $this->getPlugin()->getTeam($team);
            if (count($team->getPlayers()) != 0) {
                $teamInt[$team->getTeamInt()] = $team->getTeamInt();
            }
        }
        return $teamInt;
    }

    public function getPlayersTeam(Array $array)
    {
        $text = '';
        foreach ($array as $player) {
            $text .= "    " . $player->getName() . " " . $player->getKills() . "K\n";
        }
        return $text;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
    	$arena = $this->getPlugin()->getArena();
        if (!$sender->isOp()) return;
        if ($this->getPlugin()->running == true) {
            if (isset($args[0])) {
                if (in_array($args[0], $this->getTeamsAlive())) {
                    $team = $this->getPlugin()->getTeam($args[0]);
                    $player = [];
                    $array = [];
                    foreach ($team->getPlayers() as $players) {
                        $stats = new PlayerStats($this->getPlugin());
                        $stats->addWinTeam($players->getName());
                        $players->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Winner, Winner Chicken Dinner!");
                        $this->getPlugin()->winnerTask($players->getInstance());
                        $player[] = $players->getName();
                        $array[] = $players;
                    }
                    $twitter = new APITwitter($this->getPlugin());
                    $twitter->publicWinnerTeam($this->getPlayersTeam($array));
                    $this->getServer()->broadcastMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Congratulations to team " . TextFormat::BLUE . $args[0] . " (" . implode(", ", $player) . ")  " . TextFormat::GOLD . "for winning the UHC / MEETUP");
                }
            }
        }
    }
}