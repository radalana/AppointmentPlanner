<?php
class Database {
    private $db_host = '';
    private $db_user = '';
    private $db_pass = '';
    private $db_name = '';
    private $con = '';

    public function __construct($db_host, $db_user, $db_pass, $db_name) {
       $this->db_host = $db_host;
       $this->db_user = $db_user;
       $this->db_pass = $db_pass;
       $this->db_name = $db_name;
    }

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

    public function isExists($table, $attribute, $value) {
      // Check if the table exists
      if (!$this->tableExists($table)) {
        throw new Exception("Table '{$table}' does not exist.");
    }

    // Prepare a query using a safe approach to avoid SQL injection
    $query = "SELECT EXISTS (SELECT 1 FROM `$table` WHERE `$attribute` = ?) AS 'exists'";

      // Prepare the statement
      if ($stmt = $this->con->prepare($query)) {
          // Bind the value parameter safely
          $stmt->bind_param('s', $value);
  
          // Execute the query
          $stmt->execute();
  
          // Bind the result variable
          $stmt->bind_result($exists);
  
          // Fetch result
          $stmt->fetch();
  
          // Close the statement
          $stmt->close();
  
          // Return true if exists, false otherwise
          return $exists;
      } else {
          // Throw an exception if query preparation fails
          throw new Exception("Failed to prepare the SQL statement.");
      }
  }
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
    //für prepared-statmenst
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
    public function insert($table, $values, $rows = null) {
      // Check if the table exists
      if (!$this->tableExists($table)) {
          return false; // or throw an Exception if you prefer to handle errors that way
      }
  
      // Start building the INSERT statement
      $insert = 'INSERT INTO ' . $table;
  
      // Append columns if specified
      if ($rows != null) {
          $insert .= ' (' . implode(',', array_map(function ($col) { return "{$col}"; }, explode(',', $rows))) . ')';
      }
  
      // Create placeholders for the values
      $placeholders = array_fill(0, count($values), '?');
      $insert .= ' VALUES (' . implode(',', $placeholders) . ')';
  
      // Prepare the statement
      if ($stmt = $this->con->prepare($insert)) {
          // Dynamically determine types for binding parameters (assuming all are strings here for simplicity)
          $types = $this->determineTypes($values);
          $stmt->bind_param($types, ...$values);
  
          // Execute the statement and check if it was successful
          $execute = $stmt->execute();
          $stmt->close();
  
          return $execute;
      } else {
          // Optionally, you could handle errors more gracefully here
          error_log('MySQL Prepare error: ' . $this->con->error);
          return false;
      }
  }
  
    public function insert2($table, $values, $rows=null){
      if ($this->tableExists($table)) {
        $insert = 'INSERT INTO '.$table;
        if ($rows != null) {
            $insert .= ' ('.$rows.')';
        }
        for ($i = 0; $i < count($values); $i++) {
            $values[$i] = mysqli_real_escape_string($this->con, $values[$i]);
            if (is_string($values[$i])) {
                $values[$i] = '"'.$values[$i].'"';
            }
        }
        $values = implode(',', $values);
        $insert .= ' VALUES ('.$values.')';
        $ins = mysqli_query($this->con, $insert);
        if ($ins) {
            return true;
        } else {
            return false;
        }
    }
    }
    public function delete(){}
    public function update(){}



}