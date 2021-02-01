<?php

namespace App\Services;

use App\Entities\Url;
use Exception;
use Throwable;

class UrlAlreadyExistsException extends Exception
{
    public Url $existedUrl;

    public function __construct(Url $existedUrl, $message = "Url exists", $code = 0, Throwable $previous = null)
    {
        $this->existedUrl = $existedUrl;
        parent::__construct($message, $code, $previous);
    }
}
