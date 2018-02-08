<?php

class Orbis_CampaignMonitor_Admin {
	/**
	 * Construct.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Actions
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		$taxonomies = array(
			'orbis_person_category',
		);

		foreach ( $taxonomies as $taxonomy ) {
			// @see https://github.com/WordPress/WordPress/blob/4.4.1/wp-admin/edit-tag-form.php#L200-L211
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'taxonomy_edit_form_fields' ) );
			// @see http://sabramedia.com/blog/how-to-add-custom-fields-to-custom-taxonomies
			// @see https://github.com/WordPress/WordPress/blob/4.4.1/wp-includes/taxonomy.php#L3329-L3340
			add_action( 'edited_' . $taxonomy, array( $this, 'edited_taxonomy' ) );
		}

		add_action( 'added_term_relationship', array( $this, 'added_term_relationship' ), 10, 3 );
		add_action( 'deleted_term_relationships', array( $this, 'deleted_term_relationships' ), 10, 3 );
	}

	public function taxonomy_edit_form_fields( $term ) {
		$list_id = get_term_meta( absint( $term->term_id ), 'orbis_campaign_monitor_list_id', true );

		include plugin_dir_path( $this->plugin->file ) . 'admin/taxonomy-edit-form-fields.php';
	}

	public function edited_taxonomy( $term_id ) {
		if ( filter_has_var( INPUT_POST, 'orbis_campaign_monitor_list_id' ) ) {
			$list_id = filter_input( INPUT_POST, 'orbis_campaign_monitor_list_id', FILTER_SANITIZE_STRING );

			if ( empty( $list_id ) ) {
				delete_term_meta( $term_id, 'orbis_campaign_monitor_list_id' );
			} else {
				update_term_meta( $term_id, 'orbis_campaign_monitor_list_id', $list_id );
			}
		}
	}

	private function update_person_list( $object_id, $tt_id, $taxonomy, $action ) {
		if ( 'orbis_person_category' !== $taxonomy ) {
			return;
		}

		if ( 'orbis_person' !== get_post_type( $object_id ) ) {
			return;
		}

		$list_id = get_term_meta( $tt_id, 'orbis_campaign_monitor_list_id', true );

		if ( empty( $list_id ) ) {
			return;
		}

		$contact = new Orbis_Contact( $object_id );

		$email = $contact->get_email();

		if ( empty( $email ) ) {
			return;
		}

		$wrap = new CS_REST_Subscribers( $list_id, $this->plugin->get_auth_details() );

		switch ( $action ) {
			case 'add' :
				// @see https://github.com/campaignmonitor/createsend-php/blob/master/samples/subscriber/add.php
				$result = $wrap->add( array(
					'EmailAddress' => $contact->get_email(),
					'Name'         => $contact->get_name(),
					'Resubscribe'  => true,
				) );

				return $result;
			case 'delete' :
				// @see https://github.com/campaignmonitor/createsend-php/blob/master/samples/subscriber/delete.php
				$result = $wrap->delete( $contact->get_email() );

				return $result;
		}
	}

	/**
	 * Added term relationship.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.7/wp-includes/taxonomy.php#L2267-L2277
	 */
	public function added_term_relationship( $object_id, $tt_id, $taxonomy ) {
		$this->update_person_list( $object_id, $tt_id, $taxonomy, 'add' );
	}

	/**
	 * Deleted term relationships.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.7/wp-includes/taxonomy.php#L2408-L2418
	 */
	public function deleted_term_relationships( $object_id, $tt_ids, $taxonomy ) {
		foreach ( $tt_ids as $tt_id ) {
			$this->update_person_list( $object_id, $tt_id, $taxonomy, 'delete' );
		}
	}

	/**
	 * Admin initalize
	 */
	public function admin_init() {
		add_settings_section(
			'orbis_campaign_monitor',
			__( 'Campaign Monitor', 'orbis-campaign-monitor' ),
			'__return_false',
			'orbis'
		);

		// Client ID
		register_setting( 'orbis', 'orbis_campaign_monitor_client_id' );

		add_settings_field(
			'orbis_campaign_monitor_client_id',
			__( 'Client ID', 'orbis-campaign-monitor' ),
			array( $this, 'input_text' ),
			'orbis',
			'orbis_campaign_monitor',
			array( 'label_for' => 'orbis_campaign_monitor_client_id' )
		);

		// API Key
		register_setting( 'orbis', 'orbis_campaign_monitor_api_key' );

		add_settings_field(
			'orbis_campaign_monitor_api_key',
			__( 'API Key', 'orbis-campaign-monitor' ),
			array( $this, 'input_text' ),
			'orbis',
			'orbis_campaign_monitor',
			array( 'label_for' => 'orbis_campaign_monitor_api_key' )
		);
	}

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'orbis_person' === $post_type ) {
			add_meta_box(
				'orbis_campaign_monitor',
				__( 'Campaign Monitor', 'orbis-campaign-monitor' ),
				array( $this, 'meta_box' ),
				$post_type,
				'normal',
				'default'
			);
		}
	}

	/**
	 * Meta box.
	 */
	public function meta_box( $post ) {
		$contact = new Orbis_Contact( $post );

		include plugin_dir_path( $this->plugin->file ) . 'admin/meta-box-campaign-monitor.php';
	}

	/**
	 * Input text
	 *
	 * @param array $args
	 */
	public function input_text( $args = array() ) {
		printf(
			'<input name="%s" id="%s" type="text" value="%s" class="%s" />',
			esc_attr( $args['label_for'] ),
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) ),
			'regular-text code'
		);

		if ( isset( $args['description'] ) ) {
			printf(
				'<p class="description">%s</p>',
				esc_html( $args['description'] )
			);
		}
	}
}
