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

	private static function attributes()
	{
		$all_attr = "";

		if(self::$attributes) {
			foreach(self::$attributes as $key => $attr) {

				if(\array_key_exists($key, self::$sudo)) {
					$all_attr .= self::attributeFormat(self::$sudo[$key], $attr);
				} else {
					$all_attr .= self::attributeFormat($key, $attr);
				}
			}
		}

		return $all_attr;
	}

	/**
	 * Change key and value to attribute format
	 * 
	 * @param string
	 * @param string
	 * @return string
	 */
	private static function attributeFormat($key, $attr)
	{
		$attributes = "";
		$fields = \is_array($key) ? $key : [$key];

		foreach ($fields as $key => $field) {
			if(self::monitor($field))
				$attributes .= ' '.preg_replace(['/^(d-)/', '/^.*\_/', '/^.*\*/'], 'data-', $field).' = "'.$attr.'"';
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
		if(! \in_array($key, self::$appends)) {
			self::$appends [] = $key;

			return true;
		}

		return false;
	}
}
