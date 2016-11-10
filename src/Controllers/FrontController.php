<?php

namespace ZWorkshop\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class FrontController
{
    public function index(Application $app)
    {
        return $app->redirect('profile');
    }

    public function profile(Application $app, Request $request, $username)
    {
        return $username;
    }
}
