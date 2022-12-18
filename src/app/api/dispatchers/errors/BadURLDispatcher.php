<?php
class BadURLDispatcher
{
    public function dispatch()
    {
        echo 'URL should have the form <code>/{category}/{handler}</code>';
    }
}