# Tagger

One of the most flexible html view builder for php. It builds view in php file using same naming convention of html tag and attributes.

# Installation

This is a composer package. So, require this package in the `composer.json` of your php/framework project or run the command bellow,

~~~
composer require amsrafid/tagger
~~~

# Basic use

Very easy to use. Attribute and tag name is same as normal html.
Most notable fact is that **_sudo_** or short name is also worked as normal HTML attributes.

~~~php
\Html\Tag::{Tag name}([
	'i/id' => 'id-name',
	'c/cls/class' => 'class-name',
	'd-./_./*./data-.' => 'data-value',
	'b/body/txt/text' => string|array|number|bool|function(){} /* tag body*/
	...
]);
~~~

Array key refers attribute name and key value as attribute value.\
Note: Data attribue is also handled with sudo as like **_[d-name/\_name/\*name]_**.\
In all cases above, attribute name will be **_data-name_**.

### Attribute **_'body'_**:
Attribute **_body_** is the nested part of a tag. Body can be of five types. String or number is basic type. Special types are,
- **_Array_ type:**
	- Here, only **_associative_** array is allowed to show. In that case, **_arry key_** denotes **_tag name_** and **_value_** is a **_sequential array_** where each value is the body of each tag named in main array key.
	- Example:
	~~~php
	use Html\Tag;

	Tag::ul(['b' => ['li' => ['one', 'two', 'three']]]);
	~~~
	- Output:
	~~~html
	<ul>
		<li>one</li>
		<li>two</li>
		<li>three</li>
	</ul>
	~~~
- **_Object_ type:**
	- Returns _string_, _number_ or _associative array_ to be shown in body.
	- Mainly, object type denotes a **_function_** that contains nested elements of mother tag.
	- Example:
	~~~php
	Tag::div(function(){
		Tag::h4("First set:");
		Tag::hr();
		Tag::div(['b' => 'Having fun, isn\'t it?']);
		Tag::div(function(){
			Tag::span(function(){ return "One"; });
			Tag::span(2);

			return [
				'h3' => ['array', 'returned'],
				'u' => ['test', 'underline'],
			];
		});
	});
	~~~
	- Output:
	~~~html
	<div>
		<h4>First set:</h4>
		<hr>
		<div>Having fun, isn't it?</div>
		<div>
			<span>One</span>
			<span>2</span>
			<h3>array</h3>
			<h3>returned</h3>
			<u>test</u>
			<u>underline</u>
		</div>
	</div>
	~~~
- **_Boolean_ type**
	- Boolean type works when there is nothing to show on body. But, The tag is not a single tag like, `<img />`. Then, body value should be given as **__true__**.
	- Example:
	~~~php
	Tag::script(["s"=>"https://script.js", 'b' => true]);
	~~~
	- Output:
	~~~html
	<script src="https://script.js"></script>
	~~~

## Sudo attributes are available
List of **_sudo_** attribute is given bellow.
~~~
a 		=	alt,
ac		=	action,
c 		=	class,
cls		=	class,
cont		=	content,
cs 		=	colspan,
d 		=	disabled,
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

Tagger allows preset of **_attributes_** and **_wrapper_**. It reduces using of same attribute and wrapping on same tag.

## Preset attributes for identical tag

Preset functionality works on common attribute value using **_set_** method. Here, preseting can be stoped by using **_stopSet_** method that accepts **_string_** or **_array_** of tag name or empty for destroy all.

~~~php
Tag::set([
	'input' => [
		'c/cls/class' => 'form-control mt-2',
		...
	],
	'textarea' => '@input',		/* Same as input tag */
	...
]);

Tag::input(['type' => 'text']);
Tag::input(['type' => 'number']);
Tag::textarea(['b' => 'Text area', 'c' => 'text-danger']);

Tag::stopSet();
~~~

**Output:**
~~~html
<input type = "text" class = "form-control mt-2" />
<input type = "number" class = "form-control mt-2" />
<textarea class = "text-danger form-control mt-2">Text area</textarea>
~~~

## Preset wrapper for identical tag

Similar with **_set_** wrapping functionality works on common wrapper value, using **_wrap_** method. Here also, tag wrapping can be stoped by using **_stopWrap_** method that accepts **_string_** or **_array_** of tag name or empty for destroy all.

~~~php
Tag::wrap([
	'input' => ['div', ['c' => 'col-md-6', ...]],
	'textarea' => 'div',
	'select' => '@input'	/* Same as input tag */
	...
]);

Tag::input(['t' => 'text']);
Tag::textarea();
Tag::select(['b' => ['option' => ['one', 'two']]]);

Tag::stopWrap(['textarea']);	/* OR Tag::stopWrap('textarea'); */
Tag::textarea("Text area value");
~~~

**Output:**
~~~html
<div class = "col-md-6"><input type = "text" /></div>
<div><textarea></textarea></div>
<div class = "col-md-6">
	<select>
		<option>one</option>
		<option>two</option>
	</select>
</div>
<textarea>Text area value</textarea>
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

Here, html table is able to be generated dynamically. Where, **_body_** can be passed an array with **_key_** as **_tag name_** and **_key value_** as normal **_array_** for tag body.

~~~php
$arrs = [
	['id' => 24, 'name' => 'HTML'],
	['id' => 33, 'name' => 'CSS'],
	['id' => 49, 'name' => 'JAVASCRIP']
];
	
Tag::table(['border' => '1', 'b' => function() use($arrs) {
	Tag::tr(['b' => ['th' => ['#', 'ID', 'Name']]]);
	Tag::tr(['foreach' => $arrs, 'offset' => 'i'
		'b' => ['td' => ['@i', '@id', '@name']]
	]);
}]);
~~~

**Output**
~~~html
<table border = "1">
	<tr><th>#</th><th>ID</th><th>Name</th></tr>
	<tr><td>1</td><td>24</td><td>HTML</td></tr>
	<tr><td>2</td><td>33</td><td>CSS</td></tr>
	<tr><td>3</td><td>49</td><td>JAVASCRIP</td></tr>
</table>
~~~

## Control statement

Control statement acts as like normal **_foreach/if/elseif/else_** here. Control statement uses as attribute.

### foreach:

Act like normal foreach in php. Here, **_offset_**, **_start_** respectively used for loop array/object offset, and from which value offset count will be started.

~~~php
Tag::ul(['if' => $arrs, 'b' => function() use($arrs) {
	Tag::li([
		'foreach' => $arrs, 'offset' => 'i',
		'if' => '@id > 24',
		'v' => '@id', 'b' => '@i. @name'
	]);
}]);
~~~

**Output**
~~~html
<ul>
	<li value="33">1. CSS</li>
	<li value="49">2. JAVASCRIP</li>
</ul>
~~~

@id -> @{array key name}.\
Able to capture in any attribute value.

**Special Attributes:**
Attributes given bellow are useful only iff **_foreach_** attribute is present.

- **'if' => string**
	- Normal if condition. Ex: **_(@i > 2 && (@age == 50 || @name == 'HTML'))_**.
	- Here, **_@i_** is offset, **_@name_** is array key.
	- Note: **_@name_** value is string type. So, comparing _string_ value must be block quoted.

- **'then' => string|array**
	- This attribute works when **_'if'_** condition is valid.
	- **_String_** type value consideres as attribute value _true_. Multiple _string_ can be considered as identical attribute that seperated with _comma_ or _semicolon_ or _dot_ or _space_ `, OR ; OR . OR \s+`.
	- Ex:
		- **_'then' => 'selected disabled'_**
		- **_'then' => ['selected' => true, 'disabled' => true]_**
	- Here, **_array_** contains attribute set which will be changed after a valid **_if_** condition.

- **'offset' => string**
	- Contains loop array _offset variable name_.
	- In **logical expression**, considers to be **_started form 0_** and **in view** depends on **_start_** attribute.

- **'start' => int**
	- Denotes from where body/view offset will be started from. Default start value is **1**.

### if:

Normal **_if_** statement like php.\
Note: **_then_** attribute is allowed as same way of **_if_** statement in **_foreach_** _special attributes_ section. But, only **_array_** type value is working here.

~~~php
$var = 10;
Tag::span(['if' => $var > 10, 'b' => 'Var is greater than 10']);
~~~

**Normal use:**
~~~php
if($var > 10)
	echo "<span>Var is greater than 10</span>
~~~

### elseif:

Normal **_elseif_** statement like php. Here, this condition will only work iff **_if_** statment is present before this.
Note: **_then_** attribute is allowed as same way of **_if_** statement.

~~~php
Tag::span(['elseif' => $var > 5, 'b' => 'Var is greater than 5']);
~~~

**Normal use:**
~~~php
if ($var > 10)
	...
else if ($var > 5)
	echo "<span>Var is greater than 5</span>
~~~

### else:

Normal **_else_** statement like php. Value should be given as **_true_**. Here, this condition will only work iff **_if_** or **_elseif_** statment is present before this.
Note: **_then_** attribute is not allowed here.

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

## Authors

**_Sadman Rafid_**

## License

The tagger is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
