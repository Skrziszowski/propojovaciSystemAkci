<?php

class AuthController{

    /** @var DatabaseModel $db  Sprava databaze. */
    private $db;

    /** @var SessionModel $session  Vlastni objekt pro spravu session. */
    private $session;

    private  const KEY_USER = "user_id";

    /**
     * Inicializace pripojeni k databazi.
     */
    public function __construct() {
        require_once (DIRECTORY_MODELS ."/DatabaseModel.php");
        $this->db = new DatabaseModel();

        require_once(DIRECTORY_MODELS."/SessionModel.php");
        $this->session = new SessionModel();

    }

    public function register(string $username, string $password, string $password2, string $email, int $role): ?string
    {
        if(!empty($username) && !empty($password) && !empty($password2) && !empty($email) && !empty($role) && $password == $password2){
            $email = trim($email);
            if($this->db->userExists($email)){
                return "Uživatel s tímto emailem již existuje!";
            }else{
                $hash  = password_hash($password, PASSWORD_DEFAULT); //pro zadani lze: PASSWORD_BCRYPT / PASSWORD_ARGON2ID
                $ok = $this->db->addUser($username, $hash, $email, $role);
                if(!$ok)
                    return "Došlo k chybě při vytváření uživatele";

                $user = $this->db->findUserByEmail($email);
                if ($user) {
                    $this->session->addSession(self::KEY_USER, $user->id);
                }
            }
        } else {
            return "Neplatná nebo nekompletní data formuláře";
        }
        return null;
    }

    public function login($email, $password): ?string
    {
        if(!empty($email) && !empty($password)){
            $user = $this->db->findUserByEmail($email);
            if($user){
                if (password_verify($password, $user->password)) {
                    $this->session->addSession(self::KEY_USER,$user->id);
                }else{
                    return "Uživatel zadal špatné heslo";
                }
            }else{
                return "Uživatel nenalezen!";
            }
        }
        else{
            return "Nekompletní přihlašovací data";
        }
        return null;
    }
    public function logout(): void{
        $this->session->deleteSession(self::KEY_USER);
    }

    public function isUserLogged():bool {
        return $this->session->isSession(self::KEY_USER);
    }

    public function getLoggedUserData(){
        if (!$this->isUserLogged()) {
            return null;
        }
        $userID = $this->session->readSession(self::KEY_USER);
        if ($userID === null) {
            $this->logout();
            return null;
        }
        $userData = $this->db->getUserData($userID);
        if (empty($userData)) {
            $this->logout();
            return null;
        }
        return $userData;
    }

    public function requireRoles(array $roles): ?string
    {
        $user = $this->getLoggedUserData();
        if (!$user) {
            return "Pro přístup na tuto stránku musíte být přihlášen!";
        }

        if (!in_array($user->role, $roles)) {
            return "Na tuto stránku nemáte dostatečná oprávnění!";
        }

        return null;
    }

}


?>