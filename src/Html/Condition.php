<?php

namespace Html;

/**
 * Condition Handler
 * 
 * @method public  static bool 		 	match($condition)
 * @method private static bool 		 	overloadOperator($cond_left, $operator, $cond_right)
 * @method private static string|number trimSpecialCharFromMatch($match, $offset)
 */
class Condition
{
	/**
	 * Match condition from string
	 * 
	 * @param string $condition
	 * @return bool
	 */
	public static function match ($condition)
	{

	    preg_match('/.*(.*?)\s(.*?)\s(.*?)/sU', $condition, $condition_matches);

	    $cond_left = self::trimSpecialCharFromMatch($condition_matches, 1);
	    $cond_right = self::trimSpecialCharFromMatch($condition_matches, 3);

	    $operator = $condition_matches[2];

	    return self::overloadOperator($cond_left, $operator, $cond_right);
	}

	/**
	 * Check operator and pass conditional operation
	 * 
	 * @param string $cond_left 	Left value
	 * @param string $operator 		Conditional operator
	 * @param string $cond_right 	Right value
	 * @return bool
	 */
	private static function overloadOperator($cond_left, $operator, $cond_right)
	{
		switch($operator){
	        case '<':
	            return($cond_left < $cond_right);
	            break;
	        case '<=':
	            return($cond_left <= $cond_right);
	            break;
	        case '>':
	            return($cond_left > $cond_right);
	            break;
	        case '>=':
	            return($cond_left >= $cond_right);
	            break;
	        case '==':
	            return($cond_left == $cond_right);
	            break;	        
	        case '===':
	            return($cond_left === $cond_right);
	            break;
	        case '!=':
	            return($cond_left != $cond_right);
	            break;
	        case '!==':
	            return($cond_left !== $cond_right);
	            break;
	        case '<>':
	            return($cond_left <> $cond_right);
	            break;
	        default:
	            throw new \Exception("'{$operator}' is not a valid conditional operator");
	            break;
	    }
	}

	/**
	 * Trim a condition by special character
	 * Like $, () etc.
	 * 
	 * @param string $match  	Matching set
	 * @param string $offset 	Matching offset
	 * @return string|number
	 */
	private static function trimSpecialCharFromMatch($match, $offset)
	{
		if (isset($match[1]))
			return trim(str_replace(['$','()'],'', $match[$offset]));

		throw new \Exception("Conditional statement must need three parts with ({left value} {operator} {right value}) format");
	}
}
