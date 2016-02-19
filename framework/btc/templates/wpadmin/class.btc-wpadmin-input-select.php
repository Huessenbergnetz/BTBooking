<?php

require_once(__DIR__.'/../../table/class.btc-table-data.php');
require_once(__DIR__.'/../../form/class.btc-form-label.php');
require_once(__DIR__.'/../../form/class.btc-form-select.php');


class BTCWPAdminInputSelect {

	public static function create($id, $label, $value, array $options = array(), $disabled = false) {

		$label = new BTCFormLabel(array('for' => $id), $label);
		$select = new BTCFormSelect($options, array('id' => $id, 'value' => $value, 'disabled' => $disabled));

		return array(new BTCTableData($label), new BTCTableData($select));

	}

}

?>