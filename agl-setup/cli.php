<?php
$currentDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

return array(

    // Copy configuration files and locale structure.
    'file:copy' => array(
        array(
            $currentDir . 'app/etc/config/more/locale/main.php',
            $appPath    . 'app/etc/config/more/locale/main.php'
        ),

        array(
            $currentDir . 'app/etc/config/more/locale/events.php',
            $appPath    . 'app/etc/config/more/locale/events.php'
        ),

        array(
            $currentDir . 'app/etc/locale/en_GB.utf8/LC_MESSAGES/default.po',
            $appPath    . 'app/etc/locale/en_GB.utf8/LC_MESSAGES/default.po'
        )
    )
);
