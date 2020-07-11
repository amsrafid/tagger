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
	public static function c_for();

	/**
	* For statement
	*/
	public static function c_if();

	/**
	* For statement
	*/
	public static function c_elseif();

	/**
	* For statement
	*/
	public static function c_else();
}