<?php

namespace Moon\WpHookRegistry;

use Psr\Container\ContainerInterface;

/**
 * Central place to register WP hooks.
 *
 * - Support registering a private method as a hook callback.
 * - Support setting an I.D to a hook that can be used to remove the callback at a later time.
 */
class HookRegistry {
	/**
	 * PSR Container.
	 *
	 * @var ContainerInterface Container interface
	 */
	private $container;

	/**
	 * We'll save hook callbacks here with the following array key pattern to make it removeable later.
	 *
	 * - :hookName-func-:funcName-:priority
	 * - :hookName-method-:className-methodName-:priority
	 *
	 * @var array $callbacks Callbacks
	 */
	private $callbacks;

	/**
	 * Construct.
	 *
	 * @param ContainerInterface $container system container.
	 */
	public function __construct(ContainerInterface $container )
	{
		$this->container = $container;
	}

	public function addWithFunction($hookName, $funcName, $priority = 10, ?string $id = null)
	{
		$id = $id ?? "$hookName-func-$funcName-$priority";
		$this->callbacks[$id] = [
			'callback' => function() use ($funcName) {
				call_user_func_array($funcName, func_get_args());
			},
			'priority' => $priority,
			'hookName' => $hookName,
			'funcName' => $funcName,
			'type' => 'function'
		];

		return add_action($hookName, $this->callbacks[$id]['callback'], $priority);
	}

	public function addWithMethod($hookName, $className, $methodName, int $priority = 10, ?string $id = null)
	{
		$id = $id ?? "$hookName-method-$className-$methodName-$priority";
		$this->callbacks[$id] = [
			'callback' => function() use($className, $methodName) {
				$class_instance = $this->container->get( $className );

				$args = func_get_args();
				$closure = function() use ($methodName, $args) {
					call_user_func_array([$this, $methodName], $args);
				};

				$closure->call($class_instance);
			},
			'priority' => $priority,
			'hookName' => $hookName,
			'className' => $className,
			'methodName' => $methodName,
			'type' => 'method'
		];

		return add_action($hookName, $this->callbacks[$id]['callback'], $priority);
	}

	public function removeHookWithMethod($hookName, $classname, $methodName, int $priority = 10): bool
	{
		$id =  "$hookName-method-$classname-$methodName-$priority";
		return array_key_exists($id, $this->callbacks) && remove_filter($hookName, $this->callbacks[$id]['callback'],
				$priority);
	}


	public function removeHookWithFunction(string $hookName, string $functionName, int $priority = 10)
	{
		$id = "$hookName-func-$functionName-$priority";
		return array_key_exists($id, $this->callbacks) && remove_filter($hookName, $this->callbacks[$id]['callback'],
				$priority);
	}

	public function removeHookById($id)
	{
		if (!isset($this->callbacks[$id])) {
			return false;
		}

		$data = $this->callbacks[$id];
		return remove_filter($data['hookName'], $data['callback'], $data['priority']);
	}
}
