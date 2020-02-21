<?php

require_once JS_PATH_CLASSES.'class-jsport-database.php';

class classJsportDatabaseBase extends classJsportDatabase
{
    public $db = null;

    public function __construct()
    {
        $db_name = 'sportleague';
        $db_user = 'root';
        $db_pass = 'password';
        try {
            $this->db = new PDO('mysql:host=localhost;dbname='.$db_name, $db_user, $db_pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->db;
    }
    public function select($query, $vars = array())
    {
        return $this->query($query, $vars)->fetchAll(PDO::FETCH_OBJ);
    }
    public function insert($query, $vars = array())
    {
        return $this->query($query, $vars);
    }
    public function update($query, $vars = array())
    {
        return $this->query($query, $vars);
    }
    public function delete($query, $vars = array())
    {
        return $this->query($query, $vars);
    }
    public function insertedId()
    {
        return $this->db->lastInsertId();
    }
    public function selectObject($query, $vars = array())
    {
        return $this->query($query, $vars)->fetchObject();
    }
    public function selectValue($query, $vars = array())
    {
        return $this->query($query, $vars)->fetchColumn();
    }
    public function selectColumn($query, $vars = array())
    {
        return $this->query($query, $vars)->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    public function selectArray($query, $vars = array())
    {
        return $this->query($query, $vars)->fetch(PDO::FETCH_ASSOC);
    }
    public function selectKeyPair($query, $vars = array())
    {
        return $this->query($query, $vars)->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private function query($query, $args = array())
    {
        $sth = $this->db->prepare($query);
        if (!is_array($args)) {
            $args = explode(',', $args);
        }
        $sth->execute($args);

        return $sth;
    }
}
