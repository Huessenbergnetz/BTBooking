<?php

require_once(__DIR__.'/../../form/class.btc-input-email.php');


class BTCWPSettingsInputEmail {

	public static function render($id, $value, $desc = '') {

		$attrs = array('id' => $id, 'value' => $value, 'htmlClasses' => 'regular-text');

		if (!empty($desc)) {
			$attrs['aria'] = array('describedby' => sprintf('%s-description', $id));
		}

		$input = new BTCInputEmail($attrs);

		$input->render();
		if (!empty($desc)) {
			printf('<p id="%s-description" class="description">%s</p>', $id, $desc);
		}

	}

}

?>