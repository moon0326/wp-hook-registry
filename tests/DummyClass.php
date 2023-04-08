<?php

namespace Moon\WpHookRegistryTests;

class DummyClass
{
	public function publicMethod($string)
	{
		echo $string;
	}

	private function privateMethod($string)
	{
		echo $string;
	}
}
