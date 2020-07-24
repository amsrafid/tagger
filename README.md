	# HTML Tag

One of the most flexible view builder for php.

# Basic use

Very easy to use. Attribute and tag name is same as normal html.
Most notable fact is that **_sudo_** or short name is also work as normal HTML attributes.

~~~php
\Html\Tag::{Tag name}([
	'i/id' => 'id-name',
	'c/cls/class' => 'class-name',
	'd-./_./*./data-.' => 'data-value',
	'b/body/txt/text' => string|array|number|bool|function(){} /* tag body*/
	...
]);
~~~

Attribute name as array key and value as key value
Note: Data attribues is handled with sudo **_[d-name/\_name/\*name]_**.
In all case, attribute name will be **_data-name_**.

## Sudo attributes is available

~~~
a 		=	alt,
c 		=	class,
cls		=	class,
cont		=	content,
cs 		=	colspan,
d 		=	data,
da 		=	disabled,
dt 		=	datetime,
f 		=	for,
fa 		=	formaction,
h 		=	href,
i  		=	id,
ln 		=	lang,
m 		=	method,
mx 		=	max,
mn 		=	min,
mxlen		=	maxlength,
mnlen		=	minlength,
mt 		=	muted,
n  		=	name,
p  		=	placeholder,
pt		=	pattern,
r 		=	required,
rs 		=	rowspan,
rw 		=	rows,
s  		=	src,
sc		=	selected,
st		=	style,
t  		=	type,
v 		=	value,
val		=	value
~~~

# Preset functionality
Amsrafid Html allows preset as **_attributes_** or **_wrapper_**. That reduces using of same attribute and wrapper on same tag.

## Preset attributes for identical tag

Preset common attributes value, using **_set_** Tag.

~~~php
Tag::set([
	'input' => [
		'c/cls/class' => 'form-control',
		...
	],
	...
]);

Tag::input(['type' => 'text']);
Tag::input(['type' => 'number']);

Tag::stopSet();
~~~

**Output:**
~~~html
<input type = "text" class = "form-control" />
<input type = "number" class = "form-control" />
~~~

## Preset wrapper for identical tag

Preset common wrapper value, using **_wrap_** Tag.

~~~php
Tag::wrap([
	'input' => ['div', ['c' => 'col-md-6', ...]],
	'textarea' => 'div',
	...
]);

Tag::input(['t' => 'text']);
Tag::textarea();

Tag::stopWrap();
~~~

**Output:**
~~~html
<div class = "col-md-6"><input type = "text" /></div>
<div><textarea></textarea></div>
~~~

# Special use

## Label
Automatic **label tag** can be added before any tag using **_label_** attribute. If label containing tag has a wrapper preset, a label tag will be created into the wrapper before this.

~~~php
Tag::wrap([
	'input' => ['div', ['c' => 'col-md-6 mb-2']]
]);

Tag::input(['t' => 'text', 'i' => 'name', 'label' => 'Name *', 'p' => "Name"]);
Tag::input(['t' => 'number', 'i' => 'age', 'label' => 'Age *', 'p' => "Age"]);
~~~

**Output**
~~~html
<div class="col-md-6 mb-2">
	<label for="name">Name *</label>
	<input id="name" type="text" placeholder = "Name">
</div>
<div class="col-md-6 mb-2">
	<label for="age">Age *</label>
	<input id="age" type="number" placeholder = "Age">
</div>
~~~

## Table

Html table is able to be generated dynamically. Where, **_body_** can be passed an array with key as **_tag name_** and key value a normal array for tag body.

~~~php
$arrs = [
	['id' => 24, 'name' => 'Amsrafid'],
	['id' => 33, 'name' => 'Sadman Rafid']
];
	
Tag::table(['border' => '1', 'b' => function() use($arrs) {
	Tag::tr(['b' => ['th' => ['#', 'ID', 'Name']]]);
	Tag::tr(['foreach' => $arrs, 'offset' => 'i', 'start' => 1,
		'b' => ['td' => ['@i', '@id', '@name']]
	]);
}]);
~~~

**Output**
~~~html
<table border="1">
	<tr><th>#</th><th>ID</th><th>Name</th></tr>
	<tr><td>1</td><td>24</td><td>Amsrafid</td></tr>
	<tr><td>2</td><td>33</td><td>Sadman Rafid</td></tr>
</table>
~~~

## Control statement

As like normal control statement **_foreach/if/elseif/else_**. Control statements uses as attributes.

### foreach:

Act like normal foreach in php. Here, **_offset_**, **_start_** used for loop array/object affset, and from which value offset count will be started.

~~~php
Tag::ul(['if' => $arrs, 'b' => function() use($arrs) {
	Tag::li([
		'foreach' => $arrs, 'offset' => 'i',
		'v' => '@id', 'b' => '@i. @name'
	]);
}]);
~~~

**Output**
~~~html
<ul>
	<li value="24">1. Amsrafid</li>
	<li value="35">2. Sadman Rafid</li>
</ul>
~~~

@id -> @{array key name}.
Able to capture in any attributes value

**Special Attributes:**
Attributes given bellow are useful only iff **_foreach_** attribute is present.

- **'if' => string**
	- Normal if condition. Ex: **_(@i > 2 && (@age == 50 || '@name' == 'HTML'))_**.
	- Here, **_@i_** is offset, **_@name_** is array key.
	- Note: **_@name_** value is **string** type. So **_'@name'_** is binded with quotes.
	On the other hand, **_@age_** value is **integer** type. So, quote is not required.

- **'then' => string|array**
	- This attribute works when **_'if'_** condition is valid.
	- String value will be considered as attribute value true. Ex: **_selected_**
	- Here, array contains attribute set which will be changed after a valid if condition.

- **'offset' => string**
	- Contains loop array offset variable name.
	- In **logical expression**, consided to be **_started form 0_** and **in view** depends on **_start_** attribute.

- **'start' => int**
	- Denotes from where body/view offset will be started from. Default start value is **1**.

### if:

Normal **_if_** statement like php.

~~~php
$var = 10;
Tag::span(['if' => $var > 10, 'b' => 'Var is greated than 10']);
~~~

**Normal use:**
~~~php
if($var > 0)
	echo "<span>Var is greated than 10</span>
~~~

### elseif:

Normal **_elseif_** statement like php. Here, this condition will only work iff **_if_** statment is present before this.

~~~php
Tag::span(['elseif' => $var > 5, 'b' => 'Var is greated than 5']);
~~~

**Normal use:**
~~~php
if ($var > 10)
	...
else if ($var > 5)
	echo "<span>Var is greated than 5</span>
~~~

### else:

Normal **_else_** statement like php. Value will be **anything except _false_**. Here, this condition will only work iff **_if_** or **_elseif_** statment is present before this.

~~~php
Tag::span(['else' => true, 'b' => 'Var is less than 5']);
~~~

**Normal use:**
~~~php
if ($var > 10)
	...
else
	echo "<span>Var is less than 5</span>
~~~

## Security Vulnerabilities

If you discover a security vulnerability within this, please send an e-mail to **_Sadman Rafid_** via [amsrafid@gmail.com](mailto:amsrafid@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The Asrafid Html is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
