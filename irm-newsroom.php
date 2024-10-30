<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://irmau.com
 * @since             1.0.0
 * @package           Irm_Newsroom
 *
 * @wordpress-plugin
 * Plugin Name:       IRM Newsroom
 * Plugin URI:        http://www.irmnewsroom.com/
 * Description:       IRM Newsroom is an ASX announcements, news and social media distribution service, which enables companies to easily communicate with investors and other stakeholders across multiple online channels – including website, email subscriptions and social media channels.
 * Version:           1.2.16
 * Author:            IRM
 * Author URI:        http://irmau.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       irm-newsroom
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-irm-newsroom-activator.php
 */
function activate_irm_newsroom() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-irm-newsroom-activator.php';
	Irm_Newsroom_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-irm-newsroom-deactivator.php
 */
function deactivate_irm_newsroom() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-irm-newsroom-deactivator.php';
	Irm_Newsroom_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_irm_newsroom' );
register_deactivation_hook( __FILE__, 'deactivate_irm_newsroom' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-irm-newsroom.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */


function irm_unsubscribe_page() {

	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );

	$emailunsub_landing_page = esc_attr( get_option('emailunsub_landing_page') );
	if(!$emailunsub_landing_page > "") {
		$emailunsub_landing_page = "/unsubscribed/";
	}

	$script_url = $site_type . "://" . $site_key . "/" . $site_directory . "/js/Newsroom.js";
	$unsubscribe_url = $site_type . "://" . $site_key . "/" . $site_directory . "/data/UnsubscribeForm.aspx";

	$out = '<script type="text/javascript" src="'.$script_url.'"></script><div data-unsubscribeformsurl="'.$unsubscribe_url.'" data-gotourl="'.$emailunsub_landing_page.'">..</div>';
	return $out;

}
add_shortcode('irm_unsubscribe_form', 'irm_unsubscribe_page'); /* shortcode [irm_unsubscribe_form] for irm_unsubscribe_page */

function iguana_js() {
    /* echo '<script src="https://quoteapi.com/lib/1.8.5/quoteapi-loader.js" integrity="sha256-Zs2jee5Cu9XOmK67dVQJDI5LqiV+faelNQm8OyslG6s= sha512-lgVikkbStJeoqvs4NNkrxcnQZM5q2WZDvD71Lo8c7F7AKW4/X/5iKuZVErv/gPS/4VdoBH642y+SHtiZA+B2ag==" crossorigin="anonymous"></script>'; */
	echo '<script src="https://quoteapi.com/lib/1.15.7/quoteapi-loader.js" integrity="sha256-kJqBnp944BwFlkXp7kYJrarrpXTrVSCO7R8i2eKkuf4= sha512-srIP/oXEtvnO/K5vuXdAS4Zjfu7bUWoQSyogRuy59E4P7TfhBebPsrjWkkonIRrXwyzH1xVVr6VZrnK58mY8RA==" crossorigin="anonymous"></script>';

	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );
	$script_url = $site_type . "://" . $site_key . "/" . $site_directory . "/content/js/quoteapi.js";
    echo '<script src="'.$script_url.'"></script>';
    echo '<link rel="stylesheet" href="https://js.irmau.com/shareprice/shareprice.css">';
}

function run_irm_newsroom() {
	$plugin = new Irm_Newsroom();
	$plugin->run();

	$share_price_toggle = esc_attr( get_option('share_price_toggle') );
	if($share_price_toggle == "on") {
		add_action('wp_head', 'iguana_js', 1);
	}
}
run_irm_newsroom();

/** Step 2 (from text above). */
add_action( 'admin_menu', 'irm_newsroom_menu' );

/** Step 1. */
function irm_newsroom_menu() {
	add_options_page( 'IRM Newsroom Options', 'IRM Newsroom', 'manage_options', 'irm-newsroom', 'irm_newsroom_options' );
	add_action( 'admin_init', 'irm_newsroom_settings' );
}

function irm_newsroom_settings() {
	//register our settings
	register_setting( 'irm-newsroom-group', 'site_key' );
	register_setting( 'irm-newsroom-group', 'site_type' );
	register_setting( 'irm-newsroom-group', 'site_directory' );
	register_setting( 'irm-newsroom-group', 'email_landing_page' );
	register_setting( 'irm-newsroom-group', 'emailunsub_landing_page' );
	register_setting( 'irm-newsroom-group', 'share_price_toggle' );
}

/** Step 3. */
function irm_newsroom_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	// short-code-list

	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );
	$email_landing_page = esc_attr( get_option('email_landing_page') );
	$share_price_toggle = esc_attr( get_option('share_price_toggle') );
	$emailunsub_landing_page = esc_attr( get_option('emailunsub_landing_page') );

	if(!$share_price_toggle > "") {
		$share_price_toggle = "off";
	}

	if(!$site_type > "") {
		$site_type = "https";
	}

	if(!$site_key > "") {
		$site_key = "www.irmau.com";
	}

	if(!$site_directory > "") {
		$site_directory = "";
	}

	if(!$email_landing_page > "") {
		$email_landing_page = "/email-alerts-success/";
	}

	if(!$emailunsub_landing_page > "") {
		$emailunsub_landing_page = "/unsubscribed/";
	}

	$irm_shortcodes_list = "";
	$irm_shortcodes_list = get_data("$site_type://$site_key/$site_directory/SiteData.aspx?DataType=ListPage");

	$irm_shortcodes_flat = "";
	$irm_shortcodes_flat = get_data("$site_type://$site_key/$site_directory/SiteData.aspx?DataType=FlatPage");

	$irm_events_list = "";
	$irm_events_list = get_data("$site_type://$site_key/$site_directory/sitedata.aspx?DataType=CalendarViewPage");

	$irm_styles = "
	<style>
	.irm-trial a {
		color:#FFF
	}
	.irm-trial {
	    border-radius: 3px;
	    background: linear-gradient(#f5822a,#f15a2a);
	    padding: 12px;
	    font-size: 18px;
	    color: #FFF;
	    font-weight: bold;
	    text-shadow: 0px 1px 2px rgba(181, 108, 53, 0.82);
	    border: 1px solid rgb(241, 90, 42);
	    text-align: center;
	}
	</style>
	";

	echo $irm_styles;

	echo '<div class="wrap">';
	// echo '<p><img src="http://www.irmau.com/irm/showmedia.aspx?MediaId=1" style="background:#FFFFFF;border-radius:20px;padding:10px 50px;width:100px;"></p>';
	echo '<p class="irm-trial">If you\'d like to organise a free trial of IRM Newsroom, <a href="http://irmau.com/site/websites/newsroom-trial" target="_blank">please click here</a>.</p>';

	if( isset( $_GET[ 'tab' ] ) ) {
	    $active_tab = $_GET[ 'tab' ];
	} else {
		$active_tab = "configure";
	}

	echo $irm_tabs = '<h2 class="nav-tab-wrapper" id="irm-newsroom-tabs">
    <a href="?page=irm-newsroom&tab=configure" class="nr-configure nav-tab">Configure IRM Newsroom</a>
    <a href="?page=irm-newsroom&tab=irmevents" class="nr-irmevents nav-tab">IRM Events</a>
    <a href="?page=irm-newsroom&tab=list" class="nr-list nav-tab">List Page Shortcodes</a>
    <a href="?page=irm-newsroom&tab=flat" class="nr-flat nav-tab">HQi Featured Pages</a>
    <a href="?page=irm-newsroom&tab=shareprice" class="nr-shareprice nav-tab">Shareprice</a>
    <a href="?page=irm-newsroom&tab=menu" class="nr-menu nav-tab">Menu</a>
    <a href="?page=irm-newsroom&tab=events" class="nr-events nav-tab">Events Calendar</a>
	</h2>';

	echo '
	<script>
	(function($) {
		var urlParams = new URLSearchParams(location.search)
		var tab = urlParams.get("tab");
		console.log("irm newsroom. " + tab);
		if(tab > "") {
			$("#irm-newsroom-tabs .nr-"+tab).addClass("nav-tab-active");
		} else {
			$("#irm-newsroom-tabs .nr-configure").addClass("nav-tab-active");
		}
	})( jQuery );
	</script>
	';

	if( ($active_tab == "") || ($active_tab == "configure") ) {

		echo '<h2>Configure IRM Newsroom Below</h2>';
		echo '<p>';
		echo '<label><b>Site Key:</b> &nbsp;';
		echo '<p>You will need a site key to link your wordpress website to the IRM Newsroom software to enable your feeds.</p>';
		echo '<p><i>If you dont have a site key, please <a href="http://www.irmhelpcentre.com/irm/content/contact-support.aspx?RID=333" target="_blank">request one from here</a>. </i></p>';
		echo '<form method="post" action="options.php">';
		echo '<input type="text" name="site_type" value="'.$site_type.'" maxlength="5" placeholder="http/s" />';
		echo '://';
		echo '<input type="text" name="site_key" value="'.$site_key.'" placeholder="Site URL" />';
		echo ' / <input type="text" name="site_directory" value="'.$site_directory.'" placeholder="Site Directory" /> / ';

		echo '<h3>Email Alerts</h3>';
		echo '<p>To show the Email Alerts Signup Form please copy and paste the shortcode below to a page on your website: </p>';
		echo '<pre><code>[email_alerts_form]</code></pre>';
		echo '<p><label>Email Alerts Success Page: </label> <input type="text" name="email_landing_page" value="'.$email_landing_page.'" /> *</p>';
		echo '<p><small>* Please note that this should be a full URL including your domain name, e.g: https://irmau.com/</small></p>';
		echo '<h3>Email Alerts Unsubscribe</h3>';
		echo '<p>To allow subscribers to unsubscribe from email alerts add a link to the following page:</p>';
		echo '<pre><code>http://'.$site_key.'/'.$site_directory.'/Unsubscribe.aspx</code></pre>';
		echo '<p>or you can add the following shortcode to a page or widget</p>';
		echo '<pre><code>[irm_unsubscribe_form]</code></pre>';
		echo '<p>This will redirect them to the following page after un-subscribing:</p>';
		echo '<p><label>Email Alerts Unsubscribe Success Page: </label> <input type="text" name="emailunsub_landing_page" value="'.$emailunsub_landing_page.'" /> *</p>';
		echo '<p><small>* Please note that this should be a full URL including your domain name, e.g: https://irmau.com/</small></p>';

		echo '<h3>Shareprice</h3>';
		echo '<p>Toggle shareprice script in site header, if this is set to <b>on</b> this will insert the shareprice javascript in the header of this site for all pages. Do not enable this if you have manually added the scripts to the header.</p>';
		//echo '$share_price_toggle:' . $share_price_toggle . '<br>';
		echo '<select name="share_price_toggle">';

		if($share_price_toggle == "off") {
			echo '<option value="off" selected>off</option>';
			echo '<option value="on">on</option>';
		} else {
			echo '<option value="off">off</option>';
			echo '<option value="on" selected>on</option>';
		}
		echo '</select>';

		settings_fields( 'irm-newsroom-group' );
	  do_settings_sections( 'irm-newsroom-group' );
	  submit_button();

		echo '</form></p>';
		echo '<p><b>For testing use: <code>www.irmau.com</code></b></p>';

	}

	if( $active_tab == "irmevents" ) {

		$irm_events_list = "";
		$irm_events_list = get_data("$site_type://$site_key/$site_directory/sitedata.aspx?DataType=EventListPage");
		if($irm_events_list <= "") {
			$irm_events_list = "<p>
				No Events Found
			</p>";
		}
		$irm_events_reg = "";
		$irm_events_reg = get_data("$site_type://$site_key/$site_directory/sitedata.aspx?DataType=EventRegistrationPage");
		if($irm_events_reg <= "") {
			$irm_events_reg = "<p>
				No Events Found
			</p>";
		}

		$irm_events_html = "";
		$irm_events_html = "<h2>IRM Events</h2>
		<p>
			Use the following shortcodes to embed IRM Events into your site. If the following items are blank, there may not be any events on your site, or the site url is incorrect.
		</p>
		<p>
			<a href='https://irmau.com/irm-events/about-irm-events' class='button button-primary' target='_blank'>More About IRM Events</a>
		</p>
		<h3>Event List</h3>
		$irm_events_list
		<h3>Event Registration</h3>
		$irm_events_reg
		";
		echo $irm_events_html;
	}
	if( $active_tab == "menu" ) {
		echo '<h3>Menu</h3>';
		echo '<p>If you would like to replicate the menu from your IRM site into your wordpress site you can use the following codes.</p>';
		echo '<p>IRM Generated Menu : <pre><code>[irmmenu]</code></pre></p>';
		echo '<p>IRM Generated Menu in Wordpress Theme : <pre><code>echo do_shortcode("[irmmenu]");</code></pre></p>';
	}

	if( $active_tab == "shareprice" ) {
		echo '<h3>Share Price</h3>';
		echo '<p>If you have share prices as part of your IRM Newsroom package, you can enable them in the <b>Configure IRM Newsroom</b> Tab.</p>';
		echo '<h3>Share Price Shortcodes</h3>';
		echo '<p>Here are some shortcodes to allow you to add share prices. </p>';
		echo '<h4>Small Share Price Widget</h4>';
		echo '<p>This will add a small share price widget</p>';
		echo '<code>[sharepriceSnippet]</code>';
		echo '<h4>Share Price Table</h4>';
		echo '<p>This will add a large share price table. </p>';
		echo '<code>[sharepriceTable]</code>';
		echo '<h4>Share Price Chart</h4>';
		echo '<p>This will add a large share price chart. </p>';
		echo '<code>[sharepriceChart]</code>';
		echo '<h4>Share Price Chart Small</h4>';
		echo '<p>This will add a small share price chart. </p>';
		echo '<code>[sharepriceChartSmall]</code>';
		echo "<p><a href='http://www.irmhelpcentre.com/irm/content/shareprice-installation.aspx?RID=1594&RedirectCount=1' target='_blank' class='button button-primary'>For help installing Share Price's please click here.</a></p>";
	}

	$shortcodes_text = "<h3>Shortcodes</h3><p>Use these to embed newsfeeds into your wordpress site.</p>";

	if( $active_tab == "list" ) {
		echo $shortcodes_text;
		echo '<h2>List Page Shortcodes</h2><pre>';
		echo $irm_shortcodes_list;
		echo '</pre>';
	}

	if( $active_tab == "flat" ) {

		echo $shortcodes_text;
		echo '<h2>Flat Page Shortcodes</h2><pre>';
		echo $irm_shortcodes_flat;
		echo '</pre>';

		/*
		$irm_blog_list = get_data("$site_type://$site_key/$site_directory/sitedata.aspx?DataType=BlogPage");
		$irm_bio_list = get_data("$site_type://$site_key/$site_directory/sitedata.aspx?DataType=BiographyPage");

		echo '<h2>HQi Blog</h2>';
		echo '<pre>';
		echo $irm_blog_list;
		echo '</pre>';

		echo '<h2>HQi Biography</h2>';
		echo '<pre>';
		echo $irm_bio_list;
		echo '</pre>';
		*/

	}

	if( $active_tab == "events" ) {
		echo '<h2>Events Calendar Shortcode</h2>';
		echo '<p>If you have an events calendar you can use the following code to embed it. If it is blank you will need to create a new events page in HQi.</p>';
		echo '<pre>';
		if($irm_events_list > "") {
			echo $irm_events_list;
		} else {
			echo "No Events Found";
		}
		echo '</pre>';
	}

	echo "</div>";

	echo "<p><br><a href='http://www.irmhelpcentre.com/irm/content/how-do-i-install-my-irm-newsroom-free-trial.aspx?RID=1654' target='_blank' class='button button-primary'>For help installing IRM Newsroom please click here.</a></p>";

}

function get_data($url) {
    if(!function_exists('curl_init')) {
		if(function_exists('file_get_contents')) {
			return file_get_contents($url);
		}
		return false;
    }
	$ch = curl_init();
	$timeout = 10;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	if(isset($data)) {
		return $data;
	} else {
		return false;
	}
}

function footag_func_list( $atts ) {
	//return "foo = {$atts['foo']}";
	$id = $atts[0];
	//var_dump($atts);
	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );
	if(!$site_type > "") {
		$site_type = "https";
	}
	$url = "$site_type://$site_key/$site_directory/ShowListPage.aspx?CategoryID" . $id;
	$jsurl = "$site_type://$site_key/$site_directory/js/Newsroom.js";
	$imgurl = "$site_type://$site_key/$site_directory/pub/RF.aspx?Wordpress=true";

	return $js_data = "
	<div data-newsroomUrl='$url'>..</div>
    <script type='text/javascript' src='$jsurl'></script>
    <img src='$imgurl' style='display:none' />
	";

	//return $url_data = get_data($url);
}
function footag_func_flat( $atts ) {
	//return "foo = {$atts['foo']}";
	$id = $atts[0];
	//var_dump($atts);
	$site_key = esc_attr( get_option('site_key') );
	$site_directory = esc_attr( get_option('site_directory') );
	$site_type = esc_attr( get_option('site_type') );

	if(!$site_directory > "") {
		$site_directory = "site";
	}
	if(!$site_type > "") {
		$site_type = "http";
	}

	$url = "$site_type://$site_key/$site_directory/ShowFlat.aspx?CategoryID" . $id;
	$jsurl = "$site_type://$site_key/$site_directory/js/Newsroom.js";
	$imgurl = "$site_type://$site_key/$site_directory/pub/RF.aspx?Wordpress=true";

	return $js_data = "
	<div data-newsroomUrl='$url'>..</div>
    <script type='text/javascript' src='$jsurl'></script>
    <img src='$imgurl' style='display:none' />
	";

	//return $url_data = get_data($url);
}

function footag_func_events( $atts ) {
	$id = $atts[0];
	$site_key = esc_attr( get_option('site_key') );
	$site_directory = esc_attr( get_option('site_directory') );
	$site_type = esc_attr( get_option('site_type') );

	if(!$site_directory > "") {
		$site_directory = "site";
	}
	if(!$site_type > "") {
		$site_type = "http";
	}

	$url = "$site_type://$site_key/$site_directory/CalendarViewXml.aspx?CategoryID" . $id;
	$jsurl = "$site_type://$site_key/$site_directory/js/Newsroom.js";
	$imgurl = "$site_type://$site_key/$site_directory/pub/RF.aspx?Wordpress=true";

	return $js_data = "
	<div data-calendarurl='$url'>..</div>
    <script type='text/javascript' src='$jsurl'></script>
    <img src='$imgurl' style='display:none' />
	";

}



function irmeventlist_show( $atts ) {
	//return "foo = {$atts['foo']}";
	$id = $atts[0];
	//var_dump($atts);
	$site_key = esc_attr( get_option('site_key') );
	$site_directory = esc_attr( get_option('site_directory') );
	$site_type = esc_attr( get_option('site_type') );

	if(!$site_directory > "") {
		$site_directory = "site";
	}
	if(!$site_type > "") {
		$site_type = "http";
	}

	$url = "$site_type://$site_key/$site_directory/ShowFlat.aspx?CategoryID" . $id;
	$jsurl = "$site_type://$site_key/$site_directory/js/Newsroom.js";
	$imgurl = "$site_type://$site_key/$site_directory/pub/RF.aspx?Wordpress=true";

	return $js_data = "
	<div data-newsroomUrl='$url'>..</div>
    <script type='text/javascript' src='$jsurl'></script>
    <img src='$imgurl' style='display:none' />
	";

	//return $url_data = get_data($url);
}


// get the irm generated menu
function irmmenu() {
	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );
	$url = "$site_type://$site_key/$site_directory/ShowTopNav.aspx";
	return get_data($url);
}


function email_alerts_form() {
	$site_key = esc_attr( get_option('site_key') );
	$site_type = esc_attr( get_option('site_type') );
	$site_directory = esc_attr( get_option('site_directory') );

	$url = "$site_type://$site_key/$site_directory/data/UserRegistrationForm.aspx";
	$jsurl = "$site_type://$site_key/$site_directory/js/Newsroom.js";
	$email_landing_page = esc_attr( get_option('email_landing_page') );
	$imgurl = "$site_type://$site_key/$site_directory/pub/RF.aspx?Wordpress=true";


	return $js_data = "
	<div data-userregistrationformurl='$url' data-gotourl='$email_landing_page'>..</div>
    <script type='text/javascript' src='$jsurl'></script>
    <img src='$imgurl' style='display:none' />
	";
}

add_shortcode( 'irmeventlist', 'irmeventlist_show' );
add_shortcode( 'irmlist', 'footag_func_list' );
add_shortcode( 'irmflat', 'footag_func_flat' );
add_shortcode( 'irmcalendarview', 'footag_func_events' );
add_shortcode( 'email_alerts_form', 'email_alerts_form' );
add_shortcode( 'irmmenu', 'irmmenu' );


function irm_post_updated( $post_id, $post, $update ) {

	$save_type = "save";

	$update ? $save_type = "update" : '';

	if ( wp_is_post_revision( $post_id ) ) {
		$save_type = "revision";
	}

	$post_title = get_the_title( $post_id );
	$post_url = get_permalink( $post_id );
	$post_guid = get_the_guid( $post_id );

	$site_key = esc_attr( get_option('site_key') );

	$site_type = esc_attr( get_option('site_type') );
	if(!$site_type > "") {
		$site_type = "https";
	}

	$site_directory = esc_attr( get_option('site_directory') );
	if(!$site_directory > "") {
		$site_directory = "site";
	}


	$url = "$site_type://$site_key/$site_directory/SourceUpdateNotification.aspx?Source=WP&Action=$save_type&RssGuid=$post_guid";

	get_data($url);

}
add_action( 'save_post', 'irm_post_updated', 10, 3 );


/* process the json contact form */
function json_reg() {

	$site_key = esc_attr( get_option('site_key') );

	$site_type = esc_attr( get_option('site_type') );
	if(!$site_type > "") {
		$site_type = "https";
	}

	$site_directory = esc_attr( get_option('site_directory') );
	if(!$site_directory > "") {
		$site_directory = "irm";
	}

	$url = "$site_type://$site_key/$site_directory/json/UserRegistrationSettings.aspx";
	$content = file_get_contents($url);
	$json = json_decode($content, true);

	//var_dump($json);

	foreach($json as $key => $value){
	    if(is_array($value)) {
	    	echo "nested array found<br>";
	    	echo "key:$key - value:$value<br>";
	    	foreach($value as $key2 => $value2) {
	    		if(is_array($value2)) {
			    	echo "nested array level 2<br>";
	    			foreach($value2 as $key3 => $value3) {
		    			echo "key3:$key3 - value3:$value3<br>";
	    			}
	    		} else {
	    			echo "key2:$key2 - value2:$value2<br>";
	    		}
	    	}
	    } else {
	    	echo "no array found<br>";
	    	echo "key:$key - value:$value<br>";
		}
	}

}
add_shortcode( 'userreg', 'json_reg' ); // add the shortcode userreg to call the reg form json parsing.

function shortcode_spSnippet() {
  return '<div class="widget-wrap" style="overflow:hidden;">
  <div id="loadshareprice"><img class="asx-logo" src="https://www.irmau.com/site/content/images/asxLogo.png" alt="" />
    <div class="price" data-quoteapi="price" id="price">&nbsp;</div>
    <div class="market-cap" id="market-cap">Market Cap: <span data-quoteapi="marketCap">&nbsp;</span></div>
    <div class="spdelay">Price Delay ~20min</div>
  </div>
</div>';
}
add_shortcode('sharepriceSnippet', 'shortcode_spSnippet');

function shortcode_spTable() {
  return '<div class="shareprice-col" id="sp-asx">
  <div class="row sptable">
    <div class="columns">
      <h4>Buy</h4>
      <p data-quoteapi="bid">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Sell</h4>
      <p data-quoteapi="ask">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>First</h4>
      <p data-quoteapi="open">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>High</h4>
      <p data-quoteapi="high">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Low</h4>
      <p data-quoteapi="low">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Last</h4>
      <p data-quoteapi="close">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>MktPrice</h4>
      <p data-quoteapi="price">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Change</h4>
      <p data-quoteapi="change">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Percent Change</h4>
      <p data-quoteapi="pctChange">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Volume</h4>
      <p data-quoteapi="volume">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>Total Trades</h4>
      <p data-quoteapi="tradeCount">&nbsp;</p>
    </div>
    <div class="columns">
      <h4>MktCap</h4>
      <p data-quoteapi="marketCap">&nbsp;</p>
    </div>
  </div>
</div>';
}
add_shortcode('sharepriceTable', 'shortcode_spTable');

function shortcode_spChart() {
  return '<p class="iguana-terms">Below are share charts depicting the Company\'s performance over different time periods. The trend charts update each morning. Share prices and charts by iguana2. <a href="http://iguana2.com/legal-ir">Terms of use</a><br /> &nbsp;</p>
<div class="centered" data-quoteapi="mainChart">
  <div class="irmau-main-chart" data-quoteapi="plots">&nbsp;</div>
  <div class="irmau-from-to"><span data-quoteapi="displayedRange.from"></span> to <span data-quoteapi="displayedRange.to"></span></div>
  <div>
    <ul class="chart-buttons">
      <li data-quoteapi="range=1d">Today</li>
      <li data-quoteapi="range=1m">1 mnth</li>
      <li data-quoteapi="range=3m">3 mnths</li>
      <li data-quoteapi="range=6m">6 mnths</li>
      <li data-quoteapi="range=ytd">ytd</li>
      <li data-quoteapi="range=1y">1 yr</li>
      <li data-quoteapi="range=3y">3 yrs</li>
      <li data-quoteapi="range=5y">5 yrs</li>
      <li data-quoteapi="range=10y">10 yrs</li>
    </ul>
  </div>
  <div class="irmau-main-chart irmau-nav-chart" data-quoteapi="navChart1">&nbsp;</div>
  <form data-quoteapi="preventSubmit"><input type="checkbox" data-quoteapi="volume.visible" /> Volume <input type="checkbox" data-quoteapi="announcements.visible" /> Announcements
    <div class="chart-button"><button data-quoteapi="download">Download CSV</button></div>
  </form>
</div>';
}
add_shortcode('sharepriceChart', 'shortcode_spChart');

function shortcode_small_share_price() {
	return '<div class="irmau-small-chart" data-quoteapi="smallChart range=6m"></div>';
}
add_shortcode('sharepriceChartSmall', 'shortcode_small_share_price');


?>
