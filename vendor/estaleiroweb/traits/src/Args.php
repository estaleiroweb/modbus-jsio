<?php

namespace EstaleiroWeb\Traits;

trait Args {
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
	/**
	 * initArgs
	 * Initialize arguments of the object with backtrace of the back method like PHP8
	 * #### Implement
	 * ```php
	 * class X {use Args; function method(){$this->initArgs(DEFINITIONS1);}}
	 * #constants
	 * define('DEF_OPTIONS1',['default' => 3,"min_range"=>1,"max_range"=>120]);
	 * define('DEF_OPTIONS2',['regexp'=>"/t(.*)/"]);
	 * define('DEF_FILTER_VAR1',FILTER_VALIDATE_EMAIL);
	 * define('DEF_FILTER_VAR2',["filter"=>FILTER_CALLBACK,"flags"=>FILTER_FORCE_ARRAY,"options"=>"ucwords"]);
	 * define('DEF_FILTER_VAR3',["filter"=>FILTER_VALIDATE_INT,"options"=>DEF_OPTIONS1]);
	 * define('DEFINITIONS1',['arg1','arg2']);
	 * define('DEFINITIONS2',['arg1'=>null,'arg2'=>false,'arg3'=>DEF_FILTER_VAR1]);
	 * #instance
	 * $o=new X;
	 * $o->method(1,2,3);
	 * $o->method(['arg1'=>1,'arg2'=>2','arg3'=>3]);
	 * ```
	 * See URLs:
	 * - https://www.php.net/manual/en/function.filter-var-array.php
	 * - https://www.php.net/manual/en/filter.filters.validate.php
	 * - https://www.php.net/manual/en/filter.filters.sanitize.php
	 * - https://www.php.net/manual/en/filter.filters.misc.php
	 * - https://www.php.net/manual/en/filter.filters.flags.php
	 * - https://www.php.net/manual/en/filter.constants.php
	 * 
	 * @param  mixed $args List of arguments of the object or definitions of filter_var_array
	 * @return object self object
	 */
	function initArgs(array $definitions = []) {
		$bt = debug_backtrace();
		$args = @$bt[1]['args'];
		if (!$args || !$definitions) return $this;
		if (array_is_list($definitions)) {
			foreach ($definitions as $k => $v) {
				if (key_exists($k, $args)) $this->$v = $args[$k];
				else break;
			}
		} else {
			$idx = 0;
			$arr = [];
			foreach ($definitions as $arg => &$def) {
				if (is_null($def) || $def === false) {
					if (!key_exists($idx, $args)) {
						unset($definitions[$arg]);
						continue;
					}
				}
				$arr[$arg] = @$args[$idx++];
			}
			unset($def);

			$out = filter_var_array($arr, $definitions);
			foreach ($out as $k => $v) {
				if ($v !== false || $v === $arr[$k]) $this->$k = $v;
			}
		}
		return $this;
	}
}
