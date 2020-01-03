<?php
/**
 * @file
 * @brief Implements the BTBooking_Admin_Edit_Booking class.
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

/** @ingroup globalfns
 * @brief Creates a new BTBooking_Admin_Edit_Booking object.
 */
function call_BTBooking_Admin_Edit_Booking() {
    new BTBooking_Admin_Edit_Event();
}


/**
 * Core class used to implement the BTBooking_Admin_Edit_Booking object.
 *
 * This class provides the scripts and meta boxes hooks for editing the BTB_Booking post type.
 *
 * @todo Implement data saving.
 * @todo Implement seller notes.
 */
class BTBooking_Admin_Edit_Booking {

	/**
	 * Constructor
	 *
	 * Adds BTBooking_Admin_Edit_Booking::btb_save_booking_address_box to the @c save_post_btb_booking action.
	 */
    public function __construct() {
        add_action('save_post_btb_booking', array($this, 'btb_save_booking_address_box'));
    }


    /**
     * Enqueus needed scripts and adds meta boxes.
     */
    public static function add_btb_booking_meta_boxes() {
		wp_enqueue_script('btb-admin-scripts');
		remove_meta_box('submitdiv', 'btb_booking', 'normal');
		add_meta_box('submitdiv', __('Modify data', 'bt-booking'), array('BTBooking_Admin_Edit_Booking', 'btb_booking_modify_box'), 'btb_booking', 'normal', 'high');
		add_meta_box('btb_booking_data_box', __('Booking data', 'bt-booking'), array('BTBooking_Admin_Edit_Booking', 'btb_booking_data_box'), 'btb_booking', 'normal', 'high');
		add_meta_box('btb_booking_address_box', __('Customer address', 'bt-booking'), array('BTBooking_Admin_Edit_Booking', 'btb_booking_address_box'), 'btb_booking', 'normal', 'high');
		add_meta_box('btb_booking_customer_notes_box', _('Customer notes', 'bt-booking'), array('BTBooking_Admin_Edit_Booking', 'btb_booking_customer_notes_box'), 'btb_booking', 'normal', 'high');
    }


    /**
     * Provides the meta box for modifying booking data.
     *
     * The box provides the save button and a checkbox to initiate the booking process
     * as well as moving a booking into the trash.
     *
     * @param object $post
     */
    public static function btb_booking_modify_box($post) {
		global $action;
		?>

		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div style="display:none;">
				<?php submit_button(__('Save'), 'button', 'save'); ?>
				</div>

				<div id="minor-publishing-actions">
					<div id="save-action">
						<label for="edit-checker">
							<input type="checkbox" name="edit-checker" id="edit-checker" required>
							<?php esc_html_e('Enable edit mode', 'bt-booking'); ?>
						</label>
						<input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save'); ?>" class="button" />
						<span class="spinner"></span>
					</div>

					<?php do_action('post_submitbox_minor_actions', $post); ?>
					<div class="clear"></div>
				</div>

				<div id="misc-publishing-actions">
					<?php do_action('post_submitbox_misc_actions', $post); ?>
					<div class="clear"></div>
				</div>

				<div id="major-publishing-actions">
					<?php do_action('post_submitbox_start'); ?>
					<div id="delete-action">
					<?php
					if (current_user_can("delete_post", $post->ID)) {
						if (!EMPTY_TRASH_DAYS) {
							$delete_text = __('Delete Permanently');
						} else {
							$delete_text = __('Move to Trash');
						}
					?>
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a>
					<?php } ?>
					</div>
					<div class="clear"></div>
				</div>

			</div>
		</div>

		<?php
    }


    /**
     * Provides the meta box showing the booking data.
     *
     * This meta box shows booking data like booking code, booked evetn and time, booked slots, etc.
     *
     * @param object $post
     */
    public static function btb_booking_data_box($post) {

		$booking = btb_get_booking($post->ID);

		wp_nonce_field('btb_save_booking_data_box_data', 'btb_booking_data_box_nonce');

		$time = get_post($post->post_parent);
		$eventid = $time->post_parent;

		$stati = array(
			'btb_booked' => _x( 'Booked', 'Status General Name', 'bt-booking' ),
			'btb_prebook' => _x( 'Prebooked', 'Status General Name', 'bt-booking' ),
			'btb_canceled' => _x( 'Canceled', 'Status General Name', 'bt-booking' )
		);

		// Creating first row, showing booking code and booking status
		$row = new BTCTableRow();
		$row->add_content(BTCWPAdminLabelWithText::create('btb_code', __('Booking code', 'bt-booking'), $booking->code));
		$row->add_content(BTCWPAdminInputSelect::create('post_status', __('Booking status', 'bt-booking'), $booking->booking_status, $stati, true));

		// Creating second row, showing slots and booking time
		$row2 = new BTCTableRow();
		$row2->add_content(BTCWPAdminLabelWithText::create('btb_slots', __('Booked slots', 'bt-booking'), $booking->booked_slots));
		$row2->add_content(BTCWPAdminLabelWithText::create('btb_booking_time', __('Booking time', 'bt-booking'), wp_date(get_option('date_format') . ' ' . get_option('time_format'), $booking->booking_time)));

		// Creating third row, showing event name and booked time
		$row3 = new BTCTableRow();
		$row3->add_content(BTCWPAdminLabelWithText::create('event_title', __('Event', 'bt-booking'), '<a href="'. get_edit_post_link($booking->booked_event) .'">' . get_the_title($booking->booked_event) . '</a>'));
		$row3->add_content(BTCWPAdminLabelWithText::create('event_time', __('Event date', 'bt-booking'), get_the_title($booking->booked_time)));

		$row4 = new BTCTableRow();
		$row4->add_content(BTCWPAdminLabelWithText::create('unit_price', __('Unit price', 'bt-booking'), get_option('btb_currency', '€') .  ' ' .  number_format_i18n($booking->price, 2)));
		$row4->add_content(BTCWPAdminLabelWithText::create('total_price', __('Total price', 'bt-booking'), get_option('btb_currency', '€') .  ' ' .  number_format_i18n($booking->price * $booking->booked_slots, 2)));

		$body = new BTCTableBody(array(), array($row, $row2, $row3, $row4));

		$table = new BTCTable(array('htmlClasses' => 'form-table'), $body);

		$table->render();

    }



    /**
     * Saves the data provided by btb_booking_data_box.
     *
     * @param int $booking_id The ID of the BTB_Booking to save.
     */
    public static function btb_save_booking_address_box($booking_id) {

		if (!isset($_POST['btb_booking_data_box_nonce'])) {
			return;
		}

		if (!wp_verify_nonce($_POST['btb_booking_data_box_nonce'], 'btb_save_booking_data_box_data')) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $booking_id;
		}

		if (!current_user_can('edit_page', $booking_id)) {
			return $booking_id;
		}

    }



    /**
     * Provides the meta box showing the customers identity data.
     *
     * @param object $post
     */
    public static function btb_booking_address_box($post) {

		$booking = btb_get_booking($post->ID);

        wp_nonce_field('btb_save_booking_address_box_data', 'btb_booking_addres_box_nonce');

		$companyrow = new BTCTableRow();
		$companyrow->add_content(BTCWPAdminInputSelect::create('btb_title', __('Form of address', 'bt-booking'), $booking->title, array('mr' => __('Mr.', 'bt-booking'), 'mrs' => __('Mrs.', 'bt-booking')), true));
		$companyrow->add_content(BTCWPAdminInputText::create('btb_company', __('Company', 'bt-booking'), $booking->company, true));

		$namerow = new BTCTableRow();
		$namerow->add_content(BTCWPAdminInputText::create('btb_first_name', __('First name', 'bt-booking'), $booking->first_name, true));
		$namerow->add_content(BTCWPAdminInputText::create('btb_last_name', __('Last name', 'bt-booking'), $booking->last_name, true));

		$address = get_post_meta($post->ID, 'btb_address');
		$a = $address[0];

		$addressrow = new BTCTableRow();
		$addressrow->add_content(BTCWPAdminInputText::create('btb_address', __('Address', 'bt-booking'), $booking->address, true));
		$addressrow->add_content(BTCWPAdminInputText::create('btb_address2', __('Additional address', 'bt-booking'), $booking->address2, true));

		$cityrow = new BTCTableRow();
		$cityrow->add_content(BTCWPAdminInputText::create('btb_city', __('City', 'bt-booking'), $booking->city, true));
		$cityrow->add_content(BTCWPAdminInputText::create('btb_zip', __('Postal code', 'bt-booking'), $booking->zip, true));

		$countryrow = new BTCTableRow();
		$countryrow->add_content(BTCWPAdminInputSelect::create('btb_country', __('Country', 'bt-booking'), $booking->country, BTBookingCountries::get_countries(), true));

		$emailrow = new BTCTableRow();
		$emailrow->add_content(BTCWPAdminInputText::create('btb_mail', __('E-mail address', 'bt-booking'), $booking->email, true));
		$emailrow->add_content(BTCWPAdminInputText::create('btb_phone', __('Phone number', 'bt-booking'), $booking->phone, true));


		$table = new BTCTable(array('htmlClasses' => 'form-table'),
			new BTCTableBody(array(),
				array($companyrow, $namerow, $addressrow, $cityrow, $countryrow, $emailrow)
			)
		);

		$table->render();

    }


    /**
     * Provides the meta box showing the customer's note.
     *
     * @param object $post
     */
    public static function btb_booking_customer_notes_box($post) {

		wp_nonce_field('btb_save_booking_customer_notes_box_data', 'btb_booking_customer_notes_box_nonce');

		?>

		<textarea readonly rows="10" style="width:100%" id="btb_notes" name="btb_notes"><?php echo get_post_meta($post->ID, 'btb_notes', true); ?></textarea>

		<?php

    }
}

?>
