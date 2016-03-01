<?php

require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-form-fieldset.php');
require_once(__DIR__.'/../../form/class.btc-input-radio.php');


/**
 * @brief Renders a combination of a fieldset with radion buttons and a descriptive text after it.
 *
 */
class BTCWPSettingsInputRadios {

	/**
	* @brief Renders the HTML and prints it directly.
	*
	* @param string $id		The HTML id attribute for the radio input fieldset, this is also set as the name attribute.
	* @param array  $radios	An associative array containing data for the radio buttons.
	* 						- @a key is the value of the radio button
	* 						- @a value is the label used for the radio button
	* @param string $desc	Optional description, shown as label for the fieldset.
	* @return void
	*/
	public static function render($id, array $radios = array(), $value = '', $desc = '') {

		if (!empty($desc)) {
			$descid = 'tagline-' . $id; // description id
			$aria = array('described-by' => $descid);
		} else {
			$aria = array();
		}

		$_radios = array();
		foreach($radios as $radiovalue => $radiolabel) {
			$_radio = new BTCInputRadio(array('name' => $id, 'value' => $radiovalue, 'checked' => $radiovalue == $value));
			$_label = new BTCFormLabel(array('lineBreak' => true), $radiolabel, $_radio);
			$_radios[] = $_label;
		}

		$fieldset = new BTCFormFieldset($_radios, array('id' => $id, 'aria' => $aria));

		if (!empty($desc)) {

			$out  = $fieldset->render(false);

			$out .= '<p id="' . $descid . '" class="description">';
			$out .= $desc;
			$out .= '</p>';

			echo $out;

		} else {

			$fieldset->render();

		}

	}

}

?>