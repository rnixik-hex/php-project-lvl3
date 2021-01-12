<?php

namespace App\Entities;

use Carbon\Carbon;

class Domain
{
    public int $id;
    public string $name;
    public Carbon $created_at;
    public Carbon $updated_at;
}
