<?php
/*
Plugin Name: Widrick Minecraft Stats
Plugin URI: https://example.com
Description: Displays Player stats from minecraft server files
Version: 0.0.1
Author: Daniel Widrick
Author URI: http://widrick.net
Text Domain: widrick-miencraft-stats
Domain Path: /languages
*/



function widrick_playerStats_sortUsers_KDRatio($userA, $userB) {
	$userADeaths = $userA->stats->{'stat.deaths'} < 1 ? 1 : $userA->stats->{'stat.deaths'};
	$userBDeaths = $userB->stats->{'stat.deaths'} < 1 ? 1 : $userB->stats->{'stat.deaths'};
	
	$userAKD = $userA->stats->{'stat.mobKills'} / $userADeaths;
	$userBKD = $userB->stats->{'stat.mobKills'} / $userBDeaths;

	return ($userAKD < $userBKD) ? 1 : -1;
}

class widrick_playerStats {
	

	public $serverList;

	public function __construct() {
		$widget_options = array(
			'classname' => 'widrick_playerStats_Widget',
			'description' => 'Display player stats on a minecraft server'
		);
		$this->getServerList();
	}
	
	public function assembleServerStats($serverDirs) {
		if(!is_array($serverDirs))
			$serverDirs = Array( 0 => $serverDirs );

		$users = Array();
		foreach($serverDirs as $serverDir) {
			$directory = getcwd() . '/server-stats/' . $serverDir . '/';
			
			$usernames_json = file_get_contents($directory . 'usercache.json');
			$usernames = json_decode($usernames_json);
			$users = array_merge($users,$usernames);
		}
		foreach($users as $key => $user) {
			$users[$key]->expiresOn = 0;
		}
		$users = array_unique($users,SORT_REGULAR);
		foreach($users as $index => $user) {
			$users[$index]->stats = new stdClass();

			foreach($serverDirs as $serverStatDir) {
				$statDirectory = $directory = getcwd() . '/server-stats/' . $serverStatDir . '/stats/';
				$statFile = $statDirectory . $user->uuid . '.json';
				if(file_exists($statFile)) {
					$stats=json_decode(file_get_contents($statFile));
					$statsArray = get_object_vars($stats);
					foreach($statsArray as $statKey => $statValue) {
						if(isset($users[$index]->stats->{$statKey}) )
							$users[$index]->stats->{$statKey} += $statValue;
						else
							$users[$index]->stats->{$statKey} = $statValue;
					}
				}
			}
		}
		return $users;
	}

	private function getServerList() {
		$dir = getcwd() . '/server-stats/';
		$dirArray = array_diff(scandir($dir), array('..','.'));
		
		$this->serverList = $dirArray;
	}
}

?>
