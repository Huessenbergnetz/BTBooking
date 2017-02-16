<?php
/**
 * @file
 * @brief Implements the BTBooking_Admin_Edit_Event class.
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

/** @ingroup globalfns
 * @brief Creates a new BTBooking_Admin_Edit_Event object.
 */
function call_BTBooking_Admin_Edit_Event() {
    new BTBooking_Admin_Edit_Event();
}

/**
 * Core class used to implement the BTBooking_Admin_Edit_Event object.
 *
 * This class provides the scripts and meta boxes hooks for editing the BTB_Event post type.
 *
 * @todo Use BTC for input rendering.
 */
class BTBooking_Admin_Edit_Event {

	/**
	 * Constructor
	 *
	 * Adds @link BTBooking_Admin_Edit_Booking::btb_save_booking_address_box btb_save_booking_address_box @endlink,
	 * @link BTBooking_Admin_Edit_Event::btb_save_event_times_box @endlink and @link BTBooking_Admin_Edit_Event::btb_save_event_structured_data_box @endlink
	 * to the @c save_post_btb_event action.
	 */
    public function __construct() {
        add_action('save_post_btb_event', array($this, 'btb_save_event_meta_box'));
        add_action('save_post_btb_event', array($this, 'btb_save_event_structured_data_box'));
        add_action('save_post_btb_event', array($this, 'btb_save_event_times_box'));
        date_default_timezone_set ( get_option('timezone_string', 'UTC') );
    }

    /**
     * Enqueues needed scripts and adds meta boxes.
     */
    public static function add_btb_event_meta_boxes() {
		wp_enqueue_script('jquery-ui-datepicker');
		wp_localize_script( 'btb-admin-scripts', 'BTBooking',
                            array(
                                'date_format' => get_option('btb_date_format', 'dd.mm.yy'),
                                'strings' => array(
				'Start date' => __( 'Start date' , 'bt-booking'),
				'End date'     => __( 'End date' , 'bt-booking'),
				'Date only'     => __( 'Date only' , 'bt-booking'),
				'date_only_desc' => __('Times are ignored', 'bt-booking'),
				'Price' => __('Price', 'bt-booking'),
				'price_unit' => get_option('btb_currency', '€'),
				'Slots' => __('Slots', 'bt-booking'),
				'cancel' => __('cancel', 'bt-booking'),
				'delete' => __('delete', 'bt-booking')
				)
			) );
		wp_enqueue_script( 'btb-admin-scripts' );
		wp_enqueue_style('btb-admin-style');
        wp_enqueue_style('plugin_name-admin-ui-css',
                'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/flick/jquery-ui.css',
                false,
                '1.11.4',
                false);
        add_meta_box('btb_event_meta_box', __('Event details', 'bt-booking'), array('BTBooking_Admin_Edit_Event','btb_event_meta_box'), 'btb_event', 'normal', 'high');
		add_meta_box('btb_event_short_desc_box', __('Short description', 'bt-booking'), array('BTBooking_Admin_Edit_Event', 'btb_event_short_desc_box'), 'btb_event', 'normal', 'high');
        add_meta_box('btb_event_structured_data_box', __('Event structured data', 'bt-booking'), array('BTBooking_Admin_Edit_Event', 'btb_event_structured_data_box'), 'btb_event', 'normal', 'high');
        add_meta_box('btb_event_times_box', __('Event times', 'bt-booking'), array('BTBooking_Admin_Edit_Event', 'btb_event_times_box'), 'btb_event', 'normal', 'high');
    }


    /**
     * Provides the meta box for editing the event data.
     *
     * The box provides inputs for event type, description page, venue, default price and price hint.
     *
     * @param object $post
     */
    public static function btb_event_meta_box($post) {

		$event = btb_get_event($post->ID);

        wp_nonce_field('btb_save_event_meta_box_data', 'btb_event_meta_box_nonce');

        $descPages = get_pages(array('sort_column' => 'post_title'));
        $options = array('' => __('Nothing selected', 'bt-booking'));
		if (!empty($descPages)) {
			foreach($descPages as $key => $page) {
				$options[$page->ID] = $page->post_title;
			}
		}

		$pageChooserRow = new BTCTableRow();
		$pageChooserRow->add_content(BTCWPAdminInputSelect::create('btb_event_desc_page_field', __('Description page', 'bt-booking'), $event->desc_page, $options));

		$priceInputRow = new BTCTableRow();
		$priceInputRow->add_content(BTCWPAdminInputNumber::create('btb_event_price_field', sprintf(__('Price in %s', 'bt-booking'), get_option('btb_currency_code', 'EUR')), $event->price, 0, null, 0.01));

		$priceHintRow = new BTCTableRow();
		$priceHintRow->add_content(BTCWPAdminFormTextarea::create('btb_event_price_hint_field', __('Price hint', 'bt-booking'), $event->price_hint));

		$table = new BTCTable(
			array('htmlClass' => 'form-table'),
			new BTCTableBody(
				array(),
				array($pageChooserRow, $priceInputRow, $priceHintRow)
			)
		);

		$table->render();
    }


    /**
     * Saves the data provided by btb_event_meta_box.
     *
     * @param int $event_id The ID of the BTB_Event to save.
     */
    public function btb_save_event_meta_box($event_id) {

        if (!isset($_POST['btb_event_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['btb_event_meta_box_nonce'], 'btb_save_event_meta_box_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $event_id;
        }

        if (!current_user_can('edit_page', $event_id)) {
            return $event_id;
        }

        if (isset($_POST['btb_event_desc_page_field'])) {
            update_post_meta($event_id, 'btb_desc_page', $_POST['btb_event_desc_page_field']);
        }

        if (isset($_POST['btb_event_price_field'])) {
            update_post_meta($event_id, 'btb_price', $_POST['btb_event_price_field']);
        }

        if (isset($_POST['btb_event_price_hint_field'])) {
            update_post_meta($event_id, 'btb_price_hint', $_POST['btb_event_price_hint_field']);
        }
    }



    /**
     * Creates the meta box for the short description.
     *
     * The short description textarea box uses the post excerpt to store a short
     * description of the event.
     */
    public static function btb_event_short_desc_box($post) {
		$event = btb_get_event($post->ID);

		$short_desc_label = new BTCFormLabel(array('htmlClasses' => 'screen-reader-text', 'for' => 'excerpt'), esc_html__('Short description', 'bt-booking'));
		$short_desc_area = new BTCFormTextarea(array('id' => 'excerpt', 'cols' => 40, 'rows' => 1), $event->short_desc);

		$short_desc_label->render();
		$short_desc_area->render();

		echo '<p>' . esc_html__('The short description will be used for displaying events on summary pages as well as the description for the Schema.org structured data. Some themes might use it also for the meta description tag. If the short description is empty and a description page is set, BTBooking will use the excerpt of the description page if one is available.', 'bt-booking') . '</p>';
    }



    /**
     * Creates the meta box for managing structured data.
     *
     * This meta box shows options to managa structued data to generate for this event
     * if the option to generate structured data is enabled.
     *
     * @param object $post
     */
    public static function btb_event_structured_data_box($post) {
		$event = btb_get_event($post->ID);

		wp_nonce_field('btb_save_event_structured_data_box_data', 'btb_event_structured_data_box_nonce');

		$structtype = !empty($event->struct_data_type) ? $event->struct_data_type : get_option('btb_struct_data_default_type', 'event');

		$structTypeRow = new BTCTableRow();
		$structTypeRow->add_content(BTCWPAdminInputRadios::create(
			'structtype',
			'btb_struct_data_type',
			__('Data type', 'bt-booking'),
			array(
				'disabled' => __('Disabled', 'bt-booking'),
				'product' => __('Product', 'bt-booking'),
				'event' => __('Event', 'bt-booking')
			),
			$structtype
		));

		$eventTypeChooserRow = new BTCTableRow(array(), array('id' => 'eventTypeRow', 'hide' => ($structtype != 'event')));
		$eventTypeChooserRow->add_content(BTCWPAdminInputSelect::create('btb_event_type_field', __('Event type', 'bt-booking'), (!empty($event->event_type)) ? $event->event_type : get_option('btb_struct_data_event_type'), btb_get_event_types()));

		$venuePages = get_posts(array('post_type' => 'btb_venue', 'orderby' => 'title'));
		$venueOptions = array('' => __('Nothing selected', 'bt-booking'));
		if (!empty($venuePages)) {
			foreach($venuePages as $vKey => $vPage) {
				$venueOptions[$vPage->ID] = $vPage->post_title;
			}
		}

		$venueChooserRow = new BTCTableRow(array(), array('id' => 'venueRow', 'hide' => ($structtype != 'event')));
		$venueChooserRow->add_content(BTCWPAdminInputSelect::create('post_parent', __('Venue', 'bt-booking'), $event->venue, $venueOptions));

		$table = new BTCTable(
			array('htmlClass' => 'form-table'),
			new BTCTableBody(
				array(),
				array($structTypeRow, $eventTypeChooserRow, $venueChooserRow,)
			)
		);

		$table->render();
    }


    /**
     * Saves the data provided by btb_event_structured_data_box.
     *
     * @param int $event_id The ID of the BTB_Event to save.
     */
    public function btb_save_event_structured_data_box($event_id) {

        if (!isset($_POST['btb_event_structured_data_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['btb_event_structured_data_box_nonce'], 'btb_save_event_structured_data_box_data')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $event_id;
        }

        if (!current_user_can('edit_page', $event_id)) {
            return $event_id;
        }

        if (isset($_POST['btb_event_type_field'])) {
			update_post_meta($event_id, 'btb_event_type', $_POST['btb_event_type_field']);
        }

        if (isset($_POST['btb_struct_data_type'])) {
			update_post_meta($event_id, 'btb_struct_data_type', $_POST['btb_struct_data_type']);
        }
    }



	/**
	 * Creates the meta box for managing event dates.
	 *
	 * This meta box shows the dates associated to the event and provides
	 * actions to add new dats.
	 *
	 * @param object $post
	 */
    public static function btb_event_times_box($post) {

		$times = btb_get_times($post->ID);

        wp_nonce_field('btb_save_event_times_meta_box_data', 'btb_event_times_meta_box_nonce');

        ?>

        <div id="times_container">
            <input type="hidden" id="btb_new_times_count" name="btb_new_times_count" value="0" />
            <?php
            foreach($times as $key => $time) {
                self::_render_time($time);
            }
            ?>
        </div>

        <button id="add_time" type="button" class="button button-small" style="margin-top:10px"><?php esc_html_e('Add date', 'bt-booking'); ?></button>

        <?php


    }


    /**
     * Saves the data provided by btb_event_times_box.
     *
     * @param int $event_id The ID of the BTB_Event to save.
     */
    public function btb_save_event_times_box($event_id) {

        if (!isset($_POST['btb_event_times_meta_box_nonce'])) {
            return $event_id;
        }

        if (!wp_verify_nonce($_POST['btb_event_times_meta_box_nonce'], 'btb_save_event_times_meta_box_data')) {
            return $event_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $event_id;
        }

        if (!current_user_can('edit_post', $event_id)) {
            return $event_id;
        }

        if (isset($_POST['btb_new_times'])) {

//             error_log(print_r($_POST['btb_new_times'], true));

            $new_times = $_POST['btb_new_times'];

            foreach ($new_times as $key => $time) {

                $new_time = wp_insert_post(array(
                                            'post_content' => '',
                                            'post_title' => $time['time_name'],
                                            'post_parent' => $event_id,
                                            'post_type' => 'btb_time',
                                            'post_status' => 'publish'
                                            ));

                if ($new_time != 0) {

                    if (isset($time['start_date_secs'])) {
                        update_post_meta($new_time, 'btb_start', intval($time['start_date_secs'])/1000);
                    }

                    if (isset($time['end_date_secs'])) {
                        update_post_meta($new_time, 'btb_end', intval($time['end_date_secs'])/1000);
                    }

                    if (isset($time['slots'])) {
                        update_post_meta($new_time, 'btb_slots', $time['slots']);
                    }

                    if (isset($time['price'])) {
                        update_post_meta($new_time, 'btb_price', $time['price']);
                    }


                    update_post_meta($new_time, 'btb_date_only', isset($time['date_only']));

                }
            }
        }

        if (isset($_POST['btb_times'])) {

             $save_times = $_POST['btb_times'];

             foreach($save_times as $id => $time) {

                    if (isset($time['delete']) && $time['delete'] == "true") {

                        wp_delete_post($id, true);

                    } else {

                        wp_insert_post(array('ID' => $id,
                                                'post_content' => '',
                                                'post_title' => $time['time_name'],
                                                'post_parent' => $event_id,
                                                'post_type' => 'btb_time',
                                                'post_status' => 'publish'
                                                ));

                        if (isset($time['start_date_secs'])) {
                            update_post_meta($id, 'btb_start', intval($time['start_date_secs'])/1000);
                        }

                        if (isset($time['end_date_secs'])) {
                            update_post_meta($id, 'btb_end', intval($time['end_date_secs'])/1000);
                        }

                        if (isset($time['slots'])) {
                            update_post_meta($id, 'btb_slots', $time['slots']);
                        }

                        if (isset($time['price'])) {
                            update_post_meta($id, 'btb_price', $time['price']);
                        }


                        update_post_meta($id, 'btb_date_only', isset($time['date_only']));

                    }
             }
        }
    }



    private static function _render_input($attrs = array(), $attrs2 = array()) {

        if (empty($attrs) || !is_array($attrs)) {
            return;
        }

        $id = isset($attrs['id']) ? $attrs['id'] : null;
        $label = isset($attrs['label']) ? $attrs['label'] : null;
        $val = isset($attrs['value']) ? $attrs['value'] : null;
        $type = isset($attrs['type']) ? $attrs['type'] : null;
        $desc = isset($attrs['desc']) ? $attrs['desc'] : null;
        $class = isset($attrs['class']) ? $attrs['class'] : null;
        $ranges = isset($attrs['ranges']) ? $attrs['ranges'] : null;
        $unit = isset($attrs['unit']) ? $attrs['unit'] : null;
        $eventHandlers = isset($attrs['eventhandlers']) ? $attrs['eventhandlers'] : null;
        $name = isset($attrs['name']) ? $attrs['name'] : null;
        $data = isset($attrs['data']) ? $attrs['data'] : null;
        $pattern = isset($attrs['pattern']) ? $attrs['pattern'] : null;

        $ranges_string = '';

        if (!empty($ranges)) {
            if (isset($ranges['min'])) {
                $min = $ranges['min'];
                $ranges_string .= " min=\"$min\"";
            }
            if (isset($ranges['max'])) {
                $max = $ranges['max'];
                $ranges_string .= " max=\"$max\"";
            }
            if (isset($ranges['step'])) {
                $step = $ranges['step'];
                $ranges_string .= " step=\"$step\"";
            }
        }

        $checkbox_checked = false;

        if ($type == 'checkbox') {
			$checkbox_checked = $val;
        }

        $name_string = $name ? $name : $id;

        $data_string = '';

        if (!empty($data) && is_array($data)) {
            foreach($data as $key => $value) {
                $data_string .= " data-".$key."=\"".$value."\"";
            }
        }

        $handler_string = '';

        if (!empty($eventHandlers) && is_array($eventHandlers)) {
            foreach($eventHandlers as $event => $handle) {
                $handler_string .= " on".$event."=\"".$handle."\"";
            }
        }

        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
            </th>
            <td>
                <input id="<?php echo $id; ?>" name="<?php echo $name_string; ?>" type="<?php echo $type; ?>" <?php if ($type != 'checkbox') echo "value=\"".$val."\""; if(!empty($class)) echo " class=\"$class\""; if (!empty($desc)) echo " aria-describedby=\"$id-description\""; echo $ranges_string; echo $data_string; if ($handler_string) echo $handler_string; if ($pattern) echo " pattern=\"".$pattern."\""; if ($checkbox_checked) echo " checked"; ?> /><?php if (!empty($unit)) echo "<span> $unit</span>"; ?>
<?php if (!empty($desc) && empty($attrs2)) : ?>
<?php if ($type == 'checkbox') : ?>
                <span id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></span>
<?php else : ?>
                <p id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></p>
<?php endif; ?>
<?php endif; ?>
            </td>
            <?php
            if (!empty($attrs2) && is_array($attrs2)) {

                $id = isset($attrs2['id']) ? $attrs2['id'] : null;
                $val = isset($attrs2['value']) ? $attrs2['value'] : null;
                $type = isset($attrs2['type']) ? $attrs2['type'] : null;
                $desc = isset($attrs2['desc']) ? $attrs2['desc'] : null;
                $class = isset($attrs2['class']) ? $attrs2['class'] : null;
                $ranges = isset($attrs2['ranges']) ? $attrs2['ranges'] : null;
                $unit = isset($attrs2['unit']) ? $attrs2['unit'] : null;
                $eventHandlers = isset($attrs2['eventhandlers']) ? $attrs2['eventhandlers'] : null;
                $name = isset($attrs2['name']) ? $attrs2['name'] : null;
                $data = isset($attrs2['data']) ? $attrs2['data'] : null;
                $pattern = isset($attrs2['pattern']) ? $attrs2['pattern'] : null;

                $ranges_string = '';

                if (!empty($ranges)) {
                    if (isset($ranges['min'])) {
                        $min = $ranges['min'];
                        $ranges_string .= " min=\"$min\"";
                    }
                    if (isset($ranges['max'])) {
                        $max = $ranges['max'];
                        $ranges_string .= " max=\"$max\"";
                    }
                    if (isset($ranges['step'])) {
                        $step = $ranges['step'];
                        $ranges_string .= " step=\"$step\"";
                    }
                }

                $checkbox_checked = false;

                if ($type == 'checkbox') {
                    $checkbox_checked = !empty($val);
                }

                $name_string = $name ? $name : $id;

                $data_string = '';

                if (!empty($data) && is_array($data)) {
                    foreach($data as $key => $value) {
                        $data_string .= " data-".$key."=\"".$value."\"";
                    }
                }

                $handler_string = '';

                if (!empty($eventHandlers) && is_array($eventHandlers)) {
                    foreach($eventHandlers as $event => $handle) {
                        $handler_string .= " on".$event."=\"".$handle."\"";
                    }
                }


            ?>

            <td>
                <input id="<?php echo $id; ?>" name="<?php echo $name_string; ?>" type="<?php echo $type; ?>" <?php if ($type != 'checkbox') echo "value=\"".$val."\""; if(!empty($class)) echo " class=\"$class\""; if (!empty($desc)) echo " aria-describedby=\"$id-description\""; echo $ranges_string; echo $data_string; if ($handler_string) echo $handler_string; if ($pattern) echo " pattern=\"".$pattern."\""; if ($checkbox_checked) echo " checked"; ?> /><?php if (!empty($unit)) echo "<span> $unit</span>"; ?>
<?php if (!empty($desc)) : ?>
<?php if ($type == 'checkbox') : ?>
                <span id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></span>
<?php else : ?>
                <p id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></p>
<?php endif; ?>
<?php endif; ?>
            </td>

            <?php
            }
            ?>

        </tr>
        <?php
    }





    private static function _render_desc_page_chooser($id, $label, $value, $desc = '') {
        $pages = get_pages(array('sort_column' => 'post_title'));
        ?>
        <tr>
            <th scope="row"><label for="<?php echo $id; ?>"><?php echo $label; ?></label></th>
            <td>
                <select id="<?php echo $id; ?>" name="<?php echo $id; ?>"<?php if (!empty($desc)) echo " aria-describedby=\"$id-description\""; ?>>
                    <option value=""<?php if (!$value) echo " selected"; ?>><?php _e('Select a page', 'bt-booking'); ?></option>
<?php foreach($pages as $key => $page) : ?>
                    <option value="<?php echo $page->ID; ?>"<?php if ($value == $page->ID) echo " selected"; ?>><?php echo $page->post_title; ?></option>
<?php endforeach; ?>
                </select>
<?php if (!empty($desc)) : ?>
                <p id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></p>
<?php endif; ?>
            </td>
        </tr>
        <?php
    }



    private static function _render_text_area($id, $label, $value, $desc = '') {
        ?>
        <tr>
            <th scope="row""><label for="<?php echo $id; ?>"><?php echo $label; ?></label></th>
            <td>
                <textarea id="<?php echo $id; ?>" name="<?php echo $id; ?>" class="large-text"<?php if (!empty($desc)) echo " aria-describedby=\"$id-description\""; ?>><?php echo $value; ?></textarea>
<?php if (!empty($desc)) : ?>
                <p id="<?php echo $id; ?>-description" class="btb_admin_desc"><?php echo $desc; ?></p>
<?php endif; ?>
            </td>
        </tr>
        <?php
    }



    private static function _render_time($time) {
        $baseId = "btb_times_".$time->ID;
        $baseName = "btb_times[".$time->ID."]";
        $dataTimeId = array('time-id' => $time->ID);
        $startSecs = intval(get_post_meta($time->ID, 'btb_start', true));
        $endSecs = intval(get_post_meta($time->ID, 'btb_end', true));
        ?>
<div id="<?php echo $baseId; ?>" class="btb_saved_time btb_time_box">
    <div class="btb_time_header" data-time-id="<?php echo $time->ID; ?>" onclick="btb_toggle_content(this)">
        <?php printf('<h4 id="btb_times_%s_header">%s</h4>', $time->ID, $time->name); ?><a class="btb_delete_time" data-time-id="<?php echo $time->ID; ?>" href="#" onclick="btb_delete_time(this)"><?php echo __('delete', 'bt-booking'); ?></a>
    </div>
    <?php printf('<input type="hidden" id="btb_times_%s_time_name" name="btb_times[%s][time_name]" value="%s" />', $time->ID, $time->ID, $time->name); ?>
    <?php printf('<input type="hidden" id="btb_times_%s_start_date_secs" name="btb_times[%s][start_date_secs]" value="%u" />', $time->ID, $time->ID, $time->start*1000); ?>
    <?php printf('<input type="hidden" id="btb_times_%s_end_date_secs" name="btb_times[%s][end_date_secs]" value="%u" />', $time->ID, $time->ID, $time->end*1000); ?>
    <?php printf('<input type="hidden" id="btb_times_%s_delete" name="btb_times[%s][delete]" value="false" />', $time->ID, $time->ID); ?>
    <table id="<?php echo $baseId."_table"; ?>" class="form-table" style="display:none"><tbody>
    <?php
        self::_render_input(array(
                            'id' => $baseId."_start_date",
                            'label' => __('Start date', 'bt-booking'),
                            'value' => date('d.m.Y', $time->start),
                            'type' => 'text',
                            'class' => 'btb_start_date btb_date_picker',
                            'eventhandlers' => array('change' => 'btb_update_name_header_saved(this)'),
//                             'name' => $baseName."[start_date]",
                            'data' => $dataTimeId
                            ),
                            array(
                            'id' => $baseId."_start_time",
                            'value' => date('h:i', $time->start),
                            'type' => 'text',
                            'eventhandlers' => array('change' => 'btb_update_name_header_saved(this)'),
                            'data' => $dataTimeId,
                            'pattern' => '^[012][0-9]:\d{2}$'
                            )
        );

        self::_render_input(array(
                            'id' => $baseId."_end_date",
                            'label' =>  __('End date', 'bt-booking'),
                            'value' => date('d.m.Y', $time->end),
                            'type' => 'text',
                            'class' => 'btb_end_date btb_date_picker',
                            'eventhandlers' => array('change' => 'btb_update_name_header_saved(this)'),
//                             'name' => $baseName."[end_date]",
                            'data' => $dataTimeId
                            ),
                            array(
                            'id' => $baseId."_end_time",
                            'value' => date('h:i', $time->end),
                            'type' => 'text',
                            'eventhandlers' => array('change' => 'btb_update_name_header_saved(this)'),
                            'data' => $dataTimeId,
                            'pattern' => '^[012][0-9]:\d{2}$'
                            )

        );

        self::_render_input(array(
                            'id' => $baseId."_date_only",
                            'label' => __('Date only', 'bt-booking'),
//                             'value' => get_post_meta($time->ID, 'btb_date_only', true),
                            'value' => $time->date_only,
                            'type' => 'checkbox',
                            'desc' => __('Times are ignored', 'bt-booking'),
                            'eventhandlers' => array('change' => 'btb_update_name_header_saved(this)'),
                            'data' => $dataTimeId,
                            'name' => $baseName."[date_only]"
        ));

        self::_render_input(array(
                            'id' => $baseId."_slots",
                            'label' => __('Slots', 'bt-booking'),
//                             'value' => get_post_meta($time->ID, 'btb_slots', true),
                            'value' => $time->slots,
                            'type' => 'number',
                            'ranges' => array('min' => 1, 'step' => 1),
                            'name' => $baseName."[slots]"
        ));

        self::_render_input(array(
                            'id' => $baseId."_price",
                            'label' => __('Price', 'bt-booking'),
//                             'value' => get_post_meta($time->ID, 'btb_price', true),
                            'value' => $time->price,
                            'type' => 'number',
                            'ranges' => array('min' => 0, 'step' => 0.01),
                            'name' => $baseName."[price]",
                            'unit' => get_option('btb_currency', '€')
        ));



    ?>
    </tbody></table>
</div>
        <?php
    }

}


?>
