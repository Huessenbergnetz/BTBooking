<?php
/**
 * @file
 * @brief Implements the BTB_Venue class.
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
 * Core class used to implement the BTB_Venue object.
 */
class BTB_Venue {

	/**
	 * Venue ID.
	 *
	 * @var int $ID
	 */
	public $ID;

	/**
	 * Venue name.
	 *
	 * @var string $name
	 */
	public $name = '';

	/**
	 * Description of the venue.
	 *
	 * @var string $description
	 */
	public $description = '';

	/**
	 * Short description of the venue (aka. excerpt).
	 *
	 * @var string $short_desc
	 */
	public $short_desc = '';

	/**
	 * Street part of the venue's address.
	 *
	 * @var string $street
	 */
	public $street = '';

	/**
	 * House number part of the venue's address.
	 *
	 * @var string $house_number
	 */
	public $house_number = '';

	/**
	 * Postal code part of the venue's address.
	 *
	 * @var string $postal_code
	 */
	public $postal_code = '';

	/**
	 * City part of the venue's address.
	 *
	 * @var string $city
	 */
	public $city = '';

	/**
	 * Region part of the venue's address.
	 *
	 * @var string $region
	 */
	public $region = '';

	/**
	 * Country part of the venue's address.
	 *
	 * ISO 3166-1 alpha2 two-letter code.
	 *
	 * @var string $country
	 */
	public $country = '';

	/**
	 * True to use map coordinates if any.
	 *
	 * @var bool $use_map_coords
	 */
	public $use_map_coords = false;

	/**
	 * Latitude of the venue location.
	 *
	 * @var float $latitude
	 */
	public $latitude = 0.0;

	/**
	 * Longitude of the venue location.
	 *
	 * @var float $longitude
	 */
	public $longitude = 0.0;

	/**
	 * Stores the venue object's sanitization level.
	 *
	 * @var string $filter
	 */
	public $filter;

	/**
	 * Defines the creation time of the venue.
	 *
	 * @var string $post_date
	 */
	public $post_date = '';

	/**
	 * Defines the creation time of the venue at GMT.
	 *
	 * @var string $post_date_gmt
	 */
	public $post_date_gmt = '';

	/**
	 * Sanitized version of the post_title/name.
	 *
	 * @var string $post_name
	 */
	public $post_name = '';

	/**
	 * Last venue modification time.
	 *
	 * @var string $post_modified
	 */
	public $post_modified = '';

	/**
	 * Last venue modificatin time at GMT.
	 *
	 * @var string $post_modified_gmt
	 */
	public $post_modified_gmt = '';

	/**
	 * Global Uniquie ID for referencing the venue.
	 *
	 * @var string $guid
	 */
	public $guid = '';

	/**
	 * ID of the author that created this venue.
	 *
	 * @var int $post_author
	 */
	public $post_author = '';

	/**
	 * Status of the venue. Default 'publish'.
	 *
	 * @var string $post_status
	 */
	public $post_status = 'publish';


	/**
	 * @brief Retrieve BTB_Venue instance.
	 *
	 * @param int $venue_id Venue ID.
	 * @return BTB_Venue|false Venue object, false otherwise.
	 */
	public static function get_instance($venue_id) {
		global $wpdb;

		$venue_id = (int) $venue_id;

		if (!$venue_id) {
			return false;
		}

		$_venue = wp_cache_get($venue_id, 'btb_venues');

		if (!$_venue) {

			$_venue = $wpdb->get_row($wpdb->prepare(
			"
			SELECT ve.ID, ve.post_author, ve.post_date, ve.post_date_gmt, ve.post_content AS description, ve.post_title AS name, ve.post_excerpt AS short_desc,
				   ve.post_status, ve.post_name, ve.post_modified, ve.post_modified_gmt, ve.guid, street.meta_value AS street, hn.meta_value AS house_number,
				   zip.meta_value AS postal_code, ci.meta_value AS city, re.meta_value AS region, cc.meta_value AS country, umc.meta_value AS use_map_coords,
				   lat.meta_value AS latitude, lon.meta_value AS longitude
			FROM $wpdb->posts ve
			LEFT JOIN $wpdb->postmeta street
				 ON street.post_id = ve.ID
				 AND street.meta_key = 'btb_address_street'
			LEFT JOIN $wpdb->postmeta hn
				 ON hn.post_id = ve.ID
				 AND hn.meta_key = 'btb_address_number'
			LEFT JOIN $wpdb->postmeta zip
				 ON zip.post_id = ve.ID
				 AND zip.meta_key = 'btb_address_zip'
			LEFT JOIN $wpdb->postmeta ci
				 ON ci.post_id = ve.ID
				 AND ci.meta_key = 'btb_address_city'
			LEFT JOIN $wpdb->postmeta re
				 ON re.post_id = ve.ID
				 AND re.meta_key = 'btb_address_region'
			LEFT JOIN $wpdb->postmeta cc
				 ON cc.post_id = ve.ID
				 AND cc.meta_key = 'btb_address_country'
			LEFT JOIN $wpdb->postmeta umc
				 ON umc.post_id = ve.ID
				 AND umc.meta_key = 'btb_use_coordinates'
			LEFT JOIN $wpdb->postmeta lat
				 ON lat.post_id = ve.ID
				 AND lat.meta_key = 'btb_address_lat'
			LEFT JOIN $wpdb->postmeta lon
				 ON lon.post_id = ve.ID
				 AND lon.meta_key = 'btb_address_lon'
			WHERE ve.ID = %d AND ve.post_type = 'btb_venue';
			",
			$venue_id
			));

			if (!$_venue) {
				return false;
			}

			$_venue = btb_sanitize_venue($_venue, 'raw');
			wp_cache_add($_venue->ID, $_venue, 'btb_venues');

		} elseif (empty($_venue->filter)) {
			$_venue = btb_sanitize_venue($_venue, 'raw');
		}

		return new BTB_Venue($_venue);
	}


	/**
	 * Constructor
	 *
	 * @param BTB_Venue|object $venue Event object.
	 */
	public function __construct($venue) {
		foreach(get_object_vars($venue) as $key => $value) {
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
			return isset($this->code);
		}

		if ('post_type' == $key) {
			return true;
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
			return $this->code;
		}

		if ('post_type' == $key) {
			return 'btb_venue';
		}

		if ('post_parent' == $key) {
			return 0;
		}

		$value = get_post_meta($this->ID, $key, true);

		if ($this->filter) {
			$value = btb_sanitize_venue_field($key, $value, $this->ID, $this->filter);
		}

		return $value;
	}


	/**
	 * Returns a filtered version of this venue.
	 *
	 * @param string $filter The filter context.
	 * @return self|array|bool|object|BTB_Booking
	 */
	public function filter($filter) {
		if ($this->filter == $filter) {
			return $this;
		}

		if ($filter == 'raw') {
			return self::get_instance($this->ID);
		}

		return btb_sanitize_venue($this, $filter);
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
				'post_excerpt' => $this->short_desc,
				'post_status' => $this->post_status,
				'post_type' => 'btb_venue',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => $this->post_name,
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => $this->post_modified,
				'post_modified_gmt' => $this->post_modified_gmt,
				'post_parent' => $this->booked_time,
				'menu_order' => 0,
				'post_mime_type' => '',
				'guid' => $this->guid,
				'tax_input' => array(),
				'meta_input' => array(
					'btb_address_street' => $this->street,
					'btb_address_number' => $this->house_number,
					'btb_address_zip' => $this->postal_code,
					'btb_address_city' => $this->city,
					'btb_address_region' => $this->region,
					'btb_address_country' => $this->country,
					'btb_use_coordinates' => $this->use_map_coords,
					'btb_address_lat' => $this->latitude,
					'btb_address_lon' => $this->longitude
				)
			);
		}

		return get_object_vars($this);
	}


	/*!
	 * @brief Returns a combination from street and house number.
	 *
	 * @return string
	 */
	public function streetAndNumber() {
		if (!empty($this->street) && !empty($this->house_number)) {
			return $this->street . ' ' . $this->house_number;
		} else {
			return $this->street . $this->house_number;
		}
	}
}