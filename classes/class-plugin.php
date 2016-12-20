<?php

class Orbis_CampaignMonitor_Plugin extends Orbis_Plugin {
	public function __construct( $file ) {
		parent::__construct( $file );

		$this->set_name( 'orbis_campaign_monitor' );
		$this->set_db_version( '1.0.0' );

		// Load text domain
		$this->load_textdomain( 'orbis-campaign-monitor', '/languages/' );
	}
}
