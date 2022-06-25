<?php

namespace App\Models;

class Language
{
    public int $id;
    public string $name;
    public ?string $country;

    public function __construct(
        int $id,
        string $name,
        ?string $country = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
    }
}
