<?php

namespace Html;

/**
 * Condition Handler
 * 
 * @method public  static bool 		 	match($condition)
 */
class Condition
{
	/**
	 * Final condition validation to return a single boolean value
	 * 
	 * @param string	$condition			Condition string	[De Morgan's logical stripe]
	 * @param array 	$bracketSet			Brackets position set
	 * @param int 		$leftPosition		String initial position
	 * @param int 		$rightPosition	String final position
	 * @return bool
	 */
	private static function conditionValidate($condition, $bracketSet, $leftPosition, $rightPosition)
	{
		$bracketOut = [];
		while($leftPosition < $rightPosition) {
			if($condition[$leftPosition] != '('){
				if($condition[$leftPosition] == ')') {
					throw new \Exception("Bracket pattern is mismatched.");
				}

				$bracketOut [] = $condition[$leftPosition];
				$leftPosition++;
			} else{
				if(isset($bracketSet[$leftPosition])) {
					$bracketOut [] = self::conditionValidate(
						$condition,
						$bracketSet,
						$leftPosition + 1,
						$bracketSet[$leftPosition]
					);
				}
				else {
					throw new \Exception("Bracket pattern is mismatched.");
				}

				$leftPosition = $bracketSet[$leftPosition] + 1;
			}
		}

		return self::replaceConditionals(implode('', $bracketOut));
	}

	/**
	 * Match condition from string
	 * 
	 * @param string $condition
	 * @return boolean
	 */
	public static function match ($condition)
	{
		$simplified = self::simplifyCondition($condition);
		
		$first = 0;
		$stack = [];
		$bracketPositions = [];

		for($i = 0; $i < strlen($simplified); $i++) {
			if($simplified[$i] == "(") {
				$stack [] = $i;
			} else if ($simplified[$i] == ")") {
					$bracketPositions[end($stack)] = $i;
					array_pop($stack);
			}
		}

		return self::conditionValidate($simplified, $bracketPositions, 0, strlen($simplified));

			/*
				preg_match('/.*(.*?)\s(.*?)\s(.*?)/sU', $condition, $condition_matches);

				$cond_left = self::trimSpecialCharFromMatch($condition_matches, 1);
				$cond_right = self::trimSpecialCharFromMatch($condition_matches, 3);

				$operator = $condition_matches[2];

				return self::overloadOperator($cond_left, $operator, $cond_right);
			*/
	}

	/**
	 * Check operator and pass conditional operation
	 * 
	 * @param string $cond_left 	Left value
	 * @param string $operator 		Conditional operator
	 * @param string $cond_right 	Right value
	 * @return boolean
	 */
	private static function overloadOperator($cond_left, $operator, $cond_right)
	{
		switch($operator){
			case '<':
				return ($cond_left < $cond_right);
				break;
			case '<=':
				return ($cond_left <= $cond_right);
				break;
			case '>':
				return ($cond_left > $cond_right);
				break;
			case '>=':
				return ($cond_left >= $cond_right);
				break;
			case '==':
				return ($cond_left == $cond_right);
				break;	        
			case '===':
				return ($cond_left === $cond_right);
				break;
			case '!=':
				return ($cond_left != $cond_right);
				break;
			case '!==':
				return ($cond_left !== $cond_right);
				break;
			case '<>':
				return ($cond_left <> $cond_right);
				break;
			default:
				throw new \Exception("'{$operator}' is not a valid conditional operator");
				break;
	  }
	}

	/**
	 * Simplify and replace AND|OR condition to single Boolean value
	 * 
	 * @param string $string	De Morgan's stripe
	 * @return boolean
	 */
	private static function replaceConditionals($string)
	{
		$planeSet = [];
		for($i = 0; $i < \strlen($string); $i++) {
			if($string[$i] == '&') {
				\array_pop($planeSet);
				$planeSet [] = (($string[$i-1] + $string[$i+1]) == 2) ? 1 : 0;
				$i++;
			} else if($string[$i] != '|') {
				$planeSet [] = $string[$i];
			}
		}

		return \in_array(1, $planeSet) ? 1 : 0;
	}

	/**
	 * Simplify condition to a single De Morgan's logical stripe
	 * 
	 * @param string $condition		Condition string
	 * @return string
	 */
	private static function simplifyCondition($condition)
	{
		$simplified = [];
		$position = 0;
		$perCondition = ['', '', ''];
		$firstPosition = 0;
		$quote = true;

		for ($i = 0; $i < strlen($condition); $i++) {
			if(\in_array($condition[$i], ['"', "'"]))
				$quote = $quote ? false : true;

			if(preg_match('/[a-zA-Z0-9_!=<>]/', $condition[$i])) {
				$perCondition[$position] .= $condition[$i];
				$firstPosition = $perCondition[0] ? $firstPosition : $i;
			} else if ($quote &&
				$perCondition[0] != "" &&
				(($condition[$i] == ' ') ||
					(\in_array($condition[$i], [' ', ')']) && $position == 2))
			) {
				$position++;
			}

			if($position > 2 || ( ($i == strlen($condition) - 1) &&
				$perCondition[0] != "" && $perCondition[1] != "" && $perCondition[2] != "")
			) {
				$simplified [] = self::overloadOperator(
					$perCondition[0],
					$perCondition[1],
					$perCondition[2]
				) ? 1 : 0;

				$perCondition = ['', '', ''];
				$position = 0;
				$firstPosition = 0;
			}

			if (in_array($condition[$i], ['|', '&'])) {
				$simplified [] = $condition[$i];
				$i++;
			} else if (in_array($condition[$i], ['(', ')'])) {
				$simplified [] = $condition[$i];
			}
		}
		
		if($simplified)
			return \implode('', $simplified);

		throw new \Exception("Condition pattern '{$condition}' is not valid.");
	}
}
