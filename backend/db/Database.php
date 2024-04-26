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
    public function insert(){}
    public function delete(){}
    public function update(){}



}