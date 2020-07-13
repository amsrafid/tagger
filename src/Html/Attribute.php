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
		$all_attr = "";

		if(self::$attributes) {
			foreach(self::$attributes as $key => $attr) {
				if(\array_key_exists($key, self::$sudo)) {

					if(array_key_exists($tag, self::$preset)){
						print_r(self::$sudo[$key]);
						print_r(self::$preset);
					}
					
					$all_attr .= self::attributeFormat($tag, self::$sudo[$key], $attr);
				} else {
					$all_attr .= self::attributeFormat($tag, $key, $attr);
				}
			}
		}

		return $all_attr;
	}

	/**
	 * 
	 */
	private function concatToSetAttributes(&$value = '')
	{
		# code...
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
		$attributes = "";
		$fields = \is_array($key) ? $key : [$key];
		
		foreach ($fields as $key => $field) {
			if(self::monitor($field)) {
				$attributes .= ' '.preg_replace(['/^(d-)/', '/^.*\_/', '/^.*\*/'],
												'data-',
												$field) .' = "' .$attr .'"';
			}
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
