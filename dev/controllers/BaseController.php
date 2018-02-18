<?php

namespace dev\controllers;

use craft\web\Controller;

/**
 * Class BaseController
 */
abstract class BaseController extends Controller
{
    protected $allowAnonymous = true;
}
