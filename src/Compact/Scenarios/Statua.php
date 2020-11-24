<?php

namespace Compact\Scenarios;

use Compact\Loader;
use pocketmine\event\Listener;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\event\player\PlayerDeathEvent;

class Statua implements Listener
{

    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }


    public function onDeath(PlayerDeathEvent $e)
    {
        if ($this->getPlugin()->configuration['Scenarios']['Statua'] == true) {
            $death = $e->getPlayer();
            $level = $death->getLevel();
            $x = $death->x;
            $y = $death->y;
            $z = $death->z;
            $level->setBlock(new Vector3($x, $y, $z), Block::get(Block::NETHER_BRICK_FENCE));
            $level->setBlock(new Vector3($x, $y + 1, $z), Block::get(Block::NETHER_BRICK_FENCE));
            $level->setBlock(new Vector3($x, $y + 2, $z), Block::get(86));
            $level->setBlock(new Vector3($x + 1, $y + 1, $z), Block::get(Block::NETHER_BRICK_FENCE));
            $level->setBlock(new Vector3($x - 1, $y + 1, $z), Block::get(Block::NETHER_BRICK_FENCE));
        }
    }
}


