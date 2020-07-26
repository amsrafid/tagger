<?php

namespace Html;

trait Attribute
{
	use Credentials;

	/**
	 * Check attributes existing or format
	 * 
	 * @param string  $tag  Tag name
	 * @param string 		All formated attributes
	 */
	private static function attributes($tag)
	{
		$attrString = "";
		$attributes = self::bindedAttributes($tag);

		if($attributes) {
			foreach($attributes as $key => $attr) {
				$attrString .= self::attributeFormat($tag, $key, $attr);
			}
		}
		
		return $attrString;
	}

	/**
	 * Change key and value to attribute format
	 * 
	 * @param string		$tag		Tag name
	 * @param string		$key		Attribute name or sudo name
	 * @param string		$attr		Attribute Value
	 * @return string
	 */
	private static function attributeFormat($tag, $key, $attr)
	{
		$attributes = "";
		$key = self::getAttributeMainName($key);

		if(self::monitor($key)) {
			$key = preg_replace(['/^(d-)/', '/^.*\_/', '/^.*\*/'], 'data-', $key);
			$attributes .= ' ' . $key . (
				(gettype($attr) == 'boolean' && $attr)
					? ''
					: ' = "' . $attr .'"'
			);
		}
		
		return $attributes;
	}

	/**
	 * Assign attributes value by key
	 * 
	 * @param object 	$ctx 				Set context
	 * @param array 	$attributes Attributes set
	 * @param string 	$key 				Object real offset
	 * @param string 	$offset  		Offset variable name
	 * @param int    	$start 			Offset started from
	 * @return void
	 */
	private static function attributeValueAssign($ctx, $attributes, $key, $offset)
	{
		if($attributes) {
			$attr = $attributes;

			foreach(array_keys($attributes) as $attribute) {
				if(gettype($attr[$attribute]) == 'array') {
					foreach($attr[$attribute] as $tag => $body) {
						foreach($body as $i => $text)
							self::distributeToTokenized($ctx, $attr[$attribute][$tag][$i], $key, $offset);
					}
				} else
					self::distributeToTokenized($ctx, $attr[$attribute], $key, $offset);
			}

			return $attr;
		}

		return $attributes;
	}

	/**
	 * Bind preset attributes with main attribute
	 * 
	 * @param string $tag 	Tag name
	 * @return array
	 */
	private static function bindedAttributes($tag)
	{
		$attributes = self::$attributes;

		if(! empty(self::$preset[$tag])) {
			foreach(self::$preset[$tag] as $key => $preset) {
				$sudo = self::getAttributeMainName($key);

				if(isset($attributes[$key]))
					$attributes[$key] .= " " . $preset;
				else if (isset($attributes[$sudo]))
					$attributes[$sudo] .= " " . $preset;
				else
					$attributes[$key] = $preset;
			}
		}

		return $attributes;
	}

	/**
	 * Change matching token to value
	 * 
	 * @param object 	$ctx 				Set context
	 * @param string 	&$tokenize	Pointer for tokenized string
	 * @param array  	$matches 		Total matching tokens
	 * @param string 	$key 				Object real offset
	 * @param string 	$offset  		Offset variable name
	 * @param boolean $mode 			Check for condition or replace.
	 * @return void
	 */
	private static function changeMatchingToken($ctx, &$tokenize, $matches, $key, $offset, $mode = false)
	{
		if($matches && current($matches)) {
			if($offset) {
				if(\is_object($ctx)) {
					$ctx->{$offset} = abs($key);
				} else {
					$ctx[$offset] = abs($key);
				}
			}

			foreach ($matches[0] as $value) {
				$value = self::hasToken($value);

				if((\is_array($ctx) && isset($ctx[$value])) || (\is_object($ctx) && isset($ctx->{$value}))) {
					if(\is_object($ctx)) {
						$needle = $ctx->{$value};
					} else {
						$needle = $ctx[$value];
					}

					$tokenize = self::changeTokenToValue($value, $needle, $tokenize, $mode);
				}
			}
		}
	}

	/**
	 * Replace token with actual value
	 * 
	 * @param string 	$token 	 	Token name without @
	 * @param string 	$replace 	Token replaced with
	 * @param string 	$value    Token replaced from
	 * @param boolean $mode 		Check for condition or replace. True means condition
	 * @return string
	 */
	private static function changeTokenToValue($token, $replace, $value, $mode = false)
	{
		if($mode) {
			$replace = "'$replace'";
		}

		return preg_replace('/(\@'.$token.')/', $replace, $value);
	}

	/**
	 * Destroy key from given array
	 * 
	 * @param array		&$attributes		Attribute set
	 * @param string	$key					Array key
	 * @return null
	 */
	private static function destroyKey(&$attributes, $key)
	{
		unset($attributes[$key]);
	}

	/**
	 * Discover body text and replace sudo attribute name to real
	 * 
	 * @param array	$attributes real tag attributs
	 * @return array
	 */
	private static function discoverBody(&$attributes)
	{
		if(empty($attributes['body'])) {
			foreach(self::$attrs['tag_body']['set'] as $set) {
				if(isset($attributes[$set])) {
					$body = $attributes[$set];
					self::destroyKey($attributes, $set);

					$attributes['body'] = $body;
					return $attributes;
				}
			}

			$attributes['body'] = false;
		}
		
		return $attributes;
	}

	/**
	 * Pass to change by mach with token
	 * 
	 * @param object 	$ctx 				Set context
	 * @param string 	&$tokenize	Pointer for tokenized string
	 * @param string 	$key 				Object real offset
	 * @param string 	$offset  		Offset variable name
	 * @param int    	$start 			Offset started from
	 * @return void
	 */
	private static function distributeToTokenized($ctx, &$tokenize, $key, $offset)
	{
		preg_match_all('/[\@]\w+/', $tokenize, $matches);
		self::changeMatchingToken($ctx, $tokenize, $matches, $key, $offset);
	}

	/**
	 * Format then attributes value.
	 * Change a string to attribute and merge with real attribute set
	 * 
	 * @param array					$attributes		Attribute set
	 * @param array|string	$then					Then attribute value
	 * @return array
	 */
	private static function formatThenAttribute($attributes, $then)
	{
		if(\is_array($then)) {
			return array_merge($attributes, $then);
		}
		else if(\is_string($then)) {
			$matched = true;
			$sets = \preg_split('/[.,;\s+]/', trim(strtolower($then)));

			if(! empty($sets)) {
				$thenArray = [];

				foreach($sets as $set) {
					$set = preg_replace('/\s+/', '', $set);
					if($set) {
						if(preg_match('/\w+[\-]*(\w+)*/', $set)) {
								$thenArray[$set] = true;
						}
						else {
							$matched = false;
							break;
						}
					}
				}

				if(! empty($thenArray) && $matched) {
					return array_merge($attributes, $thenArray);
				}
			}
		}
		
		throw new \Exception("Attribute 'then', expects attribute set or single attributes as string.");
	}

	/**
	 * Check key has sudo or not
	 * 
	 * @param string $key 	Attribute sudo name
	 * @return string
	 */
	private static function getAttributeMainName($key)
	{
		return isset(self::$sudo[$key]) ? self::$sudo[$key] : $key;
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
	 * Monitoring that the attribute is already used
	 * 
	 * @param string $key attribute name
	 * @return bool
	 */
	private static function monitor($key)
	{
		$key = self::getAttributeMainName($key);
		
		if(! \in_array($key, self::$appends)) {
			self::$appends [] = $key;

			return true;
		}

		return false;
	}
}
