<?php

require_once(__DIR__.'/../../form/class.btc-input-text.php');


/**
 * @brief Renders a combination of a text input field and an optional description paragraph as they are typical for the Wordpress Admin Settings pages.
 *
 */
class BTCWPSettingsInputText {

/**
 * @brief Renders the HTML and prints it directly.
 *
 * @param string $id The HTML id attribute for the text input, this is also set as the name attribute.
 * @param string $value The content value of the text input.
 * @param string $desc Optional description, shown in a separate paragraph below the the text input.
 * @return void
 */
	public static function render($id, $value, $desc = '') {

		$attrs = array('id' => $id, 'value' => $value, 'htmlClasses' => 'regular-text');

		if (!empty($desc)) {
			$attrs['aria'] = array('describedby' => sprintf('%s-description', $id));
		}

		$input = new BTCInputText($attrs);

		$input->render();
		if (!empty($desc)) {
			printf('<p id="%s-description" class="description">%s</p>', $id, $desc);
		}

	}

}

?>