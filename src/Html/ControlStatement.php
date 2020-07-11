<?php

namespace Html;

/**
* Class Handles Control statement
*/
class ControlStatement implements ControlStatementBinding
{
	/**
	 * Control Statement states
	 * 
	 * @var array
	 */
	private static $states = ['foreach', 'for', 'if', 'elseif', 'else'];

	/**
	* Check the attribute is a control statement or not
	* 
	* @param string $statement  attribute name
	* @return bool
	*/
	public static function match($statement)
	{
		$statement = (object) (\is_array($statement) ? $statement : [$statement]);

		$set = [];
		if($statement) {
			foreach ($statement as $key => $state) {
				if(\in_array($state, self::$states))
					$set [] = $state;
			}
		}

		return $set;
	}

	/**
	* Handle Control statment
	*/
	public static function handle($tag, $attributes, $set)
	{
		$first = 'c_'.current($set);
			unset($set[0]);

		return self::{$first}($tag, $attributes, $set);
	}

	/**
	* For statement
	*/
	public static function c_foreach($tag, $attributes, $set)
	{
		$object = $attributes['foreach'];
		unset($attributes['foreach']);

		$offset = $start = 0;
		
		if(isset($attributes['offset'])) {
			$offset = $attributes['offset'];
			unset($attributes['offset']);
			if(isset($attributes['start'])) {
				$start = $attributes['start'];
				unset($attributes['start']);
			}
		}
		
		if($object) {
			foreach($object as $key => $obj) {
				Tag::{$tag}(self::attributeValueAssign($obj, $attributes, $key, $offset, $start));
			}
		}

		return '';
	}

	private static function attributeValueAssign($ctx, $attributes, $key, $offset, $start)
	{
		if($attributes) {
			$attr = $attributes;

			foreach(array_keys($attributes) as $attribute) {
				preg_match_all('/[\@]\w+/', $attr[$attribute], $matches);

				if($matches && current($matches)) {
					if($offset) {
						$ctx[$offset] = $key + $start;
					}

					foreach ($matches[0] as $value) {
						$value = preg_replace('/\@/', '', $value);
						if(isset($ctx[$value]))
							$attr[$attribute] = str_replace('@'.$value, $ctx[$value], $attr[$attribute]);
					}
				}
			}

			return $attr;
		}

		return $attributes;
	}

	/**
	* For statement
	*/
	public static function c_for($tag, $attributes, $set) {
		echo 'for';
	}

	/**
	* If statement
	*/
	public static function c_if($tag, $attributes, $set) {
		$object = $attributes['if'];
		unset($attributes['if']);
		
		if($object) {
			Tag::{$tag}($attributes);
		}

		return '';
	}

	/**
	* Else if statement
	*/
	public static function c_elseif($tag, $attributes, $set) {
		echo 'else_if';
	}

	/**
	* Else statement
	*/
	public static function c_else($tag, $attributes, $set) {
		echo 'else';
	}
}
