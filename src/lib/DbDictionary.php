<?php

declare (strict_types=1);

namespace lgdz\lib;

use PDO;

class DbDictionary
{
    private $project_name;
    private $host;
    private $port;
    private $dbname;
    private $username;
    private $password;
    private $pdo;
    private $tables = [];

    /**
     * @param mixed $project_name
     */
    public function setProjectName($project_name): void
    {
        $this->project_name = $project_name;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }

    /**
     * @param mixed $dbname
     */
    public function setDbname($dbname): void
    {
        $this->dbname = $dbname;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    private function pdo()
    {
        if (is_null($this->pdo)) {
            $this->pdo = new PDO("mysql:host={$this->host};port={$this->port};dbname={$this->dbname}", $this->username, $this->password);
        }
        return $this->pdo;
    }

    private function setCharset(string $value = 'utf8')
    {
        $this->pdo()->exec("set names {$value}");
    }

    private function setTables()
    {
        $statement = $this->pdo()->query('show tables');
        if ($statement) {
            $tables = [];
            foreach ($statement->fetchAll() as $row) {
                $tables[]['TABLE_NAME'] = $row[0];
                $this->tables = $tables;
            }
        }
    }

    private function setTableInfo()
    {
        $tables = $this->tables;
        foreach ($tables as $key => $value) {
            $sql = "SELECT * FROM information_schema.TABLES WHERE table_name = '{$value['TABLE_NAME']}' AND table_schema = '{$this->dbname}'";
            $statement = $this->pdo()->query($sql);
            foreach ($statement->fetchAll() as $t) {
                $tables[$key]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
            }
            $sql = "SELECT * FROM information_schema.COLUMNS WHERE table_name = '{$value['TABLE_NAME']}' AND table_schema = '{$this->dbname}'";
            $fields = [];
            $statement = $this->pdo()->query($sql);
            foreach ($statement->fetchAll() as $t) {
                $fields[] = $t;
            }
            $tables[$key]['COLUMN'] = $fields;
        }
        $this->tables = $tables;
    }

    private function closePdo()
    {
        $this->pdo = null;
    }

    private function buildTables()
    {
        $this->setCharset();
        $this->setTables();
        $this->setTableInfo();
        $this->closePdo();
    }

    private function buildHtml(string $save_path)
    {
        ob_start();
        require_once __DIR__ . '/../static/db_dictionary.php';
        file_put_contents($save_path, ob_get_clean());
        $this->tables = [];
    }

    public function build(string $save_path = './db.html'): void
    {
        $this->buildTables();
        $this->buildHtml($save_path);
    }
}