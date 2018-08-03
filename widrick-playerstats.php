<?php
/*
Plugin Name: Minecraft Player Stats
Plugin URI: https://example.com
Description: Displays Player stats from minecraft server files
Version: 0.0.1
Author: Daniel Widrick
Author URI: http://widrick.net
Text Domain: miencraft-player-stats
Domain Path: /languages
*/


function widrick_playerStats_sortUsers($userA, $userB) {
	$userADeaths = $userA->stats->{'stat.deaths'} < 1 ? 1 : $userA->stats->{'stat.deaths'};
	$userBDeaths = $userB->stats->{'stat.deaths'} < 1 ? 1 : $userB->stats->{'stat.deaths'};
	
	$userAKD = $userA->stats->{'stat.mobKills'} / $userADeaths;
	$userBKD = $userB->stats->{'stat.mobKills'} / $userBDeaths;

	return ($userAKD < $userBKD) ? 1 : -1;
}

class widrick_playerStats_Widget extends WP_Widget {
	
	public function __construct() {
		$widget_options = array(
			'classname' => 'widrick_playerStats_Widget',
			'description' => 'Display player stats on a minecraft server'
		);
		parent::__construct('widrick_playerStats_Widget','Minecraft Player Stats',$widget_options);
	}

	
	public function widget( $args, $instance) {
		$title = apply_filters('widget_title',$instance['title']);
		$directory = getcwd() . '/server-stats/' . $instance['serverDir'] . '/';

		$usernames_json = file_get_contents($directory . 'usercache.json');
		$usernames = json_decode($usernames_json);

		$users = array();
		
		$html = $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
		$html .= "<table class='widrick_playerStats'><tr><th width='65px'  style='white-space:nowrap'>Rank</th><th>Name</th><th width='54px' style='white-space:nowrap'>K</th><th width='54px' style='white-space:nowrap'>D</th></tr>";
		$rank = 1;
		

		foreach($usernames as $user)
		{
			$user->stats = json_Decode( file_get_contents($directory . '/stats/' . $user->uuid . '.json') );
			$users[] = $user;
		}
		usort($users,'widrick_playerStats_sortUsers');
		foreach($users as $user)
		{
			$html .= "<tr><td class='widrick_playerStats_rank' width='20%'>$rank<img src='https://cravatar.eu/helmhead/".trim($user->name)."/10.png' /></td>";
			$rank++;
			if(strlen($user->name) > 12)
				$displayName = substr($user->name,0,12) . '...';
			else
				$displayName = $user->name;
			$html .= "<td class='widrick_playerStats_name' style='white-space:nowrap; max-width:50%'>". $displayName . "</td>";
			$html .= "<td class='widrick_playerStats_kills' width='1px'>".($user->stats->{"stat.mobKills"} < 1 ? 0 : $user->stats->{"stat.mobKills"}) ."</td>";
			$html .= "<td class='widrick_playerStats_deaths' width'1px'>".($user->stats->{"stat.deaths"} < 1 ? 0 : $user->stats->{'stat.deaths'}) ."</td>";
			$html .= '</tr>';
			if($rank > 7)
				break;
		}
		$html .= "</table>";

		//var_dump($users);
		$html .= $instance['serverDir'];
		$html .= $args['after_widget'];
		echo $html;
	}
	public function form($instance) {
		$title = ! empty($instance['title'] ) ? $instance['title'] : 'Minecraft Player Stats';
		$server = ! empty($instance['serverDir'] ) ? $instance['serverDir'] : 'exampleServer';
		$html = "<p><label for='$this->get_field_id(\'title\')'>Title:</label>";
		$html .= "<input type='text' id='" . $this->get_field_id('title') . "' name='" . $this->get_field_name('title') . "' value= '" . esc_attr($title) . "' /></p>";

		$html .= "<p><label for='$this->get_field_id(\'serverDir\')>Server:</label>";
		$html .= "<input type='text' id='" . $this->get_field_id('serverDir') . "' name='" . $this->get_field_name('serverDir') . "' value='" . esc_attr($server) . "' /></p>";

		echo $html;
	}


	public function update( $new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title'] );
		$instance['serverDir'] = strip_tags($new_instance['serverDir']);
		return $instance;
	}
}

function widrick_register_playerStats_widget() {
	register_widget('widrick_playerStats_Widget');
}

add_action('widgets_init', 'widrick_register_playerStats_widget');

?>
