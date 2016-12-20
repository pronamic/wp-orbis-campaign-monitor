<?php
/*
Plugin Name: Orbis Campaign Monitor
Plugin URI: https://www.pronamic.eu/plugins/orbis-campaign-monitor/
Description: The Orbis Campaign Monitor plugin connects your Orbis environment with Campaign Monitor.

Version: 1.0.0
Requires at least: 3.5

Author: Pronamic
Author URI: https://www.pronamic.eu/

Text Domain: orbis-campaign-monitor
Domain Path: /languages/

License: Copyright (c) Pronamic

GitHub URI: https://github.com/wp-orbis/wp-orbis-campaign-monitor
*/

/**
 * Autoload
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Bootstrap
 */
function orbis_campaign_monitor_bootstrap() {
	global $orbis_campaign_monitor_plugin;

	$orbis_campaign_monitor_plugin = new Orbis_CampaignMonitor_Plugin( __FILE__ );
}

add_action( 'plugins_loaded', 'orbis_campaign_monitor_bootstrap' );
