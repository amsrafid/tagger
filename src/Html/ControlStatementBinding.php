<?php

namespace Html;

interface ControlStatementBinding
{
	/**
	* For statement
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