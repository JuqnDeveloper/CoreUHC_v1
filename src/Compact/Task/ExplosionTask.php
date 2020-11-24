<?php

namespace Compact\Task;

use Compact\Loader;

use pocketmine\scheduler\PluginTask;
use pocketmine\block\Air;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\level\Position;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class ExplosionTask extends PluginTask {
	
	private $plugin, $time = 20, $level, $position, $name, $text;
	
	public function __construct(Loader $plugin, Position $position, Level $level, $name){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->position = $position;
		$this->level = $level;
		$this->name = $name;
		$particle = new FloatingTextParticle($this->position->add(0.5, +1, 0.5), "", TextFormat::GREEN.$this->name."s".TextFormat::YELLOW." Corpse\n".TextFormat::RED ."Explodes in ".$this->time. " seconds");
		$this->level->addParticle($particle);
		$this->text = $particle;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function getServer(){
		return $this->getPlugin()->getServer();
	}
	
	public function onRun($currentTick){
		if($this->time > 0){
			$this->time--;
		}
		if($this->time == 1){
			$this->text->setInvisible(true);
			$this->level->addParticle($this->text);
			$explosion = new Explosion($this->position, 5, $this->getPlugin());
			$explosion->explodeA();
            $explosion->explodeB();
			$this->level->setBlock(new Vector3($this->position->getX(), $this->position->getY(), $this->position->getZ()), new Air());
			$this->level->setBlock(new Vector3($this->position->getX()+1, $this->position->getY(), $this->position->getZ()), new Air());
			foreach($this->getServer()->getOnlinePlayers() as $players){
				$players->sendMessage(TextFormat::RED . "[" . TextFormat::WHITE . "TimeBomb" . TextFormat::RED . "] " . TextFormat::WHITE . $this->name . "'s corpse has explode!");
			}
			$this->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}else{
			$this->text->setTitle(TextFormat::GREEN . $this->name . "'s" . TextFormat::YELLOW . " Corpse\n" . TextFormat::RED . "Explodes in " . $this->time . " seconds");
			$this->level->addParticle($this->text);
		}
		if($this->time == 0){
			$this->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
	}
}