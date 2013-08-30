User's guide
========
<pre>
&lt;?php
  
  class Puja{
    /**
    Folder includes template files
    */
    var $tpl_dir; 
    
    /**
    Folder includes php conpiled files 
    */
    var $compile_dir;
    
    /**
      Type of template compile. Allow below values:
      - include: template engine build file php from AST and include it again to run. This is default.
        This type will require set $compile_dir to build php file.
      - eval: template engine run "eval" to execute AST ( Abstract String Template)
        This type not require $compile_dir.
    */
    var $executer = 'include'; // include or eval
    var $customer_tag; // customer tag object or class
    var $customer_filter; // customer_filter object or class
    var $only_data_array = false; //
    var $include_multiable = true;
    var $extends_multiable = true;
    
    /**
    Parse $data  to $tpl_file and display.
    */
    function parse($data = array(), $tpl_file = null, $content_type = 'text/html'){}
  }
  
</pre>
