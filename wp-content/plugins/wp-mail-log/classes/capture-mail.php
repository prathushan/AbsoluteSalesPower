<?php

namespace WML\Classes;

/**
 * Capture Mail
 *
 * This class is to manage capture mail & save to databse.
 *
 */
class Capture_Mail {

	/**
	 * This function fire on wp_mail filter in bootstrap file.
	 *
	 * This function will save the email data to database table.
	 *
	 * @access public
	 * @since 0.3
	 * @param array @var $mail_info this info comes with filter.
	 * @return array unmodified $mail_info
	 */
	public static function log_email( $mail_info ) {

		$original_mail_info = $mail_info;

		global $wpdb;
		$table_name         = $wpdb->prefix . 'wml_entries';
		$attachment_present = ( is_array( $mail_info['attachments'] ) && count( $mail_info['attachments'] ) > 0 ) ? 'true' : 'false';
		if ( is_array( $mail_info['to'] ) ) {
			$mail_to = implode( ', ', $mail_info['to'] );
		} else {
			$mail_to = $mail_info['to'];
			$parts   = explode( ',', $mail_to );
			$mail_to = implode( ', ', $parts );
		}
		$attachedFiles = [];
		if($attachment_present){
			$files = $mail_info['attachments'];

			foreach ($files as $key => $value) {
				$attachedFiles[] = substr($value,strpos($value,'/uploads/') +  strlen('/uploads/') - 1,strlen($value));
			}
		};

		// sanitize email
		if(is_array($mail_to)){
			$mail_to = self::sanitize_to_email($mail_to);
		} else if(strpos($mail_to, ',') !== false){
			$mail_to = explode(',', $mail_to);
			$mail_to = self::sanitize_to_email($mail_to);
		} else {
			$mail_to = self::sanitize_to_email([$mail_to]);
		}

		// implode email
		$mail_to = implode(', ', $mail_to);

		$mail_info['subject'] = sanitize_text_field($mail_info['subject']);
		
		$allowed_html = wp_kses_allowed_html('post');
		$allowed_html['style'] = [];
		//print_r($allowed_html); die();
		// sanitize message but allow style tags
		$mail_info['message'] = wp_kses($mail_info['message'], $allowed_html);

		// Log into the database
		
		$wpdb->insert(
			$table_name,
			[
				'to_email'     => $mail_to,
				'subject'      => $mail_info['subject'],
				'message'      => $mail_info['message'],
				'headers'      => $mail_info['headers'],
				'attachments'  => $attachment_present,
				'sent_date'    => current_time( 'mysql', $gmt = 0 ),
				'captured_gmt' => current_time( 'mysql', $gmt = 1 ),
				'attachments_file' => implode(',', $attachedFiles),
			]
		);

		// return unmodifiyed array
		return $original_mail_info;
	}

	public static function sanitize_to_email( $array ) {
		
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = self::sanitize_to_email( $value );
			} else {
				$value = sanitize_email( $value );
			}
		}
		
		return $array;
	}
}
