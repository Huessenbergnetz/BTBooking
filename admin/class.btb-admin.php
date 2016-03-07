<?php
/**
 * @file
 * @brief Implements the BTBooking_Admin class.
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

require_once(BTB__PLUGIN_DIR . 'class.bt-booking-countries.php');

/**
 * Core class used to implement the BTBooking_Admin object.
 *
 * This class provides callbacks for custom columns in post overviews for BTB_Event, BTB_Booking and BTB_Venue.
 * It also loads the plugins text domain for the admin part of wordpress.
 *
 * All inits are processed when instantiating the class object.
 */
class BTBooking_Admin {

	/**
	 * Initializaton state.
	 *
	 * After initializaton this variable is set to true to prevent re initializaton.
	 *
	 * @var int $initiated
	 */
    private static $initiated = false;

    /**
     * Constructor
     *
     * Checks $initiated and and calls inits if false.
     */
    public function __construct() {
        if (!self::$initiated) {
            self::inits();
        }
    }

    /**
     * Initializes the admin part.
     *
     * Sets initiated to true and adds custom column headers and content to the post listings.
     */
    public function inits() {
        self::$initiated = true;

        load_plugin_textdomain( 'bt_booking' );

		add_filter('manage_btb_event_posts_columns', array('BTBooking_Admin', 'bt_events_columns_head'));
		add_action('manage_btb_event_posts_custom_column', array('BTBooking_Admin', 'bt_events_columns_content'), 10, 2);

		add_filter('manage_btb_booking_posts_columns', array('BTBooking_Admin', 'bt_bookings_columns_head'));
		add_action('manage_btb_booking_posts_custom_column', array('BTBooking_Admin', 'bt_bookings_columns_content'), 10, 2);

		add_filter('manage_btb_venue_posts_columns', array('BTBooking_Admin', 'bt_venues_columns_head'));
		add_action('manage_btb_venue_posts_custom_column', array('BTBooking_Admin', 'bt_venues_columns_content'), 10, 2);
    }

    /**
     * Adds custom coumn headers for BTB_Event list.
     *
     * @param array $defaults Default columns.
     */
	public static function bt_events_columns_head($defaults) {
		$cols['cb'] = $defaults['cb'];
		$cols['title'] = $defaults['title'];
		$cols['venue'] = __('Venue', 'bt-booking');
		$cols['price'] = __('Price', 'bt-booking');
		$cols['dates'] = __('Dates', 'bt-booking');
		$cols['slots'] = __('Slots', 'bt-booking');
		$cols['author'] = $defaults['author'];
		$cols['date'] = $defaults['date'];
		return $cols;
	}

	/**
	 * Provides data for the custom columns of BTB_Event list.
	 *
	 * @param string $column_name Current colummn name.
	 * @param int $event_id ID of the event.
	 */
	public static function bt_events_columns_content($column_name, $event_id) {
		$event = btb_get_event($event_id);

		if ($column_name == 'dates') {
			$times = btb_get_times($event->ID);
			$timesCount = count($times);
			if ($timesCount == 0) {
				echo "&mdash;";
			} else if ($timesCount == 1) {
				echo $times[0]->post_title;
			} else if ($timesCount > 1) {
				printf(_n('%u date', '%u dates', $timesCount, 'bt-booking'), $timesCount);
			}
		}

		if ($column_name == 'venue') {
			if ($event->venue) {
				printf('<a href="post.php?post=%d&action=edit">', $event->venue);
				echo get_the_title($event->venue);
				echo "</a>";
			} else {
				echo "&mdash;";
			}
		}

		if ($column_name == 'price') {
			echo get_option('btb_currency', '€') .  ' ' . number_format_i18n($event->price, 2);
		}

		if ($column_name == 'slots') {
			$slots = btb_get_event_slots_summary($event_id);
			echo '<abbr title="' . __('Free Slots / Prebooked Slots / Booked Slots / Total Available Slots', 'bt-booking') . '">';
			echo $slots["free"] . ' / ';
			echo $slots["prebooked"] . ' / ';
			echo $slots["booked"] . ' / ';
			echo $slots["total"];
			echo '</abbr>';
		}
	}


	/**
     * Adds custom coumn headers for BTB_Booking list.
     *
     * @param array $defaults Default columns.
     */
	public static function bt_bookings_columns_head($defaults) {
		$cols['cb'] = $defaults['cb'];
		$cols['title'] = __('Code', 'bt-booking');
		$cols['event'] = __('Event', 'bt-booking');
		$cols['event_date'] = __('Event date', 'bt-booking');
		$cols['first_name'] = __('First name', 'bt-booking');
		$cols['last_name'] = __('Last name', 'bt-booking');
		$cols['slots'] = __('Slots', 'bt-booking');
		$cols['unit_price'] = __('Unit price', 'bt-booking');
		$cols['total_price'] = __('Total price', 'bt-booking');
		$cols['booking_time'] = __('Booking time', 'bt-booking');
		return $cols;
	}


	/**
	 * Provides data for the custom columns of BTB_Booking list.
	 *
	 * @param string $column_name Current colummn name.
	 * @param int $booking_id ID of the booking.
	 */
	public static function bt_bookings_columns_content($column_name, $booking_id) {
		$booking = btb_get_booking($booking_id);

		if ($column_name == 'event') {
			printf('<a href="post.php?post=%d&action=edit">', $booking->booked_event);
			echo get_the_title($booking->booked_event);
			echo "</a>";
		}

		if ($column_name == 'event_date') {
			echo get_the_title($booking->booked_time);
		}

		if ($column_name == 'first_name') {
			echo $booking->first_name;
		}

		if ($column_name == 'last_name') {
			echo $booking->last_name;
		}

		if ($column_name == 'slots') {
			echo $booking->booked_slots;
		}

		if ($column_name == 'unit_price') {
			echo get_option('btb_currency', '€') .  ' ' . number_format_i18n($booking->price, 2);
		}

		if ($column_name == 'total_price') {
			echo get_option('btb_currency', '€') .  ' ' . number_format_i18n($booking->price * $booking->booked_slots, 2);
		}

		if ($column_name == 'booking_time') {
			date_default_timezone_set ( get_option('timezone_string', 'UTC') );
			echo '<abbr title="' . date_i18n(_x('m/d/Y h:iA', 'Date and time shown in bookings list', 'bt-booking'), $booking->booking_time) . '">';
			echo date_i18n(_x('m/d/Y', 'Short date shown in bookings list', 'bt-booking'), $booking->booking_time);
			echo '</abbr>';
		}
	}


	/**
     * Adds custom coumn headers for BTB_Venue list.
     *
     * @param array $defaults Default columns.
     */
	public static function bt_venues_columns_head($defaults) {
		$cols['cb'] = $defaults['cb'];
		$cols['title'] = __('Name', 'bt-booking');
		$cols['streetno'] = __('Street & No.', 'bt-booking');
		$cols['zip'] = __('Postal Code', 'bt-booking');
		$cols['city'] = __('City', 'bt-booking');
		$cols['state'] = __('State', 'bt-booking');
		$cols['country'] = __('Country', 'bt-booking');
		$cols['date'] = $defaults['date'];
		return $cols;
	}


	/**
	 * Provides data for the custom columns of BTB_Venue list.
	 *
	 * @param string $column_name Current colummn name.
	 * @param int $venue_id ID of the venue.
	 */
	public static function bt_venues_columns_content($column_name, $venue_id) {
		$venue = btb_get_venue($venue_id);

		if ($column_name == 'streetno') {
			echo $venue->street;
			if ($venue->house_number) {
				echo ' ' . $venue->house_number;
			}
		}

		if ($column_name == 'zip') {
			echo $venue->postal_code;
		}

		if ($column_name == 'city') {
			echo $venue->city;
		}

		if ($column_name == 'state') {
			echo $venue->region;
		}

		if ($column_name == 'country') {
			echo BTBookingCountries::get_country_by_code($venue->country);
		}
	}

}

?>