<?php
// /DatabaseSessionHandler.php

class DatabaseSessionHandler implements SessionHandlerInterface {
    /** @var mysqli */
    private $db;

    /**
     * The constructor receives the existing database connection.
     * @param mysqli $db_connection The mysqli connection object from config.php
     */
    public function __construct(mysqli $db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Called when a session starts.
     */
    public function open($savePath, $sessionName): bool {
        // We already have a DB connection, so we can just return true.
        return true;
    }

    /**
     * Called when a session is closed.
     */
    public function close(): bool {
        // Our persistent connection doesn't need to be closed here.
        return true;
    }

    /**
     * Reads the session data from the database.
     * @param string $id The session ID.
     * @return string The session data.
     */
    public function read($id): string {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->bind_param('s', $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['data'] ?? '';
            }
        }
        return ''; // Return empty string if no session is found
    }

    /**
     * Writes the session data to the database. This is an "UPSERT" (update or insert).
     * @param string $id The session ID.
     * @param string $data The serialized $_SESSION data.
     * @return bool
     */
    public function write($id, $data): bool {
        // Extract user_id from the session data if it exists
        $user_id = null;
        if (!empty($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        // Use REPLACE INTO to perform an "UPSERT" (update or insert)
        $stmt = $this->db->prepare(
            "REPLACE INTO sessions (id, user_id, data, access) VALUES (?, ?, ?, ?)"
        );
        
        $access_time = time(); // Current Unix timestamp
        
        // Bind all four parameters
        $stmt->bind_param('sisi', $id, $user_id, $data, $access_time);
        
        return $stmt->execute();
    }

    /**
     * Destroys a session record (e.g., on logout).
     * @param string $id The session ID.
     * @return bool
     */
    public function destroy($id): bool {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param('s', $id);
        return $stmt->execute();
    }

    /**
     * The garbage collector. Deletes expired sessions.
     * @param int $max_lifetime The session lifetime in seconds from php.ini.
     * @return int|false The number of deleted rows.
     */
    public function gc($max_lifetime): int|false {
        $old_time = time() - $max_lifetime;
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE access < ?");
        $stmt->bind_param('i', $old_time);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}
?>