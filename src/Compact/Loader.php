<?php

namespace Compact;

use Compact\EventListener;

use Compact\BossBar\MoveWither;

use Compact\Arena\Arena;

use Compact\Database\PlayerManager;
use Compact\Database\Spectator;
use Compact\Database\TeamManager;

use Compact\Commands\HelpopCommand;
use Compact\Commands\UHCCommand;
use Compact\Commands\FreezeCommand;
use Compact\Commands\ScenarioCommand;
use Compact\Commands\ScenariosCommand;
use Compact\Commands\MuteCommand;
use Compact\Commands\TopKillsCommand;
use Compact\Commands\TeamCommand;
use Compact\Commands\RankCommand;
use Compact\Commands\JoinTeamCommand;
use Compact\Commands\StatsCommand;
use Compact\Commands\WinnerCommand;
use Compact\Commands\WinnerTeamCommand;

use Compact\Scenarios\Cateyes;
use Compact\Scenarios\Cutclean;
use Compact\Scenarios\Fireless;
use Compact\Scenarios\NoClean;
use Compact\Scenarios\Nofall;
use Compact\Scenarios\Statua;
use Compact\Scenarios\TeamPvP;
use Compact\Scenarios\TimeBomb;
use Compact\Scenarios\TreeCapitator;
use Compact\Scenarios\Xray;

use Compact\Task\MeetupTask;
use Compact\Task\MeetupTeamTask;
use Compact\Task\MessagesAutomaticTask;
use Compact\Task\SimulatorTask;
use Compact\Task\SimulatorTeamTask;
use Compact\Task\UHCTask;
use Compact\Task\UHCTeamTask;
use Compact\Task\HealthBarTask;
use Compact\Task\WinnerTask;

use Compact\TwitterAPIExchange;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase
{

    public $configuration = [
        'Protection' => true,
        'PvP' => false,
        'GlobalMute' => false,
        'Ranks' => [
            'Owner' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD.">".TextFormat::GOLD." ".TextFormat::RESET."".TextFormat::RED."OWNER ".TextFormat::BOLD."".TextFormat::DARK_GRAY."".TextFormat::RESET."",
            'Staff' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD."> ".TextFormat::RESET."".TextFormat::GREEN."Staff ".TextFormat::DARK_GRAY."".TextFormat::BOLD."".TextFormat::RESET."",
            'Famous' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD.">".TextFormat::RESET."".TextFormat::LIGHT_PURPLE." Famous ".TextFormat::DARK_GRAY."".TextFormat::BOLD."".TextFormat::RESET."",
            'Famous+' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD.">".TextFormat::RESET."".TextFormat::LIGHT_PURPLE." Famous".TextFormat::WHITE."+".TextFormat::DARK_GRAY."".TextFormat::BOLD." ".TextFormat::RESET."",
            'YouTuber' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD.">".TextFormat::RESET."".TextFormat::DARK_RED." You".TextFormat::WHITE."Tuber ".TextFormat::DARK_GRAY."".TextFormat::BOLD."".TextFormat::RESET."",
            'Player' => "".TextFormat::DARK_GRAY."".TextFormat::BOLD.">".TextFormat::RESET."".TextFormat::AQUA." Player ".TextFormat::DARK_GRAY."".TextFormat::BOLD."".TextFormat::RESET."",
        ],
        'Scenarios' => [
            'Cateyes' => false,
            'Cutclean' => false,
            'Fireless' => false,
            'Noclean' => false,
            'Nofall' => false,
            'Statua' => false,
            'TeamPvP' => false,
            'TimeBomb' => false,
            'TreeCapitator' => false,
            'Xray' => false
        ]
    ];
    
    public $running = false;

    public $data = [
        'arena' => [],
        'players' => [],
        'spectators' => [],
        'teams' => [],
        'prefix' => ''
    ];

    public $teamsAvailable = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15];

    public $noclean = [];
    public $taskclean = [];
    
    public $freeze = [];
    public $mute = [];
    public $spam = [];

    public function onEnable()
    {
    	foreach($this->getServer()->getWhitelisted() as $name){
    		$this->getServer()->removeWhitelist($name);
    	}
    	$this->getServer()->setConfigBool("white-list", false);
    	$commands = [
    		new HelpopCommand($this),
			new UHCCommand($this),
			new FreezeCommand($this),
			new ScenarioCommand($this),
			new ScenariosCommand($this),
			new MuteCommand($this),
            new TeamCommand($this),
            new RankCommand($this),
            new JoinTeamCommand($this),
            new StatsCommand($this),
            new WinnerCommand($this),
            new WinnerTeamCommand($this),
            new TopKillsCommand($this)
    	];
    	$this->getServer()->getCommandMap()->registerAll("CompactUHC", $commands);
    	$this->getScenariosEvents();
    
    	@mkdir($this->getDataFolder());

    	$config = new Config($this->getDataFolder()."Config.yml", Config::YAML, [
    	    "HostInt" => 32
        ]);
    	$config->save();
    
    	$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    
        $data = [
            "name" => null,
            "StartTime" => 30,
            "GraceTime" => 15 * 60,
            "GameTime" => 0,
            "TpallTime" => 20 * 60,
            "WhitelistTime" => 1 * 60,
            "mode" => null,
            "status" => null
        ];
        $this->data['arena'] = new Arena($this, $data);
        
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MoveWither($this), 30);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new HealthBarTask($this), 25);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MessagesAutomaticTask($this), 1 * 60 * 15);
        
        $stats = new \SQLite3($this->getDataFolder()."stats.sqlite3");
        $stats->exec("CREATE TABLE IF NOT EXISTS stats(
        player_name TEXT NOT NULL PRIMARY KEY,
        uhc_win INT NOT NULL,
        uhc_team_win INT NOT NULL,
        kills INT NOT NULL,
        diamonds INT NOT NULL,
        gold INT NOT NULL,
        iron INT NOT NULL,
        uhc_played INT NOT NULL
        );");
        $ranks = new \SQLite3($this->getDataFolder()."ranks.sqlite3");
        $ranks->exec("CREATE TABLE IF NOT EXISTS ranks(
        player_name TEXT NOT NULL PRIMARY KEY,
        rank TEXT NOT NULL
        );");
        $bans = new \SQLite3($this->getDataFolder()."bans.sqlite3");
        $bans->exec("CREATE TABLE IF NOT EXISTS bans(
        player_name TEXT NOT NULL PRIMARY KEY,
        reason TEXT NOT NULL,
        staff TEXT NOT NULL
        );");
        $auth = new \SQLite3($this->getDataFolder()."auth.sqlite3");
        $auth->exec("CREATE TABLE IF NOT EXISTS auth(
        player_name TEXT NOT NULL PRIMARY KEY,
        password TEXT NOT NULL,
        user_twitter TEXT NOT NULL,
        ip INT NOT NULL
        );");
    }

    public function publicWinner($player, $kills){
        ini_set('display_errors', 1);
        require_once('TwitterAPIExchange.php');
        $settings = array(
            'oauth_access_token' => "1093266555750645761-DRXaqMWyJrjVYrmGQEKwz7XkVdka3F",
            'oauth_access_token_secret' => "IdMrubQv0bicwOqf6ExttvfCnFPvo21Tl4lxyRY5Pqs6P",
            'consumer_key' => "p2AURZkkbzzs5txPpfMYzyaJi",
            'consumer_secret' => "Gxqynrf3oDU16RCTu2jHDb7Vpqw1RB0ieZ4zskEJmEAQkBJYGB"
        );
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $config = new Config($this->getDataFolder()."Config.yml", Config::YAML);
        $postfield = ['status' => ">Compact UHC\n\n    •Ganador #".$config->get("HostInt")."\n\n    ".$player." ".$kills."K\n\nGracias por jugar!"];
        $requestMethod = 'POST';
        $twitter = new TwitterAPIExchange($settings);
        $twitter->buildOauth($url, $requestMethod)->setPostfields($postfield)->performRequest();
    }

    public function publicWinnerTeam($players){
        ini_set('display_errors', 1);
        require_once('TwitterAPIExchange.php');
        $settings = array(
            'oauth_access_token' => "1093266555750645761-DRXaqMWyJrjVYrmGQEKwz7XkVdka3F",
            'oauth_access_token_secret' => "IdMrubQv0bicwOqf6ExttvfCnFPvo21Tl4lxyRY5Pqs6P",
            'consumer_key' => "p2AURZkkbzzs5txPpfMYzyaJi",
            'consumer_secret' => "Gxqynrf3oDU16RCTu2jHDb7Vpqw1RB0ieZ4zskEJmEAQkBJYGB"
        );
        $url = 'https://api.twitter.com/1.1/statuses/update.json';
        $config = new Config($this->getDataFolder()."Config.yml", Config::YAML);
        $postfield = ['status' => ">Compact UHC\n\n    •Ganador #".$config->get("HostInt")."\n\n".$players."\n¡Gracias por jugar!"];
        $requestMethod = 'POST';
        $twitter = new TwitterAPIExchange($settings);
        $twitter->buildOauth($url, $requestMethod)->setPostfields($postfield)->performRequest();
    }
    
    public function publicTweetStartUHC($type, $typeMode, $whitelist, $scenarios, $hosts){
		ini_set('display_errors', 1);
		require_once('TwitterAPIExchange.php');
		$settings = array(
  			'oauth_access_token' => "1093266555750645761-DRXaqMWyJrjVYrmGQEKwz7XkVdka3F",
  			'oauth_access_token_secret' => "IdMrubQv0bicwOqf6ExttvfCnFPvo21Tl4lxyRY5Pqs6P",
  			'consumer_key' => "p2AURZkkbzzs5txPpfMYzyaJi",
  			'consumer_secret' => "Gxqynrf3oDU16RCTu2jHDb7Vpqw1RB0ieZ4zskEJmEAQkBJYGB"
		);
		$url = 'https://api.twitter.com/1.1/statuses/update.json';
		$postfield = ['status' => ">Compact UHC\n\n    ".$type." | ".$typeMode."\n\n>Scenarios: ".$scenarios."\n\n    CompactUHCs.nchost.xyz | 19122\n\n>WL: OFF\n\n>Host: ".$hosts];
		$requestMethod = 'POST';
		$twitter = new TwitterAPIExchange($settings);
		$twitter->buildOauth($url, $requestMethod)->setPostfields($postfield)->performRequest();
	}
    
    public function getDBBans(){
    	return new \SQLite3($this->getDataFolder()."bans.sqlite3");
    }
    
    public function getDBRanks(){
    	return new \SQLite3($this->getDataFolder()."ranks.sqlite3");
    }
    
    public function getDBStats(){
    	$stats = new \SQLite3($this->getDataFolder()."stats.sqlite3");
   	    return $stats;
    }

    public function getDBAuth(){
        return new \SQLite3($this->getDataFolder()."auth.sqlite3");
    }

    public function getScenariosEvents(){
        $this->getServer()->getPluginManager()->registerEvents(new Cateyes($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Cutclean($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Fireless($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new NoClean($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Nofall($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Statua($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TeamPvP($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TimeBomb($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new TreeCapitator($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new Xray($this), $this);
    }

    public function getArena()
    {
        return $this->data['arena'];
    }

    public function getTeam($value)
    {
        return $this->data["teams"][$value];
    }

    public function addPlayer($name, $data)
    {
        $this->data["players"][$name] = new PlayerManager($this, $data);
    }

    public function getPlayer($name)
    {
        $return = false;
        if (isset($this->data['players'][$name])) {
            $return = $this->data['players'][$name];
        }
        return $return;
    }

    public function getPlayers()
    {
    	$players = [];
    	foreach($this->data['players'] as $player){
    		$players[] = $player;
    	}
    	return $players;
    }

    public function removePlayer($name)
    {
        unset($this->data['players'][$name]);
    }

    public function addSpectator($name, $data)
    {
        $this->data['spectators'][$name] = new Spectator($this, $data);
    }

    public function getSpectator($name)
    {
        $return = false;
        if (isset($this->data['spectators'][$name])) {
            $return = $this->data['spectators'][$name];
        }
        return $return;
    }

    public function getSpectators()
    {
        return $this->data['spectators'];
    }

    public function removeSpectator($name)
    {
        unset($this->data['spectators'][$name]);
    }

    public function getScenario($scenario)
    {
        return $this->configuration['Scenarios'][$scenario];
    }
    
    public function getScenarios()
    {
    	return $this->configuration['Scenarios'];
    }

    public function startUHC()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UHCTask($this), 25);
    }

    public function startUHCTeam()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UHCTeamTask($this), 25);
    }

    public function startMeetup()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MeetupTask($this), 25);
    }

    public function startMeetupTeam()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new MeetupTeamTask($this), 25);
    }

    public function startSimulator()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SimulatorTask($this), 25);
    }

    public function startSimulatorTeam()
    {
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new SimulatorTeamTask($this), 25);
    }
    
    public function winnerTask(\pocketmine\Player $player){
    	$this->getServer()->getScheduler()->scheduleRepeatingTask(new WinnerTask($this, $player), 25);
    }

    public function createTeams($slots){
        foreach ($this->teamsAvailable as $team){
            $data = [
                'team' => $team,
                'maxslots' => $slots
            ];
            $this->data['teams'][$team] = new TeamManager($this, $data);
        }
    }
}