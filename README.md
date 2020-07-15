# HTML Tag generator

# Basic use

~~~
Html\Tag::{'Tag name'}([
	b/body/t/txt/text => string|bool|function(){}		<!-- Tag Body -->
	...
	Attribute name as array key and value as key value
]);
~~~

# Sudo attributes is available

~~~
a -> alt
c/cls -> class
cont -> content
i  -> id
n  -> name
p  -> placeholder
s  -> src
st  -> style
v/val -> value
...
~~~

## Preset attributes for identical tag
Preset common attributes value, using set Tag.

~~~
Tag::set([
	'input' => [
		'c/cls/class' => 'form-control',
		...
	],
	...
]);

Tag::input(['type' => 'text'])
<!--
	Output:
	<input type = "text" class = "form-control" />
-->

Tag::stopSet();
~~~


## Control statement
As like normal control statement foreach/if/elseif/else. Control statements uses as attributes.

### for

~~~
$arrs = [
	['id' => 24, 'name' => 'Amsrafid'],
	['id' => 33, 'name' => 'Sadman Rafid']
];

Tag::ul(['if' => $arrs, 'b' => function() use($arrs) {
	Tag::li(['foreach' => $arrs, 'v' => '@id', 'b' => '@i. @name']);
}]);
<!--
	@id -> @{array key name}.
	Able to capture in any attributes value
-->
~~~

### if

~~~
$var = 10;

Tag::span(['if' => $var > 10, 'b' => 'Var is greated than 10']);
<!-- 
	Normal use:
	if($var > 0)
		echo "<span>Var is greated than 10</span>
-->
~~~

### elseif

~~~
Tag::span(['elseif' => $var > 5, 'b' => 'Var is greated than 5']);
<!-- 
	Normal use:
	if ($var > 10)
		echo "<span>Var is greated than 10</span>
	else if ($var > 5)
		echo "<span>Var is greated than 5</span>
-->
~~~

### else

~~~
Tag::span(['else' => true, 'b' => 'Var is less than 5']);
<!-- 
	Normal use:	
	if ($var > 10)
		echo "<span>Var is greated than 10</span>
	else if ($var > 5)
		echo "<span>Var is greated than 5</span>
	else
		echo "<span>Var is less than 5</span>
-->
~~~
