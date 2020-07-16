<?php
namespace Html;

/*
 * Html tag builder class
 * 
 */
class Tag
{
	use Attribute;

	/**
	 * Appended attributes set
	 * 
	 * @var array
	 */
	public static $appends = [];

	/**
	 * Identical tag attribute set
	 * 
	 * @var array
	 */
	public static $attributes = [];

	/**
	 * Preset attributes
	 * 
	 * @var array
	 */
	public static $preset = [];

	/**
	 * Buld new Attribute
	 * 
	 * @param string $tag         Tag name
	 * @param string $attributes  Attributes list
	 * @return Html\Tag
	 */
	public static function build ($tag, $attributes)
	{
		self::$appends = [];
		self::$attributes = self::discoverBody($attributes);

		$body = self::$attributes['body'];
		unset(self::$attributes['body']);

		if(($body || $body == '0') && ! in_array($tag, self::$single)) {
			?><<?= "{$tag}" ?><?= self::attributes($tag) ?>><?= (gettype($body) == 'object')
				? $body(new self)
				: ((gettype($body) == 'array')
					? self::multiLevelTag($body)
					: (gettype($body) == 'boolean' && $body ? "" : $body)
				) ?></<?= $tag ?>><?php
		} else {
			?><<?= "{$tag}" ?><?= self::attributes($tag) ?> /><?php
		}

		return new self;
	}

	/**
	 * Static method magic call
	 * 
	 * @param string $tag 		Method name same as tag name
	 * @param array 	$attributes Attributes as array format
	 * @return Html\Tag
	 */
	public static function __callStatic ($tag, $attributes)
	{
		if($attributes)
			$attributes = current($attributes);

		if(\gettype($attributes) == 'array') {
			self::$attributes = current($attributes);
		}
		else {
			$attributes = ['body' => $attributes];
		}

		return self::distribute($tag, $attributes);
	}

	/**
	 * Html Comment tag
	 * 
	 * @param string|object $value 	Comment body as function or string
	 * @return Html\Tag
	 */
	public static function comment($value = '')
	{
		if (gettype($value) == 'array') {
			throw new \Exception("Invalid parameter for 'comment' tag");
			return '';
		}
		else if(gettype($value) == 'object') {
			?><!-- <?= $value(new self) ?> --><?php
		} else {
			?><!-- <?= $value ?> --><?php
		}
	}

	/**
	 * Distribute to control statement or build
	 * 
	 * @param string  $tag          Tag name
	 * @param array   $attributes   Arguments are attributes list
	 * @return null|build
	 */
	private static function distribute ($tag, $attributes)
	{
		if($set = ControlStatement::match(array_keys($attributes)))
			return ControlStatement::handle($tag, $attributes, $set);

		return Tag::build($tag, $attributes);
	}

	/**
	 * Doctype HTML special tag
	 * 
	 * @param string|object $value 	Comment body as function or string
	 * @return Html\Tag
	 */
	public static function doctype($value = 'html')
	{
		if(gettype($value) == 'array') {
			throw new \Exception("Invalid parameter for 'doctype' tag");
			return '';
		} else if (gettype($value) == 'object') {
			?><!DOCTYPE <?= $value(new self) ?>><?php
		} else {
			?><!DOCTYPE <?= $value ?>><?php
		}
	}

	/**
	 * Multi level tag only tag and value
	 * 
	 * @param array	$body		Tag body with tag body
	 * @return void
	 */
	public static function multiLevelTag($body)
	{
		if($body) {
			foreach($body as $tag => $text) {
				foreach($text as $txt)
					Tag::{$tag}($txt);
			}
		}
	}

	/**
	 * Set some preset attributes by tag name
	 * 
	 * @param array $value 	Set attributes by tag name as key. Format
	 * 		[
	 * 			'tag' => [
 	 *				'c' => 'class-name',
 	 *				...
 	 *			],
	 * 			...
	 * 		]
	 * @return void
	 */
	public static function set($value = [])
	{
		if(self::$preset) {
			foreach($value as $key => $val) {
				self::$preset[$key] = $val;
			}
		} else
			self::$preset = $value;
	}

	/**
	 * Make empty to preset attributes value
	 * 
	 * @return void
	 */
	public static function stopSet()
	{
		self::$preset = [];
	}
}
