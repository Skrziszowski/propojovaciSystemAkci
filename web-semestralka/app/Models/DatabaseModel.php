<?php

class DatabaseModel{
    private $pdo;
    public function  __construct()
    {
        $this->pdo = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $this->pdo->exec("SET NAMES 'utf8';");
    }

    public function userExists(string $email):bool
    {
        $sql = "SELECT 1 FROM " . TABLE_USER . " WHERE email = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    public function addUser(string $username, string $password, string $email, int $role):bool
    {
        $columns = ['username', 'email', 'password', 'role'];
        $values  = [$username, $email, $password, $role];
        return $this->insertIntoTable(TABLE_USER, $columns, $values);
    }

    /**
     * Jednoduche vlozeni do prislusne tabulky.
     *
     * @param string $tableName         Nazev tabulky.
     * @param string $insertStatement   Text s nazvy sloupcu pro insert.
     * @param string $insertValues      Text s hodnotami pro prislusne sloupce.
     * @return bool                     Vlozeno v poradku?
     */
    private function insertIntoTable(string $tableName, array $insertStatement, array $insertValues):bool {
        if (count($insertStatement) !== count($insertValues)) {
            throw new InvalidArgumentException('Počet sloupců a hodnot se neshoduje.');
        }

        $cols= implode(', ', $insertStatement);
        $placeholders = implode(', ', array_fill(0, count($insertValues), '?'));

        $sql = "INSERT INTO {$tableName} ({$cols}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        try {
            return $stmt->execute($insertValues);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getRolesFromId(int $minId = 3): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM roles WHERE id >= :min ORDER BY id ASC");
        $stmt->execute([':min' => $minId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserByEmail(string $email): ?object
    {
        $sql = "SELECT * FROM " . TABLE_USER . " WHERE email = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function getUserData($userID): ?object
    {
        $sql = "SELECT * FROM " . TABLE_USER . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function getCategories(): array
    {
        $sql = "SELECT * FROM " . TABLE_CATEGORY;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserPhoto($id, string $publicPath):bool
    {
        $sql = "UPDATE ".TABLE_USER." SET photoPath = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$publicPath, $id]);
    }

    public function updateUserProfile(int $userId, string $username, ?string $information): bool
    {
        $sql = "UPDATE ".TABLE_USER." SET username = ?, information = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$username, $information, $userId]);
    }

    public function updateUserPassword(int $userId, string $password): bool
    {
        $sql = "UPDATE ".TABLE_USER." SET password = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$password, $userId]);
    }
    public function addEvent(string $name, int $categoryId, int $capacity, string $eventDate, string $information,int $creatorId,?string $photoPath): bool
    {
        $columns = ['name', 'category_id', 'capacity', 'date', 'description', 'user_id','photoPath'];
        $values  = [$name, $categoryId, $capacity, $eventDate, $information, $creatorId,$photoPath];
        return $this->insertIntoTable(TABLE_EVENT, $columns, $values);
    }

    public function getUserEvents(int $userId, bool $byPlace = false): array
    {
        $column = $byPlace ? 'e.place_id' : 'e.user_id';

        $sql = "
            SELECT 
                e.*, 
                c.name AS category_name,
                u.username AS place_name
            FROM " . TABLE_EVENT . " e
            LEFT JOIN " . TABLE_CATEGORY . " c ON e.category_id = c.id
            LEFT JOIN " . TABLE_USER . " u ON e.place_id = u.id
            WHERE {$column} = ?
            ORDER BY e.date DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOfferEvents()
    {
        $sql = "SELECT e.*,  c.name AS category_name, u.username AS creator_name FROM " . TABLE_EVENT . " e
                JOIN " . TABLE_USER . " u ON e.user_id = u.id LEFT JOIN " . TABLE_CATEGORY . " c ON e.category_id = c.id
                WHERE e.place_id IS NULL ORDER BY e.date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEventPlace($placeId, $eventId)
    {
        $sql = "UPDATE ".TABLE_EVENT." SET place_id = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$placeId, $eventId]);
    }

    public function getAdminOfferEvents(): array
    {
        $sql = "SELECT 
                e.*,
                u.username AS creator_name,   -- tvůrce akce
                p.username AS place_name,     -- majitel/provozovatel prostoru
                c.name     AS category_name
            FROM " . TABLE_EVENT . " e
            JOIN " . TABLE_USER . " u 
                ON e.user_id = u.id
            LEFT JOIN " . TABLE_USER . " p 
                ON e.place_id = p.id
            LEFT JOIN " . TABLE_CATEGORY . " c 
                ON e.category_id = c.id
            WHERE e.place_id IS NOT NULL
              AND e.approved = 'čeká se na schválení'
            ORDER BY e.date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEventDecline(int $eventId, string $decline_message)
    {
        $sql = "UPDATE ".TABLE_EVENT." SET message = ?, approved = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$decline_message,'zamítnuto', $eventId]);
    }

    public function updateEventAccept(mixed $eventId)
    {
        $sql = "UPDATE ".TABLE_EVENT." SET approved = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['schváleno', $eventId]);
    }

    public function getAllUsers($roles): array
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        $roles = array_map('intval', $roles);
        $roles = array_values(array_unique($roles));
        if (empty($roles)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $sql = "SELECT 
                u.*,
                r.name AS role_name
            FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_ROLES . " r 
                ON u.role = r.id
            WHERE u.role IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($roles);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUserById(int $userId): bool
    {
        $sql = "DELETE FROM " . TABLE_USER . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId]);
    }
    public function getUserById(int $id)
    {
        $sql = "SELECT * FROM " . TABLE_USER . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getEvents()
    {
        $sql = "SELECT 
            e.*, 
            c.name AS category_name,
            placeUser.username    AS place_name,
            creatorUser.username  AS creator_name,
            creatorUser.photoPath AS creator_photoPath
        FROM " . TABLE_EVENT . " e
        LEFT JOIN " . TABLE_CATEGORY . " c 
            ON e.category_id = c.id
        LEFT JOIN " . TABLE_USER . " placeUser 
            ON e.place_id = placeUser.id
        LEFT JOIN " . TABLE_USER . " creatorUser 
            ON e.user_id = creatorUser.id
        WHERE e.approved = 'schváleno'
          AND e.`date` > NOW()
        ORDER BY e.`date` ASC";


        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveMessage(string $name, string $email, string $message):bool
    {
        $columns = ['name', 'email', 'message'];
        $values  = [$name, $email, $message];
        return $this->insertIntoTable(TABLE_MESSAGE, $columns, $values);
    }

    public function getAllMessages():array
    {
        $sql = "SELECT * FROM " . TABLE_MESSAGE;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessageById(int $id): ?object
    {
        $sql = "SELECT * FROM ".TABLE_MESSAGE." WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        return $row ?: null;
    }

    public function deleteMessage(mixed $messageId)
    {
        $sql = "DELETE FROM " . TABLE_MESSAGE . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$messageId]);
    }

    public function getUsersByRoles(array $roles): array
    {
        if (empty($roles)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $sql = "SELECT id, username, email, role 
            FROM " . TABLE_USER . " 
            WHERE role IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($roles);

        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $rows ?: [];
    }

    public function searchUsers(string $username = '', string $email = '', string $role = ''): array
    {
        $sql = "SELECT u.*, r.name AS role_name
            FROM " . TABLE_USER . " u
            LEFT JOIN " . TABLE_ROLES . " r ON u.role = r.id
            WHERE 1=1";

        $params = [];

        if ($username !== '') {
            $sql .= " AND u.username LIKE :username";
            $params[':username'] = '%' . $username . '%';
        }

        if ($email !== '') {
            $sql .= " AND u.email LIKE :email";
            $params[':email'] = '%' . $email . '%';
        }

        if ($role !== '') {
            $sql .= " AND u.role = :role";
            $params[':role'] = $role;
        }

        $sql .= " ORDER BY u.username ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllRoles(): array
    {
        $sql = "SELECT id, name FROM " . TABLE_ROLES . " ORDER BY name ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getEventById(int $id): ?array
    {
        $sql = "SELECT 
            e.*,
            c.name AS category_name,
            placeUser.username    AS place_name,
            creatorUser.username  AS creator_name,
            creatorUser.photoPath AS creator_photoPath
        FROM " . TABLE_EVENT . " e
        LEFT JOIN " . TABLE_CATEGORY . " c 
            ON e.category_id = c.id
        LEFT JOIN " . TABLE_USER . " placeUser 
            ON e.place_id = placeUser.id
        LEFT JOIN " . TABLE_USER . " creatorUser 
            ON e.user_id = creatorUser.id
        WHERE e.id = :id
        LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Vytvoření nové akce pro REST API – vrací ID nové akce nebo null.
     */
    public function createEvent(
        string $name,
        int $categoryId,
        int $capacity,
        string $eventDate,
        string $description,
        int $creatorId,
        ?int $placeId = null,
        ?string $photoPath = null
    ): ?int {
        $sql = "INSERT INTO " . TABLE_EVENT . " 
            (name, category_id, capacity, date, description, user_id, place_id, photoPath)
            VALUES (:name, :category_id, :capacity, :date, :description, :user_id, :place_id, :photoPath)";

        $stmt = $this->pdo->prepare($sql);

        $ok = $stmt->execute([
            ':name'        => $name,
            ':category_id' => $categoryId,
            ':capacity'    => $capacity,
            ':date'        => $eventDate,
            ':description' => $description,
            ':user_id'     => $creatorId,
            ':place_id'    => $placeId,
            ':photoPath'   => $photoPath,
        ]);

        if (!$ok) {
            return null;
        }

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Základní update akce pro REST API.
     */
    public function updateEvent(
        int $id,
        string $name,
        int $categoryId,
        int $capacity,
        string $eventDate,
        string $description,
        ?int $placeId = null
    ): bool {
        $sql = "UPDATE " . TABLE_EVENT . " 
                SET name = :name,
                    category_id = :category_id,
                    capacity = :capacity,
                    date = :date,
                    description = :description,
                    place_id = :place_id
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name'        => $name,
            ':category_id' => $categoryId,
            ':capacity'    => $capacity,
            ':date'        => $eventDate,
            ':description' => $description,
            ':place_id'    => $placeId,
            ':id'          => $id,
        ]);
    }

    /**
     * Smazání akce podle ID.
     */
    public function deleteEvent(int $id): bool
    {
        $sql = "DELETE FROM " . TABLE_EVENT . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }


    public function getUpcomingEventsForUser(int $userId, int $limit = 4): array
    {
        // pojistka proti nesmyslným hodnotám
        $limit = max(1, (int)$limit);

        $sql = "
        SELECT 
            e.*,
            c.name      AS category_name,
            u.username  AS place_name
        FROM " . TABLE_EVENT . " e
        LEFT JOIN " . TABLE_CATEGORY . " c 
            ON e.category_id = c.id
        LEFT JOIN " . TABLE_USER . " u 
            ON e.place_id = u.id
        WHERE (e.user_id = ? OR e.place_id = ?)
          AND e.approved = 'schváleno'
          AND e.`date` > NOW()
        ORDER BY e.`date` ASC
        LIMIT {$limit}
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadMessagesCountForUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM " . TABLE_MESSAGE;
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }



}


?>
