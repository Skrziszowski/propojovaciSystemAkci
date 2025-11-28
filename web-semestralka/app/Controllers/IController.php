<?php

interface IController {
    /**
     * Zajisti vypsani prislusne stranky.
     * @return array               Prislusne data.
     */
    public function show():array;
}


?>