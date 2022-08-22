<?php

namespace EstaleiroWeb\Traits;

/**
 * Fork of GetterAndSetter
 */
trait GetSet {
	/**
	 * Variaveis resgataveis
	 * @var array
	 */
	//protected $readonly = [];
	/**
	 * Variaveis resgataveis
	 * @var array
	 */
	//protected $protect = [];
	/**
	 * Retorna o valor da variavel sobrecarregada
	 * Implemente os métodos geteres caso queira um melhor encapsulamento
	 *
	 * @param string $nm Nome da Variavel
	 * @return mixed
	 */
	public function __get($nm) {
		if (key_exists($nm, $this->readonly)) return $this->readonly[$nm];
		if (method_exists($this, $fn = 'get' . $nm)) return $this->$fn();
		if (key_exists($nm, $this->protect)) return $this->protect[$nm];
	}
	/**
	 * Sobrecarrega as variaveis
	 * Implemente os métodos seteres caso queira um melhor encapsulamento
	 *
	 * @param string $nm Nome da Variavel
	 * @param mixed $val Valor
	 */
	public function __set($nm, $val) {
		if (method_exists($this, $fn = 'set' . $nm)) return $this->$fn($val);
		if (!key_exists($nm, $this->readonly)) $this->protect[$nm] = $val;
	}

	/**
	 * Retorna o valor da variavel sobrecarregada readonly
	 *
	 * @return array
	 */
	public function getReadonly() {
		return $this->readonly;
	}
	/**
	 * Retorna o valor da variavel sobrecarregada protected
	 *
	 * @return array
	 */
	public function getProtect() {
		return $this->protect;
	}
}
