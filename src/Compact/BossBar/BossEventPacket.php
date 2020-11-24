<?php

namespace Compact\BossBar;

use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\Info;
use pocketmine\utils\Binary;

class BossEventPacket extends DataPacket{

	const BOSS_EVENT_PACKET = 0x4a;

	const NETWORK_ID = self::BOSS_EVENT_PACKET;
  	public $eid;
	public $type;

	public function decode(){

	}

	public function encode(){
		$this->reset();
		$this->putEntityId($this->eid);
		$this->putUnsignedVarInt($this->type);
	}
}
