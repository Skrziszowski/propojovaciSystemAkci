<?php

class ApplicationStart{
    
    public function __construct(){
        require_once (DIRECTORY_CONTROLLERS."/IController.php");
        /*ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        error_reporting(E_ALL);*/
    }

    public function appStart(){
        if(isset($_GET["page"]) && array_key_exists($_GET["page"],WEB_PAGES)){
            $pageKey = $_GET["page"];
        }else{
            $pageKey = '404';
            http_response_code(404);
        }
        $pageInfo = WEB_PAGES[$pageKey];

        require_once (DIRECTORY_CONTROLLERS."/".$pageInfo["file_name"]);
        /** @var IController $controller */
        $controller = new $pageInfo["class_name"];
        $data = $controller->show();
        $this->renderInTwig($data, $pageInfo);

    }

    /**
     * Vykresleni obsahu Twigem
     * @param array $data           Data pro sablonu.
     * @param string $templateKey   Klic pro prislusnou stranku.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function renderInTwig($data, $pageInfo){

        $loader = new \Twig\Loader\FilesystemLoader(DIRECTORY_VIEWS);
        $twig = new \Twig\Environment($loader, [
            'debug' => true,
            /*'cache' => 'vlastni_cache',*/
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        echo $twig->render($pageInfo["template"], $data);
    }
}

?>