<?php

namespace Html;

trait Attribute
{
	/**
	* Convert data to attributes format
	*
	* @param string $attr        Attributes credential
	* @param string $expression  Form to expression or single
	* @return string
	*/
	private static function attrFormat($attr, $expression = true)
	{
		if(isset(self::$attributes[$attr])) {
			if($expression)
				return " {$attr} = '".self::$attributes[$attr]."'";
			
			return self::$attributes[$attr];
		}

		return "";
	}

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
	 * Change key and value to attribute format
	 * 
	 * @param string
	 * @param string
	 * @return string
	 */
	private static function attributeFormat($tag, $key, $attr)
	{
		$attributes = " ";
		$key = self::getAttributeMainName($key);

		if(self::monitor($key)) {
			$key = preg_replace(['/^(d-)/', '/^.*\_/', '/^.*\*/'], 'data-', $key);
			$attributes .= $key .' = "' . $attr .'"';
		}
		
		return $attributes;
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
}
