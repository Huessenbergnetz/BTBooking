<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-form-textarea.php');


class BTCWPAdminFormTextarea {

	public static function create($id, $label, $value) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$textarea = new BTCFormTextarea(array('id' => $id), $value);

		return array(new BTCTableData($label), new BTCTableData($textarea));

	}

}

?>