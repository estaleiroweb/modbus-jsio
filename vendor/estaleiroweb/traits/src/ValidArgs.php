<?php

namespace EstaleiroWeb\Traits;

trait ValidArgs {
	/**
	 * Check variable and if ok add to this object
	 *
	 * @param array $arr Associative array [<argument>=><null|filter_var_array definition|callback function>]
	 */
	public function validArgs(array $data, array $definition) {
		$out = filter_var_array($data, $definition);
		foreach ($out as $k => $v) if ($v !== false || $v === $data[$k]) {
			$this->$k = $v;
		}
		return $this;
	}
}
