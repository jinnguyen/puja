Built-in template filters
===
<table width="100%" >
  <tr>
    <td width="400">
      <a href="#abs">abs</a><br />
      <a href="#capfirst">capfirst</a><br />
      <a href="#date">date</a><br />
      <a href="#default">default</a><br />
      <a href="#escape">escape</a><br />
      <a href="#escapejs">escapejs</a><br />
      <a href="#join">join</a><br />
      <a href="#keys">keys</a><br />
      <a href="#length">length</a><br />
      <a href="#lower">lower</a><br />
      <a href="#nl2br">nl2br</a><br />
  </td>
    <td width="400">
      <a href="#pluralize">pluralize</a><br />
      <a href="#striptags">striptags</a><br />
      <a href="#trim">trim</a><br />
      <a href="#truncatechars">truncatechars</a><br />
      <a href="#truncatewords">truncatewords</a><br />
      <a href="#upper">upper</a><br />
      <a href="#urlencode">urlencode</a><br />
      <a href="#urldecode">urldecode</a><br />
      <a href="#urltrunc">urltrunc</a><br />
      <a href="#wordwrap">wordwrap</a><br />
      <a href="#yesno">yesno</a><br />
    </td>
  </tr>
</table>

- <strong><a name="abs">abs</a></strong><br />
The abs filter returns the absolute value.<br />
For example:
<pre>{{ age|abs }}</pre>
If <strong>age</strong> is -5, the output will be 5.

- <strong><a name="capfirst">capfirst</a></strong><br />
Capitalizes the first character of the value.<br />
For example:
<pre>{{ name|capfirst }}</pre>
If <strong>name</strong> is "puja", the output will be "Puja".

- <strong><a name="date">date</a></strong><br />
Formats a date according to the given format.<br />
Uses a similar format as PHP’s date() function (http://php.net/date) with some differences.
For example:
<pre>{{ today|date:"d/m/Y h:i:s" }}</pre>
If <strong>today</strong> is 2013-09-04 20:10:5, the output will be 04/09/2013 20:10:5.

- <strong><a name="default">default</a></strong><br />
If value evaluates to False, uses the given default. Otherwise, uses the value.<br />
For example:
<pre>{{ your_skill|default:"nothing" }}</pre>
If <strong>your_skill</strong> is "" (the empty string) or null, the output will be <strong>nothing</strong>.

- <strong><a name="escape">escape</a></strong><br />
Escapes a string’s HTML. Specifically, it makes these replacements:
<pre>
&lt; is converted to &amp;lt;
&gt; is converted to &amp;gt;
&#39; (single quote) is converted to &amp;#39;
&quot; (double quote) is converted to &amp;quot;
&amp; is converted to &amp;amp;
</pre>

- <strong><a name="escapejs">escapejs</a></strong><br />
Escapes characters for use in JavaScript strings. This does not make the string safe for use in HTML, but does protect you from syntax errors when using templates to generate JavaScript/JSON.<br />
For example:
<pre>{{ value|escapejs }}</pre>
If value is 
<pre>testing
javascript </pre>The output will be 
<pre>testing\
javascript"</pre>

- <strong><a name="join">join</a></strong><br />
Joins a array with a string, like PHP’s implode(str,array)<br />
For example:
<pre>{{ array|join:" // " }}</pre>
If <strong>array</strong> is the array ['a', 'b', 'c'], the output will be the string "a // b // c".

- <strong><a name="keys">keys</a></strong><br />
The keys filter returns the keys of an array. It is useful when you want to iterate over the keys of an array:
<pre>
{% for key in array|keys %}
    ...
{% endfor %}</pre>

- <strong><a name="length">length</a></strong><br />
Returns the length of the value. This works for both strings and lists.
For example:
<pre>{{ array|length }}</pre>
If <strong>array</strong> is ['a', 'b', 'c', 'd'], the output will be 4.

- <strong><a name="lower">lower</a></strong><br />
Converts a string into all lowercase.<br />
For example:
<pre>{{ message|lower }}</pre>
If <strong>message</strong>is "WElcome", the output will be "welcome".

- <strong><a name="nl2br">nl2br</a></strong><br />
The nl2br filter inserts HTML line breaks before all newlines in a string:
For example:
<pre>{{ content|nl2br }}</pre>
if <strong>content</strong> is:
<pre>Dear,
This is message from ...</pre>
The output will be:
<pre>Dear,&lt;br /&gt;
This is message from ...</pre>

- <strong><a name="pluralize">pluralize</a></strong><br />
Returns a plural suffix if the value is not 1. By default, this suffix is 's'.
For example:
<pre>You have {{ num_messages }} message{{ num_messages|pluralize }}.</pre>
If <strong>num_messages</strong> is 1, the output will be You have 1 message. If <strong>num_messages</strong> is 2 the output will be You have 2 messages.<br /><br />
For words that require a suffix other than 's', you can provide an alternate suffix as a parameter to the filter.<br />
For example:
<pre>You have {{ num_walruses }} walrus{{ num_walruses|pluralize:"es" }}.</pre>
For words that don’t pluralize by simple suffix, you can specify both a singular and plural suffix, separated by a comma.<br />
For example:
<pre>You have {{ num_cherries }} cherr{{ num_cherries|pluralize:"y,ies" }}.</pre>

- <strong><a name="striptags">striptags</a></strong><br />
Strips all [X]HTML tags. Except allowable tags.<br />
For example:
<pre>{{ content|striptags:"span,b" }}</pre>
If <strong>content</strong> is "&lt;div&gt;&lt;b&gt;Joel&lt;/b&gt; &lt;button&gt;is&lt;/button&gt; a &lt;span&gt;slug&lt;/span&gt;&lt;/div&gt;", the output will be "&lt;b&gt;Joel&lt;/b&gt;is a &lt;span&gt;slug&lt;/span&gt;".

- <strong><a name="trim">trim</a></strong><br />
The trim filter strips whitespace (or other characters) from the beginning and end of a string.<br />
For example:
<pre>{{ '  I like Puja.  '|trim }}</pre>
The  output will be 'I like Puja.' 
<pre>{{ '  I like Puja.'|trim('.') }}</pre>
The output will be '  I like Puja'

- <strong><a name="truncatechars">truncatechars</a></strong><br />
Truncates a string if it is longer than the specified number of characters. Truncated strings will end with a translatable ellipsis sequence ("...").<br />
For example:
<pre>{{ name|truncatechars:9 }}</pre>
If <strong>name</strong> is "Joel is a slug", the output will be "Joel i...".

- <strong><a name="truncatewords">truncatewords</a></strong><br />
Truncates a string after a certain number of words.<br />
For example:
<pre>{{ name|truncatewords:2 }}</pre>
If <strong>name</strong> is "Joel is a slug", the output will be "Joel is ...".<br />
** Newlines within the string will be removed.

- <strong><a name="upper">upper</a></strong><br />
Converts a string into all uppercase.<br />
For example:
<pre>{{ name|upper }}</pre>
If <strong>name</strong> is "Joel is a slug", the output will be "JOEL IS A SLUG".

- <strong><a name="urlencode">urlencode</a></strong><br />
The url_encode filter percent encodes a given string as URL segment or an array as query string.<br />
For example:
<pre>{{ name|urlencode }}</pre>
If <strong>name</strong> is "string with spaces", the  output would be "string%20with%20spaces"

- <strong><a name="urldecode">urldecode</a></strong><br />
Decodes URL-encoded string<br />
For example:
<pre>{{ name|urldecode }}</pre>
If <strong>name</strong> is "string%20with%20spaces", the  output would be "string with spaces"

- <strong><a name="urltrunc">urltrunc</a></strong><br />
Truncates a URL if it is longer than the specified number of characters. Truncated url will include with a translatable ellipsis sequence ("...").<br />
For example:
<pre>{{ url|urltrunc:15 }}</pre>
If <strong>url</strong> is "www.github.com/jinnguyen/puja", the output would be 'www.github..../puja'.

- <strong><a name="wordwrap">wordwrap</a></strong><br />
Wraps words at specified line length.<br />
For example:
<pre>{{ value|wordwrap:5 }}</pre>
If value is Joel is a slug, the output would be:
<pre>Joel
is a
slug</pre>
- <strong><a name="yesno">yesno</a></strong><br />
Maps values for true, false and (optionally) None, to the strings “yes”, “no”, “maybe”, or a custom mapping passed as a comma-separated list, and returns one of those strings according to the value:<br />
For example:
<pre>{{ value|yesno:"success,fail" }}</pre>
<table width="100%" >
  <tr>
    <td width="50">Value</td>
    <td width="200">Argument</td>
    <td width="200">Outputs</td>
  </tr>
  <tr>
    <td>true</td>
    <td></td>
    <td>yes</td>
  </tr>
  <tr>
    <td>false</td>
    <td></td>
    <td>no</td>
  </tr>
  <tr>
    <td>true</td>
    <td>success,fail</td>
    <td>success</td>
  </tr>
  <tr>
    <td>false</td>
    <td>success,fail</td>
    <td>fail</td>
  </tr>
</table>






