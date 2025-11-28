<?php

define("DB_SERVER","db");
define("DB_NAME","propojovaci_system_akci");
define("DB_USER","root");
define("DB_PASS","root");


define("TABLE_USER", "users");
define("TABLE_ROLES", "roles");
define("TABLE_CATEGORY", "category");
define("TABLE_EVENT", "events");
define("TABLE_MESSAGE", "message");

const DIRECTORY_CONTROLLERS = "app/Controllers";
const DIRECTORY_MODELS = "app/Models";
const DIRECTORY_VIEWS = "app/Views/";
const DEFAULT_WEB_PAGE_KEY = "home";


/** Dostupne webove stranky. */
const WEB_PAGES = array(
    //// Uvodni stranka ////
    "home" => array(
        "file_name" => "HomepageController.php",
        "class_name" => "HomepageController",
        "template" => "Pages/homePage.twig",
    ),
    //// KONEC: Uvodni stranka ////
    ///
    ///  Profil uzivatele
    "profile" => array(
        "file_name" => "ProfileController.php",
        "class_name" => "ProfileController",
        "template" => "Pages/profilePage.twig",
    ),
    /// KONEC: Profil uzivatele
    ///
    /// Jiné stránky - O nás, Kontakt, Zapomenuté heslo
    "other" => array(
        "file_name" => "OtherController.php",
        "class_name" => "OtherController",
        "template" => "Pages/otherPage.twig",
    ),
    /// KONEC Jiné stránky
    ///
    /// AJAX
    "adminUsersAjax" => array(
        "file_name"  => "AjaxController.php",
        "class_name" => "AjaxController",
        "template"   => "Blocks/Tables/userAdministrationRows.twig",
    ),
    /// KONEC AJAX
    ///
    /// 404
    '404' => [
        "file_name" => "ErrorController.php",
        'class_name' => 'ErrorController',
        "template"   => "Special/404.twig",
    ],

    /// KONEC 404
    ///
    /// API endpoint
    "api" => [
        "file_name"  => "ApiController.php",
        "class_name" => "ApiController",
        "template"   => "",
    ],
    /// KONEC API endpoint

);



?>
