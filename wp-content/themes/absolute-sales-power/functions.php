<?php
/**
 * absolute sales power functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package absolute sales power
 * @since absolute sales power 1.0
 */
/**
 * Register block styles.
 */
if (! function_exists('absolute_sales_power_block_styles')) :
    /**
     * Register custom block styles
     *
     * @since absolute sales power 1.0
     * @return void
     */
    function absolute_sales_power_block_styles()
    {
        register_block_style(
            'core/details',
            array(
                'name'         => 'arrow-icon-details',
                'label'        => __('Arrow icon', 'absolute-sales-power'),
                /*
				 * Styles for the custom Arrow icon style of the Details block
				 */
                'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}
				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}
				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
            )
        );
        register_block_style(
            'core/post-terms',
            array(
                'name'         => 'pill',
                'label'        => __('Pill', 'absolute-sales-power'),
                /*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
                'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}
				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
            )
        );
        register_block_style(
            'core/list',
            array(
                'name'         => 'checkmark-list',
                'label'        => __('Checkmark', 'absolute-sales-power'),
                /*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
                'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}
				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
            )
        );
        register_block_style(
            'core/navigation-link',
            array(
                'name'         => 'arrow-link',
                'label'        => __('With arrow', 'absolute-sales-power'),
                /*
				 * Styles for the custom arrow nav link block style
				 */
                'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
            )
        );
        register_block_style(
            'core/heading',
            array(
                'name'         => 'asterisk',
                'label'        => __('With asterisk', 'absolute-sales-power'),
                'inline_style' => "
				.is-style-asterisk:before {
					content: '';
					width: 1.5rem;
					height: 3rem;
					background: var(--wp--preset--color--contrast-2, currentColor);
					clip-path: path('M11.93.684v8.039l5.633-5.633 1.216 1.23-5.66 5.66h8.04v1.737H13.2l5.701 5.701-1.23 1.23-5.742-5.742V21h-1.737v-8.094l-5.77 5.77-1.23-1.217 5.743-5.742H.842V9.98h8.162l-5.701-5.7 1.23-1.231 5.66 5.66V.684h1.737Z');
					display: block;
				}
				/* Hide the asterisk if the heading has no content, to avoid using empty headings to display the asterisk only, which is an A11Y issue */
				.is-style-asterisk:empty:before {
					content: none;
				}
				.is-style-asterisk:-moz-only-whitespace:before {
					content: none;
				}
				.is-style-asterisk.has-text-align-center:before {
					margin: 0 auto;
				}
				.is-style-asterisk.has-text-align-right:before {
					margin-left: auto;
				}
				.rtl .is-style-asterisk.has-text-align-left:before {
					margin-right: auto;
				}",
            )
        );
    }
endif;
add_action('init', 'absolute_sales_power_block_styles');
/**
 * Enqueue block stylesheets.
 */
if (! function_exists('absolute_sales_power_block_stylesheets')) :
    /**
     * Enqueue custom block stylesheets
     *
     * @since absolute sales power 1.0
     * @return void
     */
    function absolute_sales_power_block_stylesheets()
    {
        /**
         * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
         * for a specific block. These will only get loaded when the block is rendered
         * (both in the editor and on the front end), improving performance
         * and reducing the amount of data requested by visitors.
         *
         * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
         */
        wp_enqueue_block_style(
            'core/button',
            array(
                'handle' => 'absolute-sales-power-button-style-outline',
                'src'    => get_parent_theme_file_uri('assets/css/button-outline.css'),
                'ver'    => wp_get_theme(get_template())->get('Version'),
                'path'   => get_parent_theme_file_path('assets/css/button-outline.css'),
            )
        );
    }
endif;
add_action('init', 'absolute_sales_power_block_stylesheets');
/**
 * Register pattern categories.
 */
if (! function_exists('absolute_sales_power_pattern_categories')) :
    /**
     * Register pattern categories
     *
     * @since absolute sales power 1.0
     * @return void
     */
    function absolute_sales_power_pattern_categories()
    {
        register_block_pattern_category(
            'page',
            array(
                'label'       => _x('Pages', 'Block pattern category'),
                'description' => __('A collection of full page layouts.'),
            )
        );
    }
endif;
add_action('init', 'absolute_sales_power_pattern_categories');
//To kept the client logo in the login page
function custom_login_logo()
{
?>
    <style type="text/css">
        #login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Absolute-Sales-Power-logo.png');
            background-size: contain;
            width: 100%;
            height: 100px;
        }
    </style>
    <?php
}
function child_styles()
{
    wp_enqueue_style('child-theme-css', get_stylesheet_directory_uri() . '/style.css', []);
    wp_enqueue_script('theme', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);
    //     wp_localize_script('theme', 'Theme', array('ajaxurl' => admin_url('admin-ajax.php')));
}
	add_action('wp_enqueue_scripts', 'child_styles', 9999);
add_filter('jetpack_implode_frontend_css', '__return_false', 99);
add_filter('jetpack_sharing_counts', '__return_false', 99);
add_filter('style_loader_src', 'theme_remove_wp_ver_css_js', 9999);
// remove Wp version from scripts
add_filter('script_loader_src', 'theme_remove_wp_ver_css_js', 9999);
//  remove WP version from scripts
function theme_remove_wp_ver_css_js($src)
{
    if (strpos($src, 'ver='))
        $src = remove_query_arg('ver', $src);
    return $src;
}
if (! function_exists('theme_login_url')) {
    // Define the 'theme_login_url' function
    function theme_login_url()
    {
        return home_url();
    }
    // Calling the functions only on the login page
    add_filter('login_headerurl', 'theme_login_url');
}
// Check if the function 'theme_login_title' does not already exist
if (! function_exists('theme_login_title')) {
    // Define the 'theme_login_title' function
    function theme_login_title()
    {
        return get_option('blogname');
    }
    add_filter('login_headertext', 'theme_login_title');
}
// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');
// Remove wlwmanifest link
function remove_wlwmanifest_link()
{
    remove_action('wp_head', 'wlwmanifest_link');
    // Remove RSD link
    remove_action('wp_head', 'rsd_link');
    // Remove oEmbed JSON link
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11, 0);
    add_theme_support('block-template-parts');
}
add_action('init', 'remove_wlwmanifest_link');
add_filter('wpcf7_autop_or_not', '__return_false');
//Registered Users
function create_registration_post_type()
{
    register_post_type(
        'registration',
        array(
            'labels' => array(
                'name' => __('Registrations'),
                'singular_name' => __('Registration'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Registration'),
                'edit_item' => __('Edit Registration'),
                'new_item' => __('New Registration'),
                'view_item' => __('View Registration'),
                'all_items' => __('All Registrations'),
                'search_items' => __('Search Registrations'),
                'not_found' => __('No registrations found'),
                'not_found_in_trash' => __('No registrations found in Trash'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'custom-fields'), // Custom fields for name/email
            'show_in_rest' => true, // This enables Gutenberg editor support
            'menu_icon' => 'dashicons-admin-users',
        )
    );
}
add_action('init', 'create_registration_post_type');
function save_registration_data($contact_form)
{
    // Get submission data
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $data = $submission->get_posted_data();
        // Get form title
        $form_title = $contact_form->title();
        // Check if the form title matches (replace 'Your Form Title' with your actual form title)
        if ($form_title === 'registration-form') { // Use the form title for identification
            $name = isset($data['your-name']) ? sanitize_text_field($data['your-name']) : '';
            $email = isset($data['your-email']) ? sanitize_email($data['your-email']) : '';
            // Create the registration post
            $post_id = wp_insert_post(array(
                'post_type' => 'registration',
                'post_title' => $name, // Use name as the post title
                'post_status' => 'publish',
            ));
            // Save custom fields (name and email) as post meta
            if ($post_id) {
                update_post_meta($post_id, 'Name', $name);
                update_post_meta($post_id, 'Email', $email);
            }
        }
    }
}
add_action('wpcf7_before_send_mail', 'save_registration_data');
// Add custom columns to the 'registration' post type
function add_registration_columns($columns)
{
    return array_merge(
        $columns,
        array(
            'name'  => __('Name'),
            'email' => __('Email'),
        )
    );
}
add_filter('manage_registration_posts_columns', 'add_registration_columns');
// Display the custom columns' data
function display_registration_columns($column, $post_id)
{
    switch ($column) {
        case 'name':
            echo get_post_meta($post_id, 'Name', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'Email', true);
            break;
    }
}
add_action('manage_registration_posts_custom_column', 'display_registration_columns', 10, 2);
function add_registration_export_button()
{
    $screen = get_current_screen();
    if ($screen->post_type === 'registration') {
    ?>
        <div class="wrap">
            <form method="post" action="" style="display: inline-block; margin-left: 230px; position:absolute;margin-top:8px;">
                <input type="hidden" name="export_csv" value="1" />
                <input type="submit" class="button button-primary" value="Export CSV" />
            </form>
        </div>
    <?php
    }
}
add_action('admin_notices', 'add_registration_export_button');
function handle_csv_export()
{
    if (isset($_POST['export_csv'])) {
        // Get the registrations
        $args = array(
            'post_type' => 'registration',
            'posts_per_page' => -1
        );
        $registrations = new WP_Query($args);
        if ($registrations->have_posts()) {
            // Clean output buffer to prevent header issues
            if (ob_get_length()) {
                ob_end_clean();
            }
            // Set the headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="registrations.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            $output = fopen('php://output', 'w');
            // Set the CSV column headers
            fputcsv($output, array('Name', 'Email'));
            // Loop through the registrations and output each one
            while ($registrations->have_posts()) {
                $registrations->the_post();
                $name = get_post_meta(get_the_ID(), 'Name', true);
                $email = get_post_meta(get_the_ID(), 'Email', true);
                fputcsv($output, array($name, $email));
            }
            fclose($output);
            wp_reset_postdata();
            exit; // Ensure script ends after output
        } else {
            wp_die('No registrations found.');
        }
    }
}
add_action('admin_init', 'handle_csv_export');
// Register the 'Let's Talk' post type
function create_lets_talk_post_type()
{
    register_post_type(
        'lets_talk',
        array(
            'labels' => array(
                'name' => __('Let\'s Talk'),
                'singular_name' => __('Let\'s Talk Submission'),
                'add_new' => __('Add New'),
                'add_new_item' => __('Add New Submission'),
                'edit_item' => __('Edit Submission'),
                'new_item' => __('New Submission'),
                'all_items' => __('All Submissions'),
                'search_items' => __('Search Submissions'),
                'not_found' => __('No submissions found'),
                'not_found_in_trash' => __('No submissions found in Trash'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'custom-fields'), // Custom fields for form data
            'show_in_rest' => true, // Enable Gutenberg editor support
            'menu_icon' => 'dashicons-phone',
            'publicly_queryable' => false,
            'exclude_from_search' => true,
        )
    );
}
add_action('init', 'create_lets_talk_post_type');
// Save 'Let's Talk' form data
function save_lets_talk_form_data($contact_form)
{
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $data = $submission->get_posted_data();
        $form_title = $contact_form->title();
        if ($form_title === 'lets-talk-form') {
            $name = isset($data['your-name']) ? sanitize_text_field($data['your-name']) : '';
            $email = isset($data['your-email']) ? sanitize_email($data['your-email']) : '';
            $phone = isset($data['your-phone']) ? sanitize_text_field($data['your-phone']) : '';
            $company = isset($data['your-company']) ? sanitize_text_field($data['your-company']) : '';
            $training_method = isset($data['checkbox-296']) ? implode(', ', $data['checkbox-296']) : '';
            $message = isset($data['your-message']) ? sanitize_textarea_field($data['your-message']) : '';
            $post_id = wp_insert_post(array(
                'post_type' => 'lets_talk',
                'post_title' => $name,
                'post_status' => 'publish',
            ));
            if ($post_id) {
                update_post_meta($post_id, 'Full Name', $name);
                update_post_meta($post_id, 'Email', $email);
                update_post_meta($post_id, 'Phone', $phone);
                update_post_meta($post_id, 'Company', $company);
                update_post_meta($post_id, 'Training Method', $training_method);
                update_post_meta($post_id, 'Message', $message);
            }
        }
    }
}
add_action('wpcf7_before_send_mail', 'save_lets_talk_form_data');
// Add custom columns for 'Let's Talk' post type
function add_lets_talk_columns($columns)
{
    return array_merge($columns, array(
        'name'  => __('Full Name'),
        'email' => __('Email'),
        'phone' => __('Phone Number'),
        'company' => __('Company Name'),
        'training_method' => __('Preferred Training Method'),
    ));
}
add_filter('manage_lets_talk_posts_columns', 'add_lets_talk_columns');
// Display custom columns data
function display_lets_talk_columns($column, $post_id)
{
    switch ($column) {
        case 'name':
            echo get_post_meta($post_id, 'Full Name', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'Email', true);
            break;
        case 'phone':
            echo get_post_meta($post_id, 'Phone', true);
            break;
        case 'company':
            echo get_post_meta($post_id, 'Company', true);
            break;
        case 'training_method':
            echo get_post_meta($post_id, 'Training Method', true);
            break;
    }
}
add_action('manage_lets_talk_posts_custom_column', 'display_lets_talk_columns', 10, 2);
// Add export button for 'Let's Talk' post type after the filters
function add_lets_talk_export_button()
{
    $screen = get_current_screen();
    if ($screen->post_type === 'lets_talk') {
    ?>
        <form method="post" action="" style="display: inline-block; margin-left: 200px; position:absolute;margin-top:19px;">
            <input type="hidden" name="export_csv_lets_talk" value="1" />
            <input type="submit" class="button button-primary" value="Export CSV" />
        </form>
<?php
    }
}
add_action('admin_notices', 'add_lets_talk_export_button');
// Handle CSV export for 'Let's Talk'
function handle_lets_talk_csv_export()
{
    if (isset($_POST['export_csv_lets_talk'])) {
        $args = array(
            'post_type' => 'lets_talk',
            'posts_per_page' => -1
        );
        $submissions = new WP_Query($args);
        if ($submissions->have_posts()) {
            if (ob_get_length()) {
                ob_end_clean();
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="lets_talk_submissions.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
            $output = fopen('php://output', 'w');
            // Set CSV column headers
            fputcsv($output, array('Full Name', 'Email', 'Phone', 'Company', 'Training Method', 'Message'));
            while ($submissions->have_posts()) {
                $submissions->the_post();
                $name = get_post_meta(get_the_ID(), 'Full Name', true);
                $email = get_post_meta(get_the_ID(), 'Email', true);
                $phone = get_post_meta(get_the_ID(), 'Phone', true);
                $company = get_post_meta(get_the_ID(), 'Company', true);
                $training_method = get_post_meta(get_the_ID(), 'Training Method', true);
                $message = get_post_meta(get_the_ID(), 'Message', true);
                fputcsv($output, array($name, $email, $phone, $company, $training_method, $message));
            }
            fclose($output);
            wp_reset_postdata();
            exit;
        } else {
            wp_die('No submissions found.');
        }
    }
}
add_action('admin_init', 'handle_lets_talk_csv_export');
function custom_login_captcha()
{
    if (!session_id()) {
        session_start();
    }
    echo '<p class="captcha-label">';
    echo '<label for="captcha_result">Enter the text shown:</label>';
    echo '<img src="' . esc_url(get_template_directory_uri() . '/captcha_image.php') . '" alt="CAPTCHA" class="captcha-image" />';
    echo '</p>';
    echo '<p>';
    echo '<input type="text" name="captcha_result" id="captcha_result" class="captcha-input" placeholder="Enter CAPTCHA here" />';
    echo '</p>';
}
add_action('login_form', 'custom_login_captcha');
function enqueue_theme_styles()
{
    wp_enqueue_style('theme-styles', get_stylesheet_uri());
	  wp_enqueue_script('main-js',  get_template_directory_uri() . '/assets/js/main.js',   array('jquery'),   '1.0', true);
	    wp_enqueue_style('child-theme-css', get_stylesheet_directory_uri() . '/style.css', []);
  			wp_enqueue_script( 'main-js',get_template_directory_uri() . '/assets/js/main.js',array('jquery'),'1.0', true );
			 wp_enqueue_script('highlight-text', get_template_directory_uri() . '/assets/js/main.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_theme_styles');
