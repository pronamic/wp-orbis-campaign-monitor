<?php

class Orbis_CampaignMonitor_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis_campaign_monitor' );
		$this->set_db_version( '1.0.0' );

		// Load text domain
		$this->load_textdomain( 'orbis-campaign-monitor', '/languages/' );

		// Admin
		if ( is_admin() ) {
			$this->admin = new Orbis_CampaignMonitor_Admin( $this );
		}
	}

	public function get_client_id() {
		$client_id = get_option( 'orbis_campaign_monitor_client_id' );

		return $client_id;
	}

	public function get_auth_details() {
		$api_key = get_option( 'orbis_campaign_monitor_api_key' );

		$auth = array(
			'api_key' => $api_key,
		);

		return $auth;
	}
}
