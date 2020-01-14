<?php
/*
Plugin Name: Buschtrommel Booking
Plugin URI: http://www.buschmann23.de
Description: Lorem Ipsum blablabla...
Version: 1.1.5
Author: Buschtrommel
Author URI: http://www.buschmann23.de
License: MPL 2.0
License URI: http://mozilla.org/MPL/2.0/
Text Domain: bt-booking
*/

/**
 * @file
 * @brief Base entry file for the plugin.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/** @mainpage Buschtrommel Booking Manual

 Some general info.


 */

/** @page dblayout Database Layout
 * Information about the database layout used by BT Booking.
 * @subpage optionsdb
 */


defined( 'ABSPATH' ) or die (' Am Arsch die R&auml;uber! ');

define( 'BTB_VERSION', '1.1.5' );
define( 'BTB_LEAFLET_VERSION', '0.7.7' );
define( 'BTB__MINIMUM_WP_VERSION', '5.3.0' );
define( 'BTB__PLUGIN_URL', plugin_dir_url(__FILE__) );
define( 'BTB__PLUGIN_DIR', plugin_dir_path(__FILE__) );

register_activation_hook(__FILE__, array('BTBooking', 'plugin_activation'));
register_deactivation_hook(__FILE__, array('BTBooking', 'plugin_deactivation'));

// add_action('plugins_loaded', array('BTBooking', 'init_textdomain'));

require_once( BTB__PLUGIN_DIR . 'class.bt-booking.php');

require_once( BTB__PLUGIN_DIR . 'bt-booking-functions.php');

add_action('init', array('BTBooking', 'init'));
add_action('init', array('BTBooking', 'register_clean_prebooked'));
add_action('rest_api_init', array('BTBooking', 'register_rest_data'));

require_once( BTB__PLUGIN_DIR . 'class.bt-booking-booking.php');
require_once( BTB__PLUGIN_DIR . 'class.bt-booking-time.php');
require_once( BTB__PLUGIN_DIR . 'class.bt-booking-event.php');
require_once( BTB__PLUGIN_DIR . 'class.bt-booking-venue.php');

require_once( BTB__PLUGIN_DIR . 'class.bt-booking-direct-booking.php');

add_action('init', array('BTBooking_Direct_Booking', 'register_short_code'));

require_once( BTB__PLUGIN_DIR . 'class.bt-booking-checkout.php');

add_action('init', array('BTBooking_Checkout', 'register_short_code'));

require_once( BTB__PLUGIN_DIR . 'class.bt-booking-checkout-overview.php');

add_action('init', array('BTBooking_Checkout_Overview', 'register_short_code'));

require_once( BTB__PLUGIN_DIR . 'class.bt-booking-schema-org-shortcode.php');

add_action('init', array('BTBooking_Schema_Org_Shortcode', 'register_short_code'));

if ( is_admin() ) {

	require_once(BTB__PLUGIN_DIR . 'framework/btc/templates/wpadmin/class.btc-wpadmin.php');

    require_once(BTB__PLUGIN_DIR . 'admin/class.btb-admin.php' );
    new BTBooking_Admin();

    require_once(BTB__PLUGIN_DIR . 'admin/class.btb-admin-settings.php');
    new BTBooking_Admin_Settings();

    require_once(BTB__PLUGIN_DIR . 'admin/class.btb-admin-edit-event.php' );
    add_action ('load-post.php', 'call_BTBooking_Admin_Edit_Event');
    add_action ('load-post-new.php', 'call_BTBooking_Admin_Edit_Event');

    require_once(BTB__PLUGIN_DIR . 'admin/class.btb-admin-edit-booking.php');

    require_once(BTB__PLUGIN_DIR . 'admin/class.btb-admin-edit-venue.php');
    add_action ('load-post.php', 'callBTBooking_Admin_Edit_Venue');
    add_action ('load-post-new.php', 'callBTBooking_Admin_Edit_Venue');

}


?>
