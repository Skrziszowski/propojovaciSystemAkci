<?php

require_once(DIRECTORY_CONTROLLERS."/IController.php");
class OtherController implements IController
{
    private $db;
    private $session;
    private $auth;

    public function __construct() {
        require_once(DIRECTORY_MODELS."/DatabaseModel.php");
        $this->db   = new DatabaseModel();
        require_once(DIRECTORY_MODELS."/SessionModel.php");
        $this->session = new SessionModel();
        require_once(DIRECTORY_CONTROLLERS."/AuthController.php");
        $this->auth = new AuthController();
    }

    public function show():array
    {
        $sub = $_GET['sub'] ?? 'about';
        switch ($sub) {
            case 'about':
                $title = 'O nás';
                break;
            case 'project':
                $title = 'O projektu';
                break;
            case 'contact':
                $title = 'Kontakt';
                break;
            default:
                $title = 'Informace';
                $sub = 'about';
                break;
        }

        $this->handleActions();

        // overeni login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $_SESSION['flash_error'] = $this->auth->login($_POST['email'],$_POST['password']);
        }

        //odhlášení
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->auth->logout();
            header("Location: /home");
            exit;
        }

        [$flashMessageError, $flashMessageSuccess] = $this->getFlashMessages();
        $user = $this->auth->getLoggedUserData();
        return [
            'style'               => 'otherPages',
            'title'               => $title,
            'sub'                 => $sub,
            'user'                => $user,
            'flashMessageError'   => $flashMessageError,
            'flashMessageSuccess' => $flashMessageSuccess,
        ];
    }

    private function handleActions():void
    {
        $this->handleSendContactMessage();
    }
    private function getFlashMessages(): array
    {
        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        return [$error, $success];
    }

    private function handleSendContactMessage()
    {
        if (!isset($_POST['sendContactEmail'])) {
            return;
        }
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $ok = $this->db->saveMessage($name, $email, $message);
        if ($ok) {
            $_SESSION['flash_success'] = "Zpráva se odeslala.";
        } else {
            $_SESSION['flash_error'] = "Zprávu se nepodařilo odeslat.";
        }
        header("Location: /other/contact");
        exit;
    }

}
