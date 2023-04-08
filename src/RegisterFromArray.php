<?php

namespace Moon\WpHookRegistry;

class RegisterFromArray
{
	private $hookRegistry;

	public function __construct(HookRegistry $hookRegistry, array $hooks)
	{
		$this->hookRegistry = $hookRegistry;

		foreach ($hooks as $hookName => $defs) {
			foreach ($defs as $def) {
				$type = $this->getDefType($def);
				if ($type === 'function') {
					$this->addWithFunction($hookName, $def);
				} else {
					$this->addWithMethod($hookName, $def);
				}
			}
		}
	}

	private function getDefType(array $def)
	{
		// ['print_r']
		if (count($def) === 1) {
			return 'function';
		}

		// function def. cannot have the second arg as a string
		if (is_string($def[1])) {
			return 'method';
		}

		return 'function';
	}

	private function addWithFunction($hookName, array $def)
	{
		$this->hookRegistry->addWithFunction(
			$hookName,
			$def[0],
			$def[1] ?? 10,
			$def[2] ?? null
		);
	}

	private function addWithMethod($hookName, array $def)
	{
		$this->hookRegistry->addWithMethod(
			$hookName,
			$def[0],
			$def[1],
			$def[2] ?? 10,
			$def[3] ?? null,
			$def[4] ?? null
		);
	}
}
