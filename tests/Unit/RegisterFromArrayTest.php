<?php

namespace Unit;

use Moon\WpHookRegistry\HookRegistry;
use Moon\WpHookRegistry\RegisterFromArray;
use Moon\WpHookRegistryTests\DummyClass;
use Moon\WpHookRegistryTests\TestCase;
use Moon\WpHookRegistryTests\TestContainer;

class RegisterFromArrayTest extends TestCase
{
	private $arrayConfig = [
		'init' => [
			['print_r', 9],
			[DummyClass::class, 'publicMethod', 10],
		]
	];

	public function test()
	{
		global $wp_mocks_hooks;
		$wp_mocks_hooks = [];
		$hookRegistry = new HookRegistry(new TestContainer());
	    new RegisterFromArray($hookRegistry, $this->arrayConfig);

		$this->assertArrayHasKey('init', $wp_mocks_hooks);
		$this->assertArrayHasKey(9, $wp_mocks_hooks['init']);
		$this->assertArrayHasKey(10, $wp_mocks_hooks['init']);
		$this->assertCount(2, $wp_mocks_hooks['init']);

		ob_start();
		do_action('init', 'test');
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('testtest', $output);
	}
}
