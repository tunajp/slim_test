<?php

namespace Controller;

/**
 * Dummy
 */
class Page
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function index()
    {
        echo 'hello class';
    }
}
