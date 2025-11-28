<?php

require_once(DIRECTORY_CONTROLLERS . "/IController.php");
require_once(DIRECTORY_MODELS . "/DatabaseModel.php");

class ApiController implements IController
{
    private DatabaseModel $db;

    public function __construct()
    {
        $this->db = new DatabaseModel();
    }

    public function show(): array
    {
        header('Content-Type: application/json; charset=utf-8');

        $method = $_SERVER['REQUEST_METHOD'];      // GET / POST / PUT / DELETE
        $sub    = $_GET['sub'] ?? '';             // např. "events" nebo "events/5"

        $parts = explode('/', trim($sub, '/'));   // "events/5" → ["events", "5"]
        $resource = $parts[0] ?? '';
        $id       = isset($parts[1]) ? (int)$parts[1] : null;

        if ($resource === 'events') {
            switch ($method) {
                case 'GET':
                    if ($id === null) {
                        return $this->getEvents();
                    } else {
                        return $this->getEventDetail($id);
                    }

                case 'POST':
                    return $this->createEvent();

                case 'PUT':
                case 'PATCH':
                    if ($id === null) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing event id']);
                        exit;
                    }
                    return $this->updateEvent($id);

                case 'DELETE':
                    if ($id === null) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing event id']);
                        exit;
                    }
                    return $this->deleteEvent($id);

                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
                    exit;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Unknown resource']);
        exit;
    }

    private function getEvents(): array
    {
        $events = $this->db->getEvents(); // tvoje existující metoda
        echo json_encode(['status' => 'ok', 'data' => $events]);
        exit;
    }

    private function getEventDetail(int $id): array
    {
        $event = $this->db->getEventById($id);
        if (!$event) {
            http_response_code(404);
            echo json_encode(['error' => 'Event not found']);
            exit;
        }
        echo json_encode(['status' => 'ok', 'data' => $event]);
        exit;
    }

    private function createEvent(): array
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $name       = trim($input['name'] ?? '');
        $categoryId = isset($input['category_id']) ? (int)$input['category_id'] : 0;
        $capacity   = isset($input['capacity']) ? (int)$input['capacity'] : 0;
        $date       = trim($input['date'] ?? '');
        $description= trim($input['description'] ?? '');
        $creatorId  = isset($input['user_id']) ? (int)$input['user_id'] : 0;
        $placeId    = isset($input['place_id']) ? (int)$input['place_id'] : null;

        // hodně jednoduchá validace
        if ($name === '' || $categoryId <= 0 || $date === '' || $creatorId <= 0) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'error'  => 'Missing or invalid fields (name, category_id, date, user_id)'
            ]);
            exit;
        }

        $newId = $this->db->createEvent(
            $name,
            $categoryId,
            $capacity,
            $date,
            $description,
            $creatorId,
            $placeId,
            null
        );

        if (!$newId) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'error' => 'Failed to create event']);
            exit;
        }

        http_response_code(201);
        header('Location: /api/events/' . $newId);

        echo json_encode([
            'status' => 'ok',
            'id'     => $newId
        ]);
        exit;
    }


    private function updateEvent(int $id): array
    {
        $existing = $this->db->getEventById($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'error' => 'Event not found']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $name       = isset($input['name']) ? trim($input['name']) : $existing['name'];
        $categoryId = isset($input['category_id']) ? (int)$input['category_id'] : (int)$existing['category_id'];
        $capacity   = isset($input['capacity']) ? (int)$input['capacity'] : (int)$existing['capacity'];
        $date       = isset($input['date']) ? trim($input['date']) : $existing['date'];
        $description= isset($input['description']) ? trim($input['description']) : $existing['description'];

        $placeId = array_key_exists('place_id', $input)
            ? ($input['place_id'] !== null ? (int)$input['place_id'] : null)
            : $existing['place_id'];

        $ok = $this->db->updateEvent(
            $id,
            $name,
            $categoryId,
            $capacity,
            $date,
            $description,
            $placeId
        );

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'error' => 'Failed to update event']);
            exit;
        }

        echo json_encode(['status' => 'ok']);
        exit;
    }


    private function deleteEvent(int $id): array
    {
        $ok = $this->db->deleteEvent($id);

        if (!$ok) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'error' => 'Failed to delete event']);
            exit;
        }

        echo json_encode(['status' => 'ok']);
        exit;
    }

}
