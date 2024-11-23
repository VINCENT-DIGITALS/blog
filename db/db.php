<?php

use PhpParser\Node\Stmt;

final class myDB
{
    private $servername  = "localhost";
    private $username  = "root";
    private $password  = "";
    private $db_name  = "blog_db";
    private $userTbl    = 'users';
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
    public function updateLikes($post_id, $update)
    {
        try {
            $stmtupdate = $this->conn->prepare("UPDATE posts SET likes_count = $update WHERE id = ?");
            $stmtupdate->bind_param("i", $post_id);

            if ($stmtupdate->execute()) {
                error_log("Successfully updated likes for post ID: $post_id.");
            } else {
                error_log("Failed to update likes for post ID: $post_id");
            }

            $stmtupdate->close();
        } catch (Exception $e) {
            error_log("Error updating likes count: " . $e->getMessage());
        }
    }



    public function updateLikesCount($postId)
    {
        try {
            // Query to update the likes_count field based on the number of entries in the likes table
            $stmt = $this->conn->prepare("
                UPDATE posts 
                SET likes_count = (SELECT COUNT(*) FROM likes WHERE post_id = ?)
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $postId, $postId);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error updating likes count! <br>" . $e);
        }
    }
    public function getLikesCount($post_id)
    {
        try {
            // Ensure post_id is an integer
            if (!is_numeric($post_id) || intval($post_id) != $post_id) {
                throw new Exception("Invalid post_id provided. It must be an integer.");
            }

            // Cast post_id to integer
            $post_id = intval($post_id);

            // Query to count the number of likes for the post_id
            $stmt = $this->conn->prepare("SELECT COUNT(*) as likes_count FROM likes WHERE post_id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();

            // Fetch the result
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $likes_count = $row['likes_count'];

            $stmt->close();

            return $likes_count;  // Return the number of likes
        } catch (Exception $e) {
            die("Error fetching likes count. <br>" . $e->getMessage());
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

    public function select($table, $row = "*", $where = NULL, $search = NULL)
    {
        try {
            $conditions = [];
            $types = "";

            // Exact matches (WHERE clause)
            if (!is_null($where)) {
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                    $types .= substr(gettype($value), 0, 1);
                }
            }

            // Search (LIKE clause)
            if (!is_null($search)) {
                foreach ($search as $key => $value) {
                    $conditions[] = "$key LIKE ?";
                    $types .= substr(gettype($value), 0, 1);
                    $search[$key] = "%" . $value . "%";
                }
            }

            // Build the query dynamically based on conditions
            $query = "SELECT $row FROM $table";
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $stmt = $this->conn->prepare($query);
            if (!empty($conditions)) {
                $stmt->bind_param($types, ...array_values(array_merge($where ?? [], $search ?? [])));
            }

            $stmt->execute();
            $this->res = $stmt->get_result();
        } catch (Exception $e) {
            die("Error requesting Data!. <br>" . $e);
        }
    }



    // Helper function to check if a record exists
    private function check_if_exists($query, $id)
    {
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }


    public function add_comment($table, $data)
    {
        $comment = trim($data['comment']);
        $postId = $data['post_id'];
        $userId = $data['user_id'];
        // Check if the post_id and user_id exist
        $postExists = $this->check_if_exists("SELECT id FROM posts WHERE id = ?", $postId);
        $userExists = $this->check_if_exists("SELECT id FROM users WHERE id = ?", $userId);

        if (!$postExists) {
            echo json_encode(["success" => false, "error" => "Post does not exist."]);
            return;
        }

        if (!$userExists) {
            echo json_encode(["success" => false, "error" => "User does not exist."]);
            return;
        }
        // Insert the comment into the comments table
        $stmt = $this->conn->prepare("INSERT INTO $table (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $postId, $userId, $comment);

        if ($stmt->execute()) {
            // Get the username of the user who commented
            $stmt = $this->conn->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            echo json_encode([
                "success" => true,
                "username" => $user['username']
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to save comment."]);
        }
    }

    public function fetchComments($table, $postId)
    {
        try {
            $query = "
            SELECT c.comment, c.created_at, u.username 
            FROM $table c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at DESC";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }

            $stmt->bind_param('i', $postId);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }

            $comments = $result->fetch_all(MYSQLI_ASSOC);
            return $comments ?: []; // Ensure empty array if no comments found
        } catch (Exception $e) {
            error_log("Error fetching comments: " . $e->getMessage()); // Log errors to server logs
            return ["error" => $e->getMessage()];
        }
    }


    public function add_user($table, $data)
    {
        try {
            // Prepare the SQL query
            $table_columns = implode(',', array_keys($data));
            $prep = $types = "";
            foreach ($data as $key => $value) {
                $prep .= '?,';  // Prepare placeholders
                $types .= substr(gettype($value), 0, 1);  // Get types for binding
            }
            $prep = rtrim($prep, ',');  // Remove trailing comma

            // Prepare the SQL statement
            $stmt = $this->conn->prepare("INSERT INTO $table($table_columns) VALUES ($prep)");

            // Check if statement preparation was successful
            if (!$stmt) {
                die("Error preparing statement: " . $this->conn->error);
            }

            // Bind parameters
            $stmt->bind_param($types, ...array_values($data));

            // Execute the statement
            $success = $stmt->execute();

            // Check if execution was successful
            if ($success) {
                $stmt->close();
                return true;  // Return true on success
            } else {
                // Output detailed error if execution failed
                die("Error while executing query: " . $stmt->error);
            }
        } catch (Exception $e) {
            die("Error while inserting data!. <br>" . $e->getMessage());
        }
    }

    public function loginUser($table, $row = "*", $where = NULL)
    {
        try {
            if (!is_null($where)) {
                $cond = $types = "";
                foreach ($where as $key => $value) {
                    $cond .= $key . " = ? AND ";
                    $types .= substr(gettype($value), 0, 1);
                }
                $conn = substr($cond, 0, -4); // Remove the last ' AND '
                $query = "SELECT $row FROM $table WHERE $conn"; // Build query
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param($types, ...array_values($where));
            } else {
                $stmt = $this->conn->prepare("SELECT $row FROM $table");
            }

            $stmt->execute();
            $this->res = $stmt->get_result();

            // Check if any row was returned
            if ($this->res->num_rows > 0) {
                return $this->res->fetch_assoc(); // Return the user data
            } else {
                return null; // No user found
            }
        } catch (Exception $e) {
            error_log("Error processing request: " . $e->getMessage());
            return null; // In case of error, return null
        }
    }
    public function email_exists($table, $email)
    {
        try {
            // Prepare a SQL query to check for the email
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM $table WHERE email = ?");

            if (!$stmt) {
                die("Error preparing statement: " . $this->conn->error);
            }

            // Bind the email parameter
            $stmt->bind_param("s", $email);  // 's' is for string

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            // Return true if count is greater than 0 (email exists), false otherwise
            return $count > 0;
        } catch (Exception $e) {
            die("Error while checking email existence. <br>" . $e->getMessage());
        }
    }


    function checkUser($data = array())
    {
        if (!empty($data)) {
            // Check whether the user already exists in the database 
            $checkQuery = "SELECT * FROM " . $this->userTbl . " WHERE oauth_provider = '" . $data['oauth_provider'] . "' AND oauth_uid = '" . $data['oauth_uid'] . "'";
            $checkResult = $this->conn->query($checkQuery);

            // Add modified time to the data array 
            if (!array_key_exists('modified', $data)) {
                $data['modified'] = date("Y-m-d H:i:s");
            }

            if ($checkResult->num_rows > 0) {
                // Prepare column and value format 
                $colvalSet = '';
                $i = 0;
                foreach ($data as $key => $val) {
                    $pre = ($i > 0) ? ', ' : '';
                    $colvalSet .= $pre . $key . "='" . $this->conn->real_escape_string($val) . "'";
                    $i++;
                }
                $whereSql = " WHERE oauth_provider = '" . $data['oauth_provider'] . "' AND oauth_uid = '" . $data['oauth_uid'] . "'";

                // Update user data in the database 
                $query = "UPDATE " . $this->userTbl . " SET " . $colvalSet . $whereSql;
                $update = $this->conn->query($query);
            } else {
                // Add created time to the data array 
                if (!array_key_exists('created', $data)) {
                    $data['created'] = date("Y-m-d H:i:s");
                }

                // Prepare column and value format 
                $columns = $values = '';
                $i = 0;
                foreach ($data as $key => $val) {
                    $pre = ($i > 0) ? ', ' : '';
                    $columns .= $pre . $key;
                    $values  .= $pre . "'" . $this->conn->real_escape_string($val) . "'";
                    $i++;
                }

                // Insert user data in the database 
                $query = "INSERT INTO " . $this->userTbl . " (" . $columns . ") VALUES (" . $values . ")";
                $insert = $this->conn->query($query);
            }

            // Get user data from the database 
            $result = $this->conn->query($checkQuery);
            $userData = $result->fetch_assoc();
        }

        // Return user data 
        return !empty($userData) ? $userData : false;
    }
}
