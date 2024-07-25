<?php

/**
 * file path:local/php_interface/events.php
 */

use \Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

$eventManager->AddEventHandler("rest", "OnRestServiceBuildDescription", [
        'DevResource\\Rest\\CustomRestProvider',
        'OnRestServiceBuildDescription'
    ]
);
