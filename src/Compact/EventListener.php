<?php

namespace Compact;

use Compact\Database\PlayerStats;

use pocketmine\entity\Effect;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{

    private $plugin, $db;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
        $this->db = $this->getPlugin()->getDBStats();
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function getServer()
    {
        return $this->getPlugin()->getServer();
    }

    public function getDBRanks()
    {
        return $this->getPlugin()->getDBRanks();
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $arena = $this->getPlugin()->getArena();
        $player = $event->getPlayer();
        $name = $player->getName();
        $sqlite = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
        $sql = $this->getDBRanks()->query("SELECT * FROM ranks WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
        $rank = $sql['rank'];
        $event->setJoinMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "] " . TextFormat::RESET . $this->getPlugin()->configuration['Ranks'][$rank] . " " . TextFormat::GRAY . $name);
        if ($player->getGamemode() == 3) {
            $player->setGamemode(0);
            $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
        }

        if($arena->getMode() == "MEETO2" || $arena->getMode() == "MEETO3" || $arena->getMode() == "MEETO4" || $arena->getMode() == "MEETO5" || $arena->getMode() == "UHCTO2" || $arena->getMode() == "UHCTO3" || $arena->getMode() == "UHCTO4" || $arena->getMode() == "UHCTO5" || $arena->getMode() == "SIMTO2" || $arena->getMode() == "SIMTO3" || $arena->getMode() == "SIMTO4" || $arena->getMode() == "SIMTO5"){
            if($arena->getStatus() == "whitelist"){
                $player->sendMessage(TextFormat::GRAY."[".TextFormat::BLUE."!".TextFormat::GRAY."] ".TextFormat::WHITE."Use the /jointeam <number> command to enter a team before the whitelist time is up!");
            }
        }

        if ($arena->getStatus() == "running" || $arena->getStatus() == "grace") {
            if (isset($this->getPlugin()->data["spectators"][$player->getName()])) {
                $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                $player->sendMessage(TextFormat::RED . "You are a spectator in the UHC!");
                return true;
            }
        }
        
        /**if($arena->getStatus() == "running" || "grace"){
   	     if(!isset($this->getPlugin()->data['spectators'][$player->getName()])){
   			if(!isset($this->getPlugin()->data['players'][$player->getName()])){
   			}
   		}
        }**/

        if ($arena->getStatus() == null || $arena->getStatus() == "whitelist") {
            if (isset($this->getPlugin()->data["spectators"][$player->getName()])) {
                $data = [
                    "name" => $player->getName(),
                    "team" => 0,
                    "teamInt" => 0,
                    "kills" => 0,
                    "host" => false
                ];
                unset($this->getPlugin()->data['spectators'][$player->getName()]);
                $this->getPlugin()->addPlayer($player->getName(), $data);
                return true;
            }
        }

        if ($arena->getStatus() == null || $arena->getStatus() == "whitelist") {
            if (!isset($this->getPlugin()->data["players"][$player->getName()])) {
                $data = [
                    "name" => $player->getName(),
                    "team" => 0,
                    "teamInt" => 0,
                    "kills" => 0,
                    "host" => false
                ];
                $this->getPlugin()->addPlayer($player->getName(), $data);
            }
        }
        if (is_null($sqlite['player_name'])) {
            $this->db->query("INSERT INTO stats (player_name, uhc_win, uhc_team_win, kills, diamonds, gold, iron, uhc_played) VALUES ('$name', '0', '0', '0', '0', '0', '0', '0');");
        }
        return true;
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $sql = $this->getDBRanks()->query("SELECT * FROM ranks WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
        $rank = $sql['rank'];
        $event->setQuitMessage(TextFormat::GRAY . "[" . TextFormat::RED . "-" . TextFormat::GRAY . "] " . TextFormat::RESET . $this->getPlugin()->configuration['Ranks'][$rank] . " " . TextFormat::GRAY . $name);
    }

    public function onMove(PlayerMoveEvent $event)
    {
        //$player = $this->getPlugin()->getPlayer($event->getPlayer()->getName());
        $player = $event->getPlayer();
        if (in_array($player->getName(), $this->getPlugin()->freeze)) {
            $to = clone $event->getFrom();
            $to->yaw = $event->getTo()->yaw;
            $to->pitch = $event->getTo()->pitch;
            $event->setTo($to);
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $this->getPlugin()->getPlayer($event->getPlayer()->getName());
        $position = new Vector3($player->getInstance()->x + 0.5, $player->getInstance()->y + 1, $player->getInstance()->z + 0.5);
        $last = $player->getInstance()->getLastDamageCause();
        if ($last instanceof EntityDamageByEntityEvent) {
            $damager = $this->getPlugin()->getPlayer($last->getDamager()->getName());
            $db = new PlayerStats($this->getPlugin());
            $db->addKills($damager->getName());
            $damager->addKill();
            //$damagerM = $this->getPlugin()->getPlayer($damager->getName());
            $event->setDeathMessage(TextFormat::GRAY . $player->getName() . " [" . $player->getKills() . "] " . TextFormat::RED . "was killed by " . TextFormat::GRAY . $damager->getName() . " [" . $damager->getKills() . "]");
        }
        if (!$player->isHost()) {
            $player->setSpectator();
            $this->getPlugin()->removePlayer($player->getName());
            $player->getInstance()->setGamemode(3);
            $player->getInstance()->setWhitelisted(false);
            $player->getInstance()->setSpawn($position);
        }
        if ($player->isHost()) {
            $event->setCancelled();
        }
    }

    public function onPreLogin(PlayerPreLoginEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();

        if(!$this->getServer()->isWhitelisted($player->getName())){
            $event->setCancelled();
            $event->setKickMessage(TextFormat::RED."Follow @CompactUHCs");
        }

        $sql = $this->getDBRanks()->query("SELECT * FROM ranks WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
        if (is_null($sql['player_name'])) {
            $this->getDBRanks()->exec("INSERT INTO ranks (player_name, rank) VALUES ('$name', 'Player');");
        }
    }
    
    public function onCommandPreProcess(PlayerCommandPreprocessEvent $event){
    	$command = explode(" ", $event->getMessage());
    	if($command[0] == "/me"){
    		$event->setCancelled();
    		$event->getPlayer()->sendMessage(TextFormat::RED."Do not try to use this command!");
    	}
    }

    public function onChat(PlayerChatEvent $e)
    {
        $playerE = $e->getPlayer();
        $player = $this->getPlugin()->getPlayer($e->getPlayer()->getName());
        $spectator = $this->getPlugin()->getSpectator($e->getPlayer()->getName());
        $name = $playerE->getName();
        $sql = $this->getDBRanks()->query("SELECT * FROM ranks WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
        $rank = $sql['rank'];

        //$e->setFormat($this->getPlugin()->configuration['Ranks'][$rank] . " " . TextFormat::GRAY . $playerE->getName() . " " . TextFormat::GRAY . "> " . TextFormat::WHITE . $e->getMessage());

        if (isset($this->getPlugin()->data['players'][$playerE->getName()])) {
            if (!isset($this->getPlugin()->data['spectators'][$playerE->getName()])) {
                $e->setFormat($this->getPlugin()->configuration['Ranks'][$rank] . " " . TextFormat::GRAY . $playerE->getName() . " " . TextFormat::GRAY . "> " . TextFormat::WHITE . $e->getMessage());
            }
        }

        if (!isset($this->getPlugin()->data['players'][$playerE->getName()])) {
            if (isset($this->getPlugin()->data['spectators'][$playerE->getName()])) {
                $e->setFormat("" . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . ">" . TextFormat::RESET . "" . TextFormat::DARK_GREEN . " Spect " . TextFormat::DARK_GRAY . "" . TextFormat::BOLD . "" . TextFormat::RESET . "" . TextFormat::GRAY . $playerE->getName() . " " . TextFormat::GRAY . "> " . TextFormat::WHITE . $e->getMessage());
            }
        }

        if (in_array($playerE->getName(), $this->getPlugin()->mute)) {
            $e->setCancelled();
        }
        if (in_array($e->getPlayer()->getName(), $this->getPlugin()->getSpectators())) {
            if (in_array($spectator->getName(), $this->getPlugin()->mute)) {
                $e->setCancelled();
            }
        }
        /** PLAYER */
        if ($this->getPlugin()->configuration['GlobalMute'] == true) {
        	if(isset($this->getPlugin()->data['players'][$e->getPlayer()->getName()])) {
        	    if (!$player->isHost()){
    	            $e->setCancelled();
 	               $player->getInstance()->sendMessage(TextFormat::GRAY . "[" . TextFormat::GOLD. "GlobalMute" . TextFormat::GRAY . "] " . TextFormat::RED . "You can not speak!");
 		   	}
            }
            if (isset($this->getPlugin()->data['spectators'][$e->getPlayer()->getName()])) {
                if (!$spectator->isHost()) {
                    $e->setCancelled();
                    $spectator->getInstance()->sendMessage(TextFormat::GRAY . "[" . TextFormat::GOLD . "GlobalMute" . TextFormat::GRAY . "] " . TextFormat::RED . "You can not speak!");
                }
            }
        } else {
        	if(!$playerE->isOp()){
   	     	if(!isset($this->getPlugin()->spam[$playerE->getName()])){
   	     		$last = 0;
 	       	} else {
 	       		$last = $this->getPlugin()->spam[$playerE->getName()];
   	     	}
   	     	if(time() - $last > 3){
  	      		$this->getPlugin()->spam[$playerE->getName()] = time();
  	      	} else {
	        		$e->setCancelled();
	        		$playerE->sendMessage(TextFormat::RED."Do not spam!");
    	    	}
     	   }
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $playerE = $event->getPlayer();
        $player = $this->getPlugin()->getPlayer($event->getPlayer()->getName());
        $spectator = $this->getPlugin()->getSpectator($event->getPlayer()->getName());
        if (in_array($player->getName(), $this->getPlugin()->freeze)) {
            $event->setCancelled();
        }
        if ($this->getPlugin()->configuration['Protection'] == true) {
            if (!$player->isHost() and !$playerE->isOp()) {
                $event->setCancelled();
            }
            if (in_array($event->getPlayer()->getName(), $this->getPlugin()->getSpectators())) {
                if (!$spectator->isHost()) {
                    $event->setCancelled();
                }
            }
        }
        if ($event->getBlock()->getId() == 56) {
            $stats = new PlayerStats($this->getPlugin());
            $stats->addDiamond($player->getName());
        }
        if ($event->getBlock()->getId() == 14) {
            $stats = new PlayerStats($this->getPlugin());
            $stats->addGold($player->getName());
        }
        if ($event->getBlock()->getId() == 15) {
            $stats = new PlayerStats($this->getPlugin());
            $stats->addIron($player->getName());
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $playerE = $event->getPlayer();
        $player = $this->getPlugin()->getPlayer($event->getPlayer()->getName());
        $spectator = $this->getPlugin()->getSpectator($event->getPlayer()->getName());
        if (in_array($player->getName(), $this->getPlugin()->freeze)) {
            $event->setCancelled();
        }
        if ($this->getPlugin()->configuration['Protection'] == true) {
            if (!$player->isHost() and !$playerE->isOp()) {
                $event->setCancelled();
            }
            if (in_array($event->getPlayer()->getName(), $this->getPlugin()->getSpectators())) {
                if (!$spectator->isHost()) {
                    $event->setCancelled();
                }
            }
        }
    }

    /**public function onRegain(EntityRegainHealthEvent $event)
    {
        $entity = $event->getEntity();
        if ($event->getRegainReason() == EntityRegainHealthEvent::CAUSE_EATING and $event->getRegainReason() == EntityRegainHealthEvent::CAUSE_REGEN) {
            $event->setCancelled(true);
            $entity->setHealth($entity->getHealth());
            //$event->setAmount(0);
        }
    }**/

    public function onConsume(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getId() == 322 and $item->getDamage() == 10) {
            $player->addEffect(Effect::getEffect(10)->setDuration(9 * 25)->setAmplifier(2));
            $player->addEffect(Effect::getEffect(22)->setDuration(30 * 30)->setAmplifier(4));
        }
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (in_array($entity->getName(), $this->getPlugin()->freeze)) {
                $event->setCancelled();
            }
            if ($event->getCause() == EntityDamageEvent::CAUSE_ENTITY_ATTACK || $event->getCause() == EntityDamageEvent::CAUSE_PROJECTILE) {
            	if ($this->getPlugin()->configuration['PvP'] == false) $event->setCancelled();
            }
        }
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                if (in_array($damager->getName(), $this->getPlugin()->freeze)) {
                    $event->setCancelled();
                }
            }
        }
    }
}
