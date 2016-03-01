<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCTableData extends BTCHtml {

	public $content;

	public $header = false;

	public $scope = '';

	public $colspan = -1;

	public $rowspan = -1;

	public $abbr = '';


	public function __construct($content = null, array $attrs = array(), $header = false) {

		$this->content = $content;

		$this->header = $header;

		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	protected function _render() {

		if ($this->header) {
			$this->tag_name = 'th';
		} else {
			$this->tag_name = 'td';
		}

		parent::_render();

		btc_gen_attr($this->output, 'scope', $this->scope);
		if ($this->header) {
			btc_gen_attr($this->output, 'abbr', $this->abbr);
		}
		if ($this->colspan > 1) btc_gen_attr($this->output, 'colspan', $this->colspan);
		if ($this->rowspan > -1) btc_gen_attr($this->output, 'rowspan', $this->rowspan);

		$this->output .= '>';

		if (!empty($this->content)) {
			if (is_array($this->content)) {
				foreach($this->content as $key => $object) {
					if (is_scalar($object)) {
						$this->output .= $object;
					} else if (is_object($object)) {
						$this->output .= $object->render(false);
					}
				}
			} else if (is_scalar($this->content)) {
				$this->output .= $this->content;
			} else if (is_object($this->content)) {
				$this->output .= $this->content->render(false);
			}
		}

		if ($this->header) {
			$this->output .= '</th>';
		} else {
			$this->output .= '</td>';
		}
	}
}

?>