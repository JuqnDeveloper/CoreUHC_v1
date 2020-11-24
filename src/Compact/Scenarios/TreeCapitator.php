<?php

namespace Compact\Scenarios;

use Compact\Loader;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\level\Position;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

class TreeCapitator implements Listener {
	
	private $plugin;
	
	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
	}
	
	public function getPlugin()
    {
        return $this->plugin;
    }

    public function onBreak(BlockBreakEvent $event)
    {
        if ($this->getPlugin()->configuration['Scenarios']['TreeCapitator'] == true) {
            $player = $event->getPlayer();
            $level = $player->getLevel();
            $x = $event->getBlock()->x;
            $y = $event->getBlock()->y;
            $z = $event->getBlock()->z;
            if ($event->getBlock()->getID() == 17) {
                $del_flag = true;
                $woodcount = 0;
                for ($i = 1; $i < 100; $i++) {
                    $w_blocks[$woodcount] = $level->getBlock(new Position($x, $y - $i, $z, $event->getPlayer()->getLevel()));
                    if ($w_blocks[$woodcount]->getID() != 17) {
                        break;
                    }
                    $woodcount++;
                }
                $min = $i;
                if ($w_blocks[$woodcount]->getID() != 2 && $w_blocks[$woodcount]->getID() != 3) {
                    $del_flag = false;
                }

                for ($i = 1; $i < 100; $i++) {
                    $w_blocks[$woodcount] = $level->getBlock(new Position($x, $y + $i, $z, $event->getPlayer()->getLevel()));
                    if ($w_blocks[$woodcount]->getID() != 17) {
                        break;
                    }
                    $woodcount++;
                }
                $max = $i;
                if ($del_flag) {
                    for ($j = 0; $j <= $woodcount; $j++) {
                        $level->setBlock(new Vector3($w_blocks[$j]->x, $w_blocks[$j]->y, $w_blocks[$j]->z), Block::get(0, 0), false, true);
                        unset($w_blocks[$j]);
                    }

                    $lx = $x;
                    $ly = $y - $min + 3;
                    $lz = $z;

                    $lc = 0;
                    if ($max + $min >= 3) {
                        for ($i = -3; $i <= 3; $i++) {
                            for ($j = -2; $j <= $max + $min - 3 + 3; $j++) {
                                for ($k = -3; $k <= 3; $k++) {
                                    $l_blocks[$lc] = $level->getBlock(new Position($lx + $i, $ly + $j, $lz + $k, $level));
                                    $lc++;
                                }
                            }
                        }
                        for ($i = 0; $i < $lc; $i++) {
                            $id = $l_blocks[$i]->getID();
                            if ($id != 0) $meta = $l_blocks[$i]->getDamage() % 4;
                            if ($id == 18 && ($meta == $event->getBlock()->getDamage() or !isset($meta))) {
                                $level->setBlock(new Vector3($l_blocks[$i]->x, $l_blocks[$i]->y, $l_blocks[$i]->z), Block::get(0, 0), false, true);
                                unset($l_blocks[$i]);
                            }
                        }
                        $level->dropItem(new Position($x, $y, $z, $event->getPlayer()->getLevel()), new Item(260, 0, mt_rand(0, 2)));
                    }
                    $level->dropItem(new Position($x, $y, $z, $event->getPlayer()->getLevel()), new Item($event->getBlock()->getID(), $event->getBlock()->getDamage(), $woodcount, "Block"));
                }
            }
        }
    }
}