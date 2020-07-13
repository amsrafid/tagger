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
	 * 
	 * @param string 	$tag 		Tag name
	 * @param array 	$attributes Tag Attributes
	 * @param array 	$set 		Next conditional statement
	 * @return tag view
	 */
	public static function handle($tag, $attributes, $set)
	{
		$first = 'c_'.current($set);
			unset($set[0]);

		return self::{$first}($tag, $attributes, $set);
	}

	/**
	 * Conditional foreach statement
	 * 
	 * @param string 	$tag 		Tag name
	 * @param array 	$attributes Tag Attributes
	 * @param array 	$set 		Next conditional statement
	 * @return tag view
	 */
	public static function c_foreach($tag, $attributes, $set)
	{
		$object = $attributes['foreach'];
		unset($attributes['foreach']);

		$offset = $start = 0;
		$condition = true;

		if(isset($attributes['offset'])) {
			$offset = $attributes['offset'];
			unset($attributes['offset']);
			if(isset($attributes['start'])) {
				$start = $attributes['start'];
				unset($attributes['start']);
			}
		}
		if (isset($attributes['if'])) {
			$condition = $attributes['if'];
		}
		
		if($object) {
			foreach($object as $key => $obj) {
				if(self::checkConditionals($obj, $condition, $key,  $offset, $start)){
					Tag::{$tag}(self::attributeValueAssign($obj, $attributes, $key, $offset, $start));
				}
			}
		}

		return '';
	}

	/**
	 * Change matching token to value
	 * 
	 * @param object 		$ctx 		Set context
	 * @param string|object $condition  Loop nested condition string OR object
	 * @param string 		$key 		Object real offset
	 * @param string 		$offset  	Offset variable name
	 * @param int    		$start 		Offset started from
	 * @return void
	 */
	private static function checkConditionals($ctx, $condition, $key, $offset, $start)
	{
		if (gettype($condition) == 'string') {
			preg_match_all('/[\@]\w+/', $condition, $matches);
			self::changeMatchingToken($ctx, $condition, $matches, $key, $offset, $start);

			return Condition::match($condition);
		}

		return $condition;
	}

	/**
	 * Assign attributes value by key
	 * 
	 * @param object 	$ctx 		Set context
	 * @param array 	$attributes Attributes set
	 * @param string 	$key 		Object real offset
	 * @param string 	$offset  	Offset variable name
	 * @param int    	$start 		Offset started from
	 * @return void
	 */
	private static function attributeValueAssign($ctx, $attributes, $key, $offset, $start)
	{
		if($attributes) {
			$attr = $attributes;

			foreach(array_keys($attributes) as $attribute) {
				preg_match_all('/[\@]\w+/', $attr[$attribute], $matches);

				self::changeMatchingToken($ctx, $attr[$attribute], $matches, $key, $offset, $start);
			}

			return $attr;
		}

		return $attributes;
	}

	/**
	 * Change matching token to value
	 * 
	 * @param object 	$ctx 		Set context
	 * @param string 	&$tokenize	Pointer for tokenized string
	 * @param array  	$matches 	Total matching tokens
	 * @param string 	$key 		Object real offset
	 * @param string 	$offset  	Offset variable name
	 * @param int    	$start 		Offset started from
	 * @return void
	 */
	private static function changeMatchingToken($ctx, &$tokenize, $matches, $key, $offset, $start)
	{
		if($matches && current($matches)) {
			if($offset) {
				$ctx[$offset] = abs($key + $start);
			}

			foreach ($matches[0] as $value) {
				$value = self::hasToken($value);

				if(isset($ctx[$value])) {
					$tokenize = self::changeTokenToValue($value, $ctx[$value], $tokenize);
				}
			}
		}
	}

	/**
	 * Replace token with actual value
	 * 
	 * @param string $token 	 	Token name without @
	 * @param string $replace 	Token replaced with
	 * @param string $value    Token replaced from
	 * @return string
	 */
	private static function changeTokenToValue($token, $replace, $value)
	{
		return preg_replace('/(\@'.$token.')/', $replace, $value);
	}

	/**
	 * Check value contains a key token
	 * @token format exists or not
	 * 
	 * @param string  $value 		String that should contains token
	 * @return bool
	 */
	private static function hasToken($value)
	{
		return preg_replace('/\@/', '', $value);
	}

	/**
	 * Conditional if statement
	 * 
	 * @param string 	$tag 		Tag name
	 * @param array 	$attributes Tag Attributes
	 * @param array 	$set 		Next conditional statement
	 * @return tag view
	 */
	public static function c_if ($tag, $attributes, $set)
	{
		$object = $attributes['if'];
		unset($attributes['if']);
		
		if($object) {
			Tag::{$tag}($attributes);
		}

		return '';
	}

	/**
	 * Coditional else if statement
	 * 
	 * @param string 	$tag 		Tag name
	 * @param array 	$attributes Tag Attributes
	 * @param array 	$set 		Next conditional statement
	 * @return tag view
	 */
	public static function c_elseif($tag, $attributes, $set) {
		echo 'else_if';
	}

	/**
	 * Coditional else statement
	 * 
	 * @param string 	$tag 		Tag name
	 * @param array 	$attributes Tag Attributes
	 * @param array 	$set 		Next conditional statement
	 * @return tag view
	 */
	public static function c_else($tag, $attributes, $set) {
		echo 'else';
	}
}
