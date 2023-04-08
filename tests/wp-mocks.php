<?php

// functions that are used to simulate WP's behavior;

$wp_mocks_hooks = [];

if (!function_exists('add_action')) {
	function add_action(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1) {
		global $wp_mocks_hooks;
		if (!isset($wp_mocks_hooks[$hook_name])) {
			$wp_mocks_hooks[$hook_name] = [];
		}

		$wp_mocks_hooks[$hook_name][$priority] = $callback;
	}
}

if (!function_exists('add_filter')) {
	function add_filter(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1) {
		add_action($hook_name, $callback, $priority, $accepted_args);
	}
}

if (!function_exists('do_action')) {
	function do_action(string $hook_name, $arg) {
		global $wp_mocks_hooks;
		if (isset($wp_mocks_hooks[$hook_name])) {
			ksort($wp_mocks_hooks[$hook_name]);
			if (!is_array($arg)) {
				$arg = [$arg];
			}
			foreach ($wp_mocks_hooks[$hook_name] as $callback) {
				call_user_func_array($callback, $arg);
			}
		}
	}
}


if (!function_exists('remove_filter')) {
	function remove_filter(string $hook_name, callable $callback, int $priority = 10) {
		global $wp_mocks_hooks;
		if (isset($wp_mocks_hooks[$hook_name][$priority])) {
			unset($wp_mocks_hooks[$hook_name][$priority]);
			return true;
		}
		return false;
	}
}

