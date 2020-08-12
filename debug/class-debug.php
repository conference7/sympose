<?php

class Sympose_Debug {
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_filter( 'cmb2_meta_box_url', array($this, 'set_cmb2_resources_url'));
	}

	public function set_cmb2_resources_url() {
		return plugin_dir_url(dirname(__FILE__)) . 'vendor/cmb2/cmb2/';
	}
}