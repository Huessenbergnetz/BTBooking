<?php

require_once(__DIR__.'/../../form/class.btc-form-select.php');


/**
 * @brief Renders a combination of a select and a descriptive text after it.
 *
 */
class BTCWPSettingsFormSelect {

/**
 * @brief Renders the HTML and prints it directly.
 *
 * @param string $id		The HTML id attribute for the text input, this is also set as the name attribute.
 * @param array  $options	The options fot the select element.
 * @param string $desc		Optional description, shown as label for the checkbox.
 * @param string $multi		If true, the select allows the selection of multiple values.
 * @return void
 */
	public static function render($id, array $options = array(), $value = '', $desc = '', $multi = false) {

		if (!empty($desc)) {
			$descid = 'tagline-' . $id; // description id
			$aria = array('described-by' => $descid);
		} else {
			$aria = array();
		}

		$attrs = array('id' => $id, 'value' => $value, 'options' => $options, 'aria' => $aria, 'multiple' => $multi);

		$select = new BTCFormSelect($options, $attrs);

		if (!empty($desc)) {

			$out  = $select->render(false);

			$out .= '<p id="' . $descid . '" class="description">';
			$out .= $desc;
			$out .= '</p>';

			echo $out;

		} else {

			$select->render();

		}

	}

}

?>