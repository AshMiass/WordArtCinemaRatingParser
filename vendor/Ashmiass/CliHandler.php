<?php
namespace Ashmiass;

use Ashmiass\Parser;
use Ashmiass\ParserDb;

class CliHandler
{
    protected $db;
    protected $base_url = "http://www.world-art.ru/cinema/";
    protected $args;
    protected $dbname;
    protected $root_path;
    protected $pages_for_parsing = [
        "rating_top.php",
        "rating_tv_top.php?public_list_anchor=1",
        "rating_tv_top.php?public_list_anchor=3",
        "rating_bottom.php",
        "rating_tv_top.php?public_list_anchor=4",
        "rating_tv_top.php?public_list_anchor=2"
    ];
    public function __construct($conf, $args, $root_path)
    {

        set_time_limit(0);
        $this->root_path = $root_path;
        $this->args = $args;
        $this->dbname = $conf['connection']['dbname'];
        $this->db = new ParserDb($conf['connection']);
    }

    protected function clearCache($run = false)
    {
        if (!$run) {
            return;
        }
        $temp_dir = $this->root_path . DIRECTORY_SEPARATOR . 'temp';
        foreach (scandir($temp_dir) as $file) {
            $path = $temp_dir.DIRECTORY_SEPARATOR.$file;
            if (!is_file($path) || !file_exists($path) || $file == 'parser.log') {
                continue;
            }
            unlink($path);
        }
        return true;
    }

    /**
     * TODO: make method slim
     */
    public function handle()
    {
        $arg = isset($this->args[1])? $this->args[1] : null;
        if ($arg == '--init-db') {
            return $this->initDb(true, $this->dbname);
        }
        if (in_array($arg, ['--cache-clear', '--cc'])) {
            return $this->clearCache(true);
        }
        $log = $this->root_path . DIRECTORY_SEPARATOR . 'temp' .DIRECTORY_SEPARATOR . 'parser.log';
        $continue = $arg == '--continue';
        $restore_parse_data = [];
        $next_page_url = null;
        $start_page = null;
        if ($continue && file_exists($log)) {
            $restore_parse_data = json_decode(file_get_contents($log), true);
            if ($restore_parse_data && !empty($restore_parse_data['next_page_url'])) {
                $next_page_url = $restore_parse_data['next_page_url'];
                $start_page = $restore_parse_data['start_page'];
            }
        }
        $parser = new Parser($this->base_url, $this->db, true, $log);
        foreach ($this->pages_for_parsing as $page_for_parsing) {
            if ($start_page && $start_page != $page_for_parsing) {
                continue;
            }
            $parser->parse($page_for_parsing, $next_page_url);
        }
        return true;
    }

    public function initDb(bool $run, string $db_name)
    {
        if (!$run) {
            return;
        }
        $sql = file_get_contents('..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR ."init.sql");
        $sql = str_replace('{DB_NAME}', $db_name, $sql);
        $this->db->executeSql($sql);
        return $this->db->getErrorCode();
    }
}
