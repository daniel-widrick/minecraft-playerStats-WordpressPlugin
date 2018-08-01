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
		foreach($usernames as $user)
		{
			$user->stats = json_Decode( file_get_contents($directory . '/stats/' . $user->uuid . '.json') );
			$users[] = $user;
		}
		//var_dump($users);
		$html = $args['before_widget'] . $args['before_title'] . $title . $args['after_title'];
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
