<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-form-fieldset.php');
require_once(__DIR__.'/../../form/class.btc-input-radio.php');


class BTCWPAdminInputRadios {

	/**
	 * Returns an array of two table data fields containing a label and a fieldset of radio buttons.
	 *
	 * @param string	$id 		The id for the fieldset, also used as @a for value of the label.
	 * @param string	$name 		The name for the radio fields.
	 * @param string	$label		The label used for the fieldset.
	 * @param array		$radios 	An associative array containing data for the radio buttons.
	 * 								- @a key is the value of the radio button
	 * 								- @a value is the label used for the radio button
	 * @param string	$value		The value of the radio button that should be marked as checked.
	 * @param boolean	$disabled	Set to true to disbale the fieldset.
	 *
	 * @return array
	 */
	public static function create($id, $name, $label, array $radios = array(), $value = '', $disabled = false) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$_radios = array();
		foreach($radios as $radiovalue => $radiolabel) {
			$_radio = new BTCInputRadio(array('name' => $name, 'value' => $radiovalue, 'checked' => ($radiovalue == $value)));
			$_label = new BTCFormLabel(array('lineBreak' => true), $radiolabel, $_radio);
			$_radios[] = $_label;
		}
		$fieldset = new BTCFormFieldset($_radios, array('id' => $id));

		return array(new BTCTableData($label), new BTCTableData($fieldset));

	}

}

?>