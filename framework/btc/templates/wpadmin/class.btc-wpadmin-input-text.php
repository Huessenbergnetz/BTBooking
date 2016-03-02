<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-input-text.php');


class BTCWPAdminInputText {

	public static function create($id, $label, $value, $readonly = false, $htmlClass = null) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$input = new BTCInputText(array('id' => $id, 'value' => $value, 'readonly' => $readonly, 'htmlClasses' => $htmlClass));

		return array(new BTCTableData($label), new BTCTableData($input));

	}

}

?>