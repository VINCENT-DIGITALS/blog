<?php

use PhpParser\Node\Stmt;

final class myDB
{
    private $servername  = "localhost";
    private $username  = "root";
    private $password  = "";
    private $db_name  = "blog_db";
    public $res;
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db_name);
        } catch (Exception $e) {
            die("Database connection error!. <br>" . $e);
        }
        // MySQLi connection

    }
    public function __destruct()
    {
        $this->conn->close();
    }
    public function insert($table, $data)
    {
        try {
            $table_columns = implode(',', array_keys($data));
            $prep = $types = "";
            foreach ($data as $key => $value) {
                $prep .= '?,';
                $types .= substr(gettype($value), 0, 1);
            }
            $prep = substr($prep, 0, -1);
            $stmt = $this->conn->prepare("INSERT INTO $table($table_columns) VALUES ($prep)");
            $stmt->bind_param($types, ...array_values($data));
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while inserting data!. <br>" . $e);
        }
    }
    public function select($table, $row = "*", $where = NULL)
    {
        try {
            if (!is_null($where)) {
                $cond = $types = "";
                foreach ($where as $key => $value) {
                    $cond .= $key .= "? AND ";
                    $types .= substr(gettype($value), 0, 1);
                }
                $conn = substr($cond, 0, -4);
                $stmt = $this->conn->prepare("SELECT $row FROM $table WHERE $cond");
                $stmt->bind_param($types, ...array_values($where));
            } else {
                $stmt = $this->conn->prepare("SELECT $row FROM $table");
            }
            $stmt->execute();
            $this->res = $stmt->get_result();
        } catch (Exception $e) {
            die("Error requesting Data!. <br>" . $e);
        }
    }
    public function update($table, $data, $where)
    {
        try {
            $set = $types = $whereCond = "";
            foreach ($data as $key => $value) {
                $set .= $key . " = ?, ";
                $types .= substr(gettype($value), 0, 1);
            }
            $set = rtrim($set, ', ');

            foreach ($where as $key => $value) {
                $whereCond .= $key . " = ? AND ";
                $types .= substr(gettype($value), 0, 1);
            }
            $whereCond = rtrim($whereCond, " AND ");

            $stmt = $this->conn->prepare("UPDATE $table SET $set WHERE $whereCond");
            $stmt->bind_param($types, ...array_merge(array_values($data), array_values($where)));
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while updating data: " . $e->getMessage());
        }
    }

    public function delete($table, $where)
    {
        try {
            $cond = $types = "";
            foreach ($where as $key => $value) {
                $cond .= $key . " = ? AND ";
                $types .= substr(gettype($value), 0, 1);
            }
            $cond = rtrim($cond, " AND "); // Remove the last 'AND'

            $stmt = $this->conn->prepare("DELETE FROM $table WHERE $cond");
            $stmt->bind_param($types, ...array_values($where));
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while deleting data: " . $e->getMessage());
        }
    }
    public function loginUser($table, $email, $password)
    {
        try {
            // Prepare the SQL statement with placeholders
            $select_query = $this->conn->prepare("SELECT * FROM $table WHERE email = ? AND password = ?");

            // Bind the input parameters to the prepared statement
            // "ss" specifies the types of the variables: both are strings here
            $select_query->bind_param("ss", $email, $password);

            // Execute the prepared statement
            $select_query->execute();

            // Get the result
            $result = $select_query->get_result();

            // Check if any row was returned
            if ($result->num_rows > 0) {
                echo "Login Success";
            } else {
                echo "Failed";
            }

            // Close the statement
            $select_query->close();
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error processing request: ' . $e->getMessage()]);
        }
    }
}
