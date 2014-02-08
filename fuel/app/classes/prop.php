<?php

class Prop implements \Iterator, \ArrayAccess
{
	private $data;

	public static function forge(array $data)
	{
		return new Prop($data);
	}

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	private function get_($name)
	{
		if (!array_key_exists($name, $this->data))
		{
			throw new Exception(sprintf('Undefined index: %s', $name));
		}
		return
			is_array($this->data[$name])
				? new Prop($this->data[$name])
				: $this->data[$name];
	}

	public function __get($name)
	{
		if ('_'.(int)ltrim($name, '_') == $name)
		{
			$name = (int)ltrim($name, '_');
		}
		return $this->get_($name);
	}

	function rewind()
	{
		reset($this->data);
	}

	function current()
	{
		$val = current($this->data);
		return
			is_array($val)
				? new Prop($val)
				: $val;
	}

	function key()
	{
		return key($this->data);
	}
	
	function next()
	{
		next($this->data);
	}
	
	function valid()
	{
		$key = key($this->data);
		return ($key !== NULL && $key !== FALSE);
	}

	public function offsetSet($offset, $value)
	{
		// 何も実装しない
	}

	public function offsetUnset($offset)
	{
		// 何も実装しない
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->data);
	}

	public function offsetGet($offset)
	{
		return $this->get_($offset);
	}
}
