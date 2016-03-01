<?php
/**
 * @file
 * @brief Implements the BTBooking base class.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

defined( 'ABSPATH' ) or die (' Am Arsch die R&auml;uber! ');

/**
 * Base class for instantiating the plugin.
 *
 * This class registers the post types, post stati, scripts and styles and inits the text domain.
 * You have to call init exeplicitely to register them. This also adds the @c cleen_prebooked action and the cron schedules.
 *
 * To register the cron jobs for deleting prebooked bookings after 30 minutes, you have to call register_clean_prebooked
 * explicitely. Both should be hooked to the @c init action of wordpress.
 */
class BTBooking {


	/**
	 * Initializes the plugin base functions.
	 *
	 * Inits the text domain, registers post types, post stati, scripts and styles and implements the cron jobs for cleaning
	 * prebooked bookings.
	 *
	 * Should be called in the wordpress init hook.
	 */
    public static function init() {
		self::init_textdomain();
        self::register_post_types();
        self::register_post_stati();
        self::register_scripts();
        self::register_styles();
        self::add_filters();
        add_action('clean_prebooked', array('BTBooking', 'clean_prebooked'));
        add_filter('cron_schedules', array('BTBooking', 'filter_cron_schedules'));
    }

    /**
     * Inits the plugin text domain @a bt-booking.
     *
     * Called by init.
     */
    public static function init_textdomain() {
		if (!load_plugin_textdomain('bt-booking', false, dirname(plugin_basename(__FILE__)) . '/languages'))
		{
			error_log('Error while loading text domain');
			error_log(apply_filters('plugin_locale', get_locale(), 'bt-booking'));
		}
    }


    /**
     * Registers the post types.
     *
     * btb_event, btb_time, btb_booking and btb_venue are registered by this function.
     */
    public static function register_post_types() {
        if (post_type_exists('btb_booking')) {
            return;
        }

		$event_permalink = get_option('btb_event_permalink', _x('event', 'slug', 'bt-booking'));

        register_post_type('btb_event',
            array(
                'labels'            => array(
                    'name'                  => __('Events', 'bt-booking'),
                    'singular_name'         => __('Event', 'bt-booking'),
                    'menu_name'             => __('Events', 'bt-booking' ),
                    'add_new'               => __('Add New', 'bt-booking' ),
                    'add_new_item'          => __('Add new event', 'bt-booking'),
                    'edit_item'             => __('Edit event', 'bt-booking'),
                    'new_item'              => __('New event', 'bt-booking'),
                    'view_item'             => __('View event', 'bt-booking'),
                    'search_items'          => __('Search events', 'bt-booking'),
                    'not_found'             => __('No events found', 'bt-booking'),
                    'not_found_in_trash'    => __('No events found in Trash', 'bt-booking'),
                    'featured_image'        => __('Event Image', 'bt-booking' ),
                    'set_featured_image'    => __('Set event image', 'bt-booking' ),
                    'remove_featured_image' => __('Remove event image', 'bt-booking' ),
                    'use_featured_image'    => __('Use as event image', 'bt-booking' )
                ),
                'description'           => __( 'This is where you can add new events.', 'bt-booking' ),
                'public'                => true,
                'rewrite'               => $event_permalink ? array('slug' => untrailingslashit($event_permalink), 'with_front' => false, 'feeds' => false) : false,
                'capability_type'       => 'page',
                'show_ui'               => true,
                'map_meta_cap'          => true,
                'publicly_queryable'    => true,
                'exclude_from_search'   => false,
                'hierarchical'          => false,
                'supports'              => array('title', 'editor', 'author', 'thumbnail'),
                'has_archive'           => false,
                'show_in_nav_menus'     => true,
                'register_meta_box_cb'  => array('BTBooking_Admin_Edit_Event', 'add_btb_event_meta_boxes')
            )
        );

        register_post_type('btb_time',
            array(
                'label'             => __('Time', 'bt-booking'),
                'public'            => false,
                'hierarchical'      => false,
                'supports'          => false,
                'capability_type'   => 'page'
            )
        );



        $venue_permalink = get_option('btb_venue_permalink', _x('venue', 'slug', 'bt-booking'));

        register_post_type('btb_venue',
			array(
				'labels' => array(
					'name'					=> __('Venues', 'bt-booking'),
					'singular_name'			=> __('Venue', 'bt-booking'),
					'menu_name'				=> __('Venues', 'bt-booking'),
					'name_admin_bar'		=> __('Venue', 'bt-booking'),
					'all_items'				=> __('Venues', 'bt-booking'),
					'add_new'				=> __('Add New', 'bt-booking'),
					'add_new_item'			=> __('Add New Venue', 'bt-booking'),
					'edit_item'				=> __('Edit Venue', 'bt-booking'),
					'new_item'				=> __('New Venue', 'bt-booking'),
					'view_item'				=> __('View Venue', 'bt-booking'),
					'search_items'			=> __('Search Venues', 'bt-booking'),
					'not_found'				=> __('No venues found', 'bt-booking'),
					'not_found_in_trash'	=> __('No venues found in trash', 'bt-booking'),
					'featured_image'        => __('Venue Image', 'bt-booking' ),
                    'set_featured_image'    => __('Set venue image', 'bt-booking' ),
                    'remove_featured_image' => __('Remove venue image', 'bt-booking' ),
                    'use_featured_image'    => __('Use as venue image', 'bt-booking' )
				),
				'description'			=> __('This is where you can add new venues.', 'bt-booking'),
				'public'				=> true,
				'rewrite'				=> $venue_permalink ? array('slug' => untrailingslashit($venue_permalink), 'with_front' => false, 'feeds' => false) : false,
				'capability_type'   	=> 'page',
				'show_ui'				=> true,
				'map_meta_cap'			=> true,
				'hierarchical'			=> false,
				'supports'				=> array('title', 'editor', 'excerpt', 'thumbnail'),
				'has_archive'			=> false,
				'show_in_nav_menus'		=> false,
				'show_in_menu'			=> 'edit.php?post_type=btb_event',
				'register_meta_box_cb'	=> array('BTBooking_Admin_Edit_Venue', 'add_btb_venue_meta_boxes')
			)
        );


        $booking_permalink	= get_option('btb_booking_permalink', _x('booking', 'slug', 'bt-booking'));

        $labels = array(
			'name'                => __( 'Bookings', 'bt-booking' ),
			'singular_name'       => __( 'Booking', 'bt-booking' ),
			'menu_name'           => __( 'Bookings', 'bt-booking' ),
			'name_admin_bar'      => __( 'Booking', 'bt-booking' ),
			'parent_item_colon'   => __( 'Booked event:', 'bt-booking' ),
			'all_items'           => __( 'Bookings', 'bt-booking' ),
			'add_new_item'        => __( 'Add New Booking', 'bt-booking' ),
			'add_new'             => __( 'Add New', 'bt-booking' ),
			'new_item'            => __( 'New Booking', 'bt-booking' ),
			'edit_item'           => __( 'Edit Booking', 'bt-booking' ),
			'update_item'         => __( 'Update Booking', 'bt-booking' ),
			'view_item'           => __( 'View Booking', 'bt-booking' ),
			'search_items'        => __( 'Search Booking', 'bt-booking' ),
			'not_found'           => __( 'No bookings found', 'bt-booking' ),
			'not_found_in_trash'  => __( 'No bookings found in trash', 'bt-booking' ),
		);
		$args = array(
			'label'               => __( 'Booking', 'bt-booking' ),
			'description'         => __( 'Represents a booking order line itme.', 'bt-booking' ),
			'labels'              => $labels,
			'supports'            => false,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=btb_event',
			'menu_position'       => 5,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => $booking_permalink ? array('slug' => untrailingslashit($booking_permalink), 'with_front' => false, 'feeds' => false) : false,
			'capability_type'     => 'page',
			'register_meta_box_cb'  => array('BTBooking_Admin_Edit_Booking', 'add_btb_booking_meta_boxes')
		);
		register_post_type( 'btb_booking', $args );
    }

    public static function register_post_stati() {
		if (get_post_status_object('btb_canceled')) {
			return;
		}

		$args = array(
			'label'                     => _x( 'Booked', 'Status General Name', 'bt-booking' ),
			'label_count'               => _n_noop( 'Booked <span class="count">(%s)</a>',  'Booked <span class="count">(%s)</a>', 'bt-booking' ),
			'protected'					=> true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'btb_booked', $args );

		$args = array(
			'label'                     => _x( 'Prebooked', 'Status General Name', 'bt-booking' ),
			'label_count'               => _n_noop( 'Prebooked <span class="count">(%s)</a>',  'Prebooked <span class="count">(%s)</a>', 'bt-booking' ),
			'protected'					=> true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'btb_prebook', $args );

		$args = array(
			'label'                     => _x( 'Canceled', 'Status General Name', 'bt-booking' ),
			'label_count'               => _n_noop( 'Canceled <span class="count">(%s)</a>',  'Canceled <span class="count">(%s)</a>', 'bt-booking' ),
			'protected'					=> true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => false,
		);
		register_post_status( 'btb_canceled', $args );
    }

    /**
     * Called when activating the plugin.
     *
     * Registers post types and post stati and flushed the rewrite rules.
     *
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     */
    public static function plugin_activation() {
		self::register_post_types();
		self::register_post_stati();
		flush_rewrite_rules();
    }


    /**
     * Called when deactivating the plugin.
     *
     * Removes the schedule for cleaning prebooked events.
     */
    public static function plugin_deactivation() {
		$timestamp = wp_next_scheduled('clean_prebooked');
		wp_unschedule_event($timestamp, 'clean_prebooked');
    }


    /**
     * Register the plugin's scripts to the system.
     */
    public static function register_scripts() {
        wp_register_script( 'btb-direct-booking-script', BTB__PLUGIN_URL . 'assets/btb-direct-booking.min.js', array('jquery'), BTB_VERSION, true );
        wp_register_script( 'btb-checkout-script', BTB__PLUGIN_URL . 'assets/btb-checkout.min.js', array('jquery'), BTB_VERSION, true );
        wp_register_script( 'btb-leaflet-script', BTB__PLUGIN_URL . 'assets/leaflet/leaflet.js', array('jquery'), BTB_LEAFLET_VERSION, true);
        wp_register_script( 'btb-edit-venue-script', BTB__PLUGIN_URL . 'admin/assets/edit-venue.min.js', array('btb-leaflet-script'), BTB_VERSION, true);
        wp_register_script( 'btb-edit-booking-script', BTB__PLUGIN_URL . 'admin/assets/edit-booking.min.js', array('jquery'), BTB_VERSION, true);
        wp_register_script( 'btb-edit-event-script', BTB__PLUGIN_URL . 'admin/assets/edit-event.min.js', array('jquery','postbox','jquery-ui-datepicker'), BTB_VERSION, true );
//         wp_register_script( 'btb-country-chooser-script', BTB__PLUGIN_URL . 'assets/btb-country-chooser.min.js', array('jquery', 'jquery-ui-autocomplete'), BTB_VERSION, true);
    }

    /**
     * Registers the plugin's styles.
     */
    public static function register_styles() {

		wp_register_style( 'btb-leaflet-style', BTB__PLUGIN_URL . 'assets/leaflet/leaflet.min.css', array(), BTB_LEAFLET_VERSION);
		wp_register_style( 'btb-admin-style', BTB__PLUGIN_URL . 'admin/assets/admin.min.css', array(), BTB_VERSION);

        switch(get_option('btb_style', 'default')) {
            case 'avada';
				wp_enqueue_style('btb-style', BTB__PLUGIN_URL . 'assets/btb-avada-style.min.css', array('avada-stylesheet', 'avada-shortcodes'), BTB_VERSION);
                break;
            default:
				wp_enqueue_style('btb-style', BTB__PLUGIN_URL . 'assets/btb-default-style.min.css', array(), BTB_VERSION);
                break;
        }

        add_action('wp_head', array('BTBooking', 'custom_styles'), 10000);

    }


    /**
     * Adds filters for output generation.
     */
    public static function add_filters() {

		$style = get_option('btb_style', 'default');

		if (!has_filter('btb_create_event_schema_org')) {
			add_filter('btb_create_event_schema_org', array('BTBooking_Direct_Booking', 'event_schema_org_filter'), 10, 4);
		}

		if ($style == 'avada') {
			if (!has_filter('btb_create_direct_booking_box')) {
				add_filter('btb_create_direct_booking_box', array('BTBooking_Direct_Booking', 'avada_style_filter'), 10, 5);
			}
			if (!has_filter('btb_create_checkout_form')) {
				add_filter('btb_create_checkout_form', array('BTBooking_Checkout', 'avada_style_filter'), 10, 3);
			}
		} else {
		}

    }


    /**
     * Prints the content of the custom style.
     */
    public static function custom_styles() {
    ?>
        <!--- BT Booking Custom Styles -->
        <style type="text/css">
        <?php echo get_option('btb_custom_style', ''); ?>
        </style>

    <?php
    }


    /**
     * Cleans prebooked items older than 1800 seconds.
     */
    public static function clean_prebooked() {

		$prebookings = get_posts(array('numberposts' => -1, 'post_type' => 'btb_booking', 'post_status' => 'btb_prebook'));

		if (!empty($prebookings)) {

			$currenttime = time();

			foreach($prebookings as $key => $prebook) {

				$bookingtime = intval(get_post_meta($prebook->ID, 'btb_booking_time', true));


				if (($bookingtime + 1800) < $currenttime) {

					wp_delete_post($prebook->ID, true);

				}

			}

		}

    }


    /**
     * Registers the schedule for cleaning prebooked bookings.
     */
    public static function register_clean_prebooked() {
		if (!wp_next_scheduled('clean_prebooked')) {
			wp_schedule_event(time(), 'half_hour', 'clean_prebooked');
		}
    }

    /**
     * Adds quarter and half hour to the cron schedule times.
     */
    public static function filter_cron_schedules($schedules) {
		$schedules['quarter_hour'] = array(
										'interval' => 900, //seconds
										'display' => __('Every Quarter of an Hour')
										);
		$schedules['half_hour'] = array(
										'interval' => 1800, //seconds
										'display' => __('Once Half-Hourly')
										);
		return $schedules;
    }
}

?>
