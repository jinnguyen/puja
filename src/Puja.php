<?php

/**
 * Puja v1.1
 * @author jinnguyen
 * @link http://github.com/jinnguyen/puja
 * @license MIT
 * @example
 * include 'path/to/Autoload.php';<p>
 * $tpl = new Puja;<p>
 * $tpl->cache_dir = 'path/to/cache/dir';<p>
 * $data = array(<p>
 *        'username'=>"Jin ",<p>
 * );<p>
 * $tpl->parse($data,'home.tpl');<p>
 *
 * home.tpl file:<p>
 * Welcome {{ username }}<p>
 */

class Puja
{
    /**
     * @deprecated template_dir is deprecated as of Puja 1.1, and will be removed in the future
     *  Folder includes template files
     * @var string
     *  */
    public $template_dir = null;
    
    /**
     * Check to set disable magic function
     * @var boolean
     */
    public $checkMagicFunc = false;

    /**
     * a list template dirs
     * @var string
     */
    public $template_dirs = array();

    /**
     * Folder includes compiled files
     * @var string
     */
    public $cache_dir;
    /**
     * Cache level<p>
     * 0: Default level. No cache<p>
     * 1: AUTO update each user modify template. REQUIRE configure $cache_dir
     * 2: NOT update each user modify template, only update when user delete cached file manualy. REQUIRE configure $cache_dir.
     * @var int
     */
    public $cache_level;
    /**
     * Type of template compile.
     * - eval: call eval to compile AST.
     * - include: Default value. Create a PHP file from AST and then include it. REQUIRE configure $cache_dir.
     * @var string
     */
    public $parse_executer = 'include';
    /**
     * Custom filter class
     * @var Class object
     */
    public $custom_filter;
    /**
     * Custom tags class
     * @var Class object
     */
    public $custom_tags;
    /**
     * Mode debug
     * - if mode debug = true, enable validate template's syntax [DEVELOP]
     * - if mode debug = false, disable validate template's syntax, [PRODUCTION]
     * @var Boolean
     */
    public $debug = false;
    /**
     * Set common values for template before template parse.
     * @var Array
     */
    public $headers = array();
    /**
     * Consider data is only array, not include object.
     * - if true: Puja don't run object_to_array converter (save time )
     * - if false: Puja run object_to_array converter.
     * @var Boolean
     */
    public $data_only_array = false;
    /**
     * Consider include multi level.
     * true: Default value. Allow include multi level.
     * false: only include 1 level. This option will make faster.
     * */
    public $include_multi_level = true;
    /**
     * Consider include multi extends.
     * true: Default value. Allow extends multi level..
     * false: only include 1 level. This option will make faster.
     * */
    public $extends_multi_level = true;


    function __construct()
    {

    }

    /**
     * Parse template
     * @param string $template_file
     * @param array $data
     * @param boonlean $return_value
     */
    function parse($template_file, $template_data = array(), $return_value = false)
    {
        
        $tpl = new PujaCompiler($this->checkMagicFunc);
        
        if ($this->template_dir !== null) {
            trigger_error('Puja::template_dir is deprecated as of Puja 1.1, and will be removed in the future', E_USER_DEPRECATED);
            array_unshift($this->template_dirs, $this->template_dir);
        }

        $tpl->template_dirs = $this->template_dirs;
        $tpl->cache_dir = $this->cache_dir;
        $tpl->cache_level = $this->cache_level;
        $tpl->parse_executer = $this->parse_executer;
        $tpl->custom_filter = $this->custom_filter;
        $tpl->custom_tags = $this->custom_tags;
        $tpl->debug = $this->debug;
        $tpl->headers = $this->headers;
        $tpl->data_only_array = $this->data_only_array;
        $tpl->include_multi_level = $this->include_multi_level;
        $tpl->extends_multi_level = $this->extends_multi_level;
        $context = $tpl->parse($template_file, $template_data, true);
        if ($return_value) {
            return $context;
        }
        echo $context;
    }

}