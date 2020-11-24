<?php

namespace Compact\Database;

use Compact\Loader;

class PlayerStats {
	
	private $plugin, $db;
	
	public function __construct(Loader $plugin){
		$this->plugin = $plugin;
		$this->db = $plugin->getDBStats();
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	public function addDiamond($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['diamonds'] + 1;
		$this->db->query("UPDATE stats SET diamonds = '$result' WHERE player_name = '$name';");
	}
	
	public function addGold($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['gold'] + 1;
		$this->db->query("UPDATE stats SET gold = '$result' WHERE player_name = '$name';");
	}
	
	public function addIron($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['iron'] + 1;
		$this->db->query("UPDATE stats SET iron = '$result' WHERE player_name = '$name';");
	}
	
	public function addWin($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_win'] + 1;
		$this->db->query("UPDATE stats SET uhc_win = '$result' WHERE player_name = '$name';");
	}
	
	public function addWinTeam($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_team_win'] + 1;
		$this->db->query("UPDATE stats SET uhc_team_win = '$result' WHERE player_name = '$name';");
	}
	
	public function addKills($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['kills'] + 1;
		$this->db->query("UPDATE stats SET kills = '$result' WHERE player_name = '$name';");
	}
	
	public function addUHC($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_played'] + 1;
		$this->db->query("UPDATE stats SET uhc_played = '$result' WHERE player_name = '$name';");
	}
	
	public function getDiamond($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['diamonds'];
		return $result;
		//$this->db->query("UPDATE stats SET diamonds = '$result' WHERE player_name = '$name';");
	}
	
	public function getGold($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['gold'];
		return $result;
		//$this->db->query("UPDATE stats SET gold = '$result' WHERE player_name = '$name';");
	}
	
	public function getIron($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['iron'];
		return $result;
		//$this->db->query("UPDATE stats SET iron = '$result' WHERE player_name = '$name';");
	}
	
	public function getWin($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_win'];
		return $result;
		//$this->db->query("UPDATE stats SET uhc_win = '$result' WHERE player_name = '$name';");
	}
	
	public function getWinTeam($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_team_win'];
		return $result;
		//$this->db->query("UPDATE stats SET uhc_team_win = '$result' WHERE player_name = '$name';");
	}
	
	public function getKills($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['kills'];
		return $result;
		//$this->db->query("UPDATE stats SET kills = '$result' WHERE player_name = '$name';");
	}
	
	public function getUHC($name){
		$sql = $this->db->query("SELECT * FROM stats WHERE player_name = '$name';")->fetchArray(SQLITE3_ASSOC);
		$result = $sql['uhc_played'];
		return $result;
		//$this->db->query("UPDATE stats SET uhc_played = '$result' WHERE player_name = '$name';");
	}
}