<?php

namespace Html;

/*
 * Html tag builder class
 * 
 * @method public static Html\Tag 		build($tag, $attributes)
 * @method public static Html\comment comment($value = '')
 * @method public static Html\Tag 		doctype($value = 'html')
 * @method public static void 				multiLevelTag()
 * @method public static void 				set($value = [])
 * @method public static void 				stopSet()
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
	 * Wrapper tag set
	 * 
	 * @var array
	 */
	public static $wrap = [];

	/**
	 * Buld new Attribute
	 * 
	 * @param string $tag         Tag name
	 * @param string $attributes  Attributes list
	 * @return Html\Tag
	 */
	private static function build ($tag, $attributes)
	{
		self::$appends = [];
		self::$attributes = self::discoverBody($attributes);

		$body = self::$attributes['body'];
		self::destroyKey(self::$attributes, 'body');

		if(($body || $body == '0') && ! \in_array($tag, self::$single)) {
			?><<?= "{$tag}" ?><?= self::attributes($tag) ?>><?= \is_object($body)
				? (\is_array($newBody = $body(new self))
						? self::multiLevelTag($newBody)
						: $newBody)
				: (\is_array($body)
						? self::multiLevelTag($body)
						: (\is_bool($body) && $body ? "" : $body)
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
		$wrapAttributes = [];

		if(isset(self::$wrap[$tag])) {
			if(\is_array(self::$wrap[$tag])) {
				if(isset(self::$wrap[$tag][0]) && \is_string(self::$wrap[$tag][0]))
					$wrapTag = self::$wrap[$tag][0];
				else
					throw new \Exception("Wrapper tag must be a string.");

				$wrapAttributes = isset(self::$wrap[$tag][1]) ? self::$wrap[$tag][1] : [];
			} else if(\is_string(self::$wrap[$tag])) {
				$wrapTag = self::$wrap[$tag];
			} else {
				throw new \Exception("Wrapper tag must be a string or array where format is ['wrapper tag name', attributes set as array if required]");
			}

			$wrapAttributes['b'] = function() use($tag, $attributes) {
				self::handleLabel($attributes);
				Tag::build($tag, $attributes);
			};

			return Tag::build($wrapTag, $wrapAttributes);
		}

		if($set = ControlStatement::match(array_keys($attributes)))
			return ControlStatement::handle($tag, $attributes, $set);

		self::handleLabel($attributes);
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
	 * Check id is present to attribute set and return value
	 * 
	 * @param array	$attributes		Attribute set
	 * @return string | null
	 */
	private static function hasId($attributes)
	{
		if (isset($attributes['i']))
			return $attributes['i'];
		if (isset($attributes['id']))
			return $attributes['id'];

		return null;
	}

	/**
	 * Create label tag before identical tag
	 * 
	 * @param array	&$attributes		Main tag attribute, where label attribute should be present
	 * @return \Html\Tag or true
	 */
	private static function handleLabel(&$attributes)
	{
		if(isset($attributes['label'])) {
			$attr = [];
			$label = $attributes['label'];
			self::destroyKey($attributes, 'label');

			if(\is_array($label)) {
				$attr = $label;
			} else {
				$attr['b'] = $label;
			}

			if($id = self::hasId($attributes)) {
				$attr['f'] = $id;
			}

			return Tag::label($attr);
		}

		return true;
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
	 * 			'taga' => [
 	 *				'c' => 'class-name',
 	 *				...
 	 *			],
 	 *			'tagb' => '@taga',
	 * 			...
	 * 		]
	 * @return void
	 */
	public static function set($value = [])
	{
		if($value) {
			foreach($value as $key => $val) {
				if(\is_string($val) && self::isToken($val)) {
					$token = self::replaceTokenIdentifier($val);

					if(isset($value[$token])) {
						self::$preset[$key] = $value[$token];
					} else {
						throw new \Exception("Invalid tag preset is trying to be set in '".$key."' tag.");
					}
				}
				else if (\is_array($val))
					self::$preset[$key] = $val;
				else
					throw new \Exception("Invalid attribute format for tag '".$key."'. Set of attributes or token (@tag) is expected.");
			}
		}
	}

	/**
	 * Make empty to preset attributes value
	 * 
	 * @return void
	 */
	public static function stopSet($value = [])
	{
		if(! empty($value)) {
			$value = is_array($value) ? $value : [$value];

			foreach($value as $val) {
				if(isset(self::$preset[$val])) {
					self::destroyKey(self::$preset, $val);
				}
			}
		} else {
			self::$preset = [];
		}
	}

	/**
	 * Make empty to wrapper value
	 * 
	 * @return void
	 */
	public static function stopWrap($value = [])
	{
		if(! empty($value)) {
			$value = is_array($value) ? $value : [$value];

			foreach($value as $val) {
				if(isset(self::$wrap[$val])) {
					self::destroyKey(self::$wrap, $val);
				}
			}
		} else {
			self::$wrap = [];
		}
	}

	/**
	 * Set wrapper tag with attributes by tag name
	 * 
	 * @param array $value 	Set attributes by tag name as key. Format
	 * 		['taga' => (array) ['wrapper tag', ['c' => 'class name'...]] || (string) tag name],
	 * 		['tagb' => '@taga'],
	 * 		...
	 * @return void
	 */
	public static function wrap($value = [])
	{
		if($value) {
			foreach($value as $key => $val) {
				if(\is_string($val) && self::isToken($val)) {
					$token = self::replaceTokenIdentifier($val);

					if(isset($value[$token])) {
						self::$wrap[$key] = $value[$token];
					} else {
						throw new \Exception("Invalid wrap preset is trying to be set in '".$key."' tag.");
					}
				}
				else if (\is_array($val)) {
					self::$wrap[$key] = $val;
				}
				else {
					throw new \Exception("Invalid attribute format for tag '".$key."'. Set of attributes or token (@tag) is expected.");
				}
			}
		}
	}
}
