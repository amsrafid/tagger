<?php
namespace Html;

trait Credentials
{
	/*
	* Special attribute set,
	*	used to identify in view generation
	*
	* @var object
	*/
	private static $attrs = [
		'tag_body' => [
			'set' => ['body', 'txt', 'text', 't', 'b']
		]/*,
		'tag_class' => [
			'origin' => 'class',
			'set' => ['cls', 'class']
		],
		'tag_identity' => [
			'both' => true,
			'origin' =>	['id', 'name'],
			'set' => ['id', 'name']
		]*/
	];

	/*
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

	public static $sudo = [
		'a' => 'alt',
		'c' => 'class',
		'cls' => 'class',
		'i'  => ['id', 'name'],
		'id'  => ['id', 'name'],
		'n'  => ['id', 'name'],
		'name'  => ['id', 'name'],
		'p'  => 'placeholder',
		's'  => 'src',
		'st'  => 'style',
		'v' => 'value',
		'val' => 'value'
	];
}
