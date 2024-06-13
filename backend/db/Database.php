<?php
use Exception;
use mysqli;
// Datenbankklasse für die Verbindung und Operationen
class Database {
    private $db_host = '';
    private $db_user = '';
    private $db_pass = '';
    private $db_name = '';
    private $con = null;

    // Methode zur Herstellung der Verbindung zur Datenbank
    public function __construct($db_host, $db_user, $db_pass, $db_name) {
       $this->db_host = $db_host;
       $this->db_user = $db_user;
       $this->db_pass = $db_pass;
       $this->db_name = $db_name;
    }
    private function initializeConnection() {
      $this->con = new \mysqli($this->db_host, $this->db_user, $this->db_pass);
      if ($this->con->connect_error) {
          error_log('Connection failed: ' . $this->con->connect_error);
          return false;
      }
      return true;
   }

   private function selectDatabase() {
    if (!mysqli_select_db($this->con, $this->db_name)) {
        error_log('Database selection failed: ' . $this->con->error);
        return false;
    }
    return true;
}

    // Methode zur Herstellung der Verbindung zur Datenbank
    public function connect() {
      if ($this->con === null) {
          if ($this->initializeConnection()) {
              return $this->selectDatabase();
          } else {
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
          $existsInTable = null;
          $stmt->bind_result($existsInTable);
          $stmt->fetch();
          $stmt->close();
  
          // Return true if exists, false otherwise
          return (bool)$existsInTable;
      } else {
          // Throw an exception if query preparation fails
          throw new Exception("Failed to prepare the SQL statement.");
      }
  }
  public function select($table, $columns = '*', $clauses = []) {
    // Ensure connection is established
    if (!$this->connect()) {
        throw new Exception("Failed to connect to database.");
    }

    if (!$this->tableExists($table)) {
        throw new Exception("Table '{$table}' does not exist.");
    }

    $query = $this->buildSelectQuery($table, $columns, $clauses);
    return $this->executeSelectQuery($query);
}

// Метод для построения SELECT запроса
private function buildSelectQuery($table, $columns, $clauses) {
    $query = 'SELECT ' . $columns . ' FROM ' . $table;
    $query = $this->addJoinsToQuery($query, $clauses['joins'] ?? []);
    $query = $this->addWhereToQuery($query, $clauses['where'] ?? null);
    $query = $this->addOrderToQuery($query, $clauses['order'] ?? null);
    return $query;
}

// Метод для добавления JOIN к запросу
private function addJoinsToQuery($query, $joins) {
    foreach ($joins as $join) {
        $query .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['condition'];
    }
    return $query;
}

// Метод для добавления WHERE к запросу
private function addWhereToQuery($query, $where) {
    if ($where != null) {
        $query .= ' WHERE ' . $where;
    }
    return $query;
}

// Метод для добавления ORDER BY к запросу
private function addOrderToQuery($query, $order) {
    if ($order != null) {
        $query .= ' ORDER BY ' . $order;
    }
    return $query;
}

// Метод для выполнения SELECT запроса
private function executeSelectQuery($query) {
    $result = $this->con->query($query);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC); // Все строки как ассоциативный массив
    } else {
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