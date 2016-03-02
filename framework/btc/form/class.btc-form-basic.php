<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCHtmlForm extends BTCHtml {

	public $name = '';

	public $value = '';

	public $required = false;

	public $autofocus = false;

	public $disabled = false;

	public $form = '';


	protected function _render() {

		parent::_render();

		if (empty($this->name)) {
			$this->name = $this->id;
		}

		$this->add_attr('name', $this->name);
		$this->add_attr('value', $this->value);
		$this->add_attr('required', $this->required);
		$this->add_attr('autofocus', $this->autofocus);
		$this->add_attr('disabled', $this->disabled);
		$this->add_attr('form', $this->form);
	}

}

?>
