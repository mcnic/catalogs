<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Autoimport\User;
use Illuminate\Support\Facades\Log;

class AutoimportController extends Controller
{
    public function getUser()
    {
        return User::getCurrent();
    }
}
