<?php

namespace Html;

/**
 * Bind control satement operation
 * 
 * @method Html\Tag|null public static c_foreach($tag, $attributes, $set)
 * @method Html\Tag|null public static c_if($tag, $attributes, $set)
 * @method Html\Tag|null public static c_elseif($tag, $attributes, $set)
 * @method Html\Tag|null public static c_else($tag, $attributes, $set)
 */
interface ControlStatementBinding
{
	/**
	 * Foreach statement
	 * 
	 * @param string	$tag					Tag name
	 * @param array		$attributes		Attribute set
	 * @param array		$set					Next control statement set
	 * @return Html\Tag|null
	 */
	public static function c_foreach($tag, $attributes, $set);

	/**
	 * For statement
	 * 
	 * @param string	$tag					Tag name
	 * @param array		$attributes		Attribute set
	 * @param array		$set					Next control statement set
	 * @return Html\Tag|null
	 */
	public static function c_if($tag, $attributes, $set);

	/**
	 * For statement
	 * 
	 * @param string	$tag					Tag name
	 * @param array		$attributes		Attribute set
	 * @param array		$set					Next control statement set
	 * @return Html\Tag|null
	 */
	public static function c_elseif($tag, $attributes, $set);

	/**
	 * For statement
	 * 
	 * @param string	$tag					Tag name
	 * @param array		$attributes		Attribute set
	 * @param array		$set					Next control statement set
	 * @return Html\Tag|null
	 */
	public static function c_else($tag, $attributes, $set);
}
