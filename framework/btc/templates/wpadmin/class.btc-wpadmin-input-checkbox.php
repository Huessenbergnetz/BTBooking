<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-input-checkbox.php');


class BTCWPAdminInputCheckbox {

	public static function create($id, $label, $checked) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$checkbox = new BTCInputCheckbox(array('id' => $id, 'checked' => $checked));

		return array(new BTCTableData($label), new BTCTableData($checkbox));

	}

}

?>