<?php

namespace Moon\WpHookRegistryTests;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class TestContainer implements ContainerInterface
{
	public function get(string $id)
	{
		return new $id;
	}

	public function has(string $id): bool
	{
		// TODO: Implement has() method.
	}
}
