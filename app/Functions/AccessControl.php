<?php

use App\Services\AccessControlService;

function access_control()
{
    return AccessControlService::getInstance();
}