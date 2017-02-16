<?php

require_once(__DIR__.'/../class.btc-html-basic.php');

class BTCTable extends BTCHtml {

	public $caption = null;

	public $body = null;

	public $head = null;

	public $foot = null;

	protected $tag_name = 'table';

	public function __construct(array $attrs = array(), BTCTableBody $body = null, BTCTableHead $head = null) {

		$this->body = $body;
		$this->head = $head;

		if (!empty($attrs)) {
			foreach($attrs as $key => $value) {
				$this->$key = $value;
			}
		}
	}

	protected  function _render() {

		parent::_render();

		$this->output .= '>';

		if ($this->caption) {
			$this->output .= $this->caption->render(falase);
		}

		if ($this->head) {
			$this->output .= $this->head->render(false);
		}

		if ($this->foot) {
			$this->output .= $this->foot->render(false);
		}

		if ($this->body) {
			$this->output .= $this->body->render(false);
		}

		$this->output .= '</table>';

// 		return $ret;
	}
}


?>
