<?php
/**
 * @file
 * @brief Implements the BTBooking_Checkout class.
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
require_once(BTB__PLUGIN_DIR . 'class.bt-booking-mails.php');

/**
 * Implements the @c btb_checkout shortcode.
 *
 * The @c btb_checkout shortcode handles the checkout process of Buschtrommel Booking. It shows a formular
 * to enter the customer details and can finalize the booking process.
 *
 * To show a summary of the booked item, use the @c btb_checkout_overview shortcode togehter with this shortcode.
 *
 * @par Available shortcode attributes
 * - @a headline Optional headline that is shown above the summary if set. Default: translated version of 'Booking data'.
 *
 * @par Example
 * @code
 [btb_checkout headline="Please enter your information"]
 * @endcode
 *
 * @since 1.0.0
 */
class BTBooking_Checkout {

	/**
	 * Registers the shortcode @c btb_checkout.
	 */
    public static function register_short_code() {
        add_shortcode( 'btb_checkout', array('BTBooking_Checkout','btb_checkout_func') );
    }

    /**
     * Processes the @c btb_checkout shortcode.
     *
     * This handles the POST data if avaialble and applies the @c btb_create_checkout_form filter generate
     * the display content. This filter is chosen based on the selected style.
     *
     * @param array $atts The shortcode attributes. See class description for explanation.
     */
    public static function btb_checkout_func($atts) {

		$master_instance = (get_option('btb_instance_type', 'master') == 'master');

		if (isset($_GET['booking']) && isset($_GET['btbnonce']) && !isset($_POST['btb_checkout_nonce'])) {

			// This part is executed before the customer has entered the data. It shows the form and enqueues
			// the necessary scripts.

			if (!wp_verify_nonce($_GET['btbnonce'], 'btb_direct_booking_nonce')) {
				return '<h4>' . esc_html__('Sorry, there has been an error', 'bt-booking') . '</h4><p>' . esc_html__('Security check failed.', 'bt-booking') . '</p>';
			}

			if ($master_instance) {
				$booking = btb_get_booking(intval($_GET['booking']));
			} else {
				$booking = btb_get_booking_from_api(intval($_GET['booking']), OBJECT, 'display');
			}

			if (!$booking) {
				return '<h4>' . esc_html__('Sorry, there has been an error', 'bt-booking') . '</h4><p>' . esc_html__('Booking not found. Maybe your booking session expired.', 'bt-booking') . '</p>';
			}

			if ($booking->post_type !== "btb_booking") {
				return '<h4>' . esc_html__('Sorry, there has been an error', 'bt-booking') . '</h4><p>' . esc_html__('Booking not found. Maybe your booking session expired.', 'bt-booking') . '</p>';
			}

			wp_localize_script( 'btb-scripts',
				'BTBooking',
                array(
					'require_terms' => get_option('btb_checkout_require_terms', 0),
					'strings' => array(
						'first_name' => __('First name', 'bt-booking'),
						'last_name'     => __('Last name', 'bt-booking'),
						'address' => __('Address', 'bt-booking'),
						'zip' => __('Postal code', 'bt-booking'),
						'city' => __('City', 'bt-booking'),
						'email' => __('E-mail address', 'bt-booking'),
						'phone' => __('Phone number', 'bt-booking'),
						'country' => __('Country', 'bt-booking'),
						'email_confirmation_failed' => __('The confirmation of your E-mail address failed. Please check your input.', 'bt-booking'),
						'email_check_failed' => __('The entered E-mail address seems not to be valid. Please check your input.', 'bt-booking'),
						'missing_input' => __('The following required fields are missing. Please check your input.', 'bt-booking'),
						'tems_not_accepted' => __('You have to accept our terms and conditions before you can proceed with your booking.', 'bt-booking')
					)
				)
			);

			wp_enqueue_script('btb-scripts');

			$a = shortcode_atts(
				array(
					'headline' => __('Booking data', 'bt-booking')
				),
				$atts
			);

			return apply_filters('btb_create_checkout_form', '', $booking->ID, $a);

		} else if (isset($_POST['btb_checkout_bookingid']) && isset($_POST['btb_checkout_nonce'])) {

			if (!wp_verify_nonce($_POST['btb_checkout_nonce'], 'btb_checkout_data')) {
				return '<h4>' . esc_html__('Sorry, there has been an error', 'bt-booking') . '</h4><p>' . esc_html__('Security check failed.', 'bt-booking') . '</p>';
			}

			$booking_id = $_POST['btb_checkout_bookingid'];

			if ($master_instance) {
				$booking = btb_get_booking($booking_id);
			} else {
				$booking = btb_get_booking_from_api($booking_id, OBJECT, 'display');
			}

			if ($_POST['btb_checkout_cancel'] == "true") {

				if ($booking && $booking->post_type == "btb_booking") {

					if ($master_instance) {
						$desc_page = btb_get_description_page($booking, true);
					}

					if ($master_instance) {
						btb_delete_booking($booking->ID, true);
					} else {
						btb_delete_booking_via_api($booking->ID, true);
					}

					$ret  = '<h4>' . esc_html__('Booking canceled', 'bt-booking') . '</h4>';

					$ret .= '<p>' . esc_html__('Your booking has been canceled.', 'bt-booking');

					if ($master_instance) {
						$ret .= ' <a href="' . $desc_page . '">' . esc_html__('Back to the offer.', 'bt-booking') . '</a></p>';
					}

					return $ret;

				}

			}

			if (!$booking || ($booking->post_type !== "btb_booking")) {
				return '<h4>' . esc_html__('Sorry, there has been an error', 'bt-booking') . '</h4><p>' . esc_html__('Booking not found. Maybe your booking session expired.', 'bt-booking') . '</p>';
			}

			$booking->title		= $_POST['btb_checkout_title'];
			$booking->first_name	= sanitize_text_field($_POST['btb_checkout_first_name']);
			$booking->last_name	= sanitize_text_field($_POST['btb_checkout_last_name']);
			$booking->company	= isset($_POST['btb_checkout_company']) ? sanitize_text_field($_POST['btb_checkout_company']) : null;
			$booking->address	= sanitize_text_field($_POST['btb_checkout_address']);
			$booking->address2	= isset($_POST['btb_checkout_address2']) ? sanitize_text_field($_POST['btb_checkout_address2']) : null;
			$booking->zip		= sanitize_text_field($_POST['btb_checkout_zip']);
			$booking->city		= sanitize_text_field($_POST['btb_checkout_city']);
                        $booking->country	= $_POST['btb_checkout_country'];
			$booking->email		= sanitize_email($_POST['btb_checkout_mail']);
			$booking->phone		= sanitize_text_field($_POST['btb_checkout_phone']);
			$booking->notes		= isset($_POST['btb_checkout_notes']) ? sanitize_text_field($_POST['btb_checkout_notes']) : null;
			$booking->booking_time	= time();
			$booking->booking_status = 'btb_booked';


			if (btb_update_booking($booking) == 0) {

				if ($master_instance) {
					$desc_page = btb_get_description_page($booking, true);

					btb_delete_booking($booking->ID, true);
				} else {
					btb_delete_booking_via_api($booking->ID, true);
				}

				$ret  = '<h4>' . esc_html__('Sorry, but we failed to process your booking.', 'bt-booking') . '</h4>';

				$ret .= '<p>' . esc_html__('When updating your data an error has occured.', 'bt-booking');

				if ($master_instance) {
					$ret .= ' <a href="' . $desc_page . '">' . esc_html__('Please try it again.', 'bt-booking') . '</a></p>';
				}

				$ret .= '<p>' . esc_html__('If this error still occures:', 'bt-booking') . ' <a href="' . get_permalink(get_option('btb_general_contact_page')) . '">' . esc_html__('Please contact us.', 'bt-booking') . '</a></p>';

				return $ret;

			}

			$mail_success = self::send_mails($booking);

			$ret  = '<h4>' . esc_html__('Thank you four your booking.', 'bt-booking') . '</h4>';

			// everything went fine
			if ($mail_success == 1) {
				$ret .= '<p>' . esc_html__('We have successfully received your booking. You will soon receive a confirmation to your e-mail address.', 'bt-booking') . '</p>';
			}

			// mail to operator failed
			if ($mail_success == 0) {
				$ret .= '<p>' . esc_html__('We have successfully received your booking. You will soon receive a confirmation to your e-mail address.', 'bt-booking') . '</p>';

				$ret .= '<p>' . esc_html__('Unfortunately, no notification e-mail could be sent to the site operator. Neverthelss your booking has been added properly to our system. If you want to inform us of this error:', 'bt-booking') . ' <a href="' . get_permalink(get_option('btb_general_contact_page')) . '">' . esc_html__('Please contact us.', 'bt-booking') . '</a></p>';
			}

			// mail to customer failed
			if ($mail_success == -1) {
				$ret .= '<p>' . esc_html__('We have successfully received your booking, but unfortunately there was a problem with delivery of the confirmation to your e-mail address. Therefore, please write down your booking code. Neverthelss your booking has been added properly to our system. In order to still send you a confirmation:', 'bt-booking') . ' <a href="' . get_permalink(get_option('btb_general_contact_page')) . '">' . esc_html__('Please contact us.', 'bt-booking') . '</a></p>';
			}

			// mail to operator and customer failed
			if ($mail_success == -2) {
				$ret .= '<p>' . esc_html__('We have successfully received your booking, but unfortunately there was a problem with delivery of the notification to the operator and with sending the confirmation to your e-mail address. Therefore, please write down your booking code. Neverthelss your booking has been added properly to our system. In order to still send you a confirmation:', 'bt-booking') . ' <a href="' . get_permalink(get_option('btb_general_contact_page')) . '">' . esc_html__('Please contact us.', 'bt-booking') . '</a></p>';
			}

			$ret .= '<p>' . esc_html__('Your booking code:', 'bt-booking') . ' ' . $booking->code . '</p>';

			return $ret;

		}

        ?>

        <?php
    }


    /**
     * Displays the shortcode content based on the selected style.
     *
     * @param array $atts The shortcode attributes. See class description for explanation.
     * @param int $bookingid The ID of the BTB_Booking to process.
     */
    private static function enter_data($atts, $bookingid) {

// 		wp_localize_script('btb-country-chooser-script');
//
// 		wp_enqueue_script('btb-country-chooser-script');

		$a = shortcode_atts( array(
					'headline' => __('Booking data', 'bt-booking')
				), $atts );

        switch(get_option('btb_style', 'custom')) {
            case 'avada';
                return self::enter_data_avada($a, $bookingid);
            default:
                break;
        }

    }


    /**
     * Displays the shortcode content for the avada style.
     *
     * @param string $input The input string, can be empty.
     * @param int $bookingid The ID of the BTB_Booking to process.
     * @param array $atts The shortcode attributes. See class description for explanation.
     */
    public static function avada_style_filter($input, $bookingid, array $atts) {

		$ret  = $input;

//         $ret .= '<div id="btb_checkout_table">';

		// START CREATING FORM

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		$ret .= '<form id="btb_checkout_form" method="post" onSubmit="return checkForm()">';

			$ret .= wp_nonce_field('btb_checkout_data', 'btb_checkout_nonce', true, false);

			$ret .= '<input type="hidden" value="' . $bookingid . '" id="btb_checkout_bookingid" name="btb_checkout_bookingid">';
			$ret .=	'<input type="hidden" value="false" id="btb_checkout_cancel" name="btb_checkout_cancel">';

			$ret .= '<div class="row btb_checkout_row">';

				$formLabel = new BTCFormLabel(array('for' => 'btb_checkout_title'), __('Form of address', 'bt-booking'));
				$formSelect = new BTCFormSelect(array('mr' => __('Mr.', 'bt-booking'), 'mrs' => __('Mrs.', 'bt-booking')), array('id' => 'btb_checkout_title'));
				$ret .= '<div class="col-md-2">' . $formLabel->render(false) . '<br>' . $formSelect->render(false) . '</div>';

				$firstNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_first_name'), __('First name', 'bt-booking') . '<span style="color:red">*</span>');
				$firstNameInput = new BTCInputText(array('id' => 'btb_checkout_first_name', 'required' => true));
				$ret .= '<div class="col-md-5">' . $firstNameLabel->render(false) . $firstNameInput->render(false) . '</div>';

				$lastNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_last_name'), __('Last name', 'bt-booking') . '<span style="color:red">*</span>');
				$lastNameInput = new BTCInputText(array('id' => 'btb_checkout_last_name', 'required' => true));
				$ret .= '<div class="col-md-5">' . $lastNameLabel->render(false) . $lastNameInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$addressLabel = new BTCFormLabel(array('for' => 'btb_checkout_address'), __('Address', 'bt-booking') . '<span style="color:red">*</span>');
				$addressInput = new BTCInputText(array('id' => 'btb_checkout_address', 'required' => true));
				$ret .= '<div class="col-md-12">' . $addressLabel->render(false) . $addressInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$plzLabel = new BTCFormLabel(array('for' => 'btb_checkout_zip'), __('Postal code', 'bt-booking') . '<span style="color:red">*</span>');
				$plzInput = new BTCInputText(array('id' => 'btb_checkout_zip', 'required' => true));
				$ret .= '<div class="col-md-3">' . $plzLabel->render(false) . $plzInput->render(false) . '</div>';

				$cityLabel = new BTCFormLabel(array('for' => 'btb_checkout_city'), __('City', 'bt-booking') . '<span style="color:red">*</span>');
				$cityInput = new BTCInputText(array('id' => 'btb_checkout_city', 'required' => true));
				$ret .= '<div class="col-md-4">' . $cityLabel->render(false) . $cityInput->render(false) . '</div>';

				$countryLabel = new BTCFormLabel(array('for' => 'btb_checkout_country'), __('Country', 'bt-booking') . '<span style="color:red">*</span>');
				$countrySelect = new BTCFormSelect(BTBookingCountries::get_countries(), array('id' => 'btb_checkout_country'));
				$ret .= '<div class="col-md-5">' . $countryLabel->render(false) . '<br>' . $countrySelect->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$phoneLabel = new BTCFormLabel(array('for' => 'btb_checkout_phone'), __('Phone number', 'bt-booking') . '<span style="color:red">*</span>');
				$phoneInput = new BTCInputText(array('id' => 'btb_checkout_phone', 'required' => true, 'pattern' => '^[+0123456789][\/\-\d\s]*'));
				$ret .= '<div class="col-md-6">' . $phoneLabel->render(false) . $phoneInput->render(false) . '</div>';

				$emailLabel = new BTCFormLabel(array('for' => 'btb_checkout_mail'), __('E-mail address', 'bt-booking') . '<span style="color:red">*</span>');
				$emailInput = new BTCInputText(array('id' => 'btb_checkout_mail', 'required' => true));
				$ret .= '<div class="col-md-6">' . $emailLabel->render(false) . $emailInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12">';

			$ret .= '<label for="btb_checkout_notes">' . __('Booking notes', 'bt-booking') . '</label><textarea style="width:100%" rows=5 id="btb_checkout_notes" name="btb_checkout_notes" placeholder="' . get_option('btb_checkout_notes_placeholder', __('Notes for your booking', 'bt-booking')) . '"></textarea>';

			$ret .= '</div></div>';

			$info = get_option('btb_checkout_info', '');
			if (!empty($info)) $ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="btb_checkout_info">' . $info . '</div></div></div>';


			if (get_option('btb_checkout_require_terms', 0)) {

				$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><fieldset><label for="btb_checkout_terms_accepted"><input type="checkbox" id="btb_checkout_terms_accepted" name="btb_checkout_terms_accepted" value="1" required></input>' . get_option('btb_checkout_require_text', '') . '</label></fieldset></div></div>';
			}

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="error_message_container" style="display:none"></div></div></div>';

			$ret .= '<div class="fusion-row btb_checkout_row">';

				$ret .= '<button style="float:right" type="submit" class="fusion-button button-default button-small alt">' . get_option('btb_checkout_book_now_text', __('Book now', 'bt-booking')) . '</button>';

				$ret .= '<button formnovalidate onClick="cancelBooking()" style="float:left" type="submit" class="fusion-button button-default button-small alt">' . __('Cancel booking', 'bt-booking') . '</button>';

			$ret .= '</div>';

		$ret .= '</form>';

		// END CREATING FORM

//         $ret .= '</div>'; // END CONTAINER

        return $ret;

    }



    /**
     * Displays the shortcode content for the default style.
     *
     * @param string $input The input string, can be empty.
     * @param int $bookingid The ID of the BTB_Booking to process.
     * @param array $atts The shortcode attributes. See class description for explanation.
     */
    public static function default_style_filter($input, $bookingid, array $atts) {

		$ret  = $input;

//         $ret .= '<div id="btb_checkout_table">';

		// START CREATING FORM

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		$ret .= '<form id="btb_checkout_form" method="post" onSubmit="return checkForm()">';

			$ret .= wp_nonce_field('btb_checkout_data', 'btb_checkout_nonce', true, false);

			$ret .= '<input type="hidden" value="' . $bookingid . '" id="btb_checkout_bookingid" name="btb_checkout_bookingid">';
			$ret .=	'<input type="hidden" value="false" id="btb_checkout_cancel" name="btb_checkout_cancel">';

			$ret .= '<div class="row btb_checkout_row">';

				$formLabel = new BTCFormLabel(array('for' => 'btb_checkout_title'), __('Form of address', 'bt-booking'));
				$formSelect = new BTCFormSelect(array('mr' => __('Mr.', 'bt-booking'), 'mrs' => __('Mrs.', 'bt-booking')), array('id' => 'btb_checkout_title'));
				$ret .= '<div class="col-md-2">' . $formLabel->render(false) . '<br>' . $formSelect->render(false) . '</div>';

				$firstNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_first_name'), __('First name', 'bt-booking') . '<span style="color:red">*</span>');
				$firstNameInput = new BTCInputText(array('id' => 'btb_checkout_first_name', 'required' => true));
				$ret .= '<div class="col-md-5">' . $firstNameLabel->render(false) . $firstNameInput->render(false) . '</div>';

				$lastNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_last_name'), __('Last name', 'bt-booking') . '<span style="color:red">*</span>');
				$lastNameInput = new BTCInputText(array('id' => 'btb_checkout_last_name', 'required' => true));
				$ret .= '<div class="col-md-5">' . $lastNameLabel->render(false) . $lastNameInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$addressLabel = new BTCFormLabel(array('for' => 'btb_checkout_address'), __('Address', 'bt-booking') . '<span style="color:red">*</span>');
				$addressInput = new BTCInputText(array('id' => 'btb_checkout_address', 'required' => true));
				$ret .= '<div class="col-md-12">' . $addressLabel->render(false) . $addressInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$plzLabel = new BTCFormLabel(array('for' => 'btb_checkout_zip'), __('Postal code', 'bt-booking') . '<span style="color:red">*</span>');
				$plzInput = new BTCInputText(array('id' => 'btb_checkout_zip', 'required' => true));
				$ret .= '<div class="col-md-3">' . $plzLabel->render(false) . $plzInput->render(false) . '</div>';

				$cityLabel = new BTCFormLabel(array('for' => 'btb_checkout_city'), __('City', 'bt-booking') . '<span style="color:red">*</span>');
				$cityInput = new BTCInputText(array('id' => 'btb_checkout_city', 'required' => true));
				$ret .= '<div class="col-md-4">' . $cityLabel->render(false) . $cityInput->render(false) . '</div>';

				$countryLabel = new BTCFormLabel(array('for' => 'btb_checkout_country'), __('Country', 'bt-booking') . '<span style="color:red">*</span>');
				$countrySelect = new BTCFormSelect(BTBookingCountries::get_countries(), array('id' => 'btb_checkout_country'));
				$ret .= '<div class="col-md-5">' . $countryLabel->render(false) . '<br>' . $countrySelect->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$phoneLabel = new BTCFormLabel(array('for' => 'btb_checkout_phone'), __('Phone number', 'bt-booking') . '<span style="color:red">*</span>');
				$phoneInput = new BTCInputText(array('id' => 'btb_checkout_phone', 'required' => true, 'pattern' => '^[+0123456789][\/\-\d\s]*'));
				$ret .= '<div class="col-md-6">' . $phoneLabel->render(false) . $phoneInput->render(false) . '</div>';

				$emailLabel = new BTCFormLabel(array('for' => 'btb_checkout_mail'), __('E-mail address', 'bt-booking') . '<span style="color:red">*</span>');
				$emailInput = new BTCInputText(array('id' => 'btb_checkout_mail', 'required' => true));
				$ret .= '<div class="col-md-6">' . $emailLabel->render(false) . $emailInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12">';

			$ret .= '<label for="btb_checkout_notes">' . __('Booking notes', 'bt-booking') . '</label><textarea style="width:100%" rows=5 id="btb_checkout_notes" name="btb_checkout_notes" placeholder="' . get_option('btb_checkout_notes_placeholder', __('Notes for your booking', 'bt-booking')) . '"></textarea>';

			$ret .= '</div></div>';

			$info = get_option('btb_checkout_info', '');
			if (!empty($info)) $ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="btb_checkout_info">' . $info . '</div></div></div>';


			if (get_option('btb_checkout_require_terms', 0)) {

				$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><fieldset><label for="btb_checkout_terms_accepted"><input type="checkbox" id="btb_checkout_terms_accepted" name="btb_checkout_terms_accepted" value="1" required></input>' . get_option('btb_checkout_require_text', '') . '</label></fieldset></div></div>';
			}

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="error_message_container" style="display:none"></div></div></div>';

			$ret .= '<div class="fusion-row btb_checkout_row">';

				$ret .= '<button style="float:right" type="submit" class="fusion-button button-default button-small alt">' . get_option('btb_checkout_book_now_text', __('Book now', 'bt-booking')) . '</button>';

				$ret .= '<button formnovalidate onClick="cancelBooking()" style="float:left" type="submit" class="fusion-button button-default button-small alt">' . __('Cancel booking', 'bt-booking') . '</button>';

			$ret .= '</div>';

		$ret .= '</form>';

		// END CREATING FORM

//         $ret .= '</div>'; // END CONTAINER

        return $ret;

    }



    /**
     * Displays the shortcode content for the Bootstrap 3 style.
     *
     * @param string $input The input string, can be empty.
     * @param int $bookingid The ID of the BTB_Booking to process.
     * @param array $atts The shortcode attributes. See class description for explanation.
     */
    public static function bs3_style_filter($input, $bookingid, array $atts) {

		$ret  = $input;

//         $ret .= '<div id="btb_checkout_table">';

		// START CREATING FORM

		if (!empty($atts['headline'])) {
			$ret .= '<h3>' . $atts['headline'] . '</h3>';
		}

		$ret .= '<form id="btb_checkout_form" method="post" onSubmit="return checkForm()">';

			$ret .= wp_nonce_field('btb_checkout_data', 'btb_checkout_nonce', true, false);

			$ret .= '<input type="hidden" value="' . $bookingid . '" id="btb_checkout_bookingid" name="btb_checkout_bookingid">';
			$ret .=	'<input type="hidden" value="false" id="btb_checkout_cancel" name="btb_checkout_cancel">';

			$ret .= '<div class="row btb_checkout_row">';

				$formLabel = new BTCFormLabel(array('for' => 'btb_checkout_title'), __('Form of address', 'bt-booking'));
				$formSelect = new BTCFormSelect(array('mr' => __('Mr.', 'bt-booking'), 'mrs' => __('Mrs.', 'bt-booking')), array('id' => 'btb_checkout_title'), array('htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-2">' . $formLabel->render(false) . '<br>' . $formSelect->render(false) . '</div>';

				$firstNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_first_name'), __('First name', 'bt-booking') . '<span style="color:red">*</span>');
				$firstNameInput = new BTCInputText(array('id' => 'btb_checkout_first_name', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-5">' . $firstNameLabel->render(false) . $firstNameInput->render(false) . '</div>';

				$lastNameLabel = new BTCFormLabel(array('for' => 'btb_checkout_last_name'), __('Last name', 'bt-booking') . '<span style="color:red">*</span>');
				$lastNameInput = new BTCInputText(array('id' => 'btb_checkout_last_name', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-5">' . $lastNameLabel->render(false) . $lastNameInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$addressLabel = new BTCFormLabel(array('for' => 'btb_checkout_address'), __('Address', 'bt-booking') . '<span style="color:red">*</span>');
				$addressInput = new BTCInputText(array('id' => 'btb_checkout_address', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-12">' . $addressLabel->render(false) . $addressInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$plzLabel = new BTCFormLabel(array('for' => 'btb_checkout_zip'), __('Postal code', 'bt-booking') . '<span style="color:red">*</span>');
				$plzInput = new BTCInputText(array('id' => 'btb_checkout_zip', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-3">' . $plzLabel->render(false) . $plzInput->render(false) . '</div>';

				$cityLabel = new BTCFormLabel(array('for' => 'btb_checkout_city'), __('City', 'bt-booking') . '<span style="color:red">*</span>');
				$cityInput = new BTCInputText(array('id' => 'btb_checkout_city', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-4">' . $cityLabel->render(false) . $cityInput->render(false) . '</div>';

				$countryLabel = new BTCFormLabel(array('for' => 'btb_checkout_country'), __('Country', 'bt-booking') . '<span style="color:red">*</span>');
				$countrySelect = new BTCFormSelect(BTBookingCountries::get_countries(), array('id' => 'btb_checkout_country', 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-5">' . $countryLabel->render(false) . '<br>' . $countrySelect->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row">';

				$phoneLabel = new BTCFormLabel(array('for' => 'btb_checkout_phone'), __('Phone number', 'bt-booking') . '<span style="color:red">*</span>');
				$phoneInput = new BTCInputText(array('id' => 'btb_checkout_phone', 'required' => true, 'pattern' => '^[+0123456789][\/\-\d\s]*', 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-6">' . $phoneLabel->render(false) . $phoneInput->render(false) . '</div>';

				$emailLabel = new BTCFormLabel(array('for' => 'btb_checkout_mail'), __('E-mail address', 'bt-booking') . '<span style="color:red">*</span>');
				$emailInput = new BTCInputText(array('id' => 'btb_checkout_mail', 'required' => true, 'htmlClasses' => 'form-control'));
				$ret .= '<div class="form-group col-md-6">' . $emailLabel->render(false) . $emailInput->render(false) . '</div>';

			$ret .= '</div>';

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12">';

			$ret .= '<label for="btb_checkout_notes">' . __('Booking notes', 'bt-booking') . '</label><textarea style="width:100%" rows=5 id="btb_checkout_notes" name="btb_checkout_notes" placeholder="' . get_option('btb_checkout_notes_placeholder', __('Notes for your booking', 'bt-booking')) . '"></textarea>';

			$ret .= '</div></div>';

			$info = get_option('btb_checkout_info', '');
			if (!empty($info)) $ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="btb_checkout_info">' . $info . '</div></div></div>';


			if (get_option('btb_checkout_require_terms', 0)) {

				$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><fieldset><label for="btb_checkout_terms_accepted"><input type="checkbox" id="btb_checkout_terms_accepted" name="btb_checkout_terms_accepted" value="1" required></input>' . get_option('btb_checkout_require_text', '') . '</label></fieldset></div></div>';
			}

			$ret .= '<div class="row btb_checkout_row"><div class="col-md-12"><div id="error_message_container" style="display:none"></div></div></div>';

			$ret .= '<div class="btb_checkout_row">';

				$ret .= '<button style="float:right" type="submit" class="btn btn-primary btn-sm">' . get_option('btb_checkout_book_now_text', __('Book now', 'bt-booking')) . '</button>';

				$ret .= '<button formnovalidate onClick="cancelBooking()" style="float:left" type="submit" class="btn btn-warning btn-sm">' . __('Cancel booking', 'bt-booking') . '</button>';

			$ret .= '</div>';

		$ret .= '</form>';

		// END CREATING FORM

//         $ret .= '</div>'; // END CONTAINER

        return $ret;

    }





    /**
     * Sends e-mails to customer and wordpress owner.
     *
     * @param BTB_Booking &$booking The booking object to send mails for.
     * @return int 1 if successfull, 0 if mail to owner failed, -1 if mail to customer failed, -2 if both mails failed
     */
    private static function send_mails(&$booking) {

		$notifyemail = get_option('btb_notify_to', ''); // email address of the site owner to notify about new bookings
// 		$fromemail = get_option('btb_confirm_from', ''); // email address used as from address when sending booking to customer
                $fromemail = get_option('btb_email_fromname', '') . ' <' . get_option('btb_email_from', '') . '>';

		if (empty($notifyemail) && empty($fromemail)) {
			return -2;
		}

		$tags = array(
			'{{salutation}}',
			'{{title}}',
			'{{first_name}}',
			'{{last_name}}',
			'{{company}}',
			'{{address}}',
			'{{address2}}',
			'{{zip}}',
			'{{city}}',
			'{{country}}',
			'{{mail}}',
			'{{phone}}',
			'{{notes}}',
			'{{event_name}}',
			'{{event_url}}',
			'{{event_start_date}}',
			'{{event_end_date}}',
			'{{event_start_time}}',
			'{{event_end_time}}',
			'{{slots}}',
			'{{single_price}}',
			'{{total_price}}',
			'{{booking_code}}',
			'{{booking_time}}'
		);

		if (get_option('btb_instance_type', 'master') == 'master') {
			$event = btb_get_event($booking->booked_event);
			$time = btb_get_time($booking->booked_time);
		} else {
			$event = btb_get_event_from_api($booking->booked_event, OBJECT, 'display');
			$time = btb_get_time_from_api($booking->booked_time, OBJECT, 'display');
		}

		$replacements = array(
			$booking->title == "mr" ? sprintf(__('Dear Mr. %s %s', 'bt-booking'), $booking->first_name, $booking->last_name) : sprintf(__('Dear Mrs. %s %s', 'bt-booking'), $booking->first_name, $booking->last_name),
			$booking->title == "mr" ? __('Mr.', 'bt-booking', 'bt-booking') : __('Mrs.', 'bt-booking'),
			$booking->first_name,
			$booking->last_name,
			$booking->company ? $booking->company : '',
			$booking->address,
			$booking->address2,
			$booking->zip,
			$booking->city,
			BTBookingCountries::get_country_by_code($booking->country),
			$booking->email,
			$booking->phone,
			$booking->notes,
			$event->name,
			btb_get_description_page($event, true),
			wp_date(_x('m/d/Y', 'Event date shown in e-mails', 'bt-booking'), $time->start),
			wp_date(_x('m/d/Y', 'Event date shown in e-mails', 'bt-booking'), $time->end),
			$time->date_only ? '' : wp_date(_x('h:iA', 'Event time shown in e-mails', 'bt-booking'), $time->start),
			$time->date_only ? '' : wp_date(_x('h:iA', 'Event time shown in e-mails', 'bt-booking'), $time->end),
			$booking->booked_slots,
			get_option('btb_currency', '€') .  ' ' . number_format_i18n($booking->price, 2),
			get_option('btb_currency', '€') .  ' ' . number_format_i18n($booking->price * $booking->booked_slots, 2),
			$booking->code,
			wp_date(_x('m/d/Y h:iA', 'Booking time shown in e-mails', 'bt-booking'), $booking->booking_time)
		);

		$ret = 1;

		if (!empty($notifyemail) && !empty($fromemail)) {
			if (!self::send_mail_to_owner($tags, $replacements, $notifyemail, $fromemail, $booking)) {
				$ret = 0;
			}
		} else {
			$ret = 0;
		}

		if (!empty($fromemail)) {
			if (!self::send_mail_to_customer($tags, $replacements, $booking, $fromemail)) {
				$ret = $ret - 2;
			}
		} else {
			$ret = $ret - 2;
		}

		return $ret;
    }


    private static function send_mail_to_owner(&$tags, &$replacements, &$notifyemail, &$fromemail, &$booking) {

		$replyto = $booking->first_name . ' ' . $booking->last_name . ' <' . $booking->email . '>';

		$mail = new BTBooking_Mails();
		$mail->sender = $fromemail;
		$mail->recipient = $notifyemail;
		$mail->replyto = $replyto;
		$mail->subject = str_replace($tags, $replacements, get_option('btb_notify_subject', __('New booking', 'bt-booking')));
		$mail->body = str_replace($tags, $replacements, get_option('btb_notify_template'));
		$mail->html = get_option('btb_notify_html', 0) == 1;
		return $mail->send();

    }


    private static function send_mail_to_customer(&$tags, &$replacements, &$booking, &$fromemail) {

		$recipient = $booking->first_name . ' ' . $booking->last_name . ' <' . $booking->email . '>';
		$mail = new BTBooking_Mails();
		$mail->sender = $fromemail;
		$mail->recipient = $recipient;
		$mail->replyto = get_option('btb_confirm_replyto', get_option('btb_notify_to', ''));
		$mail->subject = str_replace($tags, $replacements, get_option('btb_confirm_subject', __('Your booking', 'bt-booking')));
		$mail->body = str_replace($tags, $replacements, get_option('btb_confirm_template'));
		$mail->html = get_option('btb_confirm_html', 0) == 1;
		return $mail->send();
    }

}

?>
