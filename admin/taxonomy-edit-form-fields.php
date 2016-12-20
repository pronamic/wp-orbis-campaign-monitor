<?php

// https://www.campaignmonitor.com/forums/topic/8275/check-to-see-if-an-email-address-is-on-a-list/
// https://www.campaignmonitor.com/api/clients/#lists_for_email
// https://github.com/campaignmonitor/createsend-php/blob/master/samples/client/get_lists.php

$wrap = new CS_REST_Clients( $this->plugin->get_client_id(), $this->plugin->get_auth_details() );

$result = $wrap->get_lists();

$options = array(
	'' => __( '— Select List —', 'orbis-campaign-monitor' ),
);

if ( $result->was_successful() ) {
	foreach ( $result->response as $list ) {
		$options[ $list->ListID ] = $list->Name;
	}
}

?>
<tr class="form-field">
	<th scope="row">
		<label for="campaign-monitor-list"><?php esc_html_e( 'Campaign Monitor List', 'orbis-campaign-monitor' ); ?></label>
	</th>
	<td>
		<select id="campaign-monitor-list" name="orbis_campaign_monitor_list_id">
			<?php

			foreach ( $options as $value => $label ) {
				printf(
					'<option value="%s" %s">%s</option>',
					esc_attr( $value ),
					selected( $value, $list_id, false ),
					esc_html( $label )
				);
			}

			?>
		</select>
	</td>
</tr>
