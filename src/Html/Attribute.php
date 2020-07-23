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
	 * @param int    	$start 			Offset started from
	 * @return void
	 */
	private static function changeMatchingToken($ctx, &$tokenize, $matches, $key, $offset)
	{
		if($matches && current($matches)) {
			if($offset) {
				$ctx[$offset] = abs($key/*  + $start */);
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
					unset($attributes[$set]);

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
