<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once(DIRECTORY_CONTROLLERS."/IController.php");

class ProfileController implements IController {
    private $auth;
    private $db;

    public function __construct() {
        require_once(DIRECTORY_CONTROLLERS."/AuthController.php");
        require_once(DIRECTORY_MODELS."/DatabaseModel.php");

        $this->auth = new AuthController();
        $this->db   = new DatabaseModel();
    }

    public function show():array
    {
        $user = $this->auth->getLoggedUserData();

        $this->handleActions($user);

        [$flashMessageError, $flashMessageSuccess] = $this->getFlashMessages();

        $permissionError = $this->auth->requireRoles([1, 2, 3, 4]);
        if ($permissionError) {
            return [
                'title' => 'Profil uživatele',
                'style' => 'homepage',
                'user' => $user,
                'permissionError' => $permissionError,
                'flashMessageError' => $flashMessageError,
                'flashMessageSuccess' => $flashMessageSuccess,
            ];
        }

        $eventCategory = $this->db->getCategories();
        $events = $this->getEventsForUser($user);
        $offerEvents = $this->db->getOfferEvents();
        $adminOfferEvents = $this->db->getAdminOfferEvents();

        $userAdminRoles = [];

        if ((int)$user->role === 1) {
            $userAdminRoles = [2, 3, 4];
        } elseif ((int)$user->role === 2) {
            $userAdminRoles = [3, 4];
        }

        $allUsers = !empty($userAdminRoles)
            ? $this->db->getAllUsers($userAdminRoles)
            : [];

        $messages = $this->db->getAllMessages();

        $roles = $this->db->getAllRoles();

        if ($user && (int)$user->role === 2) {
            $roles = array_values(array_filter(
                $roles,
                fn($r) => (int)$r->id !== 1
            ));
        }


        //can See
        $canSeeUserEvents = in_array($user->role, [3,4]);
        $canSeeEventCheck = in_array($user->role, [1,2]);
        $canSeeEventOffer = ($user->role == 4);
        $canSeeUserAdministration = in_array($user->role, [1, 2]);
        $canSeeChangePasswordForm = in_array($user->role, [1,2,3,4]);
        $canSeeEventForm = ($user->role == 3);
        $canSeeProfileForm = in_array($user->role, [1,2,3,4]);
        $canSeeMessages = in_array($user->role, [1,2]);
        $canSeeSuperEvent = ($user->role == 1);
        $canSeeUserCreate = ($user->role == 1);

        $base = [
            'title' => 'Profil uživatele',
            'style' => 'homepage',
        ];

        return $base + compact(
                'user',
                'permissionError',
                'flashMessageError',
                'flashMessageSuccess',
                'eventCategory',
                'events',
                'offerEvents',
                'adminOfferEvents',
                'allUsers',
                'canSeeUserEvents',
                'canSeeEventCheck',
                'canSeeEventOffer',
                'canSeeUserAdministration',
                'canSeeChangePasswordForm',
                'canSeeEventForm',
                'canSeeProfileForm',
                'canSeeMessages',
                'messages',
                'canSeeSuperEvent',
                'roles',
                'canSeeUserCreate'
        );
    }

    private function handleActions($user):void
    {
        $this->handleLogout();
        $this->handleUploadPhoto($user);
        $this->handleSaveProfile($user);
        $this->handleChangePassword($user);
        $this->handleCreateEvent($user);
        $this->handleAcceptEvent($user);
        $this->handleAdminOfferDecline();
        $this->handleAdminOfferAccept();
        $this->handleDeleteUser();
        $this->handleMessageReply($user);
        $this->handleSendInfoEmail($user);
        $this->handleCreateAdmin($user);

    }

    private function getFlashMessages(): array
    {
        $error   = $_SESSION['flash_error']   ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        return [$error, $success];
    }
    private function handleLogout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            $this->auth->logout();
            header("Location: /home");
            exit;
        }
    }
    private function handleUploadPhoto($user): void
    {
        if (!isset($_POST['uploadPhoto']) || empty($_FILES['photo']['name'])) {
            return;
        }

        $oldPath   = $user->photoPath;
        $publicPath = $this->uploadImageGeneric('photo', 'users', 'user_' . $user->id . '_');

        $this->db->updateUserPhoto($user->id, $publicPath);
        $_SESSION['flash_success'] = "Fotografie byla úspěšně nahrána.";

        if (!empty($oldPath)) {
            $oldFull = dirname(__DIR__, 2) . "/www/img/users/" . basename($oldPath);
            if (file_exists($oldFull)) {
                unlink($oldFull);
            }
        }

        $this->redirectProfile();
    }
    private function handleSaveProfile($user): void
    {
        if (!isset($_POST['saveProfile'])) {
            return;
        }
        $username    = trim($_POST['username']    ?? '');
        $information = trim($_POST['information'] ?? '');

        if ($username === '') {
            $_SESSION['flash_error'] = "Uživatelské jméno musí být vyplněno.";
            $this->redirectProfile();
        }
        $ok = $this->db->updateUserProfile($user->id, $username, $information);
        if ($ok) {
            $_SESSION['flash_success'] = "Profil byl úspěšně aktualizován.";
        } else {
            $_SESSION['flash_error'] = "Nepodařilo se uložit změny v profilu.";
        }
        $this->redirectProfile();
    }

    private function handleChangePassword($user): void
    {
        if (!isset($_POST['changePassword'])) {
            return;
        }
        $password  = trim($_POST['password']  ?? '');
        $password2 = trim($_POST['password2'] ?? '');

        if ($password === '' || $password2 === '' || $password !== $password2) {
            $_SESSION['flash_error'] = "Hesla se neshodují nebo nejsou vyplněna.";
            $this->redirectProfile();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ok   = $this->db->updateUserPassword($user->id, $hash);
        if ($ok) {
            $_SESSION['flash_success'] = "Heslo bylo úspěšně aktualizováno.";
        } else {
            $_SESSION['flash_error'] = "Nepodařilo se uložit nové heslo v profilu.";
        }
        $this->redirectProfile();
    }

    private function handleCreateEvent($user): void
    {
        if (!isset($_POST['createEvent'])) {
            return;
        }
        $errors = [];
        $eventName = trim($_POST['eventName'] ?? '');
        if ($eventName === '') {
            $errors[] = "Název akce je povinný.";
        }

        $categoryId = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
        if ($categoryId === false || $categoryId === null) {
            $errors[] = "Vyberte kategorii.";
        }

        $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
        if ($capacity === false || $capacity < 0) {
            $errors[] = "Kapacita musí být nezáporné celé číslo.";
        }

        $dateRaw  = $_POST['date'] ?? '';
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $dateRaw);
        if (!$dateTime) {
            $errors[] = "Neplatný formát data a času.";
        }

        $information = trim($_POST['information'] ?? '');

        $eventPhotoPath = null;
        if (!empty($_FILES['event_photo']['name'])) {
            $eventPhotoPath = $this->uploadImageGeneric(
                'event_photo',
                'events',
                'event_' . $user->id . '_'
            );
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            $this->redirectProfile();
        }

        $eventDate = $dateTime->format('Y-m-d H:i:s');
        $creatorId = $user->id;

        $ok = $this->db->addEvent(
            $eventName,
            $categoryId,
            $capacity,
            $eventDate,
            $information,
            $creatorId,
            $eventPhotoPath
        );

        if ($ok) {
            $_SESSION['flash_success'] = "Akce byla úspěšně založena.";
        } else {
            $_SESSION['flash_error'] = "Při ukládání akce došlo k chybě.";
        }

        $this->redirectProfile();
    }

    private function redirectProfile(?string $panelId = null): void
    {
        $url = '/profile';

        if ($panelId !== null) {
            $url .= '?tab=' . urlencode($panelId);
        }

        header('Location: ' . $url);
        exit;
    }
    private function uploadImageGeneric(string $inputName, string $subDir, string $prefix): ?string
    {
        if (empty($_FILES[$inputName]['name'])) {
            return null;
        }

        $file    = $_FILES[$inputName];
        $maxSize = 2 * 1024 * 1024; // 2 MB

        if ($file['size'] > $maxSize) {
            $_SESSION['flash_error'] = "Soubor je příliš velký. Maximální velikost je 2 MB.";
            $this->redirectProfile();
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Nahrání souboru selhalo.";
            $this->redirectProfile();
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['flash_error'] = "Nepovolený typ souboru. Použijte JPG/PNG/WEBP.";
            $this->redirectProfile();
        }

        $uploadDir = dirname(__DIR__, 2) . "/www/img/{$subDir}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = $prefix . time() . "." . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['flash_error'] = "Nepodařilo se uložit soubor.";
            $this->redirectProfile();
        }

        return "/www/img/{$subDir}/" . $newFileName;
    }

    private function handleAcceptEvent($user): void
    {
        if (!isset($_POST['acceptEvent'])) {
            return;
        }
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($eventId === false || $eventId === null) {
            $_SESSION['flash_error'] = "Neplatné ID akce.";
            $this->redirectProfile('panel-offerEvents');
        }
        $placeId = $user->id;
        $ok = $this->db->updateEventPlace($placeId, $eventId);
        if ($ok) {
            $_SESSION['flash_success'] = "Akce se přidělila na vaše místo.";
        } else {
            $_SESSION['flash_error'] = "Při ukládání akce došlo k chybě.";
        }
        $this->redirectProfile('panel-offerEvents');
    }

    private function handleAdminOfferDecline(): void
    {
        if (!isset($_POST['declineEvent'])) {
            return;
        }
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($eventId === false || $eventId === null) {
            $_SESSION['flash_error'] = "Neplatné ID akce.";
            $this->redirectProfile();
        }
        $declineMessage = trim($_POST['decline_message'] ?? '');
        if ($declineMessage === '') {
            $_SESSION['flash_error'] = "Důvod zamítnutí nesmí být prázdný.";
            $this->redirectProfile('panel-adminOfferEvents');
        }
        $ok = $this->db->updateEventDecline($eventId, $declineMessage);
        if ($ok) {
            $_SESSION['flash_success'] = "Zamítnutí bylo odesláno.";
        } else {
            $_SESSION['flash_error'] = "Zamítnutí se nezdařilo.";
        }
        $this->redirectProfile('panel-adminOfferEvents');
    }

    private function handleAdminOfferAccept()
    {
        if (!isset($_POST['adminAcceptEvent'])) {
            return;
        }
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($eventId === false || $eventId === null) {
            $_SESSION['flash_error'] = "Neplatné ID akce.";
            $this->redirectProfile();
        }
        $ok = $this->db->updateEventAccept($eventId);
        if ($ok) {
            $_SESSION['flash_success'] = "Událost byla schválena.";
        } else {
            $_SESSION['flash_error'] = "Akceptování se nezdařilo.";
        }
        $this->redirectProfile('panel-adminOfferEvents');
    }

    private function handleDeleteUser(): void
    {
        if (!isset($_POST['deleteUser'])) {
            return;
        }
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        if ($userId === false || $userId === null) {
            $_SESSION['flash_error'] = "Neplatné ID uživatele.";
            $this->redirectProfile('panel-userAdministration');
        }
        $currentUser = $this->auth->getLoggedUserData();
        if ($currentUser && (int)$currentUser->id === $userId) {
            $_SESSION['flash_error'] = "Nemůžete smazat vlastní účet.";
            $this->redirectProfile('panel-userAdministration');
        }
        $this->deleteUserFiles($userId);
        $ok = $this->db->deleteUserById($userId);
        if ($ok) {
            $_SESSION['flash_success'] = "Uživatel byl smazán.";
        } else {
            $_SESSION['flash_error'] = "Smazání uživatele se nezdařilo.";
        }

        $this->redirectProfile('panel-adminUserTable');

    }
    private function deleteUserFiles(int $userId): void
    {
        $user = $this->db->getUserById($userId);
        if ($user && !empty($user->photoPath)) {
            $this->deleteFileByPublicPath($user->photoPath);
        }
        $events = $this->db->getUserEvents($user->id);
        if (is_array($events)) {
            foreach ($events as $event) {

                if (is_array($event)) {
                    $photoPath = $event['photoPath'] ?? null;
                } else {
                    $photoPath = $event->photoPath ?? null;
                }
                if (!empty($photoPath)) {
                    $this->deleteFileByPublicPath($photoPath);
                }
            }
        }
    }

    private function deleteFileByPublicPath(string $publicPath): void
    {
        if (str_contains($publicPath, 'avatar_default')) {
            return;
        }

        $absolutePath = $_SERVER['DOCUMENT_ROOT'] . $publicPath;
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function getEventsForUser(object $user, ?bool $forceByPlace = null): array
    {
        if ($forceByPlace !== null) {
            $byPlace = $forceByPlace;
        } else {
            if ((int)$user->role === 4) {
                $byPlace = true;
            } else {
                $byPlace = false;
            }
        }

        return $this->db->getUserEvents((int)$user->id, $byPlace);
    }
private function handleMessageReply($user): void
{
    if (!isset($_POST['sendReply'])) {
        return;
    }

    $messageId = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);
    $subject   = trim($_POST['reply_subject'] ?? '');
    $replyText = trim($_POST['reply_text'] ?? '');

    if ($messageId === false || $messageId === null) {
        $_SESSION['flash_error'] = "Neplatné ID zprávy.";
        $this->redirectProfile('panel-messages');
    }

    if ($subject === '' || $replyText === '') {
        $_SESSION['flash_error'] = "Předmět i text odpovědi musí být vyplněny.";
        $this->redirectProfile('panel-messages');
    }

    $originalMessage = $this->db->getMessageById($messageId);
    if (!$originalMessage) {
        $_SESSION['flash_error'] = "Původní zpráva nebyla nalezena.";
        $this->redirectProfile('panel-messages');
    }

    if (!$user || empty($user->email)) {
        $_SESSION['flash_error'] = "Nelze odeslat e-mail – chybí e-mailová adresa odesílatele.";
        $this->redirectProfile('panel-messages');
    }

    // původní text zprávy
    $originalText = $originalMessage->message;

    $emailBody  = "Zpráva\n\n";
    $emailBody .= "Odpověď na zprávu:\n";
    $emailBody .= $originalText . "\n";
    $emailBody .= "-----\n";
    $emailBody .= $replyText . "\n\n";
    $emailBody .= "S pozdravem,\n";
    $emailBody .= $user->username . "\n";
    $emailBody .= "HR oddělení";

    $toEmail   = $originalMessage->email;
    $fromEmail = "podpora@propojovacisystemakci.cz";
    $fromName  = $user->username ?? 'Propojovací systém akcí';

    $safeSubject = 'Re: ' . str_replace(["\r", "\n"], '', $subject);

    // PHPMailer + MailHog
    $mail = new PHPMailer(true);

    try {
        // SMTP -> MailHog
        $mail->isSMTP();
        $mail->Host       = 'mailhog';
        $mail->Port       = 1025;
        $mail->SMTPAuth   = false;
        $mail->SMTPSecure = false;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($fromEmail, $fromName);

        // jméno odesílatele můžeš vzít z původní zprávy, pokud ho máš
        $recipientName = $originalMessage->name ?? '';
        $mail->addAddress($toEmail, $recipientName);

        $mail->isHTML(false);            // posíláme čistý text
        $mail->Subject = $safeSubject;
        $mail->Body    = $emailBody;

        $mail->send();

        $_SESSION['flash_success'] = "E-mailová odpověď byla odeslána.";
        $this->db->deleteMessage($messageId);

    } catch (Exception $e) {
        // případně zaloguj $e->getMessage() nebo $mail->ErrorInfo
        $_SESSION['flash_error'] = "E-mail se nepodařilo odeslat (zkontroluj konfiguraci MailHog/SMTP).";
    }

    $this->redirectProfile('panel-messages');
}

    private function handleSendInfoEmail($user): void
    {
        if (!isset($_POST['sendInfoEmail'])) {
            return;
        }

        // případně můžeš omezit jen na adminy atd.
        if (!$user || empty($user->email)) {
            $_SESSION['flash_error'] = "Nelze odeslat e-mail – chybí e-mailová adresa odesílatele.";
            $this->redirectProfile();
        }

        // 1) načtení HTML šablony ze souboru
        $templatePath = dirname(__DIR__, 2) . '/www/emailTemplates/info_email.html';

        if (!file_exists($templatePath)) {
            $_SESSION['flash_error'] = "Šablona e-mailu nebyla nalezena.";
            $this->redirectProfile();
        }

        $htmlTemplate = file_get_contents($templatePath);
        if ($htmlTemplate === false || $htmlTemplate === '') {
            $_SESSION['flash_error'] = "Šablonu e-mailu se nepodařilo načíst.";
            $this->redirectProfile();
        }

        // 2) cíloví uživatelé – role 3 a 4
        $targetUsers = $this->db->getUsersByRoles([3, 4]);
        if (empty($targetUsers)) {
            $_SESSION['flash_error'] = "Nebyli nalezeni žádní uživatelé s rolí 3 nebo 4.";
            $this->redirectProfile();
        }

        $subject   = "Nová akce!";
        $fromEmail = 'info@propojovacisystemakci.cz';
        $fromName  = 'Propojovací systém akcí';

        // 3) rozeslání přes PHPMailer + MailHog
        $sent   = 0;
        $failed = 0;

        foreach ($targetUsers as $u) {
            if (empty($u->email)) {
                $failed++;
                continue;
            }

            $username = htmlspecialchars($u->username ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $body     = str_replace('{{USERNAME}}', $username, $htmlTemplate);

            $mail = new PHPMailer(true);

            try {
                // SMTP -> MailHog
                $mail->isSMTP();
                $mail->Host       = 'mailhog';
                $mail->Port       = 1025;
                $mail->SMTPAuth   = false;
                $mail->SMTPSecure = false;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($u->email, $username);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
                $sent++;
            } catch (Exception $e) {
                // případně logovat $e->getMessage() / $mail->ErrorInfo
                $failed++;
            }
        }

        // 4) flash message podle výsledku – PO smyčce
        if ($sent > 0 && $failed === 0) {
            $_SESSION['flash_success'] = "E-mail byl úspěšně odeslán {$sent} uživatelům.";
        } elseif ($sent > 0 && $failed > 0) {
            $_SESSION['flash_success'] = "E-mail byl odeslán {$sent} uživatelům, u {$failed} odeslání selhalo.";
        } else {
            $_SESSION['flash_error'] = "E-mail se nepodařilo odeslat žádnému uživateli.";
        }

        $this->redirectProfile();
    }

    private function handleCreateAdmin($user): void
    {
        if (!$user || (int)$user->role !== 1) {
            return;
        }

        if (!isset($_POST['register'])) {
            return;
        }

        $username  = trim($_POST['username']  ?? '');
        $email     = trim($_POST['email']     ?? '');
        $password  = $_POST['password']      ?? '';
        $password2 = $_POST['password2']     ?? '';

        if ($username === '' || $email === '' || $password === '' || $password2 === '') {
            $_SESSION['flash_error'] = 'Vyplňte všechna pole.';
            $this->redirectProfile('panel-createAdmin');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Neplatný e-mail.';
            $this->redirectProfile('panel-createAdmin');
        }

        if ($password !== $password2) {
            $_SESSION['flash_error'] = 'Hesla se neshodují.';
            $this->redirectProfile('panel-createAdmin');
        }

        if ($this->db->findUserByEmail($email)) {
            $_SESSION['flash_error'] = 'Uživatel s tímto e-mailem již existuje.';
            $this->redirectProfile('panel-createAdmin');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $isFromSuperAdmin = isset($_POST['from_superadmin']) && $_POST['from_superadmin'] === '1';

        if ($isFromSuperAdmin) {
            $role = 2;
        } else {
            $role = isset($_POST['role']) ? (int)$_POST['role'] : 0;
            if ($role <= 0) {
                $_SESSION['flash_error'] = 'Nebyla zvolena platná role.';
                $this->redirectProfile('panel-createAdmin');
            }
        }

        $ok = $this->db->addUser($username, $hash, $email, $role);

        if ($ok) {
            $_SESSION['flash_success'] = 'Administrátor byl úspěšně vytvořen.';
        } else {
            $_SESSION['flash_error'] = 'Nepodařilo se vytvořit uživatele.';
        }

        $this->redirectProfile('panel-createAdmin');
    }


}