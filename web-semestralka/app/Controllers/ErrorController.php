<?php

class ErrorController implements IController
{
    public function show():array
    {
        http_response_code(404);

        $data = array(
            "title" => "Stránka nenalezena"
        );

        return $data;
    }
}


?>