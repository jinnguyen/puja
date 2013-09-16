Custom template tags
===================
Puja’s template system comes with a wide variety of <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-tags.md">built-in tags</a> and <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-filters.md">filters</a> designed to address the presentation logic needs of your application. 
Hoever, sometime you may find yourself needing functionality that is not covered by the core set of template primitives. You can extend the template engine by defining custom tags and filters using PHP, and then make them available to your template.

- <strong>I. Writing custom template filters</strong><br />
Custom filters are just method of PHP class.<br />
For example, you need a new filter <strong>cutend</strong> to cut a number end characters of the string.<br />
Here’s an example filter definition in PHP:<br />
<strong>customer_filters.php</strong>
<pre>&lt;?php
  class CustomerFilter{
      function filter_cutend($value, $arg = null){
        if(!$arg) return $value;
        return substr($value, - $arg);
      }
  }
?&gt;
</pre>
<strong>main.php</strong><br />
<pre>&lt;?php
  include 'customer_filters.php';
  $tpl = new Puja;
  $tpl->customer_filter = new CustomerFilter;
?&gt;
</pre>
And here’s an example of how that filter would be used:
<pre>{{ name|cutend:4 }} // have argument
  {{ name|cutend }} // no argument
</pre>
If <strong>name</strong> is "I like puja", the output will be "puja" and "I like puja".<br />
** You can overwrite all built-in filter (even filter <a href="https://github.com/jinnguyen/puja/blob/master/docs/built-in-filters.md#main">main</a>) by set the customer filter name same with built-in filter name.

- <strong>II. Writing custom template tags</strong><br />

** <i>Filter and tag methods should always return something. They shouldn’t raise exceptions. They should fail silently. In case of error, they should return either the original input or an empty string – whichever makes more sense.</i>
