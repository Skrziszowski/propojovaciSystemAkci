# Propojovací systém akcí (Plzeň)

Webová aplikace pro **propojování organizátorů akcí** (kapely, speakři, tvůrci workshopů…)  
s **majiteli prostor** (školy, městské instituce, Depo2015, TechTower…) v Plzni.

Cíl: zjednodušit domluvu – od vytvoření akce, přes rezervaci prostoru až po schválení a zveřejnění.

---

## 1. Funkce a role

### Role uživatelů

- **Nepřihlášený uživatel**
   - Vidí veřejné, schválené akce.
   - Může si rozkliknout detail akce (popis, místo, datum, fotka).
   - Přes kontaktní formulář může napsat zprávu „HR“ / provozovateli systému.

- **Tvůrce (např. kapela / přednášející) – role 3**
   - Vytváří své akce a odešle žádost o schválení.
   - Spravuje svůj profil (info, fotka).

- **Majitel prostoru – role 4**
   - Vidí žádosti o akce a vybírá si akce, které se mohou v jeho prostoru konat.
   - Má vlastní profil s informacemi o prostoru.

- **Admin – role 2**
   - Schvaluje / zamítá akce (stav `approved` = *čeká se na schválení, schváleno, zamítnuto*).
   - Spravuje uživatele a může je mazat.
   - Vidí zprávy z kontaktního formuláře, může na ně odpovídat (přes MailHog).

- **SuperAdmin – role 1**
   - Všechna práva jako admin.
   - Může spravovat i ostatní administrátory.
   - Má plný přístup do administrace a k „HR“ nástrojům.

---

## 2. Hlavní funkce aplikace

- **Veřejný výpis akcí**
   - Karty akcí s fotkou, názvem, datem, místem, kategorií, krátkým popisem.
   - Detail akce v modalu.

- **Přihlášení a registrace**
   - Registrace nového uživatele s výběrem role.
   - Přihlášení přes e-mail + heslo.
   - Hesla hashovaná pomocí `password_hash()` (Bcrypt / Argon2 podle `PASSWORD_DEFAULT`).
   - Zapomenuté heslo s posláním kódu na email pro přihlášení a změnu hesla
  
- **Profil uživatele**
   - Úprava základních údajů (jméno, informace, web apod.).
   - Nahrání profilové fotky (JPG/PNG/WEBP, max. velikost 2 MB).
   - Mazání starých fotek při nahrání nové / smazání uživatele.

- **Správa akcí**
   - Vytvoření akce (název, popis, datum, čas, kategorie, prostor, fotka).
   - Stav akce (`čeká se na schválení`, `schváleno`, `zamítnuto`).
   - Propojení tvůrce a majitele prostoru.
   - Zobrazení budoucích i minulých akcí v profilu.

- **Administrace uživatelů**
   - Tabulka uživatelů (username, e-mail, role, informace).
   - Filtrování přes AJAX (jméno, e-mail, role).
   - Mazání uživatele (včetně smazání souvisejících fotek).
   - Ochrana proti smazání vlastního účtu.

- **Kontaktní formulář + zprávy**
   - Stránka „Kontakt“ – formulář `name + email + message`.
   - Ukládání zpráv do tabulky `message`.
   - Přehled zpráv v admin profilu.
   - Odpověď na zprávu přes e-mail (PHPMailer + MailHog, po odeslání se zpráva může smazat).

- **SSE – živý počet zpráv**
   - Server-Sent Events endpoint `sse_messages.php`.
   - Na profilové stránce admina se odznak u záložky „Zprávy“ aktualizuje bez reloadu.
   - SSE bere počet zpráv z DB a posílá JSON (`{ type: "messages", count: N }`).

- **Hromadné infomaily**
   - Admin může odeslat informační e-mail všem uživatelům s rolí 3 a 4.
   - HTML šablona v `www/emailTemplates/info_email.html`, personalizace `{{USERNAME}}`.
   - Odesílání přes PHPMailer → MailHog (SMTP `mailhog:1025`).

---

## 3. Technologie

- **Backend**
   - PHP 8.3 (image `php:8.3-apache`).
   - OOP, jednoduché MVC:
      - `index.php` jako front-controller.
      - Router / mapování stránek v `app/ApplicationStart.php` (`WEB_PAGES`).
      - Kontrolery v `app/Controllers`.
      - Modely v `app/Models` (např. `DatabaseModel.php`).

- **Frontend**
   - Twig šablony (`app/Views`).
   - Bootstrap 5 (CDN).
   - jQuery (CDN) pro jednoduché interakce (AJAX filtrování, drobné skripty).
   - Vlastní CSS v `www/css`.

- **Databáze**
   - MariaDB 11 (MySQL kompatibilní).
   - Přístup přes PDO (`DatabaseModel`).
   - Oddělené konstanty pro názvy tabulek (např. `TABLE_USERS`, `TABLE_EVENT`, `TABLE_MESSAGE`, `TABLE_ROLES`, `TABLE_CATEGORY`).

- **Závislosti (Composer)**
   - Twig.
   - PHPMailer.
   - Další pomocné balíčky (symfony komponenty).
   - Composer běží **uvnitř Docker kontejneru**, instalace přes:
     ```bash
     docker exec -it web-semestralka-web composer install
     ```

- **Notifikace / e-mail**
   - PHPMailer + MailHog pro lokální vývoj.
   - SMTP host: `mailhog`, port: `1025`, bez autentizace.


- **REST API (ApiController)**
  - Aplikace obsahuje jednoduché REST API na adrese `/api/events`, které přes `ApiController` umožňuje číst, vytvářet, upravovat a mazat akce (metody GET, POST, PUT/PATCH, DELETE) ve formátu JSON. API využívá stejný datový model `events` jako hlavní část systému a je připravené pro integraci s dalšími klienty (např. SPA nebo mobilní aplikace).

---

## 4. Struktura projektu

Zjednodušený přehled:

```text
web-semestralka/
├── app/
│   ├── ApplicationStart.php        # start app, router (WEB_PAGES)
│   ├── Controllers/                # HomepageController, ProfileController, OtherController, AjaxController, ApiController, ...
│   ├── Models/
│   │   ├── DatabaseModel.php       # PDO wrapper, práce s DB
│   │   └── ...                     # další modely
│   └── Views/
│       ├── Layouts/                # base.twig, hlavička/patička
│       ├── Pages/                  # homePage.twig, profilePage.twig, otherPage.twig, ...
│       ├── Blocks/                 # tabulky, modaly, karty, partialy
│       └── Special/                # 404.twig, flash messages apod.
├── config/
│   └── settings.php                # DB připojení, konstanty, cesty
│   └── propojovaci_system_akci.sql # vytvoření databáze a přidání demodat 
├── vendor/                         # Composer balíčky (Twig, PHPMailer, ...)
├── www/
│   ├── css/, js/, img/             # statická aktiva
│   ├── emailTemplates/             # HTML šablony e-mailů
│   └── .htaccess                   # přepis URL (mod_rewrite)
├── index.php                       # front controller (čte ?page, spouští controller)
├── sse_messages.php                # SSE endpoint pro počet zpráv
├── composer.json
├── composer.lock
├── Dockerfile
└── docker-compose.yml
```

## 5. Databázový model (hlavní tabulky)

Základní přehled (schéma může být rozšířené):

### `users`
- `id` (PK)
- `username`
- `email` (UNIQUE)
- `password` (hash)
- `role` (FK → `roles.id`)
- `information` (TEXT)
- `photoPath` (profilová fotka)

### `roles`
- `id` (PK)
- `name`
   - např. `SuperAdmin`, `Admin`, `Tvůrce`, `Majitel prostoru`

### `events`
- `id` (PK)
- `user_id` (FK → tvůrce, vazba na `users.id`)
- `place_id` (FK → uživatel reprezentující prostor, vazba na `users.id`)
- `category_id` (FK → `categories.id`)
- `name`
- `description`
- `date`
- `photoPath`
- `approved`
   - `ENUM('čeká se na schválení','schváleno','zamítnuto')`

### `categories`
- `id` (PK)
- `name`
   - např. `koncert`, `přednáška`, `workshop`, `festival`, …

### `message`
- `id` (PK)
- `name` (jméno odesílatele)
- `email`
- `message` (TEXT)
- případně další sloupce:
   - `is_read`
   - `created_at`
   - další dle verze DB


## 6. Docker prostředí

### Služby (`docker-compose.yml`)

#### `web`
- image stavěná z `Dockerfile` (PHP 8.3 + Apache)
- porty: `8080:80` → aplikace na `http://localhost:8080/`
- volume: `.:/var/www/html` → projekt z hosta mountnutý do kontejneru
- `environment`:
   - `MAIL_HOST=mailhog`
   - `MAIL_PORT=1025`

#### `db` (MariaDB 11)
- `MYSQL_ROOT_PASSWORD=root`
- `MYSQL_DATABASE=propojovaci_system_akci`
- volume: `db_data:/var/lib/mysql` → persistentní data

#### `adminer`
- GUI pro DB na `http://localhost:8082`

#### `mailhog`
- SMTP server: `localhost:1025`
- Web UI pro e-maily: `http://localhost:8025`


## 7. Instalace a spuštění

### Předpoklady
- Docker + Docker Compose
- Git (volitelné, ale doporučené)

### Kroky

#### 1. Klonování projektu
```bash
git clone <URL_REPA> web-semestralka
cd web-semestralka
```

#### 2. Build a start Docker prostředí
```bash
docker compose build        # jen při změně Dockerfile
docker compose up -d
```

#### 3. Composer závislosti (uvnitř kontejneru)
```bash
docker exec -it web-semestralka-web composer install
```

#### 4. Databáze
- Připojit se na Adminer: `http://localhost:8082`
- Server: `db`
- Uživatel: `root`
- Heslo: `root`
- Databáze: `propojovaci_system_akci`
- Importovat SQL dump (pokud je v projektu, např. `config/sql/propojovaci_system_akci.sql`)

#### 5. Přístup na aplikaci
- Web: `http://localhost:8080/` (případně `http://localhost:8080/home` podle rewritů)
- MailHog UI: `http://localhost:8025`


## 8. Bezpečnost a technické poznámky

### Hesla
- ukládána hashovaně:  
  - `password_hash($password, PASSWORD_DEFAULT)`
- ověřování:  
  - `password_verify()`

### DB
- všechny dotazy přes připravené statementy:
  - `$pdo->prepare(...)`
  - bind parametrů (`bindParam`, `execute` s poli)

### Uploady
- kontrola MIME typů:
  - `image/jpeg`
  - `image/png`
  - `image/webp`
- max. velikost uploadu omezená v konfiguraci:
  - `.htaccess` / PHP (`upload_max_filesize`, `post_max_size`)
- mazání starých souborů:
  - při změně fotky
  - při smazání uživatele

### SSE
- v `sse_messages.php`:
  - po přečtení `user_id` se volá `session_write_close()` → SSE neblokuje ostatní requesty
- periodický dotaz na počet zpráv:
  - `sleep(5)`
  - posílání JSON eventů s počtem zpráv


## 9. Testovací účty (lokální vývoj)

Ukázkové účty v demodatabázi pro rychlé přihlášení:

| Role             | Přihlašovací údaj / e-mail | Heslo       |
|------------------|----------------------------|-------------|
| Super Admin      | `admin`                    | `admin`     |
| Admin (role 2)   | `tomas.svoboda@email.cz`   | `tomas`     |
| Tvůrce (role 3)  | `petr.novak@email.cz`      | `petr`      |
| Prostor (role 4) | `techtower@email.cz`       | `techtower` |

> Tyto údaje jsou **pouze pro lokální vývoj**.  
> V produkční instalaci je nutné vytvořit nové uživatele s bezpečnými hesly.


## 10. Autor

- **Autor:** Filip Skrziszowski
- **Škola:** Západočeská univerzita v Plzni – Fakulta aplikovaných věd
- **Předmět:** KIV/WEB – semestrální projekt