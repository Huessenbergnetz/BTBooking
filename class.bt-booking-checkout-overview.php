<?php
/**
 * @file
 * @brief Implements the BTBooking_Checkout_Overview class.
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

require_once(BTB__PLUGIN_DIR . 'framework/btc/table/btc-table.php');
require_once(BTB__PLUGIN_DIR . 'framework/btc/form/btc-form.php');
require_once(BTB__PLUGIN_DIR . 'class.bt-booking-countries.php');


/**
 * @brief Implements the @c btb_checkout_overview shortcode.
 *
 * The @c btb_checkout_overview shortcode shows a summary overview of the booked items. It can be used together
 * with the @c btb_checkout shortcode to show the summary when the customer checks out. The code is separated into two
 * shortcodes to make it easier to place it on the checkout page.
 *
 * @par Available shortcode attributes
 * - @a headline Optional headline that is shown above the summary if set. Default: empty.
 *
 * @par Exmaple
 * @code
 [btb_checkout_overview headline="Your booking"]
 * @endcode
 *
 * @since 1.0.0
 */
class BTBooking_Checkout_Overview {

	/**
	 * Registers the shortcode btb_checkout_overview.
	 */
	public static function register_short_code() {
		add_shortcode( 'btb_checkout_overview', array('BTBooking_Checkout_Overview', 'btb_checkout_overview_func') );
	}

	/**
	 * Processes the btb_checkout_overview shortcode.
	 *
	 * Based on the set style it calls a sub function to render the output.
	 *
	 * @param array $atts The shortcode attributes. See class description for explanation.
	 */
	public static function btb_checkout_overview_func($atts) {

		if (isset($_GET['booking']) && isset($_GET['btbnonce']) && !isset($_POST['btb_checkout_nonce'])) {

			if (!wp_verify_nonce($_GET['btbnonce'], 'btb_direct_booking_nonce')) {
				return;
			}
			
			$master_instance = (get_option('btb_instance_type', 'master') == 'master');

			if ($master_instance) {
				$booking = btb_get_booking($_GET['booking'], OBJECT, 'display');
			} else {
				$booking = btb_get_booking_from_api($_GET['booking'], OBJECT, 'display');
			}
			

			if (!$booking) {
				return;
			}

			if ($booking->post_type !== "btb_booking") {
				return;
			}
			
			if ($master_instance) {
				$time = btb_get_time($booking->booked_time, OBJECT, 'display');
			} else {
				$time = btb_get_time_from_api($booking->booked_time, OBJECT, 'display');
			}
			
			if (!$time) {
				return;
			}
			
			if ($time->post_type !== "btb_time") {
				return;
			}
			
			if ($master_instance) {
				$event = btb_get_event($time->event, OBJECT, 'display');
			} else {
				$event = btb_get_event_from_api($time->event, OBJECT, 'display');
			}
			
			if (!$event) {
				return;
			}
			
			if ($event->post_type !== "btb_event") {
				return;
			}

			$a = shortcode_atts( array(
					'headline' => ''
				), $atts );


			switch(get_option('btb_style', 'custom')) {
				case 'avada':
					return self::btb_checkout_overview_avada($a, $booking, $time, $event);
				case 'bootstrap3':
					return self::btb_checkout_overview_bs3($a, $booking, $time, $event);
				default:
					return self::btb_checkout_overview_default($a, $booking, $time, $event);
			}

		}

    }

    /**
     * Renders the shortcode output for the Avada style.
     *
     * @param array			$atts The shortcode attributes. See class description for explanation.
     * @param BTB_Booking	$booking The booking object.
     * @param BTB_Time		$time The time object.
     * @param BTB_Event		$event The event object.
     */
    private static function btb_checkout_overview_avada($atts, $booking, $time, $event) {

		$slots = $booking->booked_slots;
		$price = $booking->price;
		$total = $slots * $price;


		$ret = '<div id="btb_checkout_table">'; // START CONTAINER

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		// START CREATING BOOKING TABLE FULL

		$headerRow = new BTCTableRow();
		$headerRow->add_content(new BTCTableData(__('Event', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Date', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Single price', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Slots', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Total price', 'bt-booking'), array(), true));

		$header = new BTCTableHead(array(), $headerRow);

		$contentRow = new BTCTableRow();
		$contentRow->add_content(new BTCTableData($event->post_title));
		$contentRow->add_content(new BTCTableData($time->post_title));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));
		$contentRow->add_content(new BTCTableData($slots));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$body = new BTCTableBody(array(), array($contentRow));

		$bookingTableFull = new BTCTable(array('htmlClasses' => 'table table-responsive btb_checkout_full_table'), $body, $header);

		$ret .= $bookingTableFull->render(false);

		// END CREATING BOOKING TABLE FULL



		// START CREATING BOOKING TABLE SMALL

		$row1 = new BTCTableRow();
		$row1->add_content(new BTCTableData(__('Event', 'bt-booking'), array('scope' => 'row'), true));
		$row1->add_content(new BTCTableData($event->post_title));

		$row2 = new BTCTableRow();
		$row2->add_content(new BTCTableData(__('Date', 'bt-booking'), array('scope' => 'row'), true));
		$row2->add_content(new BTCTableData($time->post_title));

		$row3 = new BTCTableRow();
		$row3->add_content(new BTCTableData(__('Single price', 'bt-booking'), array('scope' => 'row'), true));
		$row3->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));

		$row4 = new BTCTableRow();
		$row4->add_content(new BTCTableData(__('Slots', 'bt-booking'), array('scope' => 'row'), true));
		$row4->add_content(new BTCTableData($slots));

		$row5 = new BTCTableRow();
		$row5->add_content(new BTCTableData(__('Total price', 'bt-booking'), array('scope' => 'row'), true));
		$row5->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$smallBody = new BTCTableBody(array(), array($row1, $row2, $row3, $row4, $row5));

		$bookingTableSmall = new BTCTable(array('htmlClasses' => 'table table-responsive btb_checkout_small_table'), $smallBody);

		$ret .= $bookingTableSmall->render(false);

		// END CREATING BOOKING TABLE SMALL

		$ret .= '</div>'; // END CONTAINER

		return $ret;

    }
    
    
    /**
     * Renders the shortcode output for the default style.
     *
     * @param array			$atts The shortcode attributes. See class description for explanation.
     * @param BTB_Booking	$booking The booking object.
     * @param BTB_Time		$time The time object.
     * @param BTB_Event		$event The event object.
     */
    private static function btb_checkout_overview_default($atts, $booking, $time, $event) {

		$slots = $booking->booked_slots;
		$price = $booking->price;
		$total = $slots * $price;


		$ret = '<div id="btb_checkout_table">'; // START CONTAINER

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		// START CREATING BOOKING TABLE FULL

		$headerRow = new BTCTableRow();
		$headerRow->add_content(new BTCTableData(__('Event', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Date', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Single price', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Slots', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Total price', 'bt-booking'), array(), true));

		$header = new BTCTableHead(array(), $headerRow);

		$contentRow = new BTCTableRow();
		$contentRow->add_content(new BTCTableData($event->post_title));
		$contentRow->add_content(new BTCTableData($time->post_title));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));
		$contentRow->add_content(new BTCTableData($slots));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$body = new BTCTableBody(array(), array($contentRow));

		$bookingTableFull = new BTCTable(array('htmlClasses' => 'table table-responsive btb_checkout_full_table'), $body, $header);

		$ret .= $bookingTableFull->render(false);

		// END CREATING BOOKING TABLE FULL



		// START CREATING BOOKING TABLE SMALL

		$row1 = new BTCTableRow();
		$row1->add_content(new BTCTableData(__('Event', 'bt-booking'), array('scope' => 'row'), true));
		$row1->add_content(new BTCTableData($event->post_title));

		$row2 = new BTCTableRow();
		$row2->add_content(new BTCTableData(__('Date', 'bt-booking'), array('scope' => 'row'), true));
		$row2->add_content(new BTCTableData($time->post_title));

		$row3 = new BTCTableRow();
		$row3->add_content(new BTCTableData(__('Single price', 'bt-booking'), array('scope' => 'row'), true));
		$row3->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));

		$row4 = new BTCTableRow();
		$row4->add_content(new BTCTableData(__('Slots', 'bt-booking'), array('scope' => 'row'), true));
		$row4->add_content(new BTCTableData($slots));

		$row5 = new BTCTableRow();
		$row5->add_content(new BTCTableData(__('Total price', 'bt-booking'), array('scope' => 'row'), true));
		$row5->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$smallBody = new BTCTableBody(array(), array($row1, $row2, $row3, $row4, $row5));

		$bookingTableSmall = new BTCTable(array('htmlClasses' => 'table table-responsive btb_checkout_small_table'), $smallBody);

		$ret .= $bookingTableSmall->render(false);

		// END CREATING BOOKING TABLE SMALL

		$ret .= '</div>'; // END CONTAINER

		return $ret;

    }
    
    
    
    /**
     * Renders the shortcode output for the default style.
     *
     * @param array			$atts The shortcode attributes. See class description for explanation.
     * @param BTB_Booking	$booking The booking object.
     * @param BTB_Time		$time The time object.
     * @param BTB_Event		$event The event object.
     */
    private static function btb_checkout_overview_bs3($atts, $booking, $time, $event) {

		$slots = $booking->booked_slots;
		$price = $booking->price;
		$total = $slots * $price;


		$ret = '<div id="btb_checkout_table">'; // START CONTAINER

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		// START CREATING BOOKING TABLE FULL

		$headerRow = new BTCTableRow();
		$headerRow->add_content(new BTCTableData(__('Event', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Date', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Single price', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Slots', 'bt-booking'), array(), true));
		$headerRow->add_content(new BTCTableData(__('Total price', 'bt-booking'), array(), true));

		$header = new BTCTableHead(array(), $headerRow);

		$contentRow = new BTCTableRow();
		$contentRow->add_content(new BTCTableData($event->post_title));
		$contentRow->add_content(new BTCTableData($time->post_title));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));
		$contentRow->add_content(new BTCTableData($slots));
		$contentRow->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$body = new BTCTableBody(array(), array($contentRow));

		$bookingTableFull = new BTCTable(array('htmlClasses' => 'table table-responsive hidden-xs hidden-sm btb_checkout_full_table'), $body, $header);

		$ret .= $bookingTableFull->render(false);

		// END CREATING BOOKING TABLE FULL



		// START CREATING BOOKING TABLE SMALL

		$row1 = new BTCTableRow();
		$row1->add_content(new BTCTableData(__('Event', 'bt-booking'), array('scope' => 'row'), true));
		$row1->add_content(new BTCTableData($event->post_title));

		$row2 = new BTCTableRow();
		$row2->add_content(new BTCTableData(__('Date', 'bt-booking'), array('scope' => 'row'), true));
		$row2->add_content(new BTCTableData($time->post_title));

		$row3 = new BTCTableRow();
		$row3->add_content(new BTCTableData(__('Single price', 'bt-booking'), array('scope' => 'row'), true));
		$row3->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($price, 2, ',', '.'))));

		$row4 = new BTCTableRow();
		$row4->add_content(new BTCTableData(__('Slots', 'bt-booking'), array('scope' => 'row'), true));
		$row4->add_content(new BTCTableData($slots));

		$row5 = new BTCTableRow();
		$row5->add_content(new BTCTableData(__('Total price', 'bt-booking'), array('scope' => 'row'), true));
		$row5->add_content(new BTCTableData(sprintf("%s %s", get_option('btb_currency', '€'), number_format($total, 2, ',', '.'))));

		$smallBody = new BTCTableBody(array(), array($row1, $row2, $row3, $row4, $row5));

		$bookingTableSmall = new BTCTable(array('htmlClasses' => 'table table-responsive hidden-md hidden-lg btb_checkout_small_table'), $smallBody);

		$ret .= $bookingTableSmall->render(false);

		// END CREATING BOOKING TABLE SMALL

		$ret .= '</div>'; // END CONTAINER

		return $ret;

    }

}