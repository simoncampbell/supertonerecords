<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/db.php
 */

return array(

    '*' => array(
        'tablePrefix' => 'craft',
    ),

    'supertone.craft.dev' => array(
        'server' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'database' => 'supertone_supertone_local',
    ),

    'supertone.sitestaged.com' => array(
        'server' => 'localhost',
        'user' => 'supertone_stage',
        'password' => 'xmPdg8nVVwZVGaixAG8DWGAinxKPPo',
        'database' => 'supertone_stage',
    ),

    'supertonerecords.com' => array(
        'server' => 'localhost',
        'user' => 'supertone_prod',
        'password' => 'REZLVsJJYVERyJTuPN3VvGxBZnNd2h',
        'database' => 'supertone_prod',
    ),

);
