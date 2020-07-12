<?php
namespace Html;

class Tag
{
	use Credentials,
			Attribute;

	public static $attributes = [];
	public static $appends = [];

	public static function __callStatic ($tag, $attributes)
	{
		self::$appends = [];
		if($attributes)
			$attributes = current($attributes);

		if(\in_array(gettype($attributes), ['object', 'string'])) {
			$attributes = ['body' => $attributes];
		}
		else {
			self::$attributes = current($attributes);
		}

		return self::distribute($tag, $attributes);
	}

	/**
	 * Buld new Attribute
	 * 
	 * @param string $tag         Tag name
	 * @param string $attributes  Attributes list
	 * @return null|string
	 */
	public static function build($tag, $attributes)
	{
		self::$appends = [];
		self::$attributes = $attributes;

		$body = self::any('tag_body', false);

		if($body && ! in_array($tag, self::$single)) {
			?><<?= "{$tag}" ?><?= self::attributes() ?>><?= (gettype($body) == 'object')
				? $body(new self)
				: $body ?></<?= $tag ?>><?php
		} else {
			?><<?= "{$tag}" ?><?= self::attributes() ?> /><?php
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

					$attrFin = self::attrFormat($attr, $expression);

					if($attrFin && self::monitor($attr)) {
						if(isset(self::$attrs[$name]['both']) && self::$attrs[$name]['both']) {
							$format .= self::attrFormat($attr, $expression);
						}
						else {
							return $attrFin;
						}
					}

			}
		}

		return $format;
	}

	/**
	 * Distribute to control statement or build
	 * 
	 * @param string  $tag          Tag name
	 * @param array   $attributes   Arguments are attributes list
	 * @return null|build
	 */
	private static function distribute($tag, $attributes)
	{
		if($set = ControlStatement::match(array_keys($attributes)))
			return ControlStatement::handle($tag, $attributes, $set);

		return self::build($tag, $attributes);
	}
}
