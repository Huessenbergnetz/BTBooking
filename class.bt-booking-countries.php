<?php
/**
 * @file
 * @brief Implements the BTBookingCountries and CCLabelValueObject classes.
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
 * Implements a list of ISO-3166 alpha 2 country codes and corresponding names.
 */
class BTBookingCountries {

	/**
	 * Returns an array of country codes and country names.
	 *
	 * [country_code] => country_name
	 *
	 * @param bool $sort If true, the country names are sorted alphabetically.
	 * @param bool $labelValueObject If true, an array of objects will be returned, with the public
	 *								 members \a value and \a label, where value is the country code
	 *								 and label the name of the country.
	 * @return array
	 */
	public static function get_countries($sort = true, $labelValueObject = false) {
		$countries = array
		(
			'AF' => __('Afghanistan', 'bt-booking'),
			'AX' => __('Aland Islands', 'bt-booking'),
			'AL' => __('Albania', 'bt-booking'),
			'DZ' => __('Algeria', 'bt-booking'),
			'AS' => __('American Samoa', 'bt-booking'),
			'AD' => __('Andorra', 'bt-booking'),
			'AO' => __('Angola', 'bt-booking'),
			'AI' => __('Anguilla', 'bt-booking'),
			'AQ' => __('Antarctica', 'bt-booking'),
			'AG' => __('Antigua And Barbuda', 'bt-booking'),
			'AR' => __('Argentina', 'bt-booking'),
			'AM' => __('Armenia', 'bt-booking'),
			'AW' => __('Aruba', 'bt-booking'),
			'AU' => __('Australia', 'bt-booking'),
			'AT' => __('Austria', 'bt-booking'),
			'AZ' => __('Azerbaijan', 'bt-booking'),
			'BS' => __('Bahamas', 'bt-booking'),
			'BH' => __('Bahrain', 'bt-booking'),
			'BD' => __('Bangladesh', 'bt-booking'),
			'BB' => __('Barbados', 'bt-booking'),
			'BY' => __('Belarus', 'bt-booking'),
			'BE' => __('Belgium', 'bt-booking'),
			'BZ' => __('Belize', 'bt-booking'),
			'BJ' => __('Benin', 'bt-booking'),
			'BM' => __('Bermuda', 'bt-booking'),
			'BT' => __('Bhutan', 'bt-booking'),
			'BO' => __('Bolivia', 'bt-booking'),
			'BA' => __('Bosnia And Herzegovina', 'bt-booking'),
			'BW' => __('Botswana', 'bt-booking'),
			'BV' => __('Bouvet Island', 'bt-booking'),
			'BR' => __('Brazil', 'bt-booking'),
			'IO' => __('British Indian Ocean Territory', 'bt-booking'),
			'BN' => __('Brunei Darussalam', 'bt-booking'),
			'BG' => __('Bulgaria', 'bt-booking'),
			'BF' => __('Burkina Faso', 'bt-booking'),
			'BI' => __('Burundi', 'bt-booking'),
			'KH' => __('Cambodia', 'bt-booking'),
			'CM' => __('Cameroon', 'bt-booking'),
			'CA' => __('Canada', 'bt-booking'),
			'CV' => __('Cape Verde', 'bt-booking'),
			'KY' => __('Cayman Islands', 'bt-booking'),
			'CF' => __('Central African Republic', 'bt-booking'),
			'TD' => __('Chad', 'bt-booking'),
			'CL' => __('Chile', 'bt-booking'),
			'CN' => __('China', 'bt-booking'),
			'CX' => __('Christmas Island', 'bt-booking'),
			'CC' => __('Cocos (Keeling) Islands', 'bt-booking'),
			'CO' => __('Colombia', 'bt-booking'),
			'KM' => __('Comoros', 'bt-booking'),
			'CG' => __('Congo', 'bt-booking'),
			'CD' => __('Congo, Democratic Republic', 'bt-booking'),
			'CK' => __('Cook Islands', 'bt-booking'),
			'CR' => __('Costa Rica', 'bt-booking'),
			'CI' => __('Cote D\'Ivoire', 'bt-booking'),
			'HR' => __('Croatia', 'bt-booking'),
			'CU' => __('Cuba', 'bt-booking'),
			'CY' => __('Cyprus', 'bt-booking'),
			'CZ' => __('Czech Republic', 'bt-booking'),
			'DK' => __('Denmark', 'bt-booking'),
			'DJ' => __('Djibouti', 'bt-booking'),
			'DM' => __('Dominica', 'bt-booking'),
			'DO' => __('Dominican Republic', 'bt-booking'),
			'EC' => __('Ecuador', 'bt-booking'),
			'EG' => __('Egypt', 'bt-booking'),
			'SV' => __('El Salvador', 'bt-booking'),
			'GQ' => __('Equatorial Guinea', 'bt-booking'),
			'ER' => __('Eritrea', 'bt-booking'),
			'EE' => __('Estonia', 'bt-booking'),
			'ET' => __('Ethiopia', 'bt-booking'),
			'FK' => __('Falkland Islands (Malvinas)', 'bt-booking'),
			'FO' => __('Faroe Islands', 'bt-booking'),
			'FJ' => __('Fiji', 'bt-booking'),
			'FI' => __('Finland', 'bt-booking'),
			'FR' => __('France', 'bt-booking'),
			'GF' => __('French Guiana', 'bt-booking'),
			'PF' => __('French Polynesia', 'bt-booking'),
			'TF' => __('French Southern Territories', 'bt-booking'),
			'GA' => __('Gabon', 'bt-booking'),
			'GM' => __('Gambia', 'bt-booking'),
			'GE' => __('Georgia', 'bt-booking'),
			'DE' => __('Germany', 'bt-booking'),
			'GH' => __('Ghana', 'bt-booking'),
			'GI' => __('Gibraltar', 'bt-booking'),
			'GR' => __('Greece', 'bt-booking'),
			'GL' => __('Greenland', 'bt-booking'),
			'GD' => __('Grenada', 'bt-booking'),
			'GP' => __('Guadeloupe', 'bt-booking'),
			'GU' => __('Guam', 'bt-booking'),
			'GT' => __('Guatemala', 'bt-booking'),
			'GG' => __('Guernsey', 'bt-booking'),
			'GN' => __('Guinea', 'bt-booking'),
			'GW' => __('Guinea-Bissau', 'bt-booking'),
			'GY' => __('Guyana', 'bt-booking'),
			'HT' => __('Haiti', 'bt-booking'),
			'HM' => __('Heard Island & Mcdonald Islands', 'bt-booking'),
			'VA' => __('Holy See (Vatican City State)', 'bt-booking'),
			'HN' => __('Honduras', 'bt-booking'),
			'HK' => __('Hong Kong', 'bt-booking'),
			'HU' => __('Hungary', 'bt-booking'),
			'IS' => __('Iceland', 'bt-booking'),
			'IN' => __('India', 'bt-booking'),
			'ID' => __('Indonesia', 'bt-booking'),
			'IR' => __('Iran, Islamic Republic Of', 'bt-booking'),
			'IQ' => __('Iraq', 'bt-booking'),
			'IE' => __('Ireland', 'bt-booking'),
			'IM' => __('Isle Of Man', 'bt-booking'),
			'IL' => __('Israel', 'bt-booking'),
			'IT' => __('Italy', 'bt-booking'),
			'JM' => __('Jamaica', 'bt-booking'),
			'JP' => __('Japan', 'bt-booking'),
			'JE' => __('Jersey', 'bt-booking'),
			'JO' => __('Jordan', 'bt-booking'),
			'KZ' => __('Kazakhstan', 'bt-booking'),
			'KE' => __('Kenya', 'bt-booking'),
			'KI' => __('Kiribati', 'bt-booking'),
			'KR' => __('Korea', 'bt-booking'),
			'KW' => __('Kuwait', 'bt-booking'),
			'KG' => __('Kyrgyzstan', 'bt-booking'),
			'LA' => __('Lao People\'s Democratic Republic', 'bt-booking'),
			'LV' => __('Latvia', 'bt-booking'),
			'LB' => __('Lebanon', 'bt-booking'),
			'LS' => __('Lesotho', 'bt-booking'),
			'LR' => __('Liberia', 'bt-booking'),
			'LY' => __('Libyan Arab Jamahiriya', 'bt-booking'),
			'LI' => __('Liechtenstein', 'bt-booking'),
			'LT' => __('Lithuania', 'bt-booking'),
			'LU' => __('Luxembourg', 'bt-booking'),
			'MO' => __('Macao', 'bt-booking'),
			'MK' => __('Macedonia', 'bt-booking'),
			'MG' => __('Madagascar', 'bt-booking'),
			'MW' => __('Malawi', 'bt-booking'),
			'MY' => __('Malaysia', 'bt-booking'),
			'MV' => __('Maldives', 'bt-booking'),
			'ML' => __('Mali', 'bt-booking'),
			'MT' => __('Malta', 'bt-booking'),
			'MH' => __('Marshall Islands', 'bt-booking'),
			'MQ' => __('Martinique', 'bt-booking'),
			'MR' => __('Mauritania', 'bt-booking'),
			'MU' => __('Mauritius', 'bt-booking'),
			'YT' => __('Mayotte', 'bt-booking'),
			'MX' => __('Mexico', 'bt-booking'),
			'FM' => __('Micronesia, Federated States Of', 'bt-booking'),
			'MD' => __('Moldova', 'bt-booking'),
			'MC' => __('Monaco', 'bt-booking'),
			'MN' => __('Mongolia', 'bt-booking'),
			'ME' => __('Montenegro', 'bt-booking'),
			'MS' => __('Montserrat', 'bt-booking'),
			'MA' => __('Morocco', 'bt-booking'),
			'MZ' => __('Mozambique', 'bt-booking'),
			'MM' => __('Myanmar', 'bt-booking'),
			'NA' => __('Namibia', 'bt-booking'),
			'NR' => __('Nauru', 'bt-booking'),
			'NP' => __('Nepal', 'bt-booking'),
			'NL' => __('Netherlands', 'bt-booking'),
			'AN' => __('Netherlands Antilles', 'bt-booking'),
			'NC' => __('New Caledonia', 'bt-booking'),
			'NZ' => __('New Zealand', 'bt-booking'),
			'NI' => __('Nicaragua', 'bt-booking'),
			'NE' => __('Niger', 'bt-booking'),
			'NG' => __('Nigeria', 'bt-booking'),
			'NU' => __('Niue', 'bt-booking'),
			'NF' => __('Norfolk Island', 'bt-booking'),
			'MP' => __('Northern Mariana Islands', 'bt-booking'),
			'NO' => __('Norway', 'bt-booking'),
			'OM' => __('Oman', 'bt-booking'),
			'PK' => __('Pakistan', 'bt-booking'),
			'PW' => __('Palau', 'bt-booking'),
			'PS' => __('Palestinian Territory, Occupied', 'bt-booking'),
			'PA' => __('Panama', 'bt-booking'),
			'PG' => __('Papua New Guinea', 'bt-booking'),
			'PY' => __('Paraguay', 'bt-booking'),
			'PE' => __('Peru', 'bt-booking'),
			'PH' => __('Philippines', 'bt-booking'),
			'PN' => __('Pitcairn', 'bt-booking'),
			'PL' => __('Poland', 'bt-booking'),
			'PT' => __('Portugal', 'bt-booking'),
			'PR' => __('Puerto Rico', 'bt-booking'),
			'QA' => __('Qatar', 'bt-booking'),
			'RE' => __('Reunion', 'bt-booking'),
			'RO' => __('Romania', 'bt-booking'),
			'RU' => __('Russian Federation', 'bt-booking'),
			'RW' => __('Rwanda', 'bt-booking'),
			'BL' => __('Saint Barthelemy', 'bt-booking'),
			'SH' => __('Saint Helena', 'bt-booking'),
			'KN' => __('Saint Kitts And Nevis', 'bt-booking'),
			'LC' => __('Saint Lucia', 'bt-booking'),
			'MF' => __('Saint Martin', 'bt-booking'),
			'PM' => __('Saint Pierre And Miquelon', 'bt-booking'),
			'VC' => __('Saint Vincent And Grenadines', 'bt-booking'),
			'WS' => __('Samoa', 'bt-booking'),
			'SM' => __('San Marino', 'bt-booking'),
			'ST' => __('Sao Tome And Principe', 'bt-booking'),
			'SA' => __('Saudi Arabia', 'bt-booking'),
			'SN' => __('Senegal', 'bt-booking'),
			'RS' => __('Serbia', 'bt-booking'),
			'SC' => __('Seychelles', 'bt-booking'),
			'SL' => __('Sierra Leone', 'bt-booking'),
			'SG' => __('Singapore', 'bt-booking'),
			'SK' => __('Slovakia', 'bt-booking'),
			'SI' => __('Slovenia', 'bt-booking'),
			'SB' => __('Solomon Islands', 'bt-booking'),
			'SO' => __('Somalia', 'bt-booking'),
			'ZA' => __('South Africa', 'bt-booking'),
			'GS' => __('South Georgia And Sandwich Isl.', 'bt-booking'),
			'ES' => __('Spain', 'bt-booking'),
			'LK' => __('Sri Lanka', 'bt-booking'),
			'SD' => __('Sudan', 'bt-booking'),
			'SR' => __('Suriname', 'bt-booking'),
			'SJ' => __('Svalbard And Jan Mayen', 'bt-booking'),
			'SZ' => __('Swaziland', 'bt-booking'),
			'SE' => __('Sweden', 'bt-booking'),
			'CH' => __('Switzerland', 'bt-booking'),
			'SY' => __('Syrian Arab Republic', 'bt-booking'),
			'TW' => __('Taiwan', 'bt-booking'),
			'TJ' => __('Tajikistan', 'bt-booking'),
			'TZ' => __('Tanzania', 'bt-booking'),
			'TH' => __('Thailand', 'bt-booking'),
			'TL' => __('Timor-Leste', 'bt-booking'),
			'TG' => __('Togo', 'bt-booking'),
			'TK' => __('Tokelau', 'bt-booking'),
			'TO' => __('Tonga', 'bt-booking'),
			'TT' => __('Trinidad And Tobago', 'bt-booking'),
			'TN' => __('Tunisia', 'bt-booking'),
			'TR' => __('Turkey', 'bt-booking'),
			'TM' => __('Turkmenistan', 'bt-booking'),
			'TC' => __('Turks And Caicos Islands', 'bt-booking'),
			'TV' => __('Tuvalu', 'bt-booking'),
			'UG' => __('Uganda', 'bt-booking'),
			'UA' => __('Ukraine', 'bt-booking'),
			'AE' => __('United Arab Emirates', 'bt-booking'),
			'GB' => __('United Kingdom', 'bt-booking'),
			'US' => __('United States', 'bt-booking'),
			'UM' => __('United States Outlying Islands', 'bt-booking'),
			'UY' => __('Uruguay', 'bt-booking'),
			'UZ' => __('Uzbekistan', 'bt-booking'),
			'VU' => __('Vanuatu', 'bt-booking'),
			'VE' => __('Venezuela', 'bt-booking'),
			'VN' => __('Viet Nam', 'bt-booking'),
			'VG' => __('Virgin Islands, British', 'bt-booking'),
			'VI' => __('Virgin Islands, U.S.', 'bt-booking'),
			'WF' => __('Wallis And Futuna', 'bt-booking'),
			'EH' => __('Western Sahara', 'bt-booking'),
			'YE' => __('Yemen', 'bt-booking'),
			'ZM' => __('Zambia', 'bt-booking'),
			'ZW' => __('Zimbabwe', 'bt-booking'),
		);

		if ($sort) {
			setlocale(LC_COLLATE, get_option('WPLANG') . '.utf8');
			asort($countries, SORT_LOCALE_STRING);
		}

		if ($labelValueObject) {
			$objArray = array();

			foreach($countries as $cc => $name) {
				$obj = new CCLabelValueObject($name, $cc);
				$objArray[] = $obj;
			}

			return $objArray;

		} else {

			$nothing[''] = __('Nothing selected', 'bt-booking');

			return array_merge($nothing, $countries);

		}
	}

	/**
	 * Returns the name of the country of \a $code.
	 *
	 * @param string $code ISO 3166 alpha 2 country code.
	 * @return string Country name.
	 */
	public static function get_country_by_code($code) {
		$countries = self::get_countries(false);
		return $countries[$code];
	}

}


/**
 * Used as container for a country object witch country code and name.
 *
 * Main purpose of this class is to use it in an array that gets converted
 * to a JSON string.
 */
class CCLabelValueObject {

	/**
	 * Name of the country.
	 *
	 * @var string $label
	 */
	public $label;

	/**
	 * ISO 3166 alpha 2 country code.
	 *
	 * @var string $value
	 */
	public $value;

	/**
	 * Contstuctor
	 *
	 * @param string $l Label
	 * @param string $v Value
	 */
	public function __construct($l, $v) {
		$this->label = $l;
		$this->value = $v;
	}
}

?>