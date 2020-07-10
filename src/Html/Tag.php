<?php
namespace Html;

class Tag
{
	use Credentials;

	public static $attributes = [];
	public static $appends = [];

	public static function __callStatic ($tag, $attributes)
	{

		self::$appends = [];
		if($attributes)
			$attributes = current($attributes);

		if(gettype($attributes) =='object') {
			$attributes = ['body' => $attributes];
		}
		else {
			self::$attributes = current($attributes);
		}

		self::build($tag, $attributes);
	}

	public static function build($tag, $attributes = [])
	{
		self::$appends = [];
		if ($attributes)
			self::$attributes = $attributes;

		$body = self::any('tag_body', false);

		if($body && ! in_array($tag, self::$single)) {
			?><<?= "{$tag}" ?><?= self::attributes() /*$identity.$class*/ ?>><?= (gettype($body) == 'object')
				? $body(new self)
				: $body ?></<?= $tag ?>><?php
		} else {
			?><<?= "{$tag}" ?><?= self::attributes() /*$identity.$class*/ ?> /><?php
		}
	}

	/**
	* Find any attribute by inner origin name
	*	from view given list 
	* 
	* @param string $name 	inner atribute name
	* @return string
	*/
	private static function any($name, $expression = true)
	{
		$format = "";

		if(isset(self::$attrs[$name])) {
			foreach (self::$attrs[$name]['set'] as $i => $attr) {
				/*if(!\in_array($attr, self::$appends)) {
					self::$appends [] = $attr;*/
					$attrFin = self::attrFormat($attr, $expression);

					if($attrFin && self::monitor($attr)) {
						if(isset(self::$attrs[$name]['both']) && self::$attrs[$name]['both']) {
							$format .= self::attrFormat($attr, $expression);
						}
						else {
							return $attrFin;
						}
					}
				// }
			}
		}

		return $format;
	}

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

	private static function attributeFormat($key, $attr)
	{
		$attributes = "";
		$fields = \is_array($key) ? $key : [$key];

		foreach ($fields as $key => $field) {
			if(self::monitor($field))
				$attributes .= ' '.$field.' = "'.$attr.'"';
		}

		return $attributes;
	}

	private static function monitor($key)
	{
		if(! \in_array($key, self::$appends)) {
			self::$appends [] = $key;

			return true;
		}

		return false;
	}
}
