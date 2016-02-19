<?php

require_once(__DIR__.'/../../form/class.btc-form-textarea.php');


/**
 * @brief Renders a combination of a textarea and a descriptive text beforce.
 *
 */
class BTCWPSettingsFormTextarea {

/**
 * @brief Renders the HTML and prints it directly.
 *
 * @param string $id The HTML id attribute for the text input, this is also set as the name attribute.
 * @param string $desc Optional description, shown as label for the checkbox.
 * @return void
 */
	public static function render($id, $value = '', $desc = '', $rows = 0) {

		$descid = 'tagline-' . $id; // description id
		$aria = array('described-by' => $descid);

		$attrs = array('id' => $id, 'value' => $value, 'rows' => $rows, 'aria' => $aria, 'style' => 'width:100%', 'htmlClass' => 'large-text code');

		$textarea = new BTCFormTextarea($attrs, $value);

		if (!empty($desc)) {

			$out  = '<p id="' . $descid . '" class="description">';
			$out .= $desc;
			$out .= '</p>';

			$out .= $textarea->render(false);

			echo $out;

		} else {

			$textarea->render();

		}

	}

}

?>