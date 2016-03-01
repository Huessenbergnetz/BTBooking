<?php
/**
 * @file
 * @brief Implements the BTB_Event class.
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
 * Core class used to implement the BTB_Event object.
 */
class BTB_Event {

	/**
	 * Event ID.
	 *
	 * @var int $ID
	 */
	public $ID;

	/**
	 * ID of the author of this event.
	 *
	 * @var int $post_author
	 */
	public $post_author = 0;

	/**
	 * Name of the event.
	 *
	 * @var string $name
	 */
	public $name = '';

	/**
	 * The post type. Should be btb_event.
	 *
	 * @var string $post_type
	 */
	public $post_type = 'btb_event';

	/**
	 * The status is publish by default.
	 *
	 * @var string $post_status
	 */
	public $post_status = 'publish';

	/**
	 * Stores the event object's sanitization level.
	 *
	 * @var string $filter
	 */
	public $filter;

	/**
	 * Defines the creation time of the event.
	 *
	 * @var string $post_date
	 */
	public $post_date = '';

	/**
	 * Defines the creation time of the event at GMT.
	 *
	 * @var string $post_date_gmt
	 */
	public $post_date_gmt = '';

	/**
	 * Sanitized version of the event title/name.
	 *
	 * @var string $post_name
	 */
	public $post_name = '';

	/**
	 * Last event modification time.
	 *
	 * @var string $post_modified
	 */
	public $post_modified = '';

	/**
	 * Last event modificatin time at GMT.
	 *
	 * @var string $post_modified_gmt
	 */
	public $post_modified_gmt = '';

	/**
	 * Global Uniquie ID for referencing the event.
	 *
	 * @var string $guid
	 */
	public $guid = '';

	/**
	 * ID of the description page for this event.
	 *
	 * A description page can be used as a separate page,
	 * describing the event.
	 *
	 * @var int $desc_page
	 */
	public $desc_page = 0;

	/**
	 * General price for this event.
	 *
	 * The general price can be overwritten by per time prices.
	 *
	 * @var float $price
	 */
	public $price = 0.0;

	/**
	 * Hint how the price has to be interpreted.
	 *
	 * @var string $price_hint
	 */
	public $price_hint = '';

	/**
	 * ID of the venue this event is located at.
	 *
	 * @var int $venue
	 */
	public $venue = 0;


	/**
	 * Detailed escription of this event.
	 *
	 * @var string $description
	 */
	public $description = '';

	/**
	 * Excerpt of the event description (aka. short description).
	 *
	 * @var string $short_desc
	 */
	public $short_desc = '';

	/**
	 * Type of the event. Used for schema.org.
	 *
	 * @var string $event_type
	 */
	public $event_type = '';

	/**
	 * Type of the struct data. Used for schema.org.
	 *
	 * Possible values: disabled, event, product
	 *
	 * @var string $struct_data_type
	 */
	public $struct_data_type = '';

	/**
	 * Retrieve BTB_Event instance.
	 *
	 * @param int $event_id Event ID.
	 * @return BTB_Event|false BTB_Event object, false otherwise.
	 */
	public static function get_instance($event_id) {
		global $wpdb;

		$event_id = (int) $event_id;

		if (!$event_id) {
			return false;
		}

		$_event = wp_cache_get($event_id, 'btb_events');

		if (!$_event) {

			$_event = $wpdb->get_row($wpdb->prepare(
			"
			SELECT ev.ID, ev.post_author, ev.post_title AS name, ev.post_status, ev.post_date, ev.post_date_gmt, ev.post_name,
						  ev.post_modified, ev.post_modified_gmt, ev.guid, pr.meta_value AS price, ph.meta_value AS price_hint, ev.post_parent AS venue,
						  dp.meta_value AS desc_page, ev.post_content AS description, ev.post_excerpt AS short_desc, et.meta_value AS event_type, sd.meta_value AS struct_data_type
			FROM $wpdb->posts ev
			LEFT JOIN $wpdb->postmeta pr
					  ON pr.post_id = ev.ID
					  AND pr.meta_key = 'btb_price'
			LEFT JOIN $wpdb->postmeta ph
					  ON ph.post_id = ev.ID
					  AND ph.meta_key = 'btb_price_hint'
			LEFT JOIN $wpdb->postmeta dp
					  ON dp.post_id = ev.ID
					  AND dp.meta_key = 'btb_desc_page'
			LEFT JOIN $wpdb->postmeta et
					  ON et.post_id = ev.ID
					  AND et.meta_key = 'btb_event_type'
			LEFT JOIN $wpdb->postmeta sd
					  ON sd.post_id = ev.ID
					  AND sd.meta_key = 'btb_struct_data_type'
			WHERE ev.ID = %d AND ev.post_type = 'btb_event' LIMIT 1
			",
			$event_id
			));

			if (!$_event) {
				return false;
			}

			$_event = btb_sanitize_event($_event, 'raw');

			wp_cache_add($_event->ID, 'btb_events');

		} elseif (empty($_event->filter)) {
			$_event = btb_sanitize_event($_event, 'raw');
		}

		return new BTB_Event($_event);
	}


	/**
	 * Constructor
	 *
	 * @param BTB_Event|obejct $event Event object.
	 */
	public function __construct($event) {
		foreach(get_object_vars($event) as $key => $value) {
			$this->$key = $value;
		}
	}


	/**
	 * Isset-er.
	 *
	 * @param string $key Property to check if set.
	 * @return bool
	 */
	public function __isset($key) {
		if ('ancestors' == $key) {
			return true;
		}

		if ('page_template' == $key) {
			return false;
		}

		if ('post_category' == $key) {
			return true;
		}

		if ('tags_input' == $key) {
			return true;
		}

		if ('post_title' == $key) {
			return isset($this->name);
		}

		if ('post_parent' == $key) {
			return true;
		}

		return metadata_exists('post', $this->ID, $key);
	}



	/**
	 * Getter
	 *
	 * @param string $key Key to get.
	 * @return mixed
	 */
	public function __get($key) {
		if ('page_template' == $key && $this->__isset($key)) {
			return get_post_meta( $this->ID, '_wp_page_template', true );
		}

		if ('post_category' == $key) {
			return array();
		}

		if ('tags_input' == $key) {
			return array();
		}

		if ('ancestors' == $key) {
			return array();
		}

		if ('post_title' == $key) {
			return $this->name;
		}

		if ('post_parent' == $key) {
			return $this->venue;
		}

		$value = get_post_meta($this->ID, $key, true);

		if ($this->filter) {
			$value = btb_sanitize_event_field($key, $value, $this->ID, $this->filter);
		}

		return $value;
	}


	/**
	 * Returns a filtered version of this event.
	 *
	 * @param string $filter The filter context.
	 * @return self|array|bool|object|BTB_Event
	 */
	public function filter($filter) {
		if ($this->filter == $filter) {
			return $this;
		}

		if ($filter == 'raw') {
			return self::get_instance($this->ID);
		}

		return btb_sanitize_event($this, $filter);
	}



	/**
	 * @brief Convert object to array.
	 *
	 * @param bool $wp_post_array If true, the array will be compatible to the WP_Post object/array.
	 * @return array Object as Array.
	 */
	public function to_array($wp_post_array = false) {

		if ($wp_post_array) {
			return array(
				'ID' => $this->ID,
				'post_author' => $this->post_author,
				'post_date' => $this->post_date,
				'post_date_gmt' => $this->post_date_gmt,
				'post_content' => $this->description,
				'post_content_filtered' => '',
				'post_title' => $this->name,
				'post_excerpt' =>$this->short_desc,
				'post_status' => $this->post_status,
				'post_type' => 'btb_event',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => $this->post_name,
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => $this->post_modified,
				'post_modified_gmt' => $this->post_modified_gmt,
				'post_parent' => $this->venue,
				'menu_order' => 0,
				'post_mime_type' => '',
				'guid' => $this->guid,
				'tax_input' => array(),
				'meta_input' => array(
					'btb_desc_page' => $this->desc_page,
					'btb_price' => $this->price,
					'btb_price_hint' => $this->price_hint,
					'btb_event_type' => $this->event_type,
					'btb_struct_data_type' => $this->struct_data_type
				)
			);
		}

		return get_object_vars($this);
	}
}