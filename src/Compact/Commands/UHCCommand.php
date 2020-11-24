<?php

namespace Compact\Commands;

use Compact\Loader;
use Compact\AutoTweet\APITwitter as AutoTweet;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\level\Level;

class UHCCommand extends Command
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        parent::__construct("uhc", "Use for commands UHC");
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

    public function getHosts()
    {
        $host = [];
        foreach ($this->getPlugin()->getArena()->getPlayersEveryone() as $player) {
            if ($player->isHost()) {
                $host[] = $player;
            }
        }
        return $host;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        $arena = $this->getPlugin()->getArena();
        if (isset($args[0])) {
            switch ($args[0]) {
                case "host":
                case "h":
                    if (isset($args[1])) {
                        $list = implode(" ", $args);
                        $worte = explode(" ", $list);
                        unset($worte[0]);
                        foreach ($worte as $name) {
                            $player = $this->getPlugin()->getPlayer($name);
                            if ($player->isOnline()) {
                                $player->setHost();
                                $player->getInstance()->sendMessage(TextFormat::GREEN . "You are a new host for this UHC");
                            }
                        }
                    } else {
                        $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Use /uhc host <hosters>");
                    }
                    break;
                case "pvp":
                case "p":
                    if ($this->getPlugin()->configuration['PvP'] == false) {
                        $this->getPlugin()->configuration['PvP'] = true;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The PvP is activated!");
                        }
                        return true;
                    } else {
                        $this->getPlugin()->configuration['PvP'] = false;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The PvP is disabled!");
                        }
                        return true;
                    }
                    break;
                case "protection":
                case "pro":
                    if ($this->getPlugin()->configuration['Protection'] == true) {
                        $this->getPlugin()->configuration['Protection'] = false;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The protection was deactivated");
                        }
                        return true;
                    } else {
                        $this->getPlugin()->configuration['Protection'] = true;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The protection was activated!");
                        }
                        return true;
                    }
                    break;

                case "globalmute":
                case "gmute":
                    if($this->getPlugin()->configuration['GlobalMute'] == false){
                        $this->getPlugin()->configuration['GlobalMute'] = true;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The GlobalMute was activated!");
                        }
                        return true;
                    } else {
                        $this->getPlugin()->configuration['GlobalMute'] = false;
                        foreach ($arena->getPlayersEveryone() as $player) {
                        	if(!$player->isOnline()) return;
                            $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "The GlobalMute was deactivated!");
                        }
                        return true;
                    }
                    break;
                case "tpall":
                case "t":
                    foreach ($arena->getPlayersEveryone() as $player) {
                    	if($player->isOnline()){
                    	    $player->getInstance()->teleport($sender);
               	         $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Teleport...");
                		}
                    }
                    break;
                case "health":
                case "ht":
                    foreach ($arena->getPlayersEveryone() as $player) {
                    	if(!$player->isOnline()) return;
                        $player->getInstance()->setFood(20);
                        $player->getInstance()->setHealth(20);
                    }
                    break;
                case "clearall":
                case "c":
                    foreach ($arena->getPlayersEveryone() as $player) {
                    	if(!$player->isOnline()) return;
                        $player->getInstance()->getInventory()->clearAll();
                        $player->getInstance()->setFood(20);
                        $player->getInstance()->setHealth(20);
                        $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Your inventory was cleaned!");
                    }
                    break;
                case "arena":
                    if ($arena->getLevelArena() !== null) {
                        if ($this->getPlugin()->running == true) {
                            if ($arena->getStatus() == "running") {
                                if ($arena->getArenaActive() == false) {
                                    $arena->setArenaActive(true);
                                    $arena->setGraceTime($arena->getTimeRunning() + 20);
                                    $this->getPlugin()->configuration['PvP'] = false;
                                    foreach ($arena->getPlayersEveryone() as $player) {
                                    	if(!$player->isOnline()) return;
                                        $player->getInstance()->teleport($this->getServer()->getLevelByName($arena->getLevelArena())->getSafeSpawn());
                                        $player->getInstance()->sendMessage("" . TextFormat::DARK_GRAY . "[ " . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::DARK_GRAY . " ] " . TextFormat::GOLD . "Teleported to the final Arena! PvP on in 20 seconds");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "You have already started the end of the event!");
                                }
                            } else {
                                $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "The event has to start first!");
                            }
                        } else {
                            $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "You cannot use this command if an event does not start!");
                        }
                    } else {
                        $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Define the world of the final arena!");
                    }
                    break;
                case "configuration":
                case "conf":
                    if (isset($args[1])) {
                        switch ($args[1]) {
                            case "pvp":
                                if (isset($args[2])) {
                                    if (is_numeric($args[2])) {
                                        $arena->setGraceTime($args[2] * 60);
                                        $sender->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Configuration" . TextFormat::GRAY . "] " . TextFormat::WHITE . "Configuration done!");
                                    } else {
                                        $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Invalid number!");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Use /uhc configuration pvp <minutes>");
                                }
                                break;
                            case "tpall":
                                if (isset($args[2])) {
                                    if (is_numeric($args[2])) {
                                        $arena->setGraceTime($args[2] * 60);
                                        $sender->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Configuration" . TextFormat::GRAY . "] " . TextFormat::WHITE . "Configuration done!");
                                    } else {
                                        $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Invalid number!");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Use /uhc configuration tpall <minutes>");
                                }
                                break;
                            case "level":
                                if (isset($args[2])) {
                                    $world = $this->getServer()->getLevelByName($args[2]);
                                    if ($world instanceof Level) {
                                        $this->getServer()->loadLevel($args[2]);
                                        $arena->setName($args[2]);
                                        $sender->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Configuration" . TextFormat::GRAY . "] " . TextFormat::WHITE . "Configuration done!");
                                    } else {
                                        $sender->sendMessage(TextFormat::RED . "The world does not exist!");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::RED . "Place a world!");
                                }
                                break;
                            case "whitelist":
                                if ($this->getPlugin()->running == false) {
                                    if (isset($args[2])) {
                                        if (is_numeric($args[2])) {
                                            $arena->setWhitelistTime($args[2] * 60);
                                            $sender->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Configuration" . TextFormat::GRAY . "] " . TextFormat::WHITE . "Configuration done!");
                                        } else {
                                            $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Invalid number!");
                                        }
                                    } else {
                                        $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "Use /uhc configuration pvp <minutes>");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::RED . "You cannot use this command when the event started!");
                                }
                                break;
                            case "arena":
                                if (isset($args[2])) {
                                    $world = $this->getServer()->getLevelByName($args[2]);
                                    if ($world instanceof Level) {
                                        $this->getServer()->loadLevel($args[2]);
                                        $arena->setLevelName($args[2]);
                                        $sender->sendMessage(TextFormat::GRAY . "[" . TextFormat::BLUE . "Configuration" . TextFormat::GRAY . "] " . TextFormat::WHITE . "Configuration done!");
                                    } else {
                                        $sender->sendMessage(TextFormat::RED . "The world does not exist!");
                                    }
                                } else {
                                    $sender->sendMessage(TextFormat::RED . "Place a world!");
                                }
                                break;
                        }
                    }
                    break;
                case "start":
                case "s":
                    if (isset($args[1])) {
                        switch ($args[1]) {
                            case "meetup":
                                if (isset($args[2])) {
                                    switch ($args[2]) {
                                        case "ffa":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startMeetup();
                                                        $arena->setMode("MEEFFA");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("MEETUP", "FFA", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to2":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startMeetupTeam();
                                                        $this->getPlugin()->createTeams(2);
                                                        $arena->setMode("MEETO2");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("MEETUP", "TO2", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to3":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startMeetupTeam();
                                                        $this->getPlugin()->createTeams(3);
                                                        $arena->setMode("MEETO3");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("MEETUP", "TO3", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to4":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startMeetupTeam();
                                                        $this->getPlugin()->createTeams(4);
                                                        $arena->setMode("MEETO4");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("MEETUP", "TO4", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to5":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startMeetupTeam();
                                                        $this->getPlugin()->createTeams(5);
                                                        $arena->setMode("MEETO5");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("MEETUP", "TO5", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                    }
                                }
                                break;
                            case "uhc":
                                if (isset($args[2])) {
                                    switch ($args[2]) {
                                        case "ffa":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startUHC();
                                                        $arena->setMode("UHCFFA");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("UHC", "FFA", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to2":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startUHCTeam();
                                                        $arena->setMode("UHCTO2");
                                                        $this->getPlugin()->createTeams(2);
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("UHC", "TO2", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to3":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startUHCTeam();
                                                        $arena->setMode("UHCTO3");
                                                        $this->getPlugin()->createTeams(3);
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("UHC", "TO3", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to4":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startUHCTeam();
                                                        $arena->setMode("UHCTO4");
                                                        $this->getPlugin()->createTeams(4);
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("UHC", "TO4", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to5":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startUHCTeam();
                                                        $arena->setMode("UHCTO5");
                                                        $this->getPlugin()->createTeams(5);
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("UHC", "TO5", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                    }
                                }
                                break;
                            case "simulator":
                                if (isset($args[2])) {
                                    switch ($args[2]) {
                                        case "ffa":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startSimulator();
                                                        $arena->setMode("SIMFFA");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("SIMULATOR", "FFA", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to2":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startSimulatorTeam();
                                                        $this->getPlugin()->createTeams(2);
                                                        $arena->setMode("SIMTO2");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("SIMULATOR", "TO2", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to3":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startSimulatorTeam();
                                                        $this->getPlugin()->createTeams(3);
                                                        $arena->setMode("SIMTO3");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("SIMULATOR", "TO3", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to4":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startSimulatorTeam();
                                                        $this->getPlugin()->createTeams(4);
                                                        $arena->setMode("SIMTO4");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("SIMULATOR", "TO4", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                        case "to5":
                                            if ($this->getPlugin()->running == false) {
                                                if ($arena->getName() !== null) {
                                                    if (count($this->getHosts()) >= 1) {
                                                        $this->getPlugin()->startSimulatorTeam();
                                                        $this->getPlugin()->createTeams(5);
                                                        $arena->setMode("SIMTO5");
                                                        $tweet = new AutoTweet($this->getPlugin());
                                                        $whitelist = gmdate("i:s", $arena->getWhitelistTime());
                                                        $tweet->publicTweetStartUHC("SIMULATOR", "TO5", $whitelist);
                                                        $arena->setStatus("whitelist");
                                                        $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "> " . TextFormat::RESET . TextFormat::GOLD."Starting the event..");
                                                    } else {
                                                        $sender->sendMessage(TextFormat::RED . "You have not assigned to the hosts!");
                                                    }
                                                } else {
                                                    $sender->sendMessage(TextFormat::RED . "You have not assigned the level!");
                                                }
                                            } else {
                                                $sender->sendMessage(TextFormat::RED . "Another event is already started!");
                                            }
                                            break;
                                    }
                                }
                                break;
                        }
                    }
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Use /uhc <command>");
        }
        return true;
    }
}