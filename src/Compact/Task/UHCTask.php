<?php

namespace Compact\Task;

use Compact\Loader;
use Compact\Arena\Kits;
use Compact\BossBar\BossEventPacket;
use Compact\Database\PlayerStats;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\item\Item;
use pocketmine\entity\Entity;
use pocketmine\utils\UUID;

class UHCTask extends PluginTask
{

    private $plugin;

    private $whitelist;
    private $starting;
    private $time;
    private $pvp;
    private $tpall;

    public function __construct(Loader $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $arena = $this->getPlugin()->getArena();
        $arena->setGraceTime(15 * 60);
        $arena->setTpallTime(25 * 60);
        $this->whitelist = $arena->getWhitelistTime();
        $this->starting = $arena->getStartTime();
        $this->time = $arena->getGameTime();
        $this->pvp = $arena->getGraceTime();
        $this->tpall = $arena->getTpallTime();
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getServer()
    {
        return $this->getPlugin()->getServer();
    }

    public function getPlayersCount()
    {
        $players = [];
        $arena = $this->getPlugin()->getArena();
        foreach ($arena->getPlayers() as $player) {
            if (!$player->isHost()) {
            	if($player->isOnline()){
        	        $players[] = $player;
        		}
            }
        }
        return $players;
    }
    
    public function getSpectatorsCount(){
    	$spectators = [];
    	$arena = $this->getPlugin()->getArena();
    	foreach($arena->getSpectators() as $spectator){
    		if(!$spectator->isHost()){
    			if($spectator->isOnline()){
    				$spectators[] = $spectator;
    			}
    		}
    	}
    	return $spectators;
    }

    public function addBossBar($player)
    {
        $title = TextFormat::DARK_GRAY . "           " . TextFormat::BOLD . "[" . TextFormat::RESET . "" . TextFormat::BLUE . " Compact" . TextFormat::GOLD . "UHC " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "]" . TextFormat::RESET . "\n\n" . TextFormat::GRAY . "Grace period end in: " . TextFormat::GOLD . gmdate("i:s", $this->pvp);
        $pk = new AddPlayerPacket();
        $pk->uuid = UUID::fromRandom();
        $pk->x = $player->getX();
        $pk->y = $player->getY() - 5;
        $pk->z = $player->getZ();
        $pk->eid = 11000;
        $pk->speedX = 0;
        $pk->speedY = 0;
        $pk->speedZ = 0;
        $pk->yaw = 0;
        $pk->pitch = 0;
        $pk->item = Item::get(Item::AIR);
        $flags = 1 << Entity::DATA_FLAG_INVISIBLE;
        $flags |= 0 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
        $flags |= 0 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
        $flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
        $pk->metadata = [Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
            Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title],
            Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1]];
        $player->dataPacket($pk);
        $pk = new BossEventPacket();
        $pk->eid = 11000;
        $player->dataPacket($pk);
    }

    public function getHud($player)
    {
        $player->getInstance()->sendPopup("         " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "[" . TextFormat::RESET . "" . TextFormat::BLUE . " Compact" . TextFormat::GOLD . "UHC " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "]" . TextFormat::RESET . " " . TextFormat::YELLOW . "" . gmdate("i:s", $this->time) . "\n" . TextFormat::GRAY . "Kills: " . TextFormat::GOLD . "" . $player->getKills() . " " . TextFormat::GRAY . "Players Alive: " . TextFormat::GOLD . "" . count($this->getPlayersCount()) . " " . TextFormat::GRAY . "Spect: " . TextFormat::GOLD . "" . count($this->getSpectatorsCount()) . "\n         " . TextFormat::GRAY . "X: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorX() . " " . TextFormat::GRAY . "Y: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorY() . " " . TextFormat::GRAY . "Z: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorZ());
    }

    public function getHudHost($player)
    {
        $player->getInstance()->sendPopup("         " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "[" . TextFormat::RESET . "" . TextFormat::BLUE . " Compact" . TextFormat::GOLD . "UHC " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "]" . TextFormat::RESET . " " . TextFormat::YELLOW . "" . gmdate("i:s", $this->time) . "\n" . TextFormat::GRAY . "Type: " . TextFormat::GOLD . "HOST" . " " . TextFormat::GRAY . "Players Alive: " . TextFormat::GOLD . "" . count($this->getPlayersCount()) . " " . TextFormat::GRAY . "Spect: " . TextFormat::GOLD . "" . count($this->getSpectatorsCount()) . "\n         " . TextFormat::GRAY . "X: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorX() . " " . TextFormat::GRAY . "Y: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorY() . " " . TextFormat::GRAY . "Z: " . TextFormat::GOLD . "" . $player->getInstance()->getFloorZ());
    }


    public function onRun($tick)
    {
        $arena = $this->getPlugin()->getArena();
        $pvp = $arena->getGraceTime();
        $tpall = $arena->getTpallTime();
        $status = $arena->getStatus();
        if ($status == "whitelist") {
            $this->whitelist--;
            if ($this->whitelist <= 1) {
                $arena->setStatus("starting");
                $this->getServer()->setConfigBool("white-list", true);
                $this->getPlugin()->running = true;
                foreach ($arena->getPlayersEveryone() as $player) {
                    if ($player->isOnline()) {
                        $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "The whitelist is active, you can't leave the UHC!");
                    }
                }
            }
            foreach ($arena->getPlayersEveryone() as $player) {
                $player->getInstance()->sendPopup(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::GRAY . "The whitelist will be activated in " . TextFormat::YELLOW . gmdate("i:s", $this->whitelist));
            }
        }
        if ($status == "starting") {
            $this->starting--;
            if ($this->starting <= 1) {
                $arena->setStatus("grace");
                $this->getPlugin()->configuration['Protection'] = false;
                foreach ($arena->getPlayers() as $player) {
                    if($player->isOnline()) {
                        $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC" . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "Good luck and enjoy the game!");
                        if ($player->isOnline()) {
                            if (!$player->isHost()) {
                                $player->getInstance()->teleport($this->getServer()->getLevelByName($arena->getName())->getSafeSpawn());
                                $kits = new Kits();
                                $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "You were added to the whitelist!");
                                $kits->getKitUHC($player->getInstance());
                                $player->getInstance()->setWhitelisted(true);
                                $stats = new PlayerStats($this->getPlugin());
                                $stats->addUHC($player->getName());
                            } else {
                                $player->getInstance()->setWhitelisted(true);
                                $player->getInstance()->teleport($this->getServer()->getLevelByName($arena->getName())->getSafeSpawn());
                                $player->getInstance()->setGamemode(1);
                            }
                        }
                    }
                }
            }
            foreach ($arena->getPlayersEveryone() as $player) {
                if ($player->isOnline()) {
                    $player->getInstance()->sendPopup(TextFormat::GRAY . "UHC starting at " . TextFormat::GOLD . $this->starting . "\n" . TextFormat::BLUE . str_repeat("|", $this->starting) . TextFormat::GRAY . str_repeat("|", 30 - $this->starting));
                }
            }
        }
        if($status == "grace"){
            $this->time++;
            $this->pvp--;
            $arena->setTimeRunning($this->time);
            if($this->pvp <= 1){
                $arena->setStatus("running");
                $arena->setGraceTime($arena->getGraceTime() + (10 * 60));
                $this->getPlugin()->configuration['PvP'] = true;
                foreach ($arena->getPlayersEveryone() as $player) {
                    if ($player->isOnline()) {
                        $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "Grace period is over!");
                    }
                }
            }
            foreach ($arena->getPlayersEveryone() as $player){
                if($player->isOnline()) {
                    if (!$player->isHost()) {
                        $this->getHud($player);
                        //$this->addBossBar($player->getInstance());
                    } else {
                        $this->getHudHost($player);
                        //$this->addBossBar($player->getInstance());
                    }
                }
            }
        }
        if ($status == "running") {
            $this->time++;
            $arena->setTimeRunning($this->time);
            if($this->time == 5){
            	$this->getServer()->broadcastMessage(TextFormat::GRAY."[".TextFormat::BLUE."!".TextFormat::GRAY."] ".TextFormat::WHITE."El PvP se activa en el minuto ".gmdate("i:s", $arena->getGraceTime()));
            }
            if($this->time == 10 * 60){
            	$this->getServer()->broadcastMessage(TextFormat::GRAY."[".TextFormat::BLUE."!".TextFormat::GRAY."] ".TextFormat::WHITE."El PvP se activa en el minuto ".gmdate("i:s", $arena->getGraceTime()));
            }
            foreach ($arena->getPlayersEveryone() as $player) {
                if($player->isOnline()) {
                    if (!$player->isHost()) {
                        $this->getHud($player);
                    } else {
                        $this->getHudHost($player);
                    }
                }
            }
            if($this->time >= ($pvp - 5) and $this->time <= ($pvp - 1)){
                $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET.TextFormat::YELLOW."PvP enable in ".($pvp - $this->time)."!");
            }

            if ($this->time == $pvp) {
                $this->getPlugin()->configuration['PvP'] = true;
                $this->getServer()->broadcastMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET.TextFormat::YELLOW."PvP enabled!");
            }
            if ($arena->getArenaActive() == false) {
                if ($this->time == ($tpall - 10)) {
                    $this->getPlugin()->configuration['PvP'] = false;
                    foreach ($arena->getPlayersEveryone() as $player) {
                        if ($player->isOnline()) {
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "PvP disabled!");
                        }
                    }
                }
                if ($this->time >= ($tpall - 5) and $this->time <= ($tpall - 1)) {
                    foreach ($arena->getPlayersEveryone() as $player) {
                        if ($player->isOnline()) {
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "PvP disable in ".($tpall - $this->time)."!");
                        }
                    }
                }
                if ($this->time == $tpall) {
                    foreach ($arena->getPlayersEveryone() as $player) {
                        if ($player->isOnline()) {
                            $player->getInstance()->teleport($this->getServer()->getLevelByName($arena->getName())->getSafeSpawn());
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::GRAY . "Teleporting..");
                        }
                    }
                }
                if($this->time >= ($tpall + 25) and $this->time <= ($tpall + 29)) {
                    foreach ($arena->getPlayersEveryone() as $player) {
                        if ($player->isOnline()) {
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "PvP enable in ".(($tpall + 30) - $this->time)."!");
                        }
                    }
                }
                if ($this->time == ($tpall + 30)) {
                    $this->getPlugin()->configuration['PvP'] = true;
                    $arena->setTpallTime($tpall + (10 * 60));
                    foreach ($arena->getPlayersEveryone() as $player) {
                        if ($player->isOnline()) {
                            $player->getInstance()->sendMessage(TextFormat::BOLD . TextFormat::DARK_GRAY . "[ " . TextFormat::RESET . TextFormat::BLUE . "Compact" . TextFormat::GOLD . "UHC " . TextFormat::BOLD . TextFormat::DARK_GRAY . "] " . TextFormat::RESET . TextFormat::YELLOW . "PvP activated!");
                        }
                    }
                }
            }
        }
    }
}