# EstaleiroWeb\Traits
PHP Traits used in others projects of EstaleiroWeb

## Traits Decription

### Trait Debug
Implements the method who prints all trace from the position where was called. 
```php
class MyClass {
	use EstaleiroWeb\Traits\Debug;
	public function __construct(){
		$this->fnA();
	}
	public function fnA(){
		$this->fnB();
	}
	public function fnB(){
		$this->debug();
	}
}
new MyClass;
```

### Trait GetterAndSetter
Automates the overload of Getters and Setters methods.
```php 
class MyClass {
	use EstaleiroWeb\Traits\GetterAndSetter;
	private $xValue; 
	public function __construct($value=1){
		$this->readonly=[
			'v1'=>$value,
			'v2'=>$value,
		];
		$this->protect=[
			'v3'=>$value,
		];
		$this->v4=$value;
	}
	public function getV4(){
		$this->xValue;
	}
	public function setV2($value){
		$this->xValue=$value * 3;
	}
	public function setV4($value){
		$this->xValue=$value * 3;
	}
}
$o=new MyClass;
print "{$o->v1},{$o->v2},{$o->v3},{$o->v4}\n";
// 1,1,1,3
$o->v1=2;
$o->v2=2;
$o->v3=2;
$o->v4=2;
print "{$o->v1},{$o->v2},{$o->v3},{$o->v4}\n";
// 1,6,2,6
```

### Trait GetterAndSetterRO
Idem GetterAndSetter, but implements only readonly methods.

### Trait LoadParameters
Easily load parameters in order.

### Trait NetTools
Implements some methods to tools network
- nmap: Check if a host's port is free
- getFreeRandomPort: Dicovery a free port to use.
- checkHost: Check is exists host
- goURL: Redirect to another URL
- hostname: Get hostname
	
### Trait Options
In implementation

### Trait OverLoadElements
In implementation

### Trait SessionConfig
In implementation

### Trait Singleton
Implements a singleton class
```php
class MyClass {
	use EstaleiroWeb\Traits\Singleton;
	public $v;
	protected function __construct($v){
		$this->v=$v;
	}
}
$a=new MyClass(1);
// ERROR
$b=MyClass::singleton(1);
$c=MyClass::singleton(2);
print "{$b->v},{$c->v}";
// 1,1
$b-v=3;
print "{$b->v},{$c->v}";
// 3,3
```

### Trait SingletonClass
Same as Singleton, but creates a new instance for each different class it called.

### Trait SingletonKey
Same as Singleton, but creates a new instance for each different key it passed.
