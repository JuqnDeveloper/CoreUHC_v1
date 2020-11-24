<?php

namespace Compact\Arena;

use pocketmine\item\Item;
use pocketmine\entity\Effect;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Kits {
	
	public function getKitUHC(Player $player){
		$inv = $player->getInventory();
       // $player->teleport($s);
        $player->setHealth(20);
        $player->setGamemode(0);
        $player->removeAllEffects();
        $player->getInventory()->clearAll();
        $player->addEffect(Effect::getEffect(15)->setVisible(false)->setDuration(8 * 8)->setAmplifier(10));
        $player->addEffect(Effect::getEffect(2)->setVisible(false)->setDuration(8 * 8)->setAmplifier(10));
        $player->addEffect(Effect::getEffect(16)->setVisible(false)->setDuration(2000 * 2000)->setAmplifier(1));
        $inv->setItem(0, Item::get(274, 0, 1));
        $inv->setItem(1, Item::get(275, 0, 1));
        $inv->setItem(2, Item::get(260, 0, 15));
        $inv->setItem(3, Item::get(364, 0, 32));
	}
	
	public function getKitMeetup(Player $player){
		$inv = $player->getInventory();
       // $player->teleport($s);
        $player->setHealth(20);
        $player->setGamemode(0);
        $player->getInventory()->clearAll();
        $player->addEffect(Effect::getEffect(15)->setVisible(false)->setDuration(8 * 8)->setAmplifier(10));
        $player->addEffect(Effect::getEffect(2)->setVisible(false)->setDuration(8 * 8)->setAmplifier(10));
        $inv->setHelmet(Item::get(310, 0, 1));
        $inv->setChestplate(Item::get(311, 0, 1));
        $inv->setLeggings(Item::get(312, 0, 1));
        $inv->setBoots(Item::get(313, 0, 1));
        $inv->setItem(0, Item::get(276, 0, 1));
        $inv->setItem(1, Item::get(322, 0, 15));
        $inv->setItem(2, Item::get(364, 0, 32));
        $inv->setItem(3, Item::get(30, 0, 10));
        $inv->setItem(4, Item::get(278, 0, 1));
        $inv->setItem(5, Item::get(5, 0, 64));
        $inv->setItem(6, Item::get(279, 0, 1));
        $inv->setItem(7, Item::get(1, 0, 64));
	}
	
	public function getKitSimulator(Player $player){
		switch (mt_rand(1, 5)) {
            Case "1"://Kit 1
                $player->setHealth(20);
                $player->setFood(20);
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->removeAllEffects();
                $player->getInventory()->setHelmet(Item::get(306, 0, 1));
                $player->getInventory()->setChestplate(Item::get(311, 0, 1));
                $player->getInventory()->setLeggings(Item::get(308, 0, 1));
                $player->getInventory()->setBoots(Item::get(313, 0, 1));
                $player->getInventory()->addItem(Item::get(276, 0, 1));
                $player->getInventory()->addItem(Item::get(261, 0, 1));
                $player->getInventory()->addItem(Item::get(346, 0, 1));
                $player->getInventory()->addItem(Item::get(278, 0, 1));
                $player->getInventory()->addItem(Item::get(279, 0, 1));
                $player->getInventory()->addItem(Item::get(322, 0, 9));
                $apple = Item::get(322);
                $apple->setDamage(10);
                $apple->setCount(3);
                $apple->setCustomName(TextFormat::GOLD."GoldenHead");
                $player->getInventory()->addItem($apple);
                $player->getInventory()->addItem(Item::get(364, 0, 64));
                $player->getInventory()->addItem(Item::get(1, 0, 64));
                $player->getInventory()->addItem(Item::get(5, 0, 64));
                $player->getInventory()->addItem(Item::get(30, 0, 6));
                $player->getInventory()->addItem(Item::get(262, 0, 64));
                Break;

            Case "2"://Kit 2
                $player->setHealth(20);
                $player->setFood(20);
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->removeAllEffects();
                $player->getInventory()->setHelmet(Item::get(310, 0, 1));
                $player->getInventory()->setChestplate(Item::get(311, 0, 1));
                $player->getInventory()->setLeggings(Item::get(312, 0, 1));
                $player->getInventory()->setBoots(Item::get(309, 0, 1));
                $player->getInventory()->addItem(Item::get(267, 0, 1));
                $player->getInventory()->addItem(Item::get(261, 0, 1));
                $player->getInventory()->addItem(Item::get(346, 0, 1));
                $player->getInventory()->addItem(Item::get(278, 0, 1));
                $player->getInventory()->addItem(Item::get(279, 0, 1));
                $player->getInventory()->addItem(Item::get(322, 0, 7));
                $apple = Item::get(322);
                $apple->setDamage(10);
                $apple->setCount(4);
                $apple->setCustomName(TextFormat::GOLD."GoldenHead");
                $player->getInventory()->addItem($apple);
                $player->getInventory()->addItem(Item::get(364, 0, 64));
                $player->getInventory()->addItem(Item::get(1, 0, 64));
                $player->getInventory()->addItem(Item::get(5, 0, 64));
                $player->getInventory()->addItem(Item::get(30, 0, 3));
                $player->getInventory()->addItem(Item::get(262, 0, 64));
                Break;

            Case "3"://Kit 3
                $player->setHealth(20);
                $player->setFood(20);
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->removeAllEffects();
                $player->getInventory()->setHelmet(Item::get(306, 0, 1));
                $player->getInventory()->setChestplate(Item::get(307, 0, 1));
                $player->getInventory()->setLeggings(Item::get(312, 0, 1));
                $player->getInventory()->setBoots(Item::get(309, 0, 1));
                $player->getInventory()->addItem(Item::get(276, 0, 1));
                $player->getInventory()->addItem(Item::get(261, 0, 1));
                $player->getInventory()->addItem(Item::get(346, 0, 1));
                $player->getInventory()->addItem(Item::get(278, 0, 1));
                $player->getInventory()->addItem(Item::get(279, 0, 1));
                $player->getInventory()->addItem(Item::get(322, 0, 6));
                $apple = Item::get(322);
                $apple->setDamage(10);
                $apple->setCount(5);
                $apple->setCustomName(TextFormat::GOLD."GoldenHead");
                $player->getInventory()->addItem($apple);
                $player->getInventory()->addItem(Item::get(364, 0, 64));
                $player->getInventory()->addItem(Item::get(1, 0, 64));
                $player->getInventory()->addItem(Item::get(5, 0, 64));
                $player->getInventory()->addItem(Item::get(262, 0, 64));
                Break;

            Case "4"://Kit 4
                $player->setHealth(20);
                $player->setFood(20);
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->removeAllEffects();
                $player->getInventory()->setHelmet(Item::get(310, 0, 1));
                $player->getInventory()->setChestplate(Item::get(307, 0, 1));
                $player->getInventory()->setLeggings(Item::get(308, 0, 1));
                $player->getInventory()->setBoots(Item::get(313, 0, 1));
                $player->getInventory()->addItem(Item::get(267, 0, 1));
                $player->getInventory()->addItem(Item::get(261, 0, 1));
                $player->getInventory()->addItem(Item::get(346, 0, 1));
                $player->getInventory()->addItem(Item::get(278, 0, 1));
                $player->getInventory()->addItem(Item::get(279, 0, 1));
                $player->getInventory()->addItem(Item::get(322, 0, 9));
                $apple = Item::get(322);
                $apple->setDamage(10);
                $apple->setCount(5);
                $apple->setCustomName(TextFormat::GOLD."GoldenHead");
                $player->getInventory()->addItem($apple);
                $player->getInventory()->addItem(Item::get(364, 0, 64));
                $player->getInventory()->addItem(Item::get(1, 0, 64));
                $player->getInventory()->addItem(Item::get(5, 0, 64));
                $player->getInventory()->addItem(Item::get(30, 0, 8));
                $player->getInventory()->addItem(Item::get(262, 0, 64));
                Break;

            Case "5"://Kit 5
                $player->setHealth(20);
                $player->setFood(20);
                $player->setGamemode(0);
                $player->getInventory()->clearAll();
                $player->removeAllEffects();
                $player->getInventory()->setHelmet(Item::get(310, 0, 1));
                $player->getInventory()->setChestplate(Item::get(311, 0, 1));
                $player->getInventory()->setLeggings(Item::get(308, 0, 1));
                $player->getInventory()->setBoots(Item::get(309, 0, 1));
                $player->getInventory()->addItem(Item::get(267, 0, 1));
                $player->getInventory()->addItem(Item::get(261, 0, 1));
                $player->getInventory()->addItem(Item::get(346, 0, 1));
                $player->getInventory()->addItem(Item::get(278, 0, 1));
                $player->getInventory()->addItem(Item::get(279, 0, 1));
                $player->getInventory()->addItem(Item::get(322, 0, 5));
                $apple = Item::get(322);
                $apple->setDamage(10);
                $apple->setCount(7);
                $apple->setCustomName(TextFormat::GOLD."GoldenHead");
                $player->getInventory()->addItem($apple);
                $player->getInventory()->addItem(Item::get(364, 0, 64));
                $player->getInventory()->addItem(Item::get(1, 0, 64));
                $player->getInventory()->addItem(Item::get(5, 0, 64));
                $player->getInventory()->addItem(Item::get(262, 0, 64));
                Break;
        }
	}
}