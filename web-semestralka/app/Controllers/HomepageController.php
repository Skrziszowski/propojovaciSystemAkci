<?php
require_once(DIRECTORY_CONTROLLERS."/IController.php");

class HomepageController implements IController {

    /** @var AuthController $auth  Prihlaseni. */
    private $auth;

    /** @var DatabaseModel $db Databaze */
    private $db;

    public function __construct() {
        require_once (DIRECTORY_MODELS."/DatabaseModel.php");
        $this->db = new DatabaseModel();

        require_once (DIRECTORY_CONTROLLERS ."/AuthController.php");
        $this->auth = new AuthController();
    }

    public function show():array{

        //odhlášení
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->auth->logout();
            header("Location: /home");
            exit;
        }

        //zapomenute heslo
        $this->handleForgotPasswordRequest();
        $this->handleForgotPasswordVerify();
        $this->handleForgotPasswordChange();

        [$flashMessageError, $flashMessageSuccess] = $this->getFlashMessages();

        //user se chce registrovat
        $showRegister = isset($_GET['showRegister']) ? true : false;
        //user se chce prihlasit
        if(isset($_GET['openNav'])){
            $showRegister = false;
        }
        $openNav = isset($_GET['openNav']) ? true : false;

        //overeni registrace
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $flashMessageError = $this->auth->register($_POST['username'],$_POST['password'],$_POST['password2'],$_POST['email'],$_POST['role']);
            $showRegister = false;
        }

        // overeni login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $flashMessageError = $this->auth->login($_POST['email'],$_POST['password']);
        }

        // mozny role
        $roles = $this->db->getRolesFromId(3);
        //nacteni uzivatele
        $user = $this->auth->getLoggedUserData();

        // ---------- PAGINACE EVENTŮ ----------
        $events = $this->db->getEvents();              // VŠECHNY eventy
        $perPage = 12;                                 // kolik karet na „stránku“ (3 řádky * 4 karty)
        $totalPages = max(1, (int)ceil(count($events) / $perPage));
        $currentPage = 1;                              // jen pro initial state v Twigu
        // ---------- /PAGINACE EVENTŮ ----------

        $userDetails        = [];
        $userUpcomingEvents = [];
        $userIdsMap         = [];

// nasbíráme unikátní ID uživatelů z eventů
        foreach ($events as $event) {
            if (!empty($event['user_id'])) {
                $userIdsMap[(int)$event['user_id']] = true;
            }
            if (!empty($event['place_id'])) {
                $userIdsMap[(int)$event['place_id']] = true;
            }
        }

        $userIds = array_keys($userIdsMap);

        if (!empty($userIds)) {
            foreach ($userIds as $uid) {
                $userObj = $this->db->getUserById($uid);
                if ($userObj) {
                    $userDetails[$uid]        = $userObj;
                    $userUpcomingEvents[$uid] = $this->db->getUpcomingEventsForUser($uid, 4);
                }
            }
        }

        $forgotStage = null;
        if (isset($_GET['forgot'])) {
            $forgotStage = $_SESSION['forgot_stage'] ?? 'email';
        }

        //předání dat šabloně
        $data = array(
            "title" => "Úvodní stránka",
            "style" => "homepage",
            "roles" => $roles,
            "user" => $user,
            "showRegister" => $showRegister,
            "openNav" => $openNav,
            'flashMessageError' => $flashMessageError,
            'flashMessageSuccess' => $flashMessageSuccess,

            "events"      => $events,
            "perPage"     => $perPage,
            "totalPages"  => $totalPages,
            "currentPage" => $currentPage,

            'forgotStage' => $forgotStage,
            'userDetails' => $userDetails,
            'userUpcomingEvents' => $userUpcomingEvents
        );

        return $data;
    }

    private function handleForgotPasswordRequest(): void
    {
        if (!isset($_POST['forgotPasswordRequest'])) {
            return;
        }

        $email = trim($_POST['forgot_email'] ?? '');

        if ($email === '') {
            $_SESSION['flash_error']  = "Zadejte e-mail.";
            $_SESSION['forgot_stage'] = 'email';
            return;
        }

        $user = $this->db->findUserByEmail($email);
        if (!$user) {
            $_SESSION['flash_error']  = "Uživatel s tímto e-mailem neexistuje.";
            $_SESSION['forgot_stage'] = 'email';
            return;
        }

        $code = $this->generateResetCode((int)$user->id, (int)$user->role);

        $subject = "Obnovení hesla – Propojovací systém akcí";

        $body  = "Dobrý den,\n\n";
        $body .= "obdrželi jsme žádost o změnu hesla k vašemu účtu.\n\n";
        $body .= "Váš ověřovací kód: {$code}\n\n";
        $body .= "Pokud jste o změnu hesla nežádali, tento e-mail ignorujte.\n\n";
        $body .= "S pozdravem,\nPropojovací systém akcí\n";

        $headers   = [];
        $headers[] = "From: \"Propojovací systém akcí\" <no-reply@propojovacisystemakci.cz>";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";
        $headersStr = implode("\r\n", $headers);

        $ok = mail($user->email, $subject, $body, $headersStr);

        if ($ok) {
            // až teď uložím do session
            $_SESSION['reset_user_id'] = (int)$user->id;
            $_SESSION['reset_email']   = $user->email;
            $_SESSION['reset_code']    = $code;
            $_SESSION['forgot_stage']  = 'code';

            $_SESSION['flash_success'] = "Na váš e-mail jsme odeslali ověřovací kód.";
        } else {
            $_SESSION['flash_error']  = "E-mail se nepodařilo odeslat (zkontrolujte konfiguraci mailu).";
            $_SESSION['forgot_stage'] = 'email';
        }

        // zůstaň na /home?forgot=1
    }

    private function generateResetCode(int $userId, int $role): string
    {
        $first  = random_int(0, 9);        // 1 cifra random
        $idPart = $userId % 100;           // 2 cifry userID (funguje jen do 99)
        $rand   = random_int(0, 99);       // 2 random cifry
        $roleD  = $role % 10;              // 1 cifra role

        return sprintf('%d%02d%02d%d', $first, $idPart, $rand, $roleD);
    }
    private function handleForgotPasswordVerify(): void
    {
        // klik na "Zavřít / zrušit"
        if (isset($_POST['forgotCancel'])) {
            unset(
                $_SESSION['reset_user_id'],
                $_SESSION['reset_email'],
                $_SESSION['reset_code'],
                $_SESSION['reset_verified'],
                $_SESSION['forgot_stage']
            );

            $_SESSION['flash_info'] = "Obnova hesla byla zrušena.";
            header('Location: /home');
            exit;
        }

        if (!isset($_POST['forgotPasswordVerify'])) {
            return;
        }

        $inputCode = trim($_POST['reset_code'] ?? '');
        $sessionCode = $_SESSION['reset_code'] ?? null;

        if ($sessionCode === null) {
            $_SESSION['flash_error'] = "Chybí vygenerovaný kód. Začněte znovu.";
            $_SESSION['forgot_stage'] = 'email';
            return;
        }

        if ($inputCode !== $sessionCode) {
            $_SESSION['flash_error'] = "Neplatný ověřovací kód.";
            $_SESSION['forgot_stage'] = 'code';
            return;
        }

        $_SESSION['reset_verified'] = true;
        $_SESSION['forgot_stage']   = 'new_password';
        $_SESSION['flash_success']  = "Kód byl ověřen, nastavte si nové heslo.";
    }
    private function handleForgotPasswordChange(): void
    {
        if (!isset($_POST['changePassword'])) {
            return;
        }

        if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_user_id'])) {
            $_SESSION['flash_error'] = "Nelze změnit heslo – chybí ověření kódu.";
            $_SESSION['forgot_stage'] = 'email';
            return;
        }

        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($password === '' || $password2 === '') {
            $_SESSION['flash_error'] = "Vyplňte obě pole pro heslo.";
            $_SESSION['forgot_stage'] = 'new_password';
            return;
        }

        if ($password !== $password2) {
            $_SESSION['flash_error'] = "Hesla se neshodují.";
            $_SESSION['forgot_stage'] = 'new_password';
            return;
        }

        $userId = (int)$_SESSION['reset_user_id'];
        $hash   = password_hash($password, PASSWORD_DEFAULT);

        $ok = $this->db->updateUserPassword($userId, $hash);

        if (!$ok) {
            $_SESSION['flash_error'] = "Nepodařilo se uložit nové heslo.";
            $_SESSION['forgot_stage'] = 'new_password';
            return;
        }

        $_SESSION['user_id'] = $userId;

        unset(
            $_SESSION['reset_user_id'],
            $_SESSION['reset_email'],
            $_SESSION['reset_code'],
            $_SESSION['reset_verified'],
            $_SESSION['forgot_stage']
        );

        $_SESSION['flash_success'] = "Heslo bylo úspěšně změněno a jste přihlášen.";
        header("Location: /home");
        exit;
    }

    private function getFlashMessages(): array
    {
        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        return [$error, $success];
    }

}

?>