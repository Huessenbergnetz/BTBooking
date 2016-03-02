<?php

require_once 'class.btc-form-basic.php';

class BTCFormTextarea extends BTCHtmlForm {

	public $cols = 0;

	public $rows = 0;

	public $dirname = '';

	public $minlength = 0;

	public $maxlength = 0;

	public $pattern = '';

	public $placeholder = '';

	public $readonly = false;

	public $content = '';

	protected $tag_name = 'textarea';

	public function __construct(array $attrs = array(), $content = '') {

		$this->content = $content;
		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	protected function _render() {

		parent::_render();

		$this->add_attr('cols', $this->cols);
		$this->add_attr('rows', $this->rows);
		$this->add_attr('minlength', $this->minlength);
		$this->add_attr('maxlength', $this->maxlength);
		$this->add_attr('dirname', $this->dirname);
		$this->add_attr('pattern', $this->pattern);
		$this->add_attr('placeholder', $this->placeholder);
		$this->add_attr('readonly', $this->readonly);


		$this->output .= '>';

		if (!empty($this->content)) {
			$this->output .= $this->content;
		}

		$this->closeTag();
	}
}

?>