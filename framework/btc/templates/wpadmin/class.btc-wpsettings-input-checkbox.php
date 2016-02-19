<?php

require_once(__DIR__.'/../../form/class.btc-input-checkbox.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');


/**
 * @brief Renders a combination of a checkbox and a descriptive label.
 *
 */
class BTCWPSettingsInputCheckbox {

/**
 * @brief Renders the HTML and prints it directly.
 *
 * @param string $id The HTML id attribute for the text input, this is also set as the name attribute.
 * @param string $desc Optional description, shown as label for the checkbox.
 * @return void
 */
	public static function render($id, $checked = false, $desc = '') {

		$attrs = array('id' => $id, 'value' => '1', 'checked' => $checked);

		$checkbox = new BTCInputCheckbox($attrs);

		if (!empty($desc)) {

			$out  = '<fieldset>';
			$out .= '<legend class="screen-reader-text"><span>' . $desc . '</span></legend>';

			$label = new BTCFormLabel(array(), $desc, $checkbox);
			$out .= $label->render(false);

			$out .= '</fieldset>';

			echo $out;

		} else {

			$checkbox->render();

		}

	}

}

?>