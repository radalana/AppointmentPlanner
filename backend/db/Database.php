<?php
// Datenbankklasse für die Verbindung und Operationen
class Database {
    private $db_host = '';
    private $db_user = '';
    private $db_pass = '';
    private $db_name = '';
    private $con = '';

    // Methode zur Herstellung der Verbindung zur Datenbank
    public function __construct($db_host, $db_user, $db_pass, $db_name) {
       $this->db_host = $db_host;
       $this->db_user = $db_user;
       $this->db_pass = $db_pass;
       $this->db_name = $db_name;
    }

    // Methode zur Herstellung der Verbindung zur Datenbank
    public function connect() {
      if (!$this->con) {
        $this->con = mysqli_connect($this->db_host, $this->db_user, $this->db_pass);
        if ($this->con) {
          $seldb = mysqli_select_db($this->con, $this->db_name);
          if ($seldb) {
            return true;
          } else {
            return false;
          }
        }else {
          return false;
        }
      } else {
        return true;
      }
    }

    // Methode zum Trennen der Verbindung zur Datenbank
    public function disconnect() {
       if ($this->con) {
          if (mysqli_close($this->con)) {
            $this->con = false;
            return true;
          } else {
            return false;
          }
       }
    }

    // Methode zur Überprüfung, ob eine Tabelle vorhanden ist
    private function tableExists($table) {
      $tablesInDb = mysqli_query($this->con, 'SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
      if ($tablesInDb) {
        if (mysqli_num_rows($tablesInDb) == 1) {
          return true;
        } else {
          return false;
        }
      }
    }

    // Methode zur Überprüfung, ob ein Wert in einer Tabelle vorhanden ist
    public function isExists($table, $attribute, $value) {
      // Check if the table exists
      if (!$this->tableExists($table)) {
        throw new Exception("Table '{$table}' does not exist.");
    }

    // Vorbereiten einer Abfrage mit sicherem Ansatz, um SQL-Injektion zu vermeiden
    $query = "SELECT EXISTS (SELECT 1 FROM `$table` WHERE `$attribute` = ?) AS 'exists'";

      // Prepare the statement
      if ($stmt = $this->con->prepare($query)) {
          // Bind the value parameter safely
          $stmt->bind_param('s', $value);
          $stmt->execute();
          $stmt->bind_result($exists);
          $stmt->fetch();
          $stmt->close();
  
          // Return true if exists, false otherwise
          return $exists;
      } else {
          // Throw an exception if query preparation fails
          throw new Exception("Failed to prepare the SQL statement.");
      }
  }

    // Methode zum Auswählen von Daten aus einer Tabelle
    public function select($table, $columns='*', $where = null, $order = null, $joins = []){
            $query = 'SELECT '.$columns.' FROM '.$table;
            foreach($joins as $join) {
              $query .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['condition'];
            }
            if ($where != null) {
              $query .= ' WHERE '. $where;
            }
            if ($order != null) {
              $query .= ' ORDER BY '.$order;
            }
            if ($this->tableExists($table)) {
              $result = $this->con->query($query);
              if ($result) {
                $arrAssoc = $result->fetch_all(MYSQLI_ASSOC);; //all rows as assoc arr
                return $arrAssoc;
              } else {
                return false;
              } 
            } else {//if table does not exist
              return false;
            }
    }

    // Methode zum Bestimmen der Datentypen für prepared statements
    private function determineTypes($values) {
      $types = '';
      foreach ($values as $value) {
          if (is_int($value)) {
              $types .= 'i';  // integer
          } elseif (is_float($value)) {
              $types .= 'd';  // double
          } else {
              $types .= 's';  // string
          }
      }
      return $types;
    }

    // Methode zum Einfügen von Daten in eine Tabelle
    public function insert($table, $values, $rows = null) {
      if (!$this->tableExists($table)) {
          return false;
      }
      //INSERT statement
      $insert = 'INSERT INTO ' . $table;
      if ($rows != null) {
          $insert .= ' (' . implode(',', array_map(function ($col) { return "{$col}"; }, explode(',', $rows))) . ')';
      }
  
      // Create placeholders for the values
      $placeholders = array_fill(0, count($values), '?');
      $insert .= ' VALUES (' . implode(',', $placeholders) . ')';
  
      // Prepare the statement
      if ($stmt = $this->con->prepare($insert)) {
          $types = $this->determineTypes($values);
          $stmt->bind_param($types, ...$values);
  
           // Abfrage ausführen und auf Erfolg prüfen
          $execute = $stmt->execute();
          $stmt->close();
  
          return $execute;
      } else {
          error_log('MySQL Prepare error: ' . $this->con->error);
          return false;
      }
  }
}