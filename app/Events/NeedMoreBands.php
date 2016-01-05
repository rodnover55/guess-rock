<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 05.01.16
 * Time: 21:56
 */

namespace App\Events;


class NeedMoreBands extends Event {
    private $genre;

    public function setGenre($genre) {
        $this->genre = $genre;

        return $this;
    }

    public function getGenre() {
        return $this->getGenre();
    }
}