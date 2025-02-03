<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// format currency
function cf7pp_format_currency($price) {
	$price = floatval(preg_replace('/[^\d\.]/', '', $price));
	$price =number_format((float)$price, 2, '.', '');
	return $price;
}


// display activation notice
function cf7pp_my_plugin_admin_notices() {
	if (!get_option('cf7pp_my_plugin_notice_shown')) {
		echo "<div class='updated'><p><a href='admin.php?page=cf7pp_admin_table'>Click here to view the plugin settings</a>.</p></div>";
		update_option("cf7pp_my_plugin_notice_shown", "true");
	}
}
add_action('admin_notices', 'cf7pp_my_plugin_admin_notices');


// admin footer rate us link
function cf7pp_admin_rate_us( $footer_text ) {
	
	$screen = get_current_screen();

	if ($screen->base == 'contact_page_cf7pp_admin_table') {
		
		$rate_text = sprintf( __( 'Thank you for using software from <a href="%1$s" target="_blank">WP Plugin</a>! Please <a href="%2$s" target="_blank">rate us on WordPress.org</a>', '' ),
			'https://wpplugin.org',
			'https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/reviews/?filter=5'
		);
		
		return str_replace( '</span>', '', $footer_text ) . ' | ' . $rate_text . '</span>';
		
	} else {
		return $footer_text;
	}

}
add_filter( 'admin_footer_text', 'cf7pp_admin_rate_us' );

/**
 * Get plugin instalation timestamp.
 * @since 1.8
 * @return timestamp
 */
function cf7pp_get_instalation_timestamp(){
	$dir = dirname(dirname(__FILE__));
	return filectime($dir);
}

/**
 * Convert numeric currency code to ISO 4217
 * @since 1.9.4
 * @return string
 */
function cf7pp_free_currency_code_to_iso( $code ) {
	$currencies = [
		'1' => 'AUD',
		'2' => 'BRL',
		'3' => 'CAD',
		'4' => 'CZK',
		'5' => 'DKK',
		'6' => 'EUR',
		'7' => 'HKD',
		'8' => 'HUF',
		'9' => 'ILS',
		'10' => 'JPY',
		'11' => 'MYR',
		'12' => 'MXN',
		'13' => 'NOK',
		'14' => 'NZD',
		'15' => 'PHP',
		'16' => 'PLN',
		'17' => 'GBP',
		'18' => 'RUB',
		'19' => 'SGD',
		'20' => 'SEK',
		'21' => 'CHF',
		'22' => 'TWD',
		'23' => 'THB',
		'24' => 'TRY',
		'25' => 'USD'
	];

	return !empty( $currencies[$code] ) ? $currencies[$code] : 'USD';
}

/**
 * Convert numeric language code to locale code
 * @since 1.9.4
 * @return string
 */
function cf7pp_free_language_code_to_locale( $code ) {
	$languages = [
		'1' => 'da_DK',
		'2' => 'nl_BE',
		'3' => 'en_US',
		'4' => 'fr_CA',
		'5' => 'de_DE',
		'6' => 'he_IL',
		'7' => 'it_IT',
		'8' => 'ja_JP',
		'9' => 'no_NO',
		'10' => 'pl_PL',
		'11' => 'pt_BR',
		'12' => 'ru_RU',
		'13' => 'es_ES',
		'14' => 'sv_SE',
		'15' => 'zh_CN',
		'16' => 'zh_HK',
		'17' => 'zh_TW',
		'18' => 'tr_TR',
		'19' => 'th_TH',
		'20' => 'en_GB'
	];

	return !empty( $languages[$code] ) ? $languages[$code] : 'default';
}