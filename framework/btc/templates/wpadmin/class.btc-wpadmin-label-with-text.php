<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');


class BTCWPAdminLabelWithText {

	public static function create($id, $label, $value) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$content = $value;

		return array(new BTCTableData($label), new BTCTableData($content));

	}

}

?>