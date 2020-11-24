<?php

namespace Compact\Task;

use Compact\Loader;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class MessagesAutomaticTask extends PluginTask {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}

	public function sendMessage($prefix, Array $array){
	    $rand = array_rand($array, 1);
	    $returnMessage = $array[$rand];
	    $this->getServer()->broadcastMessage($prefix." ".TextFormat::WHITE.$returnMessage);
    }
	
	public function onRun($tick){
	    $arena = $this->getPlugin()->getArena();
		$prefix = TextFormat::GRAY."[".TextFormat::BLUE."!".TextFormat::GRAY."] ";
		if($this->getPlugin()->running == false){
			$lobby = [
			    "Recuerda seguirnos en Twitter @CompactUHC",
                "Use /scenarios para ver los scenarios del evento",
                "Si te bugeas usa /helpop",
                "Cualquier bug o error reportalo en nuestro Twitter @CompactUHC",
                "Recuerda no usar hacks o seras baneado",
                "Para ver tus stats, usa /stats",
                "Gracias por jugar nuestros eventos!"
            ];
			$this->sendMessage($prefix, $lobby);
		}
		if($this->getPlugin()->running == true){
			if($arena->getArenaActive() == false){
			    if($arena->getMode() == "UHCFFA" || $arena->getMode() == "MEEFFA" || $arena->getMode() == "SIMFFA") {
                    $ffa = [
                        //"El PvP se activa en el minuto " . gmdate("i:s", $arena->getGraceTime()),
                        "El Tpall se realizara en el minuto " . gmdate("i:s", $arena->getTpallTime()),
                        "Recuerda seguirnos en Twitter @CompactUHC",
                        "Use /scenarios para ver los scenarios del evento",
                        "Si te bugeas usa /helpop",
                        "Cualquier bug o error reportalo en nuestro Twitter @CompactUHC",
                        "Recuerda no usar hacks o seras baneado",
                        "Para ver tus stats, usa /stats",
                        "Gracias por jugar nuestros eventos!"
                    ];
                    $this->sendMessage($prefix, $ffa);
                }
                if($arena->getMode() == "MEETO2" || $arena->getMode() == "MEETO3" || $arena->getMode() == "MEETO4" || $arena->getMode() == "MEETO5" || $arena->getMode() == "UHCTO2" || $arena->getMode() == "UHCTO3" || $arena->getMode() == "UHCTO4" || $arena->getMode() == "UHCTO5" || $arena->getMode() == "SIMTO2" || $arena->getMode() == "SIMTO3" || $arena->getMode() == "SIMTO4" || $arena->getMode() == "SIMTO5"){
                    $team = [
                        "Usa el comando /tc para hablar con tu equipo!",
                        //"El PvP se activa en el minuto " . gmdate("i:s", $arena->getGraceTime()),
                        "El Tpall se realizara en el minuto " . gmdate("i:s", $arena->getTpallTime()),
                        "Recuerda seguirnos en Twitter @CompactUHC",
                        "Use /scenarios para ver los scenarios del evento",
                        "Si te bugeas usa /helpop",
                        "Cualquier bug o error reportalo en nuestro Twitter @CompactUHC",
                        "Recuerda no usar hacks o seras baneado",
                        "Para ver tus stats, usa /stats",
                        "Gracias por jugar nuestros eventos!"
                    ];
                    $this->sendMessage($prefix, $team);
                }
			}
			if($arena->getArenaActive() == true){
			    $event = [
   	             "Recuerda seguirnos en Twitter @CompactUHC",
   	             "Use /scenarios para ver los scenarios del evento",
   	             "Si te bugeas usa /helpop",
   	             "Cualquier bug o error reportalo en nuestro Twitter @CompactUHC",
 	               "Recuerda no usar hacks o seras baneado",
    	            "Para ver tus stats, usa /stats",
	                "Gracias por jugar nuestros eventos!"
	            ];
			    $this->sendMessage($prefix, $event);
			}
        }
	}
}