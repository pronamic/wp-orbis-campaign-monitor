<?php

// https://www.campaignmonitor.com/forums/topic/8275/check-to-see-if-an-email-address-is-on-a-list/
// https://www.campaignmonitor.com/api/clients/#lists_for_email
// https://github.com/campaignmonitor/createsend-php/blob/master/samples/client/get_lists_for_email.php

$wrap = new CS_REST_Clients( $this->plugin->get_client_id(), $this->plugin->get_auth_details() );

$result = $wrap->get_lists_for_email( $contact->get_email() );

?>
<style type="text/css">
	.orbis-table thead th {
		font-weight: bold;
	}

	.postbox .orbis-table {
		border: 0;
	}
</style>

<?php if ( $result->was_successful() ) : ?>

	<table class="orbis-table widefat">
		<thead>
			<tr>
				<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
					<th scope="col"><?php esc_html_e( 'ID', 'orbis-campaign-monitor' ); ?></th>
				<?php endif; ?>
				<th scope="col"><?php esc_html_e( 'Name', 'orbis-campaign-monitor' ); ?></th>
				<th scope="col"><?php esc_html_e( 'State', 'orbis-campaign-monitor' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Added', 'orbis-campaign-monitor' ); ?></th>
			</tr>
		</thead>

		<tbody>

			<?php foreach ( $result->response as $list ) : ?>

				<tr>
					<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
						<td>
							<code><?php echo esc_html( $list->ListID ); ?></code>
						</td>
					<?php endif; ?>
					<td>
						<?php echo esc_html( $list->ListName ); ?>
					</td>
					<td>
						<?php echo esc_html( $list->SubscriberState ); ?>
					</td>
					<td>
						<?php echo esc_html( $list->DateSubscriberAdded ); ?>
					</td>
				</tr>

			<?php endforeach; ?>

		</tbody>
	</table>

<?php endif; ?>
