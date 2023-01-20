<?php

namespace TMAddons\Elementor\Builder\Conditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Condition_Base {
	public static function get_priority() {
		return 100;
	}

	abstract public function get_label();

	abstract public function get_all_label();

	abstract public function get_name();

	public function check( $args ) {
		return false;
	}
}
