<?php

namespace Compact\Task;

use Compact\Loader;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class WinnerTask extends PluginTask {

    private $plugin, $player, $time = 20;

    public function __construct(Loader $owner, Player $player)
    {
        parent::__construct($owner);
        $this->plugin = $owner;
        $this->player = $player;
    }

    public function getPlugin(){
        return $this->plugin;
    }

    public function getServer(){
        return $this->getPlugin()->getServer();
    }

    public function onRun($currentTick)
    {
        $this->time--;
        $player = $this->player;
        if($this->time !== 0){
            $center = new Vector3($player->getX(), $player->getY() + 3, $player->getZ());
            $radius = 1;
            $particles = array(new FlameParticle($center), new HeartParticle($center), new RedstoneParticle($center), new PortalParticle($center));
            $rand = $particles[array_rand($particles)];
            $particle = $rand;
            for ($a = 0; $a < 100; $a++){
                $pitch = (mt_rand() / mt_getrandmax() - 0.5) * M_PI;
                $yaw = mt_rand() / mt_getrandmax() * 2 * M_PI;
                $yi = -sin($pitch);
                $delta = cos($pitch);
                $xi = -sin($yaw) * $delta;
                $zi = cos($yaw) * $delta;
                $vector = new Vector3($xi, $yi, $zi);
                $pi = $center->add($vector->normalize()->multiply($radius));
                $particle->setComponents($pi->x, $pi->y + 0.3, $pi->z);
                $player->getLevel()->addParticle($particle);
            }
        } else {
            if($this->time == 0){
                $this->getServer()->getScheduler()->cancelTask($this->getTaskId());
            }
        }
    }
}