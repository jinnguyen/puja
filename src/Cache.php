<?php

class PujaCache
{
    /**
     * The folder containt cached files<p>
     * @var string
     */
    public $dircache;
    /**
     * Cache level<p>
     * 0: No cache<p>
     * 1: AUTO update each user modify template
     * 2: NOT update each user modify template, only update when user delete cached file manualy
     * @var int
     */
    public $level;

    public function __construct()
    {

    }

    /**
     * Get cache info: cache file name and cache validate
     * @param string $tpl_name
     * @param int $template_mtime : last modified time file.
     * @return Array('file'=>file_cache, 'valid'=> file cache validate)
     */
    public function get($tpl_name, $template_mtime = 0)
    {
        $tpl_name = str_replace('/', '__slash__', $tpl_name);
        $cache_file = $this->dircache . $tpl_name . '.php';
        $validate = false;

        if (file_exists($cache_file)) {
            switch ($this->level) {
                case 0:
                    $validate = false;
                    break;
                case 1:
                    if ($template_mtime <= filemtime($cache_file)) $validate = true;
                    break;
                case 2:
                    $validate = true;
                    break;
            }
        }

        return array('file' => $cache_file, 'valid' => $validate);
    }

    /**
     * Write cache content to file
     * @param string $cache_file
     * @param string $cache_content
     * @throws PujaException
     */
    public function set($cache_file, $cache_content)
    {
        try {
            $fp = fopen($cache_file, 'w');
        } catch (Exception $e) {
            throw new PujaException('Require permision for folder ' . $this->dircache);
        }

        fwrite($fp, $cache_content);
        fclose($fp);
    }
}