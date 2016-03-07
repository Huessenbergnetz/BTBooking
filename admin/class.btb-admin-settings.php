<?php
/**
 * @file
 * @brief Implements the BTBooking_Admin_Settings class.
 * @author Matthias Fehring
 * @version 1.0.0
 * @date 2016
 *
 * @copyright
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/** @page optionsdb Options DB Layout
 * Overview of keys used in the options table (wp_options).
 * @tableofcontents
 *
 * The following database keys are saved in the wp_options table. Here is
 * a description of the @a option_name keys and their default values.
 *
 * These option values are defined in the class BTBooking_Admin_Settings
 * in the file class.btb-admin-settings.php.
 *
 * @section generalsettings General Settings
 * - @c btb_general_contact_page \n
 *   ID of the general contact page used for reporting issues and other stuff. Default: @a nothing
 * - @c btb_currency \n
 *   The currency symbol. Default: \a €
 * - @c btb_currency_code \n
 *   The ISO 4217 three letter currency code. Default: @a EUR
 *
 * @section checkoutsettings Checkout Settings
 * - @c btb_terms_page \n
 *   ID of the page that contains the service and usage terms. Default: @a nothing
 * - @c btb_contact_page \n
 *   ID of the page providing a contact forumlar for individual booking requests. Default: @a nothing
 * - @c btb_checkout_page \n
 *   ID of the page that contains the @c btb_checkout and @c btb_checkout_overview shortcode. Default: @a nothing
 * - @c btb_checkout_header \n
 *   Optional header for the checkout page. Default: @a empty
 * - @c btb_checkout_book_now_text \n
 *   Text that is shown on the button to finish the cehckout. Default: @a Book @a now
 * - @c btb_checkout_info \n
 *   Info text shown to the user on the checkout page. Default: @a empty
 * - @c btb_checkout_require_terms \n
 *   Boolean value to force the user to accept the terms and condtions before fnishing the booking. Default: @a false
 * - @c btb_checkout_require_text \n
 *   Text that is shown to the user to require the acceptance of the terms and conditions. Default: @a empty
 *
 * @section stylesettings Style Settings
 * - @c btb_style \n
 *   Style used for displaying the content. Default: @a default
 * - @c btb_custom_style \n
 *   Custom CSS style added to the template. Default: @a empty
 * - @c btb_clearfix_tag \n
 *   The name of a clear fix CSS class used by the current theme. Default: @a empty
 *
 * @section structdatasettings Struct Data Settings
 * - @c btb_struct_data_enabled \n
 *   Boolean value that enables or disables the inclustion of custom data. Default: @a false
 * - @c btb_struct_data_default_type \n
 *   Default Schema.org data type for events. Default: @a event
 * - @c btb_struct_data_event_type \n
 *   Default Schema.Org Event type used for events. Default: @a Event
 * - @c btb_struct_data_orga_info_page \n
 *   Page containing information about your organization. Default: @a empty
 * - @c btb_struct_data_src_desc \n
     The source for the meta description of the event. Default: @a default
 * - @c btb_struct_data_orga_type \n
 *   Type of the Schema.org organization type. Default: @a Organization
 * - @c btb_struct_data_organization \n
 *   Name of the organization. Default: @a empty
 * - @c btb_struct_data_description \n
 *   Description of the organization. Default: @a empty
 * - @c btb_struct_data_orga_url \n
 *   URL of the organization. Default: @a site
 * - @c btb_struct_data_isicv4 \n
 *   ISIC V4 Code. Default: @a empty
 * - @c btb_struct_data_pobox \n
 *   Post office box. Default: @a empty
 * - @c btb_struct_data_street \n
 *   Street and house number: Default: @a empty
 * - @c btb_struct_data_postalcode \n
 *   Postal code: Default: @a empty
 * - @c btb_struct_data_city \n
 *   Region where the organization is located in. Default: @a empty
 * - @c btb_struct_data_country \n
 *   ISO 3166 alpha 2 country code of the organization's home country. Default: @a empty
 * - @c btb_struct_data_email \n
 *   Main contact email address of the organization. Default: @a empty
 * - @c btb_struct_data_phone \n
 *   Main contact phone number of the organization. Default: @a empty
 * - @c btb_struct_data_fax \n
 *   Main contact fax number of the organization. Default: @a empty
 * - @c btb_struct_data_facebook \n
 *   The facebook profile page of the organization. Default: @a empty
 * - @c btb_struct_data_instagram \n
 *   Instagram profile page of the organization. Default: @a empty
 * - @c btb_struct_data_twitter \n
 *   Twitter profile page of the organization. Default: @a empty
 * - @c btb_struct_data_googleplus \n
 *   Google+ profile page of the organization. Default: @a empty
 *
 * @section shortcodesettings Shortcode Settings
 * - @c btb_shortcode_headline \n
 *   Headline for the direct booking box. Default: @a Booking
 * - @c btb_shortcode_buttontext \n
 *   Text used for the booking button. Default: @a Book
 * - @c btb_shortcode_buttonclass \n
 *   HTML class used for styling the booking button. Default: @a empty
 * - @c btb_shortcode_timeselectortext \n
 *   Label used for the time selector. Default: @a Dates
 * - @c btb_shortcode_timeselectorclass \n
 *   HTML class used to style the time selector. Default: @a empty
 * - @c btb_shortcode_timeselectorlayout \n
 *   Base layout of the time selector, dropdown, bigdropdown or radiolist. Default: @a dropdown
 * - @c btb_shortcode_amount_label \n
 *   Label used for the amount input. Default: @a People
 * - @c btb_shortcode_amount_class \n
 *   HTML class use to style the amount input. Default: @a empty
 * - @c btb_shortcode_amount_label \n
 *   HTML class for an optional DIV that surrounds the amount input. Default: @a empty
 * - @c btb_shortcode_ind_req_label \n
 *   Label used for the individual request contact link. Default: @a Individual @a request
 * - @c btb_shortcode_force_ind_req \n
 *   Show individual request link when there are free slots. Default: @a false
 */

defined( 'ABSPATH' ) or die (' Am Arsch die R&auml;uber! ');

require_once(BTB__PLUGIN_DIR . 'class.bt-booking-countries.php');

/**
 * Core class used to implement the BTBooking_Admin_Edit_Booking object.
 *
 * This class provides the settings interface for BT Booking by using the WordPress setting API.
 *
 * @todo Use more generic callbacks where possible.
 * @todo Add more documentation.
 * @todo Add help tabs.
 */
class BTBooking_Admin_Settings {


    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'settings_init'));
    }

    public function add_settings_page() {
        $settings_page = add_submenu_page('edit.php?post_type=btb_event', esc_html__('Settings'), esc_html__('Settings'), 'manage_options', 'btb-settings', array('BTBooking_Admin_Settings', 'render_settings'));
        add_action('load-'.$settings_page, array($this, 'add_btb_help_tabs'));
    }

    public static function add_btb_help_tabs() {
		$screen = get_current_screen();

		$screen->add_help_tab(array(
			'id' => 'btb_settings_general_help_tab',
			'title' => esc_html__('General', 'bt-booking'),
			'callback' => array('BTBooking_Admin_Settings', 'general_help_tab')
		));
    }

    public static function general_help_tab() {
    ?>
		<p>Kleiner Test</p>
    <?php
    }

    public static function render_settings() {

        ?>

        <div class="wrap">

            <h1>BT Booking » <?php esc_html_e('Settings'); ?></h1>
            <?php settings_errors(); ?>

            <?php
			$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
            ?>

            <h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php echo $active_tab == 'general' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=general"><?php esc_html_e('General'); ?></a>
				<a class="nav-tab<?php echo $active_tab == 'checkout' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=checkout"><?php esc_html_e('Checkout', 'bt-booking'); ?></a>
				<a class="nav-tab<?php echo $active_tab == 'email' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=email"><?php esc_html_e('E-Mails', 'bt-booking'); ?></a>
				<a class="nav-tab<?php echo $active_tab == 'style' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=style"><?php esc_html_e('Style', 'bt-booking'); ?></a>
				<a class="nav-tab<?php echo $active_tab == 'structdata' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=structdata"><?php esc_html_e('Structured Data', 'bt-booking'); ?></a>
				<a class="nav-tab<?php echo $active_tab == 'shortcode' ? ' nav-tab-active' : ''; ?>" href="?post_type=btb_event&page=btb-settings&tab=shortcode"><?php esc_html_e('Shortcode', 'bt-booking'); ?></a>
            </h2>

            <form method="post" action="options.php">

            <?php

            if ($active_tab == 'general') {
				settings_fields('btb-settings-general');
				do_settings_sections('btb-settings-general');
            } else if ($active_tab == 'checkout') {
				settings_fields('btb-settings-checkout');
				do_settings_sections('btb-settings-checkout');
            } else if ($active_tab == 'email') {
				settings_fields('btb-settings-email');
				do_settings_sections('btb-settings-email');
            } else if ($active_tab == 'style') {
				settings_fields('btb-settings-style');
				do_settings_sections('btb-settings-style');
            } else if ($active_tab == 'structdata') {
				settings_fields('btb-settings-structdata');
				do_settings_sections('btb-settings-structdata');
            } else if ($active_tab == 'shortcode') {
				settings_fields('btb-settings-shortcode');
				do_settings_sections('btb-settings-shortcode');
            }

            submit_button();

            ?>

            </form>

        </div>

        <?php
    }

    public function settings_init() {
		register_setting('btb-settings-general', 'btb_general_contact_page');
        register_setting('btb-settings-general', 'btb_currency', 'sanitize_text_field');
        register_setting('btb-settings-general', 'btb_currency_code', 'sanitize_text_field');

        register_setting('btb-settings-checkout', 'btb_terms_page');
        register_setting('btb-settings-checkout', 'btb_contact_page');
        register_setting('btb-settings-checkout', 'btb_checkout_page');
        register_setting('btb-settings-checkout', 'btb_checkout_header', 'sanitize_text_field');
        register_setting('btb-settings-checkout', 'btb_checkout_book_now_text', 'sanitize_text_field');
        register_setting('btb-settings-checkout', 'btb_checkout_info');
        register_setting('btb-settings-checkout', 'btb_checkout_require_terms');
        register_setting('btb-settings-checkout', 'btb_checkout_require_text');

        register_setting('btb-settings-email', 'btb_confirm_from', 'sanitize_email');
        register_setting('btb-settings-email', 'btb_confirm_subject');
        register_setting('btb-settings-email', 'btb_confirm_template');
        register_setting('btb-settings-email', 'btb_confirm_html');
        register_setting('btb-settings-email', 'btb_notify_to', 'sanitize_email');
        register_setting('btb-settings-email', 'btb_notify_subject');
        register_setting('btb-settings-email', 'btb_notify_template');
        register_setting('btb-settings-email', 'btb_notify_html');

        register_setting('btb-settings-style', 'btb_style');
        register_setting('btb-settings-style', 'btb_custom_style');
        register_setting('btb-settings-style', 'btb_clearfix_tag', 'sanitize_text_field');

        register_setting('btb-settings-structdata', 'btb_struct_data_enabled');
        register_setting('btb-settings-structdata', 'btb_struct_data_default_type');
        register_setting('btb-settings-structdata', 'btb_struct_data_event_type');
        register_setting('btb-settings-structdata', 'btb_struct_data_orga_info_page');
        register_setting('btb-settings-structdata', 'btb_struct_data_src_desc');
        register_setting('btb-settings-structdata', 'btb_struct_data_orga_type');
        register_setting('btb-settings-structdata', 'btb_struct_data_organization', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_description', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_orga_url', 'esc_url_raw');
        register_setting('btb-settings-structdata', 'btb_struct_data_isicv4');
        register_setting('btb-settings-structdata', 'btb_struct_data_pobox', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_street', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_postalcode', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_city', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_region', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_country');
        register_setting('btb-settings-structdata', 'btb_struct_data_email', 'sanitize_email');
        register_setting('btb-settings-structdata', 'btb_struct_data_phone', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_fax', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_facebook', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_instagram', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_twitter', 'sanitize_text_field');
        register_setting('btb-settings-structdata', 'btb_struct_data_googleplus', 'sanitize_text_field');

        register_setting('btb-settings-shortcode', 'btb_shortcode_headline', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_buttontext', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_buttonclass', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_timeselectortext', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_timeselectorclass', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_timeselectorlayout');
        register_setting('btb-settings-shortcode', 'btb_shortcode_amount_label', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_amount_class', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_amount_surrounding', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_ind_req_label', 'sanitize_text_field');
        register_setting('btb-settings-shortcode', 'btb_shortcode_force_ind_req');

        // Start general section

        add_settings_section('btb-settings-general', esc_html__('General', 'bt-booking'), array($this, 'print_section_general_info'), 'btb-settings-general');

        add_settings_field('btb_general_contact_page',
			esc_html__('Contact page', 'bt-booking'),
			array($this, 'settings_page_select'),
			'btb-settings-general',
			'btb-settings-general',
			array(
				'id' => 'btb_general_contact_page',
				'default' => '',
				'description' => esc_html__('Select a page with a generic contact formular where customers can contact you in case of issues during the booking process', 'bt-booking')
			)
        );

        add_settings_section('btb-settings-currency', esc_html__('Currency', 'bt-booking'), array($this, 'print_section_currency_info'), 'btb-settings-general');

        add_settings_field('btb_currency',
			esc_html__('Currency symbol', 'bt-booking'),
			array($this, 'settings_input_text'),
			'btb-settings-general',
			'btb-settings-currency',
			array(
				'id' => 'btb_currency',
				'default' => '€',
				'description' => esc_html__('Enter the name or symbol of your currency. This is used for displaying prices.', 'bt-booking')
			)
        );

        add_settings_field('btb_currency_code',
			esc_html__('Currency code', 'bt-booking'),
			array($this, 'settings_input_text'),
			'btb-settings-general',
			'btb-settings-currency',
			array(
				'id' => 'btb_currency_code',
				'default' => 'EUR',
				'description' => wp_kses(__('Enter the <a href="https://en.wikipedia.org/wiki/ISO_4217">ISO 4217</a> three-letter currency code. This is used mainly for structured data.', 'bt-booking'), array('a' => array('href' => array())))
			)
        );

        // End general section


        // Start checkout section

        add_settings_section('btb-settings-checkout', esc_html__('Checkout', 'bt-booking'), array($this, 'print_section_checkout_info'), 'btb-settings-checkout');
        add_settings_field('btb_checkout_page', esc_html__('Checkout page', 'bt-booking'), array($this, 'checkout_page_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_contact_page', esc_html__('Contact page', 'bt-booking'), array($this, 'contact_page_cb'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_checkout_header', esc_html__('Checkout header', 'bt-booking'), array($this, 'checkout_header_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_checkout_book_now_text', esc_html__('Book now button', 'bt-booking'), array($this, 'book_now_text_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_checkout_info', esc_html__('Info text', 'bt-booking'), array($this, 'checkout_info_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_terms_page', esc_html__('Terms and conditions', 'bt-booking'), array($this,'terms_page_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_checkout_require_terms', esc_html__('Require terms accepted', 'bt-booking'), array($this, 'require_terms_callback'), 'btb-settings-checkout', 'btb-settings-checkout');
        add_settings_field('btb_checkout_require_text', esc_html__('Require terms text', 'bt-booking'), array($this, 'require_text_callback'), 'btb-settings-checkout', 'btb-settings-checkout');

        // End Checkout seciton


        // Start confirmation email section

        add_settings_section('btb-settings-confirm-email', esc_html__('Confirmation E-mail', 'bt-booking'), array($this, 'print_section_confirm_email_info'), 'btb-settings-email');
        add_settings_field('btb_confirm_from', esc_html__('Confirmation sender', 'bt-booking'), array($this, 'confirm_from_cb'), 'btb-settings-email', 'btb-settings-confirm-email');
        add_settings_field('btb_confirm_subject', esc_html__('Confirmation subject', 'bt-booking'), array($this, 'confirm_subject_cb'), 'btb-settings-email', 'btb-settings-confirm-email');
        add_settings_field('btb_confirm_template', esc_html__('Confirmation template', 'bt-booking'), array($this, 'confirm_template_cb'), 'btb-settings-email', 'btb-settings-confirm-email');
        add_settings_field('btb_confirm_html', esc_html__('HTML E-mail', 'bt-booking'), array($this, 'confirm_html_cb'), 'btb-settings-email', 'btb-settings-confirm-email');

        // End confirmation email section


        // Start notification email section

        add_settings_section('btb-settings-notify-email', esc_html__('Notification E-mail', 'bt-booking'), array($this, 'print_section_notify_email_info'), 'btb-settings-email');
        add_settings_field('btb_notify_to', esc_html__('Notification address', 'bt-booking'), array($this, 'notify_to_cb'), 'btb-settings-email', 'btb-settings-notify-email');
        add_settings_field('btb_notify_subject', esc_html__('Notification subject', 'bt-booking'), array($this, 'notify_subject_cb'), 'btb-settings-email', 'btb-settings-notify-email');
		add_settings_field('btb_notify_template', esc_html__('Notification template', 'bt-booking'), array($this, 'notify_template_cb'), 'btb-settings-email', 'btb-settings-notify-email');
		add_settings_field('btb_notify_html', esc_html__('HTML E-mail', 'bt-booking'), array($this, 'notify_html_cb'), 'btb-settings-email', 'btb-settings-notify-email');

		// End notifiaction email section


		// Start style section

        add_settings_section('btb-settings-style', esc_html__('Style'), array($this, 'print_section_style_info'), 'btb-settings-style');
        add_settings_field('btb_style',
			esc_html__('Style', 'bt-booking'),
			array($this, 'settings_generic_select'),
			'btb-settings-style',
			'btb-settings-style',
			array(
				'id' => 'btb_style',
				'default' => 'default',
// 				'description' => esc_html__('Layout used for the time selector.', 'bt-booking'),
				'options' => array('default' => esc_html__('Default'), 'avada' => 'Avada')
			)
		);
        add_settings_field('btb_custom_style', esc_html__('Custom style', 'bt-booking'), array($this, 'custom_style_callback'), 'btb-settings-style', 'btb-settings-style');
        add_settings_field('btb_clearfix_tag', esc_html__('Clearfix tag', 'bt-booking'), array($this, 'clearfix_tag_callback'), 'btb-settings-style', 'btb-settings-style');

        // End style section


        // Start struct data sections

        add_settings_section('btb-settings-struct-data', esc_html__('Structured Data', 'bt-booking'), array($this, 'print_section_struct_data_info'), 'btb-settings-structdata');
		add_settings_field('btb_struct_data_enabled', esc_html__('Enable', 'bt-booking'), array($this, 'struct_data_enabled_callback'), 'btb-settings-structdata', 'btb-settings-struct-data');

		add_settings_field('btb_struct_data_default_type',
			esc_html__('Default data type', 'bt-booking'),
			array($this, 'settings_input_radios'),
			'btb-settings-structdata',
			'btb-settings-struct-data',
			array(
				'id' => 'btb_struct_data_default_type',
				'default' => 'event',
				'radios' => array(
					'disabled' => __('Disabled', 'bt-booking'),
					'product' => __('Product', 'bt-booking'),
					'event' => __('Event', 'bt-booking')
				),
				'description' => esc_html__('Default data type used for your events, you can change it per event.', 'bt-booking')
			)
		);

		add_settings_field('btb_struct_data_event_type',
			esc_html__('Default event type', 'bt-booking'),
			array($this, 'settings_generic_select'),
			'btb-settings-structdata',
			'btb-settings-struct-data',
			array(
				'id' => 'btb_struct_data_event_type',
				'default' => 'Event',
				'options' => btb_get_event_types(),
				'description' => esc_html__('Default event type if your default data type is event.', 'bt-booking'), array('code' => array())
			)
		);

		add_settings_field('btb_struct_data_orga_info_page',
			esc_html__('Organization info page', 'bt-booking'),
			array($this, 'settings_page_select'),
			'btb-settings-structdata',
			'btb-settings-struct-data',
			array(
				'id' => 'btb_struct_data_orga_info_page',
				'default' => '',
				'description' => wp_kses(__('Select a page that contains structured Schema.org data about your organization. You can use the settings below together with the <code>btb_schema_organization</code> shortcode.', 'bt-booking'), array('code' => array()))
			)
		);

		add_settings_field('btb_struct_data_src_desc',
			esc_html__('Description source', 'bt-booking'),
			array($this, 'settings_generic_select'),
			'btb-settings-structdata',
			'btb-settings-struct-data',
			array(
				'id' => 'btb_struct_data_src_desc',
				'default' => 'default',
				'options' => array(
					'default' => __('Default', 'bt-booking'),
					'yoastseo' => 'Yoast SEO'
				),
				'description' => esc_html__('Select the source for the meta description of the events. By default, the short description of the event or the excerpt of a description page are used.', 'bt-booking')
			)
		);

		add_settings_section('btb-settings-struct-data-organization', esc_html__('Organization', 'bt-booking'), array($this, 'print_section_struct_data_organization_info'), 'btb-settings-structdata');
		add_settings_field('btb_struct_data_orga_type', esc_html__('Organization type', 'bt-booking'), array($this, 'struct_data_orga_type_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-organization');
		add_settings_field('btb_struct_data_organization', esc_html__('Name', 'bt-booking'), array($this, 'struct_data_organization_name_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-organization');
		add_settings_field('btb_struct_data_description', esc_html__('Description', 'bt-booking'), array($this, 'struct_data_organization_description_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-organization');
		add_settings_field('btb_struct_data_orga_url',
			esc_html__('Website', 'bt-booking'),
			array($this, 'settings_input_url'),
			'btb-settings-structdata',
			'btb-settings-struct-data-organization',
			array(
				'id' => 'btb_struct_data_orga_url',
				'default' => '',
				'description' => esc_html__('URL of your organization\'s website. By default your site URL is used.', 'bt-booking'),
				'placeholder' => get_option('siteurl')
			)
		);
		add_settings_field('btb_struct_data_isicv4', 'ISIC V4', array($this, 'struct_data_address_isicv4_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-organization');

		add_settings_section('btb-settings-struct-data-address', esc_html__('Address', 'bt-booking'), array($this, 'print_section_struct_data_address_info'), 'btb-settings-structdata');
		add_settings_field('btb_struct_data_pobox', esc_html__('PO Box', 'bt-booking'), array($this, 'struct_data_address_pobox_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');
		add_settings_field('btb_struct_data_street', esc_html__('Street', 'bt-booking'), array($this, 'struct_data_address_street_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');
		add_settings_field('btb_struct_data_postalcode', esc_html__('Postal code', 'bt-booking'), array($this, 'struct_data_address_postalcode_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');
		add_settings_field('btb_struct_data_city', esc_html__('City', 'bt-booking'), array($this, 'struct_data_address_city_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');
		add_settings_field('btb_struct_data_region', esc_html__('Region', 'bt-booking'), array($this, 'struct_data_address_region_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');
		add_settings_field('btb_struct_data_country', esc_html__('Country', 'bt-booking'), array($this, 'struct_data_address_country_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-address');

		add_settings_section('btb-settings-struct-data-contact', esc_html__('Contact', 'bt-booking'), array($this, 'print_section_struct_data_contact_info'), 'btb-settings-structdata');
		add_settings_field('btb_struct_data_email', esc_html__('E-mail', 'bt-booking'), array($this, 'struct_data_email_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-contact');
		add_settings_field('btb_struct_data_phone', esc_html__('Phone number', 'bt-booking'), array($this, 'struct_data_phone_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-contact');
		add_settings_field('btb_struct_data_fax', esc_html__('Fax number', 'bt-booking'), array($this, 'struct_data_fax_cb'), 'btb-settings-structdata', 'btb-settings-struct-data-contact');


		add_settings_section('btb-settings-struct-data-social', esc_html__('Social Profiles', 'bt-booking'), array($this, 'print_section_struct_data_social_info'), 'btb-settings-structdata');

		add_settings_field('btb_struct_data_facebook',
			esc_html__('Facebook', 'bt-booking'),
			array($this, 'settings_input_url'),
			'btb-settings-structdata',
			'btb-settings-struct-data-social',
			array(
				'id' => 'btb_struct_data_facebook',
				'default' => '',
				'description' => esc_html__('Your organization\'s Facebook profile page.', 'bt-booking') . ' ' . esc_html__('E.g.', 'bt-booking') . ' https://www.facbook.com/MyCompanyPage',
				'placeholder' => 'https://www.facbook.com/MyCompanyPage'
			)
		);

		add_settings_field('btb_struct_data_instagram',
			esc_html__('Instagram', 'bt-booking'),
			array($this, 'settings_input_url'),
			'btb-settings-structdata',
			'btb-settings-struct-data-social',
			array(
				'id' => 'btb_struct_data_instagram',
				'default' => '',
				'description' => esc_html__('Your organization\'s Instagram profile page.', 'bt-booking') . ' ' . esc_html__('E.g.', 'bt-booking') . ' https://www.instagram.com/MyCompanyPage',
				'placeholder' => 'https://www.instagram.com/MyCompanyPage'
			)
		);

		add_settings_field('btb_struct_data_twitter',
			esc_html__('Twitter', 'bt-booking'),
			array($this, 'settings_input_url'),
			'btb-settings-structdata',
			'btb-settings-struct-data-social',
			array(
				'id' => 'btb_struct_data_twitter',
				'default' => '',
				'description' => esc_html__('Your organization\'s Twitter profile page.', 'bt-booking') . ' ' . esc_html__('E.g.', 'bt-booking') . ' https://twitter.com/MyCompanyPage',
				'placeholder' => 'https://twitter.com/MyCompanyPage'
			)
		);

		add_settings_field('btb_struct_data_googleplus',
			esc_html__('Google+', 'bt-booking'),
			array($this, 'settings_input_url'),
			'btb-settings-structdata',
			'btb-settings-struct-data-social',
			array(
				'id' => 'btb_struct_data_googleplus',
				'default' => '',
				'description' => esc_html__('Your organization\'s Google+ profile page.', 'bt-booking') . ' ' . esc_html__('E.g.', 'bt-booking') . ' https://plug.google.com/MyCompanyPage',
				'placeholder' => 'https://plus.google.com/MyCompanyPage'
			)
		);

		// End struct data section



		// Start shortcode section

		add_settings_section('btb-settings-shortcode', 'Shortcode', array($this, 'print_section_shortcode_info'), 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_headline', esc_html__('Booking headline', 'bt-booking'), array($this, 'shortcode_headline_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_buttontext', esc_html__('Book button text', 'bt-booking'), array($this, 'shortcode_buttontext_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_buttonclass', esc_html__('Book button class', 'bt-booking'), array($this, 'shortcode_buttonclass_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_timeselectortext', esc_html__('Time selector label', 'bt-booking'), array($this, 'shortcode_timeselectortext_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_timeselectorclass', esc_html__('Time selector class', 'bt-booking'), array($this, 'shortcode_timeselectorclass_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_timeselectorlayout',
			esc_html__('Time selector layout', 'bt-booking'),
			array($this, 'settings_generic_select'),
			'btb-settings-shortcode',
			'btb-settings-shortcode',
			array(
				'id' => 'btb_shortcode_timeselectorlayout',
				'default' => 'dropdown',
				'description' => esc_html__('Layout used for the time selector.', 'bt-booking'),
				'options' => array('dropdown' => esc_html__('Dropdown', 'bt-booking'), 'bigdropdown' => esc_html__('List', 'bt-booking'), 'radiolist' => esc_html__('Radio list', 'bt-booking')/*, 'styledlist' => esc_html__('Styled list', 'bt-booking')*/)
			)
		);
		add_settings_field('btb_shortcode_amount_label', esc_html__('Amount input label', 'bt-booking'), array($this, 'shortcode_amount_label_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_amount_class', esc_html__('Amount input class', 'bt-booking'), array($this, 'shortcode_amount_class_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_amount_surrounding', esc_html__('Amount surrounding class', 'bt-booking'), array($this, 'shortcode_amount_surrounding_cb'), 'btb-settings-shortcode', 'btb-settings-shortcode');
		add_settings_field('btb_shortcode_ind_req_label',
			esc_html__('Individual request label', 'bt-booking'),
			array($this, 'settings_input_text'),
			'btb-settings-shortcode',
			'btb-settings-shortcode',
			array(
				'id' => 'btb_shortcode_ind_req_label',
				'default' => esc_html__('Individual request', 'bt-booking'),
				'description' => esc_html__('Text or the individual request link.', 'bt-booking')
			)
		);
		add_settings_field('btb_shortcode_force_ind_req',
			esc_html__('Force individual request', 'bt-booking'),
			array($this, 'settings_input_checkbox'),
			'btb-settings-shortcode',
			'btb-settings-shortcode',
			array(
				'id' => 'btb_shortcode_force_ind_req',
				'default' => 0,
				'description' => esc_html__('Show the link for making an individual request when there are event times.', 'bt-booking')
			)
		);

		// End shortcode section

    }

    // Start print info callbacks

    public function print_section_general_info() {
    }

    public function print_section_currency_info() {
    }

    public function print_section_style_info() {
    }

    public function print_section_checkout_info() {
    }

    public function print_section_confirm_email_info() {
    }

    public function print_section_notify_email_info() {
    }

    public function print_section_struct_data_info() {
    }

    public function print_section_struct_data_organization_info() {
    }

    public function print_section_struct_data_address_info() {
    }

    public function print_section_struct_data_contact_info() {
    }

    public function print_section_struct_data_social_info() {
    }

    public function print_section_shortcode_info() {
    }

    // End print info callbacks


    // Start settings callbacks

    public function date_format_callback() {
        BTCWPSettingsInputText::render('btb_date_format', get_option('btb_date_format','dd.mm.yy'));
    }


    public function custom_style_callback() {
        ?>
        <textarea id="btb_custom_style" class="large-text code" style="width:100%" rows="10" name="btb_custom_style"><?php echo get_option('btb_custom_style',''); ?></textarea>
        <?php
    }


    public function checkout_page_callback() {
		$pages = get_pages();
		$options = array('' => esc_html__('Nothing selected', 'bt-booking'));
		if (!empty($pages)) {
			foreach($pages as $key => $page) {
				$options[$page->ID] = $page->post_title;
			}

		}

		BTCWPSettingsFormSelect::render('btb_checkout_page', $options, get_option('btb_checkout_page', ''), esc_html__('Page that containts the [btb_checkout] shortcode to display the booking checkout.', 'bt-booking'));
    }

    public function checkout_header_callback() {
		BTCWPSettingsInputText::render('btb_checkout_header', get_option('btb_checkout_header',''), esc_html__('Text that is shown as header on top of the checkout page. Leave blank to disable.', 'bt-booking'));
    }


    public function terms_page_callback() {
		$pages = get_pages();
		$options = array('' => esc_html__('Nothing selected', 'bt-booking'));
		if (!empty($pages)) {
			foreach($pages as $key => $page) {
				$options[$page->ID] = $page->post_title;
			}

		}

		BTCWPSettingsFormSelect::render('btb_terms_page', $options, get_option('btb_terms_page', ''), esc_html__('Page that provides information about your terms and conditions.', 'bt-booking'));
    }

    public function contact_page_cb() {
		$pages = get_pages();
		$options = array('' => esc_html__('Nothing selected', 'bt-booking'));
		if (!empty($pages)) {
			foreach($pages as $key => $page) {
				$options[$page->ID] = $page->post_title;
			}

		}

		BTCWPSettingsFormSelect::render('btb_contact_page', $options, get_option('btb_contact_page', ''), esc_html__('Page that provides a contact formular used for events without dates.', 'bt-booking'));
    }

    public function book_now_text_callback() {
		BTCWPSettingsInputText::render('btb_checkout_book_now_text', get_option('btb_checkout_book_now_text', esc_html__('Book now','bt-booking')), esc_html__('Text that is shown on the button to finish the checkout.', 'bt-booking'));
    }

    public function clearfix_tag_callback() {
		BTCWPSettingsInputText::render('btb_clearfix_tag', get_option('btb_clearfix_tag', ''));
    }

    public function checkout_info_callback() {
		BTCWPSettingsFormTextarea::render('btb_checkout_info', get_option('btb_checkout_info',''), esc_html__('Information text that is shown on the checkout page direct above the submit button. HTML can be used.', 'bt-booking'), 10);
    }

    public function require_terms_callback() {
		BTCWPSettingsInputCheckbox::render('btb_checkout_require_terms', get_option('btb_checkout_require_terms', 0) == 1, esc_html__('If enabled, the consumer has to check a checkbox to confirm that he has read the terms and coniditons.', 'bt-booking'));
    }

     public function require_text_callback() {
		BTCWPSettingsFormTextarea::render('btb_checkout_require_text', get_option('btb_checkout_require_text',''), esc_html__('If the user is required to confirm that he has read the terms and condition, enter a description here. HTML can be used.', 'bt-booking'), 5);
    }

	public function confirm_from_cb() {
		BTCWPSettingsInputEmail::render('btb_confirm_from', get_option('btb_confirm_from', ''), esc_html__('This is the e-mail address that is used as the sender address in E-mails to customers.', 'bt-booking'));
	}

	public function confirm_subject_cb() {
		BTCWPSettingsInputText::render('btb_confirm_subject', get_option('btb_confirm_subject'), esc_html__('The subject of the confirmation e-mail to the customer. You can use the same placeholders as for the template.', 'bt-booking'));
	}

	public function confirm_template_cb() {
		$desc = esc_html__('Create a template for an E-mail that is sent to your customers after they finished the booking process. You can use plain text or HTML If you are using HTML, do not forget to activate the checkbox down below this text area. In your template you can use the following tags that will be replaced by the corresponding customer and booking details data when the E-mail will be sent.', 'bt-booking');
		$desc .= '<p><code>{{salutation}}</code>, <code>{{title}}</code>, <code>{{first_name}}</code>, <code>{{last_name}}</code>, <code>{{company}}</code>, <code>{{address}}</code>, <code>{{address2}}</code>, <code>{{zip}}</code>, <code>{{city}}</code>, <code>{{country}}</code>, <code>{{mail}}</code>, <code>{{phone}}</code>, <code>{{notes}}</code>, <code>{{event_name}}</code>, <code>{{event_url}}</code>, <code>{{event_start_date}}</code>, <code>{{event_end_date}}</code>, <code>{{event_start_time}}</code>, <code>{{event_end_time}}</code>, <code>{{slots}}</code>, <code>{{single_price}}</code>, <code>{{total_price}}</code>, <code>{{booking_code}}</code>, <code>{{booking_time}}</code></p>';
		BTCWPSettingsFormTextarea::render('btb_confirm_template', get_option('btb_confirm_template',''), $desc, 15);
	}

    public function confirm_html_cb() {
		BTCWPSettingsInputCheckbox::render('btb_confirm_html', get_option('btb_confirm_html', 0) == 1, esc_html__('Enable this if you are using HTML code in your confirmation E-mail template.', 'bt-booking'));
    }

    public function notify_to_cb() {
		BTCWPSettingsInputEmail::render('btb_notify_to', get_option('btb_notify_to', ''), esc_html__('This is the E-mail address to which notifications about new bookings are sent. Leave blank to disable.', 'bt-booking'));
	}

    public function notify_subject_cb() {
		BTCWPSettingsInputText::render('btb_notify_subject', get_option('btb_notify_subject'), esc_html__('The subject of the notification e-mail to you.', 'bt-booking'));
    }

	public function notify_template_cb() {
		BTCWPSettingsFormTextarea::render('btb_notify_template', get_option('btb_notify_template',''), esc_html__('Create a template for an E-mail that is sent to you and informs you about new bookings. You can use the same tags as for the confirmation E-mail template.', 'bt-booking'), 15);
	}

    public function notify_html_cb() {
		BTCWPSettingsInputCheckbox::render('btb_notify_html', get_option('btb_notify_html', 0) == 1, esc_html__('Enable this if you are using HTML code in your notification E-mail template.', 'bt-booking'));
    }

    public function struct_data_enabled_callback() {
		BTCWPSettingsInputCheckbox::render('btb_struct_data_enabled', get_option('btb_struct_data_enabled', 0) == 1, esc_html__('Enable the output of structured data.', 'bt-booking'));
    }

//     public function struct_data_default_type_cb() {
// 		BTCWPSettingsFormSelect::render('btb_struct_data_event_type', btb_get_event_types(), get_option('btb_struct_data_event_type', 'Event'));
//     }

    public function struct_data_orga_type_cb() {
		BTCWPSettingsFormSelect::render('btb_struct_data_orga_type', btb_get_organization_types(), get_option('btb_struct_data_orga_type', 'Organization'));
    }

    public function struct_data_organization_name_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_organization', get_option('btb_struct_data_organization', ''), esc_html__('Name of your organization used for the structured data.', 'bt-booking'));
    }

    public function struct_data_organization_description_cb() {
		BTCWPSettingsFormTextarea::render('btb_struct_data_description', get_option('btb_struct_data_description', ''), esc_html__('Short description of your organization.', 'bt-booking'), 1);
    }

    public function struct_data_address_isicv4_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_isicv4', get_option('btb_struct_data_isicv4', ''), wp_kses(__('The International Standard of Industrial Classification of All Economic Activities (<a href="https://en.wikipedia.org/wiki/International_Standard_Industrial_Classification">ISIC</a>), Revision 4 code.', 'bt-booking'), array('a' => array('href' => array()))));
    }

    public function struct_data_address_pobox_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_pobox', get_option('btb_struct_data_pobox', ''), esc_html__('PO box of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_address_street_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_street', get_option('btb_struct_data_street', ''), esc_html__('Street and number of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_address_postalcode_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_postalcode', get_option('btb_struct_data_postalcode', ''), esc_html__('Postal code of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_address_city_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_city', get_option('btb_struct_data_city', ''), esc_html__('City of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_address_region_cb() {
		BTCWPSettingsInputText::render('btb_struct_data_region', get_option('btb_struct_data_region', ''), esc_html__('State or region of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_address_country_cb() {
		BTCWPSettingsFormSelect::render('btb_struct_data_country', BTBookingCountries::get_countries(), get_option('btb_struct_data_country', ''), esc_html__('Country of your organization\'s address.', 'bt-booking'));
    }

    public function struct_data_email_cb() {
		BTCWPSettingsInputEmail::render('btb_struct_data_email', get_option('btb_struct_data_email', ''), esc_html__('Your organization\'s official contact e-mail address.', 'bt-booking'));
    }

    public function struct_data_phone_cb() {
		BTCWPSettingsInputTel::render('btb_struct_data_phone', get_option('btb_struct_data_phone', ''), esc_html__('Your organization\'s official contact phone number.', 'bt-booking'));
    }

    public function struct_data_fax_cb() {
		BTCWPSettingsInputTel::render('btb_struct_data_fax', get_option('btb_struct_data_fax', ''), esc_html__('Your organization\'s official contact Fax number.', 'bt-booking'));
    }

    public function shortcode_headline_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_headline', get_option('btb_shortcode_headline', esc_html__('Booking', 'bt-booking')), esc_html__('Headline used for the booking box.', 'bt-booking'));
    }

    public function shortcode_buttontext_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_buttontext', get_option('btb_shortcode_buttontext', esc_html__('Book', 'bt-booking')), esc_html__('Text used for the booking button', 'bt-booking'));
    }

    public function shortcode_buttonclass_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_buttonclass', get_option('btb_shortcode_buttonclass', ''), esc_html__('HTML class used for styling the Book button.', 'bt-booking'));
    }

    public function shortcode_timeselectortext_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_timeselectortext', get_option('btb_shortcode_timeselectortext', esc_html__('Dates', 'bt-booking')), esc_html__('Label used for the time selector.', 'bt-booking'));
    }

    public function shortcode_timeselectorclass_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_timeselectorclass', get_option('btb_shortcode_timeselectorclass', ''), esc_html__('HTML class used for styling the time selector.', 'bt-booking'));
    }

    public function shortcode_amount_label_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_amount_label', get_option('btb_shortcode_amount_label', esc_html__('People', 'bt-booking')), esc_html__('Label used for the amount input.', 'bt-booking'));
    }

    public function shortcode_amount_class_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_amount_class', get_option('btb_shortcode_amount_class', ''), esc_html__('HTML class used for the amount input.', 'bt-booking'));
    }

    public function shortcode_amount_surrounding_cb() {
		BTCWPSettingsInputText::render('btb_shortcode_amount_surrounding', get_option('btb_shortcode_amount_surrounding', ''), esc_html__('HTML class used for a DIV surrounding the amount input.', 'bt-booking'));
    }





    public function settings_input_text($args) {
		BTCWPSettingsInputText::render($args['id'], get_option($args['id'], $args['default']), $args['description']);
    }

    public function settings_input_url($args) {
		BTCWPSettingsInputUrl::render($args['id'], get_option($args['id'], $args['default']), $args['description'], $args['placeholder']);
    }

    public function settings_page_select($args) {
		$pages = get_pages();
		$options = array('' => esc_html__('Nothing selected', 'bt-booking'));
		if (!empty($pages)) {
			foreach($pages as $key => $page) {
				$options[$page->ID] = $page->post_title;
			}

		}

		BTCWPSettingsFormSelect::render($args['id'], $options, get_option($args['id'], $args['default']), $args['description']);
    }

    public function settings_generic_select($args) {
		BTCWPSettingsFormSelect::render($args['id'], $args['options'], get_option($args['id'], $args['default']), $args['description']);
    }

    public function settings_input_checkbox($args) {
		BTCWPSettingsInputCheckbox::render($args['id'], get_option($args['id'], $args['default']) == 1, $args['description']);
    }

    public function settings_input_radios($args) {
		BTCWPSettingsInputRadios::render($args['id'], $args['radios'], get_option($args['id'], $args['default']), $args['description']);
    }

    // End settings callbacks

};

?>