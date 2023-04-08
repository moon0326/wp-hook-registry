<?php

namespace Moon\WpHookRegistryTests\Unit;

use Moon\WpHookRegistry\HookRegistry;
use Moon\WpHookRegistryTests\DummyClass;
use Moon\WpHookRegistryTests\TestCase;
use Moon\WpHookRegistryTests\TestContainer;

class HookRegistryTest extends TestCase
{
	public function setUp(): void
	{
	    parent::setUp();
		$this->hook = new HookRegistry(new TestContainer());
	}

	public function test_addWithFunction()
	{
		$this->hook->addWithFunction("test-action", "print_r");
		ob_start();
		do_action('test-action', 'test');
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('test', $output);
	}

	public function test_addWithMethod()
	{
		$this->hook->addWithMethod("test-method", DummyClass::class, "publicMethod");
		ob_start();
		do_action("test-method", "iAmPublicMethod");
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('iAmPublicMethod', $output);
	}

	public function test_addWithMethod_id()
	{
		$id = 'you-can-assign-an-id';
		$this->hook->addWithMethod("test-method", DummyClass::class, "publicMethod", 10, $id);
		$this->hook->removeHookById($id);
		ob_start();
		do_action("test-method", "iAmPublicMethod");
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEmpty($output);
	}

	public function test_addWithMethod_private()
	{
		$this->hook->addWithMethod("test-method", DummyClass::class, "privateMethod");
		ob_start();
		do_action("test-method", "iAmPrivateMethod");
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('iAmPrivateMethod', $output);
	}

	public function test_removeHookWithFunction()
	{
		$hookName = "test-remove-global-function";
		$this->hook->addWithFunction($hookName, "print_r");
		$this->hook->removeHookWithFunction($hookName, 'print_r');
		ob_start();
		do_action($hookName, 'test');
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('', $output);
	}

	public function test_removeHookWithMethod()
	{
	    $hookName = 'test-remove-method';
		$this->hook->addWithMethod($hookName, DummyClass::class, 'publicMethod');
		$this->hook->removeHookWithMethod($hookName, DummyClass::class, 'publicMethod');
		do_action($hookName, "iShouldNotEcho");
		ob_start();
		$output = ob_get_contents();
		ob_end_clean();
		$this->assertEmpty($output);
	}
}
