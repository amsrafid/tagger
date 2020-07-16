<?php
namespace Html;

trait Credentials
{
	/**
	 * Special attribute set,
	 *	used to identify in view generation
	 *
	 * @var object
	 */
	private static $attrs = [
		'tag_body' => [
			'set' => ['body', 'txt', 'text', 't', 'b']
		]
	];

	/**
	 * Tags who has no ending tag by birth
	 * 
	 * @var array
	 */
	public static $single = [
		'area',
		'base',
		'br',
		'col',
		'embed',
		'hr',
		'img',
		'input',
		'link',
		'meta',
		'param',
		'source',
		'track',
		'wbr'
	];

	/**
	 * Attributes sudo name
	 * 
	 * @var array
	 */
	public static $sudo = [
		'a' => 'alt',
		'c' => 'class',
		'cls' => 'class',
		'cont' => 'content',
		'i'  => 'id',
		'ln' => 'lang',
		'n'  => 'name',
		'p'  => 'placeholder',
		's'  => 'src',
		'st'  => 'style',
		'v' => 'value',
		'val' => 'value'
	];
}
