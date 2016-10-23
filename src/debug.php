<?php

/**
 * Puja Debug Mode<p>
 * Should anable on dev and disable in production<p>
 *
 *
 * */
class PujaDebug
{

    public $content;
    public $tpl_file;
    public $tpl_dirs = array();
    public $operators;
    public $nodelist = array();
    public $custom_tags;

    public $debug_html;

    private $extends_count = 0;
    public $error = array(
        'wrong_php_variable' => 'Wrong PHP variable name',
        'wrong_operator' => 'Wrong operator in begin',

        'multi_extends' => "'extends' cannot appear more than once in the same template",
        'invalid_block_tag' => "Invalid block tag: '{tag}'",
        'invalid_block_tag_and_expected_if_tags' => "Invalid block tag: '{tag}', expected  'elseif', 'else' or 'endif'",
        'invalid_block_tag_and_expected_endif' => "Invalid block tag: '{tag}', expected  'endif'",
        'invalid_block_tag_and_expected_for_tags' => "Invalid block tag: '{tag}', expected  'empty' or 'endfor'",
        'invalid_block_tag_and_expected_endfor' => "Invalid block tag: '{tag}', expected  'endfor'",
        'invalid_block_tag_and_expected_endblock' => "Invalid block tag: '{tag}', expected  'endblock'",
        'expected_tag' => "Expected '{tag}'",
        'wrong_syntax' => 'wrong syntax',
        'dont_support_tag' => "Tag '{tag}' dont support on this version!",
        'suggest_for' => "Invalid tag: 'for', sample: {% for rs in news %} or {% for key,value in news %}",
        'suggest_tag_without_arg' => "Invalid tag: '{tag}', sample: {% {tag} %}",
        'suggest_with_arg' => "Invalid tag: '{tag}', sample {% {tag} variable %}",
        'suggest_include_get_file' => "Invalid tag: '{tag}', sample {% {tag} filename.tpl %}",
        'check_file_not_exists' => "File '{arg}' doesnt exist in folders: [{tpl_dirs}]",
        'not_support_multi_arg' => "'{tag}' takes one argument",
        'not_support_multi_file' => "'{tag}' takes one file",


    );

    public function __construct()
    {
    }

    private function serialize_mark($content, $reverse = false)
    {
        $search_arr = array('{#', '{$', '{{', '{%');
        $replay_arr = array('[:temp_lpuja_comment:]', '[:temp_lpuja_specialvar:]', '[:temp_lpuja_variable:]', '[:temp_lpuja_percent:]');
        if ($reverse) {
            $content = str_replace($replay_arr, $search_arr, $content);
        } else {
            $content = str_replace($search_arr, $replay_arr, $content);
        }

        return $content;
    }

    private function trace_bug($tag, $error_type, $error_tag = null, $arg = null)
    {

        if (!$this->debug_html) {
            $this->debug_html = file_get_contents('debug.html', true);
        }

        $content = htmlentities($this->content, NULL, 'utf-8');

        $content = preg_replace('/' . $tag . '/', '<span class="__template__engine_current_line__ current_bug">' . $this->serialize_mark($tag) . '</span>', $content, 1);
        $content = $this->serialize_mark($content, true);

        $lines = explode("\n", $content);
        $bug_content = '';
        $current_line = count($lines);
        foreach ($lines as $line => $val) {
            $bug_content .= '<li class="line_' . ($line + 1) . '">' . $val . "</li>";
            if (strpos($val, '__template__engine_current_line__')) $current_line = $line + 1;

        }

        $error_message = str_replace(
            array('{tag}', '{arg}', '{tpl_dirs}'),
            array(
                $error_tag ? $error_tag : $tag,
                $arg,
                '<br /> - ' . implode('<br /> - ', $this->tpl_dirs) . '<br />'
            ),
            $this->error[$error_type]
        );

        echo str_replace(array('{{tpl_file}}', '{{error_body}}', '{{line}}', '{{error_message}}'), array($this->tpl_file, $bug_content, $current_line, $error_message), $this->debug_html);
        exit();
    }

    private function valid_file_exists($file)
    {
        if (!count($this->tpl_dirs)) return false;
        foreach ($this->tpl_dirs as $dir) {
            if (file_exists($dir . $file)) return true;
        }
        return false;
    }

    private function valid_syntax_detail($tag, $full_tag, $arg)
    {
        switch ($tag) {
            case 'for':
                if (!$arg) $this->trace_bug($full_tag, 'suggest_' . $tag);
                if (!preg_match('/\s+([a-z0-9\_]+?)\s+in\s+([a-z0-9\_\.]+?)\s+/i', ' ' . $arg . ' ')
                    && !preg_match('/\s+([a-z0-9\_]+?)\s*,\s*([a-z0-9\_]+?)\s+in\s+([a-z0-9\_\.]+?)\s+/i', ' ' . $arg . ' ')
                ) $this->trace_bug($full_tag, 'suggest_' . $tag);
                break;
            case 'empty':
            case 'endfor':
            case 'else':
            case 'endif':
                if ($arg) $this->trace_bug($full_tag, 'suggest_tag_without_arg', $tag);
                break;
            case 'if':
            case 'elseif':
                if (!$arg) $this->trace_bug($full_tag, 'suggest_with_arg', $tag);
                break;
            case 'include':
            case 'get_file':
                if ($tag == 'get_file' && substr($arg, -6) == 'escape') $arg = trim(substr($arg, 0, -6));
                if (!$arg) $this->trace_bug($full_tag, 'suggest_with_arg', $tag);
                
                if (strpos($arg, ' ')) {
                    if ($tag == 'get_file') $this->trace_bug($full_tag, 'not_support_multi_file', $tag);
                    else {
                        $args = explode(' ', $arg);
                        $arg = $args[0];
                    }
                }
                $check = $this->valid_file_exists($arg);
                if (!$check) $this->trace_bug($full_tag, 'check_file_not_exists', $tag, $arg);
                break;
            case 'print':
                if (!$arg) $this->trace_bug($full_tag, 'suggest_with_arg', $tag);
                if (strpos($arg, ' ')) $this->trace_bug($full_tag, 'not_support_multi_arg', $tag);
                break;
            case 'extends':
                if ($this->extends_count) $this->trace_bug($full_tag, 'multi_extends', $tag);
                if (!$arg) $this->trace_bug($full_tag, 'suggest_with_arg', $tag);
                if (strpos($arg, ' ')) $this->trace_bug($full_tag, 'not_support_multi_arg', $tag);
                $check = $this->valid_file_exists($arg);
                if (!$check) $this->trace_bug($full_tag, 'check_file_not_exists', $tag, $arg);
                $this->extends_count += 1;
                break;

            case 'set':
                if (!$arg) $this->trace_bug($full_tag, 'suggest_with_arg', $tag);
                break;
        }
    }

    public function valid_syntax()
    {
        $start = microtime();
        preg_match_all('/\{\#\s*(.*?)\s*\#\}/i', $this->content, $matches);

        if (count($matches[0])) {
            foreach ($matches[0] as $key => $val) $matches[1][$key] = $this->serialize_mark($val);
            $this->content = str_replace($matches[0], $matches[1], $this->content);
        }

        //Validate end tag
        preg_match_all('/\{\{\s([a-z0-9\|\.]*?)\s(.*?)(\{\{|\n)/i', $this->content, $matches);
        if ($matches[0]) {
            $index_all = 0;
            foreach ($matches[0] as $index => $tag) {
                $this->nodelist['variable_end_tag']['tag'][$index_all] = array('name' => $tag, 'index' => $index_all, 'arg' => $matches[2][$index]);
                $this->nodelist['variable_end_tag']['tag_full'][$index_all] = $matches[0][$index];
                $index_all++;
                $tag = str_replace("\n", "", $tag);
                if (!strpos($tag, '}}')) {
                    $this->trace_bug($tag, 'wrong_syntax', 'missing_end_variable');
                }
            }
        }


        //validate variable
        preg_match_all('/\{\{\s*(.*?)\s*\}\}/i', $this->content, $matches);
        if ($matches[0]) {
            $index_all = 0;
            foreach ($matches[0] as $index => $tag) {
                $this->nodelist['variable']['tag'][$index_all] = array('name' => $tag, 'index' => $index_all, 'arg' => $matches[1][$index]);
                $this->nodelist['variable']['tag_full'][$index_all] = $matches[0][$index];
                $index_all++;
            }
        }

        // validate control structors
        preg_match_all('/\{\%\s*([a-z0-9\_]*?)\s(.*?)\%\}/i', $this->content, $matches);
        if (count($matches[1])) {
            $levels = array();
            $expect_ends = array('if' => 'endif', 'for' => 'endfor', 'block' => 'endblock');
            $expect_ends_flip = array_flip($expect_ends);
            $builtin_tags = array('extends', 'include', 'set', 'get_file', 'block', 'endblock', 'if', 'elseif', 'else', 'endif', 'for', 'empty', 'endfor', 'print');

            if ($this->custom_tags) {
                $builtin_tags = array_merge($builtin_tags, get_class_methods($this->custom_tags));
            }
            foreach ($matches[1] as $key => $tag) {

                $arg = trim($matches[2][$key]);
                $full_match = $matches[0][$key];

                if (!in_array($tag, $builtin_tags)) {
                    $this->trace_bug($full_match, 'dont_support_tag', $tag);
                }
                if (in_array($tag, array('if', 'for', 'block'))) {
                    $len = count($levels);
                    $levels[$len] = array($tag);

                } elseif (in_array($tag, array('elseif', 'else', 'empty'))) {
                    $len = count($levels) - 1;
                    if ($levels[$len][0] == 'block') {
                        $this->trace_bug($full_match, 'invalid_block_tag_and_expected_endblock');
                    } elseif ($levels[$len][0] == 'if') {
                        if ($tag == 'empty') $this->trace_bug($full_match, 'invalid_block_tag_and_expected_if_tags');
                        $lensub = count($levels[$len]) - 1;
                        if ($levels[$len][$lensub] == 'else') $this->trace_bug($full_match, 'invalid_block_tag_and_expected_endif');
                    } elseif ($levels[$len][0] == 'for') {
                        if (in_array($tag, array('else', 'elseif'))) $this->trace_bug($full_match, 'invalid_block_tag_and_expected_for_tags');
                        $lensub = count($levels[$len]) - 1;
                        if ($levels[$len][$lensub] == 'empty') $this->trace_bug($full_match, 'invalid_block_tag_and_expected_endfor');
                    }
                    $levels[$len][] = $tag;
                } elseif (in_array($tag, array('endif', 'endfor', 'endblock'))) {
                    $len = count($levels) - 1;
                    if ($len < 0 || $levels[$len][0] != $expect_ends_flip[$tag]) $this->trace_bug($full_match, 'invalid_block_tag', $expect_ends[$levels[$len][0]]);
                    unset($levels[$len]);
                }

                $this->valid_syntax_detail($tag, $matches[0][$key], $arg);
                $this->content = preg_replace('#' . preg_quote(str_replace('/', '\/', $full_match)) . '#i', $this->serialize_mark($full_match), $this->content, 1);
            }

            while (count($levels)) {
                $len = count($levels) - 1;
                $last_item = $levels[$len];
                $this->trace_bug($last_item[0], 'expected_tag', $expect_ends[$last_item[0]]);
            }
        }
    }
}