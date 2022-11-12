<?php

namespace EstaleiroWeb\Traits;
/**
 * Implement auto getters and setters caller methods\
 * Where get**PropertyName** or set**PropertyName** have first priority\
 * And in second priority we have protected/privated priority\
 * And finaly $readonly/$protect
 * It needs implement 2 properties:
 * ```php
 * // readonly and hide property
 * protected $readonly = ['property1'=>'value','property2'=>'value','propertyN'=>'value',];
 * // read and write property
 * protected $protect = ['property1'=>'value','property2'=>'value','propertyN'=>'value',];
 * ```
 */
trait GetSet {
	/**
	 * Variaveis resgataveis
	 * @example protected $readonly = ['property1'=>'value','property2'=>'value','propertyN'=>'value',]
	 * @var array
	 */
	//protected $readonly = [];
	/**
	 * Variaveis resgataveis
	 * @example protected $protect = ['property1'=>'value','property2'=>'value','propertyN'=>'value',]
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
		if (method_exists($this, $fn = 'get' . $nm)) return $this->$fn();
		if (property_exists($this, $nm)) return $this->$nm;
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
		elseif (
			!key_exists($nm, $this->readonly) &&
			!property_exists($this, $nm)
		) $this->protect[$nm] = $val;
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
