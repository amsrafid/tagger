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
		
		if($object) {
			foreach($object as $obj) {
				Tag::{$tag}($attributes);
			}
		}

		return null;
	}

	/**
	* For statement
	*/
	public static function c_for() {
		echo 'for';
	}

	/**
	* If statement
	*/
	public static function c_if() {
		echo 'if';
	}

	/**
	* Else if statement
	*/
	public static function c_elseif() {
		echo 'else_if';
	}

	/**
	* Else statement
	*/
	public static function c_else() {
		echo 'else';
	}
}
