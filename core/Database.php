<?php namespace Core;

use PDOException;

class Database
{
   private $pdo;
   private $sql = '';

   public function connect()
   {
     
     $host = DB_HOST;
     $db   = DB_DATABASE;
     $user = DB_USERNAME;
     $pwrd = DB_PASSWORD;

      try {
         $pdo = new \PDO("mysql:host=$host; dbname=$db; charset=utf8", $user, $pwrd);
      } catch (PDOException $e) {
         print 'ERROR: ' . $e->getMessage();
      }
      $this->pdo = $pdo;

   }

   public function select($field = '*')
   {
      $this->sql .= 'SELECT ' . $field;
      return $this;
   }

   public function from($table)
   {
      $this->sql .= ' FROM ' . $table;
      return $this;
   }

   public function where($fieldname, $value)
   {
      $this->sql .= ' WHERE ' . $fieldname . ' = ' . "'$value'";
      return $this;
   }

   public function insert($table, $columns, $values)
   {
      $this->sql .= "INSERT INTO $table ($columns) VALUES ($values)";
   }
   public function update($table, $setContent)
   {
      $this->sql .= " UPDATE $table SET $setContent";
      return $this;
   }

   public function getOne()
   {
      $stmt = $this->execute();
      return $stmt->fetchObject();
   }

   public function getAll()
   {
      $stmt = $this->execute();
      $data = [];
      while ($row = $stmt->fetchObject()) {
         $data[] = $row;
      }
      return $data;
   }

   public function execute()
   {
      $this->connect();
      $sql = $this->sql;
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $this->sql = '';
      return $stmt;
   }

}