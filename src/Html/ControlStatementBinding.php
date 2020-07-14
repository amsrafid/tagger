<?php

namespace Html;

interface ControlStatementBinding
{
	/**
	 * Foreach statement
	 * 
	 * @param string	$tag					Tag name
	 * @param array		$attributes		Attribute set
	 * @param array		$set					Next control statement set
	 * @return 
	 */
	public static function c_foreach($tag, $attributes, $set);

	/**
	 * For statement
	 */
	public static function c_if($tag, $attributes, $set);

	/**
	 * For statement
	 */
	public static function c_elseif($tag, $attributes, $set);

	/**
	 * For statement
	 */
	public static function c_else($tag, $attributes, $set);
}