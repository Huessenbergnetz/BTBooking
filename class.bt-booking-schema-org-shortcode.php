<?php
/**
 * @file
 * @brief Implements the BTBooking_Schema_Org_Shortcode class.
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
 * Implements the @c btb_schema_organization shortcode.
 *
 * The @c btb_schema_organization shortcode adds Schema.org Organization data as JSON-LD to a page or post.
 * This data can be referenced by the @c btb_direct_booking shortcode that also adds JSON-LD Schema.org data about the event.
 *
 * The default options are taken from the plugin settings but can be individually overriden via the shortcode attributes.
 *
 * More information about Schema.org Organization: https://schema.org/Organization
 *
 * You should place this shortcode on your start page.
 *
 * @par Available shortcode attributes
 * - @a type The base type of the organization.
 * - @a id By default this ist the URL of your wordpress site.
 * - @a name The name of your organization/business.
 * - @a description Short description of your organization.
 * - @a isicv4 ISIS Revision 4 code.
 * - @a pobox Post Office Box.
 * - @a street Street of your organization's address.
 * - @a zip Postal code of your organization's address.
 * - @a city City of your organization's address.
 * - @a region Region of your organization's address.
 * - @a country Country of your organization's address as ISO 3166 alpha 2 code.
 * - @a email Main contact e-mail address of your organization.
 * - @a phone Main contact phone number of your organization.
 * - @a fax Main contact fax number of your organization.
 * - @a facebook Facebook profile page of your organization.
 * - @a instagram Instagram profile page of your organization.
 * - @a twitter Twitter profile page of your organization.
 * - @a googleplus Google+ profile page of your organization.
 *
 * @par Example
 * @code
 [btb_schema_organization type="LocalBusiness" name="My litte Company" id="http://www.company.com" twitter="https://twitter.com/MyLittleCompany" country="DE"]
 * @endcode
 *
 * @since 1.0.0
 */
class BTBooking_Schema_Org_Shortcode {

	/**
	 * Registers the shortcode @c btb_schema_organization.
	 */
    public static function register_short_code() {
        add_shortcode( 'btb_schema_organization', array('BTBooking_Schema_Org_Shortcode','schema_organization_func') );
    }

    /**
     * Processes the btb_schema_organization shortcode.
     *
     * Prints a JSON-LD script containing the Schema.org information.
     *
     * @param array $atts Shortcode attributes. See class description for explanation.
     */
    public static function schema_organization_func($atts) {

		if (!extension_loaded('json')) {
			return "";
		}

		$a = shortcode_atts(
			array(
				'type' => get_option('btb_struct_data_orga_type', 'Organization'),
				'id' => get_option('siteurl'),
				'name' => get_option('btb_struct_data_organization', ''),
				'description' => get_option('btb_struct_data_description', ''),
				'isicv4' => get_option('btb_struct_data_isicv4', ''),
				'pobox' => get_option('btb_struct_data_pobox', ''),
				'street' => get_option('btb_struct_data_street', ''),
				'zip' => get_option('btb_struct_data_postalcode', ''),
				'city' => get_option('btb_struct_data_city', ''),
				'region' => get_option('btb_struct_data_region', ''),
				'country' => get_option('btb_struct_data_country', ''),
				'email' => get_option('btb_struct_data_email', ''),
				'phone' => get_option('btb_struct_data_phone', ''),
				'fax' => get_option('btb_struct_data_fax', ''),
				'facebook' => get_option('btb_struct_data_facebook', ''),
				'instagram' => get_option('btb_struct_data_instagram', ''),
				'twitter' => get_option('btb_struct_data_twitter', ''),
				'googleplus' => get_option('btb_struct_data_googleplus', '')
			),
			$atts
		);

		$out .= "\n<script type='application/ld+json'>\n";

		$schema = array(
			'@context' => 'http://schema.org',
			'@type' => $a['type'],
			'@id' => $a['id']
		);

		if (!empty($a['name'])) $schema['name'] = $a['name'];
		if (!empty($a['description'])) $schema['description'] = $a['description'];
		if (!empty($a['isicv4'])) $schema['isicV4'] = $a['isicv4'];

		$schema['url'] = get_option('siteurl');

		$address = array('@type' => 'PostalAddress');

		if (!empty($a['pobox'])) $address['postOfficeBoxNumber'] = $a['pobox'];
		if (!empty($a['street'])) $address['streetAddress'] = $a['street'];
		if (!empty($a['zip'])) $address['postalCode'] = $a['zip'];
		if (!empty($a['city'])) $address['addressLocality'] = $a['city'];
		if (!empty($a['region'])) $address['addressRegion'] = $a['region'];
		if (!empty($a['country'])) $address['addressCountry'] = $a['country'];

		if (count($address) > 1) $schema['address'] = $address;

		if (!empty($a['email'])) $schema['email'] = $a['email'];
		if (!empty($a['phone'])) $schema['telephone'] = $a['phone'];
		if (!empty($a['fax'])) $schema['faxNumber'] = $a['fax'];

		$sameAs = array();
		if (!empty($a['facebook'])) $sameAs[] = $a['facebook'];
		if (!empty($a['instagram'])) $sameAs[] = $a['instagram'];
		if (!empty($a['twitter'])) $sameAs[] = $a['twitter'];
		if (!empty($a['googleplus'])) $sameAs[] = $a['googleplus'];

		if (count($sameAs) == 1) {
			$schema['sameAs'] = $sameAs[0];
		} elseif (count($sameAs) > 1) {
			$schema['sameAs'] = $sameAs;
		}

		$out .= json_encode($schema);

		$out .= "\n</script>\n";

		return $out;
    }
}