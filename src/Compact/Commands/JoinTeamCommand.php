<?php

namespace Compact\Commands;

use Compact\Loader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class JoinTeamCommand extends Command
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("jointeam", "Use for join team");
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
            if (in_array($args[0], $this->getPlugin()->teamsAvailable)) {
                if (count($this->getPlugin()->data['teams']) > 0) {
                    if ($this->getPlugin()->running == false) {
                        $player = $this->getPlugin()->getPlayer($sender->getName());
                        $team = $this->getPlugin()->getTeam($args[0]);
                        if (!$team->isFull()) {
                            $player->data["team"] = $team->getTeamInt();
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::GRAY . "You entered the team " . TextFormat::YELLOW . $team->getTeamInt());
                        } else {
                            $sender->sendMessage(TextFormat::RED . "The team is complete!");
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "The event has begun!");
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED."The event does not allow teams!");
                }
            } else {
                $sender->sendMessage(TextFormat::RED."This team does not exist!");
            }
        } else {
            $sender->sendMessage(TextFormat::RED."Use /jointeam <teamInt>");
        }
    }
}