<?php

namespace Html;

/**
* Class Handles Control statement
*/
class ControlStatement implements ControlStatementBinding
{
	use Attribute;

	/**
	 * Control Statement states
	 * 
	 * @var array
	 */
	private static $states = ['foreach', 'if', 'elseif', 'else'];

	/**
	 * Monitor Control statement if
	 * 
	 * @var bool
	 */
	private static $monitorIf = true;

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
		$attributes = self::discoverBody($attributes);
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

		$count = $offset = $start = 0;
		$condition = true;
		$then = [];

		if(isset($attributes['offset'])) {
			$offset = $attributes['offset'];
			unset($attributes['offset']);
			if(isset($attributes['start'])) {
				$start = $attributes['start'];
				unset($attributes['start']);
				$count = $start - 1;
			}
		}
		if (isset($attributes['if'])) {
			$condition = $attributes['if'];
			unset($attributes['if']);
		}
		if (isset($attributes['then'])) {
			$then = $attributes['then'];
			unset($attributes['then']);
		}
		
		if($object) {
			foreach($object as $key => $obj) {
				if($then) {
					$attrThen = $attributes;
					if(self::checkConditionals($obj, $condition, $key,  $offset)){
						$attrThen = array_merge($attributes, $then);
					}
					$count++;
					Tag::{$tag}(self::attributeValueAssign($obj, $attrThen, $count, $offset));
				} else {
					if(self::checkConditionals($obj, $condition, $key,  $offset)){
						$count++;
						Tag::{$tag}(self::attributeValueAssign($obj, $attributes, $count, $offset));
					}
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
	private static function checkConditionals($ctx, $condition, $key, $offset)
	{
		if (gettype($condition) == 'string') {
			preg_match_all('/[\@]\w+/', $condition, $matches);
			self::changeMatchingToken($ctx, $condition, $matches, $key, $offset, true);

			return Condition::match($condition);
		}

		return $condition;
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
		self::$monitorIf = true;
		
		if($object) {
			return Tag::{$tag}($attributes);
		} else {
			self::$monitorIf = false;
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
		$object = $attributes['elseif'];
		unset($attributes['elseif']);
		
		if(! self::$monitorIf && $object) {
			self::$monitorIf = true;
			return Tag::{$tag}($attributes);
		}

		return '';
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
		$object = $attributes['else'];
		unset($attributes['else']);
		
		if(! self::$monitorIf && $object != false) {
			self::$monitorIf = true;
			return Tag::{$tag}($attributes);
		}
		
		self::$monitorIf = true;
		return '';
	}
}
