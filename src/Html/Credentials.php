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
			'set' => ['b', 'body', 'txt', 'text']
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
		'af' => 'autofocus',
		'c' => 'class',
		'cls' => 'class',
		'cont' => 'content',
		'cs' => 'colspan',
		'd' => 'data',
		'da' => 'disabled',
		'dt' => 'datetime',
		'f' => 'for',
		'fa' => 'formaction',
		'h' => 'href',
		'i'  => 'id',
		'ln' => 'lang',
		'm' => 'method',
		'mx' => 'max',
		'mn' => 'min',
		'mxlen' => 'maxlength',
		'mnlen' => 'minlength',
		'mt' => 'muted',
		'n'  => 'name',
		'p'  => 'placeholder',
		'pt'  => 'pattern',
		'r' => 'required',
		'rs' => 'rowspan',
		'rw' => 'rows',
		's'  => 'src',
		'sc'  => 'selected',
		'st'  => 'style',
		't'  => 'type',
		'v' => 'value',
		'val' => 'value'
	];
}
