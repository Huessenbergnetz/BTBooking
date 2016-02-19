<?php
/**
 * @file
 * @brief Implements the BTBooking_Admin_Edit_Venue class.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

defined('ABSPATH') or die ('Am Arsch die R&auml;uber!');

require_once(BTB__PLUGIN_DIR . 'framework/btc/table/btc-table.php');
require_once(BTB__PLUGIN_DIR . 'framework/btc/form/btc-form.php');
require_once(BTB__PLUGIN_DIR . 'class.bt-booking-countries.php');

/** @ingroup globalfns
 * @brief Creates a new BTBooking_Admin_Edit_Venue object.
 */
function callBTBooking_Admin_Edit_Venue() {
	new BTBooking_Admin_Edit_Venue();
}


/**
 * Core class used to implement the BTBooking_Admin_Edit_Venue object.
 *
 * This class provides the scripts and meta boxes hooks for editing the BTB_Venue post type.
 */
class BTBooking_Admin_Edit_Venue {

	/**
	 * Constructor
	 *
	 * Adds @link BTBooking_Admin_Edit_Venue::btb_save_venue_address_box btb_save_venue_address_box @endlink to the @c save_post_btb_venue action.
	 */
	public function __construct() {
		add_action('save_post_btb_venue', array($this, 'btb_save_venue_address_box'));
	}

	/**
	 * Enqueues needed scripts and styles and adds meta boxes.
	 */
	public static function add_btb_venue_meta_boxes() {
		wp_enqueue_script('btb-leaflet-script');
		wp_enqueue_style('btb-leaflet-style');
		wp_localize_script('btb-country-chooser-script', 'BTBCountries', array('vals' => BTBookingCountries::get_countries(true, true)));
		wp_enqueue_script('btb-country-chooser-script');
		add_meta_box('btb_venue_address_box', __('Location', 'bt-booking'), array('BTBooking_Admin_Edit_Venue', 'btb_venue_address_box'), 'btb_venue', 'normal', 'high');
	}


	/**
	 * Provides the meta box for editinig the venue address.
	 *
	 * The box provides input fields for the addres and map location of the venue.
	 *
	 * @param object $post
	 */
	public static function btb_venue_address_box($post) {

		$venue = btb_get_venue($post->ID);

		wp_localize_script('btb-edit-venue-script', 'BTBooking', array(
			'lat' => $venue->latitude,
			'lng' => $venue->longitude,
			'queryErrorMsg' => __('When querying the data an error has occurred.', 'bt-booking'),
			'nothingFoundMsg' => __('For the specified address no place could be found.', 'bt-booking')
		));
		wp_enqueue_script('btb-edit-venue-script');

		wp_nonce_field('btb_save_venue_address_box_data', 'btb_venue_address_box_nonce');

		// Creating first row, showing street name and house number
		$row1 = new BTCTableRow();
		$row1->add_content(BTCWPAdminInputText::create('btb_address_street', esc_html__('Street', 'bt-booking'), $venue->street));
		$row1->add_content(BTCWPAdminInputText::create('btb_address_number', esc_html__('Number', 'bt-booking'), $venue->house_number));

		// Creating second row, showing postal code and city
		$row2 = new BTCTableRow();
		$row2->add_content(BTCWPAdminInputText::create('btb_address_zip', esc_html__('Postal code', 'bt-booking'), $venue->postal_code));
		$row2->add_content(BTCWPAdminInputText::create('btb_address_city', esc_html__('City', 'bt-booking'), $venue->city));

		// Creating third row, showing state/region and country
		$row3 = new BTCTableRow();
		$row3->add_content(BTCWPAdminInputText::create('btb_address_region', esc_html__('State/Region', 'bt-booking'), $venue->region));
		$row3->add_content(BTCWPAdminInputSelect::create('btb_address_country', esc_html__('Country', 'bt-booking'), $venue->country, BTBookingCountries::get_countries()));

		// Creating fourth row, showing switch for using map coordinates
		$row4 = new BTCTableRow();
		$row4->add_content(BTCWPAdminInputCheckbox::create('btb_use_coordinates', esc_html__('Use map coordinates', 'bt-booking'), $venue->use_map_coords));
		$row4->add_content(new BTCTableData());
		$saButtonAttrs = array('id' => 'search_address', 'type' => 'button', 'htmlClass' => 'button button-small');
		if (!$venue->use_map_coords) {
		$saButtonAttrs["style"] = "display:none";
		}
		$row4->add_content(new BTCTableData(new BTCFormButton($saButtonAttrs, esc_html__('Search address', 'bt-booking'))));


		$table = new BTCTable(
			array('htmlClass' => 'form-table'),
			new BTCTableBody(
				array(),
				array($row1, $row2, $row3, $row4)
			)
		);

		$table->render();

		$loc_table = new BTCTable(array('id' => 'locs_table', 'htmlClass' => 'form-table', 'style' => 'display:none;'));
		$loc_head_row = new BTCTableRow();
		$thstyle = array('padding' => '15px 10px');
		$loc_head_row->add_content(new BTCTableData(esc_html__('Choose', 'bt-booking'), array('style' => $thstyle), true));
		$loc_head_row->add_content(new BTCTableData(esc_html__('Street & No.', 'bt-booking'), array('style' => $thstyle), true));
		$loc_head_row->add_content(new BTCTableData(esc_html__('City', 'bt-booking'), array('style' => $thstyle), true));
		$loc_head_row->add_content(new BTCTableData(esc_html__('ZIP', 'bt-booking'), array('style' => $thstyle), true));
		$loc_head_row->add_content(new BTCTableData(esc_html__('State', 'bt-booking'), array('style' => $thstyle), true));
		$loc_head = new BTCTableHead(array(), $loc_head_row);
		$loc_body = new BTCTableBody(array('id' => 'locs_body'));
		$loc_table->head = $loc_head;
		$loc_table->body = $loc_body;
		$loc_table->render();

		?>
		<input type="hidden" id="btb_address_lat" name="btb_address_lat" value="<?php echo $venue->latitude ?>">
		<input type="hidden" id="btb_address_lon" name="btb_address_lon" value="<?php echo $venue->longitude ?>">

		<div id="mapMessages">
		</div>

		<div id="venueMap" style="height:300px;<?php echo $venue->use_map_coords ? '' : ' display:none'; ?>">
		</div>

		<?php

	}


	/**
	 * Saves the data provided by btb_venue_address_box.
	 *
	 * @param int $venue_id The ID of the BTB_Venue to save.
	 */
	public function btb_save_venue_address_box($venue_id) {

		if (!isset($_POST['btb_venue_address_box_nonce'])) {
			return;
		}

		if (!wp_verify_nonce($_POST['btb_venue_address_box_nonce'], 'btb_save_venue_address_box_data')) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $venue_id;
		}

		if (!current_user_can('edit_post', $venue_id)) {
			return $venue_id;
		}

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_street');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_number');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_zip');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_city');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_region');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_country');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_lon');

		self::btb_save_post_value($venue_id, $_POST, 'btb_address_lat');

		update_post_meta($venue_id, 'btb_use_coordinates', isset($_POST['btb_use_coordinates']) ? 1 : 0);

	}


	/**
	 * Helper function for saving BTB_Event post metadata.
	 *
	 * @param int $event_id The ID of the BTB_Event.
	 * @param array $postData Array containing the POST data.
	 * @param string $metaField Name of the meta field to update.
	 */
	private function btb_save_post_value($event_id, array &$postData = array(), $metaField = '') {
		if (empty($postData) || $metaField == "") {
			return;
		}

		if (isset($postData[$metaField])) {
			update_post_meta($event_id, $metaField, trim($postData[$metaField]));
		}
	}
}

?>