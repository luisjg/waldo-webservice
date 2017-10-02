<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function sendSuccessJSONResponse($data, $code=200, $success=true)
    {
        return response($data, $code);
    }
}
