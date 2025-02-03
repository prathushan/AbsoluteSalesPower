<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// display other plugins page
function cf7pp_extensions_page()	{
	setlocale( LC_MONETARY, get_locale() );
	$extensions     = cf7pp_get_extensions();
	$extensions 	= array_reverse($extensions);
	$tags           = '<a><em><strong><blockquote><ul><ol><li><p>';
	$length         = 55;

	?>
	<div class="wrap about-wrap cf7pp-about-wrapp">
		<h3>
			<?php _e( 'You may be interested in our other popular WordPress plugins:', 'cf7pp' ); ?>
		</h3>
			Make your site <i><b> do more </b></i> today.
		
		<div class="cf7pp-extension-wrapper grid3">
			<?php foreach ( $extensions as $key => $extension ) :
				$the_excerpt = '';
				$slug        = $extension->info->slug;
				$price       = false;
				$link        = 'https://wpplugin.org/downloads/' . $slug .'/';
				$link        = esc_url( add_query_arg( array(
					'utm_source'   => 'plugin-extensions-page',
					'utm_medium'   => 'plugin',
					'utm_campaign' => 'cf7pp_extensions_page',
					'utm_content'  => $extension->info->title
				), $link ) );
				
				if ( ! empty( $extension->info->excerpt ) ) {
					$the_excerpt = $extension->info->excerpt;
				}
				
				$the_excerpt   = strip_shortcodes( strip_tags( stripslashes( $the_excerpt ), $tags ) );
				$the_excerpt   = preg_split( '/\b/', $the_excerpt, $length * 2+1 );
				$excerpt_waste = array_pop( $the_excerpt );
				$the_excerpt   = implode( $the_excerpt ); ?>
				
                <article class="col">
                    <div class="cf7pp-extension-item">
                        <div class="cf7pp-extension-item-img">
                            <a href="<?php echo $link; ?>" target="_blank"><img src="<?php echo $extension->info->thumbnail; ?>" /></a>
                        </div>
                        <div class="cf7pp-extension-item-desc">
                            <p class="cf7pp-extension-item-heading"><?php echo $extension->info->title; ?></p>
                            <div class="cf7pp-extension-item-excerpt">
                            	<p><?php echo $the_excerpt; ?></p>
                            </div>
                            <div class="cf7pp-extension-buy-now">
                                    <a href="<?php echo $link; ?>" class="button-primary" target="_blank"><?php echo __( 'Learn More', 'cf7pp' ); ?></a>
                            </div>
                        </div>
                    </div>
                </article>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

}


/* Retrieve the published extensions from wpplugin.org and store within transient. */
function cf7pp_get_extensions()	{
	$extensions = get_transient( '_cf7pp_extensions_feed' );

	if ( false === $extensions || doing_action( 'cf7pp_daily_scheduled_events' ) ) {
		$route    = esc_url( 'https://wpplugin.org/edd-api/v2/products/?category=CF7' );
		$number   = 20;
		$endpoint = add_query_arg( array( 'number' => $number ), $route );
		$response = wp_remote_get( $endpoint );
		
		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			$body    = wp_remote_retrieve_body( $response );
			$content = json_decode( $body );
			
			if ( is_object( $content ) && isset( $content->products ) ) {
				set_transient( '_cf7pp_extensions_feed', $content->products, DAY_IN_SECONDS / 2 ); // Store for 24 hours
				$extensions = $content->products;
			}
		}
	}

	return $extensions;
}
add_action( 'cf7pp_daily_scheduled_events', 'cf7pp_get_extensions' );
