<?php


function widrick_playerStats_whiteListed_stats() {
	$stats = array();
	$stats['Mob Kills'] = 'stat.mobKills';
	$stats['Deaths'] = 'stat.deaths';
	$stats['Dmg Done'] = 'stat.damageDealt';
	$stats['Dmg Recv'] = 'stat.damageTaken';
	$stats['Enchantment'] = 'stat.itemEnchanted';
	$stats['Waystones'] = 'stat.waystones:waystonesActivated';
	$stats['Breeding'] = 'stat.animalsBred';
	$stats['Walking'] = 'stat.walkOneCm';
	$stats['Sneaking'] = 'stat.crouchOneCm';
	$stats['Flying'] = 'stat.flyOneCm';
	$stats['Boating'] = 'stat.boatOneCm';
	$stats['Horse Back'] = 'stat.horseOneCm';
	$stats['Jumps'] = 'stat.jump';
	$stats['Sessions'] = 'stat.leaveGame';
	return $stats;
}
function widrick_playerStats_table_shortCode($atts, $conent = null) {
	wp_enqueue_style('widrick_playerStats_shortcode');
	$playerStats = new widrick_playerStats();
	$a = shortcode_atts ( array(
		'serverlist' => $playerStats->serverList
	), $atts);

	$users = $playerStats->assembleServerStats($a['serverlist']);
	usort($users,'widrick_playerStats_sortUsers_KDRatio');

	$html = '<div class="widrick_fullPlayerStatsContainer">';
	$html .= '<table class="widrick_fullPlayerStatsTable">';
	$html .= '<thead><tr><th>Player</th>';

	$statNames = widrick_playerStats_whiteListed_stats();
	foreach($statNames as $statName => $statValue) {
		$html .= '<th>' . $statName . '</th>';
	}
	$html .= "</tr></thead><tbody>";

	foreach($users as $user) {
		$html .= '<tr><td nowrap style="overflow:hidden">';
		$displayName = $user->name;
		$html .= '<img src="https://cravatar.eu/helmhead/'. $user->name. '/20.png" />' . $yser->name . '</td>';
		foreach($statNames as $statName => $statValue) {
			$html .= '<td>';
			if(isset($user->stats->{$statValue}))
				$html .= $user->stats->{$statValue};
			else
				$html .= "0";
		}
		$html .= '</tr>';
	}

	$html .= "</tbody></table></div>";
	echo $html;
}


wp_register_style('widrick_playerStats_shortcode',plugin_dir_url(__FILE__).'/widrick-playerstats.css');
add_shortcode('widrick_playerstats','widrick_playerStats_table_shortCode');
wp_enqueue_script('jquery.tablesorter',plugin_dir_url(__FILE__).'/jquery.tablesorter.min.js',array('jquery'));
wp_enqueue_script('widrick_playerstats',plugin_dir_url(__FILE__).'/widrick_playerStats.js',array('jquery.tablesorter'));
