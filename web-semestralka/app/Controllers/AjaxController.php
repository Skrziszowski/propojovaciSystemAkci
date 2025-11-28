<?php
require_once(DIRECTORY_CONTROLLERS . "/IController.php");
require_once(DIRECTORY_MODELS . "/DatabaseModel.php");
require_once(DIRECTORY_CONTROLLERS . "/AuthController.php");

class AjaxController implements IController
{
    private $db;
    private $auth;

    public function __construct()
    {
        $this->db = new DatabaseModel();
        $this->auth = new AuthController();
    }

    public function show(): array
    {
        $user = $this->auth->getLoggedUserData();

        if (!$user) {
            http_response_code(403);
            return ['allUsers' => []];
        }

        $username = trim($_GET['username'] ?? '');
        $email    = trim($_GET['email'] ?? '');
        $role     = trim($_GET['role'] ?? '');

        $allUsers = $this->db->searchUsers($username, $email, $role);


        $userAdminRoles = [];
        if ((int)$user->role === 1) {
            // superAdmin vidí role 2,3,4
            $userAdminRoles = [2, 3, 4];
        } elseif ((int)$user->role === 2) {
            // admin vidí role 3,4
            $userAdminRoles = [3, 4];
        }

        if (!empty($userAdminRoles)) {
            $allUsers = array_values(array_filter(
                $allUsers,
                function ($u) use ($userAdminRoles) {
                    // $u může být objekt (searchUsers vrací FETCH_OBJ)
                    $roleId = isset($u->role) ? (int)$u->role : 0;
                    return in_array($roleId, $userAdminRoles, true);
                }
            ));
        } else {
            $allUsers = [];
        }

        return [
            'allUsers' => $allUsers,
        ];
    }

}
