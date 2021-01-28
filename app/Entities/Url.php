<?php

namespace App\Entities;

use Carbon\Carbon;

class Url
{
    public int $id;
    public string $name;
    public Carbon $createdAt;
    public Carbon $updatedAt;
}
