<?php

include("Cat.php");
Class Cats{
private $conn;

    public function __construct(){

        $userName='restapi';
        $password='restapi';
        $dsn='localhost/xe';
        
        $this->conn = oci_connect($userName, $password, $dsn);
     
        if (!$this->conn) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            echo "Failed to connect to database";
        }
    }

        public function get_all() {
            $response = Array();
            $query = 'SELECT * FROM CATS order by ID ASC';
    
            $query = oci_parse($this->conn, $query);
            oci_execute($query);
    
            while ($row = oci_fetch_assoc($query)) {
                $cat = new cat();
                $cat->id = $row['ID'];
                $cat->name = $row['NAME'];
                $cat->breed = $row['BREED'];
                $cat->sex = $row['SEX'];
                $cat->age = $row['AGE'];
                $cat->colour = $row['COLOUR'];
                
                array_push($response, $cat);
            }
            return $response;
        }
    
        public function search($field, $value) {
            $response = Array();
        if ($field=='id'){
            $filter = 'ID'; 
        }else if ($field=='name'){
            $filter = 'NAME';
        
        }else{
            throw new Exception('Unknown field');
        }           
        $query = 'SELECT * FROM cats WHERE ' . $filter . '=:value';
            $query = oci_parse($this->conn, $query);
            oci_bind_by_name($query, ':value', $value, -1);
            oci_execute($query);

            while ($row = oci_fetch_assoc($query)) {
                $cat = new cat();
                $cat->id = $row['ID'];
                $cat->name = $row['NAME'];
                $cat->breed = $row['BREED'];
                $cat->sex = $row['SEX'];
                $cat->age = $row['AGE'];
                $cat->colour = $row['COLOUR'];
                array_push($response, $cat);
            }
            return $response;
        }
    
        public function delete($id) {
            $query = 'DELETE FROM cats WHERE ID = :id';
            $query = oci_parse($this->conn, $query);
            oci_bind_by_name($query, ':id', $id, -1);
            oci_execute($query);
        }

        public function delete_all() {
            $query='truncate table CATS';
            $query = oci_parse($this->conn, $query);
            oci_execute($query);
        }
    
        public function update($cat) {
            $query = 'UPDATE CATS SET NAME=:name, BREED=:breed, AGE=:age, SEX=:sex, COLOUR=:colour '
                    . 'WHERE ID=:id';
            $query = oci_parse($this->conn, $query);
            oci_bind_by_name($query, ':name', $cat->name);
            oci_bind_by_name($query, ':breed', $cat->breed);
            oci_bind_by_name($query, ':sex', $cat->sex);
            oci_bind_by_name($query, ':age', $cat->age);
            oci_bind_by_name($query, ':colour', $cat->colour);
            oci_bind_by_name($query, ':id', $cat->id);
            oci_execute($query);
        }
    
    
        public function add($cat) {
            $sql = "SELECT ID from(SELECT ID FROM CATS ORDER BY ID DESC)where ROWNUM='1'";
            $result = oci_parse($this->conn, $sql);
            oci_execute($result);
            while(oci_fetch($result))
            $id=oci_result($result,"ID")+1;
            
        
            $query = 'INSERT INTO CATS (ID, NAME, BREED, SEX, AGE, COLOUR) '
                    . 'VALUES(:id,:name,:breed,:sex,:age,:colour)';
            $query = oci_parse($this->conn, $query);
            
            oci_bind_by_name($query, ':id', $id);
            oci_bind_by_name($query, ':name', $cat->name);
            oci_bind_by_name($query, ':breed', $cat->breed);
            oci_bind_by_name($query, ':sex', $cat->sex);
            oci_bind_by_name($query, ':age', $cat->age);
            oci_bind_by_name($query, ':colour', $cat->colour);
           
            oci_execute($query);
        }
    }

?>