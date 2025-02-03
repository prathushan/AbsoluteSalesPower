<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Add custom post type 'cf7pp_payments' for storing payments.
 * @since 1.8
 */
add_action('init', 'cf7pp_payments_init');
function cf7pp_payments_init(){
	register_post_type('cf7pp_payments', array(
		'labels'				=> array(
			'name'               => __('PayPal & Stripe Payments', 'contact-form-7'),
			'singular_name'      => __('PayPal & Stripe Payments', 'contact-form-7'),
			'add_new'            => __('Add New', 'contact-form-7'),
			'add_new_item'       => __('Add new payment', 'contact-form-7'),
			'edit_item'          => __('Edit payment', 'contact-form-7'),
			'new_item'           => __('New payment', 'contact-form-7'),
			'view_item'          => __('View payment', 'contact-form-7'),
			'search_items'       => __('Find payment', 'contact-form-7'),
			'not_found'          => __('No payments found.', 'contact-form-7'),
			'not_found_in_trash' => __('No payments found in Trash.', 'contact-form-7'),
			'parent_item_colon'  => '',
			'menu_name'          => __('PayPal & Stripe Payments', 'contact-form-7'),
		),
		'public'				=> false,
		'show_ui'				=> true,
		'show_in_menu'			=> 'wpcf7',
		'capability_type'		=> 'page',
		'supports'				=> array('custom-fields'),
		'rewrite'				=> false,
		'query_var'				=> false,
		'delete_with_user'		=> false,
		'register_meta_box_cb'	=> 'cf7pp_payments_add_metaboxes',
	) );
}

/**
 * Print Payment ID instead of Title on payment details screen.
 * @since 1.8
 */
add_action('edit_form_after_title', 'cf7pp_payments_print_id');
function cf7pp_payments_print_id($post) {
	if ($post->post_type === 'cf7pp_payments') {
		printf(
			'<h3>%s %s</h3>',
			__('Payment:', 'contact-form-7'),
			$post->ID
		);
    }
}

/**
 * Remove submit metabox.
 * @since 1.8
 */
add_action('admin_menu', 'cf7pp_payments_remove_meta_box');
function cf7pp_payments_remove_meta_box() {
    remove_meta_box('submitdiv', 'cf7pp_payments', 'side');
}

/**
 * Adds custom submit metabox.
 * @since 1.8
 */
function cf7pp_payments_add_metaboxes() {
	add_meta_box('submitdiv', __('Payment actions', 'contact-form-7'), 'cf7pp_payments_submit_metabox', 'cf7pp_payments', 'side');
}

/**
 * Display cf7pp_payments submit form fields.
 *
 * @since 1.8
 */
function cf7pp_payments_submit_metabox($post) {
	global $action;

	$post_id = (int) $post->ID;
	$payment_statuses = cf7pp_get_payment_statuses();
	?>

	<div class="submitbox" id="submitpost">
		<div id="minor-publishing">
			<div id="misc-publishing-actions">
				<div class="misc-pub-section misc-pub-post-status">
					<?php _e( 'Status:' ); ?>
					<span id="post-status-display"><?php echo cf7pp_get_payment_status_label($post->post_status); ?></span>
					<a href="#post_status" class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>
					<div id="post-status-select" class="hide-if-js">
						<input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ( 'auto-draft' === $post->post_status ) ? 'draft' : $post->post_status ); ?>" />
						<label for="post_status" class="screen-reader-text"><?php _e( 'Set status' ); ?></label>
						<select name="post_status" id="post_status">
							<?php foreach ($payment_statuses as $status => $value) {
								printf(
									'<option%s value="%s">%s</option>',
									selected($post->post_status, $status),
									$status,
									$value['label']
								);
							} ?>
						</select>
						<a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e( 'OK' ); ?></a>
						<a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?></a>
					</div>			
				</div>

				<?php
				if ( 0 !== $post_id ) {
					$date_string = __( '%1$s at %2$s' );
					$date_format = _x( 'M j, Y', 'publish box date format' );
					$time_format = _x( 'H:i', 'publish box time format' );
					$date = sprintf(
						$date_string,
						date_i18n( $date_format, strtotime( $post->post_date ) ),
						date_i18n( $time_format, strtotime( $post->post_date ) )
					);
				}
				?>
				<div class="misc-pub-section curtime misc-pub-curtime">
					<span id="timestamp">
						<?php printf(__('Made on: %s', 'contact-form-7'), '<b>' . $date . '</b>'); ?>
					</span>
					<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button">
						<span aria-hidden="true"><?php _e( 'Edit' ); ?></span>
						<span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span>
					</a>
					<fieldset id="timestampdiv" class="hide-if-js">
						<legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
						<?php touch_time( ( 'edit' === $action ), 1 ); ?>
					</fieldset>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<div id="major-publishing-actions">
			<div id="delete-action">
			<?php
			if ( current_user_can( 'delete_post', $post_id ) ) {
				if ( ! EMPTY_TRASH_DAYS ) {
					$delete_text = __( 'Delete permanently' );
				} else {
					$delete_text = __( 'Move to Trash' );
				}
				?>
				<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post_id ); ?>"><?php echo $delete_text; ?></a>
				<?php
			}
			?>
			</div>

			<div id="publishing-action">
				<span class="spinner"></span>
				<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ); ?>" />
				<?php submit_button( __( 'Update' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<?php
}

/**
 * Remove post row actions.
 * @since 1.8
 */
add_filter('post_row_actions', 'cf7pp_remove_payments_quick_edit', 10, 2);
function cf7pp_remove_payments_quick_edit($actions, $post) {
	if ($post->post_type != 'cf7pp_payments') return $actions;

    return array();
}

/**
 * Get custom post statuses, used for payment status.
 * @since 1.8
 * @return associative array of cf7pp payment statuses
 */
function cf7pp_get_payment_statuses() {
	$payment_statuses = array(
		'cf7pp-pending'	=> array(
			'label'				=> __('Pending', 'contact-form-7'),
			'label_count'		=> _n_noop('Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'contact-form-7')
		),
		'cf7pp-completed'	=> array(
			'label'				=> __('Completed', 'contact-form-7'),
			'label_count'		=> _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'contact-form-7')
		),
		'cf7pp-failed'	=> array(
			'label'				=> __('Failed', 'contact-form-7'),
			'label_count'		=> _n_noop('Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'contact-form-7')
		),
		'cf7pp-abandoned'	=> array(
			'label'				=> __('Abandoned', 'contact-form-7'),
			'label_count'		=> _n_noop('Abandoned <span class="count">(%s)</span>', 'Abandoned <span class="count">(%s)</span>', 'contact-form-7')
		)
	);

	return $payment_statuses;
}

/**
 * Get payment status label.
 * @since 1.8
 * @return string
 */
function cf7pp_get_payment_status_label($status) {
	$payment_statuses = cf7pp_get_payment_statuses();
	return array_key_exists($status, $payment_statuses) ? $payment_statuses[$status]['label'] : $status;
}

/**
 * Register custom payment statuses.
 * @since 1.8
 */
add_action('init', 'cf7pp_register_payment_statuses');
function cf7pp_register_payment_statuses() {
	$payment_statuses = cf7pp_get_payment_statuses();

	foreach ($payment_statuses as $payment_status => $values) {
		$values = array_merge(
			array(
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true
			),
			$values
		);

		register_post_status( $payment_status, $values );
	}
}

/**
 * Add custom post statuses in the quick edit screen of the Payments admin grid
 * @since 1.8
 */
add_action('admin_footer-edit.php', 'cf7pp_custom_post_statuses_to_quick_edit');
function cf7pp_custom_post_statuses_to_quick_edit() {
    global $post;

    if (isset($post->post_type) && $post->post_type == 'cf7pp_payments') {
    	$payment_statuses = cf7pp_get_payment_statuses();
    	$options = '<option value="-1">— No Change —</option>';
    	foreach ($payment_statuses as $payment_status => $values) {
    		$options .= '<option value="' . $payment_status . '">' . $values['label'] . '</option>';
    	}

        echo "<script>
		    	jQuery(document).ready( function($) {
		        	$('select[name=\"_status\"]').html('" . $options . "');
		    	});
    		</script>";
    }
}

/**
 * Add the custom columns for cf7pp_payments post type
 * @since 1.8
 */
add_filter( 'manage_cf7pp_payments_posts_columns', 'cf7pp_custom_edit_payments_columns' );
function cf7pp_custom_edit_payments_columns($columns) {
    unset($columns['title']);
    unset($columns['date']);

    $columns['payment_id'] = __('Payment #', 'contact-form-7');
    $columns['details'] = __('Details', 'contact-form-7');
	$columns['date'] = __('Date', 'contact-form-7');
    $columns['amount'] = __('Amount', 'contact-form-7');
    $columns['transaction_type'] = __('Transaction Type', 'contact-form-7');
	$columns['payment_status'] = __('Payment status', 'contact-form-7');

    return $columns;
}

/**
 * Add the data to the custom columns
 * @since 1.8
 */
add_action( 'manage_cf7pp_payments_posts_custom_column' , 'cf7pp_custom_edit_payments_columns_data', 99, 2 );
function cf7pp_custom_edit_payments_columns_data( $column, $post_id ) {
	switch ($column) {
		case 'payment_id':
			echo $post_id;
			break;
		case 'details':
			echo '<a href="' . get_edit_post_link($post_id) . '"><strong>View Order Details</strong></a>';

			echo '<div class="hidden" id="inline_' . $post_id . '">
					<div class="post_title">' . get_post_meta($post_id, 'transaction_id', true) . '</div>
					<div class="_status">' . get_post_status($post_id) . '</div>
				</div>';
			break;
		case 'amount':
			echo get_post_meta($post_id, 'amount', true);
			break;
		case 'transaction_type':
			$gateway = get_post_meta($post_id, 'gateway', true);
			echo strtolower($gateway) == 'paypal' ? 'PayPal' : ucfirst($gateway);
			break;
		case 'payment_status':
			$status = isset($_GET['post_status']) && $_GET['post_status'] == 'trash' ? get_post_meta($post_id, '_wp_trash_meta_status', true) : get_post_status($post_id);
			echo cf7pp_get_payment_status_label($status);
			break;
	}
}

/**
 * Remove date status
 * @since 1.8
 */
add_filter('post_date_column_status', 'cf7pp_column_date_remove_status', 10, 4);
function cf7pp_column_date_remove_status($status, $post, $column, $mode) {
	if ($post->post_type != 'cf7pp_payments' || $column != 'date') return $status;

	return '';
}

/**
 * Format column date
 * @since 1.8
 */
add_filter('post_date_column_time', 'cf7pp_column_date_format', 10, 4);
function cf7pp_column_date_format($t_time, $post, $column, $mode) {
	if ($post->post_type != 'cf7pp_payments' || $column != 'date') return $t_time;

    return get_the_date('', $post);
}

/**
 * Add custom filters - Transaction type and Payment status
 * @since 1.8
 */
add_action('restrict_manage_posts', 'filter_cf7pp_payments_by_payment_status', 10, 2);
function filter_cf7pp_payments_by_payment_status($post_type, $which) {
	// Apply this only on a 'cf7pp_payments' post type
	if ($post_type != 'cf7pp_payments') return;

	// Display Transaction type filter HTML
	$selected_gateway = isset($_GET['cf7pp_gateway']) ? sanitize_text_field($_GET['cf7pp_gateway']) : '';
	printf(
		'<select name="cf7pp_gateway" id="cf7pp_gateway" class="postform">
			<option value="">Show all Transaction types</option>
			<option value="paypal"%s>PayPal</option>
			<option value="stripe"%s>Stripe</option>
		</select>',
		$selected_gateway == 'paypal' ? ' selected="selected"' : '',
		$selected_gateway == 'stripe' ? ' selected="selected"' : ''
	);

	// Display Payment status filter HTML
	$status_field = isset($_GET['post_status']) && $_GET['post_status'] == 'trash' ? 'trash_status' : 'post_status';
	printf(
		'<select name="%s" id="post_status" class="postform">
			<option value="">Show all Payment status</option>',
		$status_field
	);

	$selected_status = isset($_GET[$status_field]) ? sanitize_text_field($_GET[$status_field]) : '';
	$payment_statuses = cf7pp_get_payment_statuses();
	foreach ($payment_statuses as $payment_status => $values) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			$payment_status,
			$selected_status == $payment_status ? ' selected="selected"' : '',
			$values['label']
		);
	}

	print('</select>');
}

/**
 * This hook will alter the main query according to the selection of cf7pp payment gateway
 * @since 1.8
 */
add_action('parse_query', 'cf7pp_payments_filter_query');
function cf7pp_payments_filter_query($query) {
    global $pagenow;

    if ($pagenow != 'edit.php' || !isset($_GET['post_type']) || $_GET['post_type'] != 'cf7pp_payments' || !isset($_GET['filter_action']) || $_GET['filter_action'] != 'Filter') return;
    
    $meta_query = array();

    if ( !empty($_GET['cf7pp_gateway']) ) {
    	$meta_query[] = array(
        	'key'     => 'gateway',
        	'value'   => sanitize_text_field($_GET['cf7pp_gateway']),
        	'compare' => '='
        );
    }

    if ( !empty($_GET['trash_status']) ) {
    	$meta_query[] = array(
        	'key'     => '_wp_trash_meta_status',
        	'value'   => sanitize_text_field($_GET['trash_status']),
        	'compare' => '='
        );
    }

    if ( !empty($meta_query) ) {
	    $query->set('meta_query', $meta_query);
	}
}

/**
 * Restore payment status when untrash post
 * @since 1.8
 */
add_filter('wp_untrash_post_status', 'cf7pp_restore_payment_status', 10, 3);
function cf7pp_restore_payment_status($new_status, $post_id, $previous_status) {
	if (get_post_type($post_id) == 'cf7pp_payments') {
		$new_status = $previous_status;
	}

	return $new_status;
}

/**
 * Remove link "Mine" on payments edit page
 * @since 1.8
 */
add_filter('views_edit-cf7pp_payments', 'cf7pp_views_remove_mine');
function cf7pp_views_remove_mine($views) {
	unset($views['mine']);
	return $views;
}