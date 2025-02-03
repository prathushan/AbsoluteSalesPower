<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function cf7pp_free_ppcp_order_create( $ppcp_status, $name, $price, $id, $currency, $payment_id, $return_url, $cancel_url ) {
	if ( empty( $ppcp_status['seller_id'] ) ) {
		die( __( 'An error occurred.' ) );
	}

	if ( empty( $price ) ) {
		die( __( 'Price can\'t be empty.' ) );
	}

	$return_url_args = [
		'action' => 'ppcp-finalize',
		'nonce' => wp_create_nonce( 'cf7pp-free-frontend-request' ),
		'env' => $ppcp_status['env'],
		'seller_id' => $ppcp_status['seller_id']
	];

	$body = [
		'env' => $ppcp_status['env'],
		'seller_id' => $ppcp_status['seller_id'],
		'items' => [
			[
				'name' => $name,
				'sku' => $id,
				'price' => $price,
			]
		],
		'currency' => $currency,
		'return_url' => add_query_arg(
			$return_url_args,
			( !empty( $return_url ) ? $return_url : wp_get_referer() )
		),
		'cancel_url' => add_query_arg(
			[
				'ppcp' => 'cancel'
			],
			( !empty( $cancel_url ) ? $cancel_url : wp_get_referer() )
		)
	];

	$response = wp_remote_post(
		CF7PP_FREE_PPCP_API . 'create-order',
		[
			'timeout' => 60,
			'body' => $body
		]
	);

	$body = wp_remote_retrieve_body( $response );
	$response = json_decode( $body, true );

	if ( empty( $response['success'] ) || empty( $response['payer_action_url'] ) ) {
		die( !empty( $response['message'] ) ? $response['message'] : __( "Can't create an order." ) );
	}

	update_post_meta( $payment_id, '_cf7pp_ppcp_order_id', $response['order_id'] );

	wp_redirect( $response['payer_action_url'] );
	die();
}

add_action( 'wp', 'cf7pp_free_ppcp_order_finalize' );
function cf7pp_free_ppcp_order_finalize() {
	if ( !empty( $_GET['action'] ) && $_GET['action'] === 'ppcp-finalize' ) {
		global $wpdb;

		if ( !wp_verify_nonce( $_GET['nonce'], 'cf7pp-free-frontend-request' ) ) {
			die( __( 'The request has not been authenticated. Please reload the page and try again.' ) );
		}

		$paypal_order_id = sanitize_text_field( $_GET['token'] );

		$env = sanitize_text_field( $_GET['env'] );
		$seller_id = sanitize_text_field( $_GET['seller_id'] );

		$response = wp_remote_post(
			CF7PP_FREE_PPCP_API . 'finalize-order',
			[
				'timeout' => 60,
				'body' => [
					'env' => $env,
					'seller_id' => $seller_id,
					'order_id' => $paypal_order_id
				]
			]
		);

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		$payment_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_cf7pp_ppcp_order_id' AND meta_value=%s", $paypal_order_id ) );
		if ( !empty( $payment_id ) ) {
			$payer_email = !empty( $data['payer_email'] ) ? $data['payer_email'] : '';
			$payment_status = !empty( $data['success'] ) ? 'completed' : 'failed';
			cf7pp_complete_payment( $payment_id, $payment_status, $data['transaction_id'], $payer_email );
		}

		$redirect_url = remove_query_arg( ['action', 'nonce', 'env', 'seller_id', 'intent', 'token', 'PayerID', 'ppcp'] );
		$redirect_url = add_query_arg( 'ppcp', ( empty( $data['success'] ) ? 'fail' : 'success' ), $redirect_url );

		wp_redirect( $redirect_url );
		die();
	}
}

add_filter( 'wpcf7_form_elements', 'cf7pp_free_ppcp_message' );
function cf7pp_free_ppcp_message( $output ) {
	$ppcp_message = '';
	if ( !empty( $_GET['ppcp'] ) && in_array( $_GET['ppcp'], ['success', 'fail', 'cancel'] ) ) {
		$ppcp_message .= '<style>';
		$ppcp_message .= '.wpcf7-form .cf7pp-ppcp-message{margin:1em 0 3em;padding:0.2em 1em;border:2px solid;}';
		$ppcp_message .= '.wpcf7-form .cf7pp-ppcp-message-success{border-color:#46b450}';
		$ppcp_message .= '.wpcf7-form .cf7pp-ppcp-message-fail{border-color:#dc3232}';
		$ppcp_message .= '.wpcf7-form .cf7pp-ppcp-message-cancel{border-color:#ffb900}';
		$ppcp_message .= '</style>';
		if ( $_GET['ppcp'] === 'success' ) {
			$ppcp_message .= '<div class="cf7pp-ppcp-message cf7pp-ppcp-message-success">' . __( 'Transaction completed!' ) . '</div>';
		} elseif ( $_GET['ppcp'] === 'fail' ) {
			$ppcp_message .= '<div class="cf7pp-ppcp-message cf7pp-ppcp-message-fail">' . __( 'An unknown error occurred while completing the order. Please reload the page and try again.' ) . '</div>';
		} elseif ( $_GET['ppcp'] === 'cancel' ) {
			$ppcp_message .= '<div class="cf7pp-ppcp-message cf7pp-ppcp-message-cancel">' . __( 'The payment was canceled on the PayPal side.' ) . '</div>';
		}
	}

	return $ppcp_message . $output;
}