<?php
/**
 * Created by PhpStorm.
 * User: ascet
 * Date: 05.01.16
 * Time: 19:29
 */

namespace App\Models;


class Band extends BaseModel{
    public function images() {
        return $this->hasMany(Image::class);
    }
}