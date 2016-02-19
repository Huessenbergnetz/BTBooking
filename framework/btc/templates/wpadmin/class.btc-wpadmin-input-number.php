<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-input-number.php');


class BTCWPAdminInputNumber {

	public static function create($id, $label, $value, $min = null, $max = null, $step = null, $readonly = false) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$input = new BTCInputNumber(array('id' => $id, 'value' => $value, 'min' => $min, 'max' => $max, 'step' => $step, 'readonly' => $readonly));

		return array(new BTCTableData($label), new BTCTableData($input));

	}

}

?>