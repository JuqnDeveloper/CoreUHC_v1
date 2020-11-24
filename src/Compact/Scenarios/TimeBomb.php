<?php

namespace Compact\Scenarios;

use Compact\Loader;
use Compact\Task\ExplosionTask;

use pocketmine\event\Listener;
use pocketmine\block\Block;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\math\Vector3;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest;

class TimeBomb implements Listener
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
    
    public function getServer()
    {
    	return $this->getPlugin()->getServer();
    }
    
    public function createChest(\pocketmine\Player $player, $event){
        $lvl = $player->getLevel();
        $position = $player->getPosition();
        $nbt = new CompoundTag("", [
        new ListTag("Items", []),
        new StringTag("special", "timebomb"),
        new StringTag("Id", Tile::CHEST),
        new IntTag("x", $player->getX()),
        new IntTag("y", $player->getY()),
        new IntTag("z", $player->getZ())]);
        $chest = Tile::createTile("Chest", $player->chunk, $nbt);
        $lvl->setBlock(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()), Block::get(54), true, true);
        $nbt = new CompoundTag("", [
        new ListTag("Items", []),
        new StringTag("special", "timebomb"),
        new StringTag("Id", Tile::CHEST),
        new IntTag("x", $player->getX()+1),
        new IntTag("y", $player->getY()),
        new IntTag("z", $player->getZ())]);
        $chest_2 = Tile::createTile("Chest", $player->chunk, $nbt);
        $lvl->setBlock(new Vector3($chest_2->getX(), $chest_2->getY(), $chest_2->getZ()), Block::get(54), true, true);
        $chest->pairWith($chest_2);
        $chest_2->pairWith($chest);
        if($chest instanceof Chest){
            $chest->getInventory()->setContents($event->getDrops());
        }
        $event->setDrops([]);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ExplosionTask($this->getPlugin(), $position, $player->getLevel(), $player->getName()), 20);
    }
    
    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($this->getPlugin()->configuration['Scenarios']['TimeBomb'] == true){
        	$this->createChest($player, $event);
        }
    }
}