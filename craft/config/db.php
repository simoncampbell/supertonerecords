<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/db.php
 */

return array(

	// The database server name or IP address. Usually this is 'localhost' or '127.0.0.1'.
	'server' => 'localhost',

	// The database username to connect with.
	'user' => 'root',

	// The database password to connect with.
	'password' => 'root',

	// The name of the database to select.
	'database' => 'supertone_supertone_local',

	// The prefix to use when naming tables. This can be no more than 5 characters.
	'tablePrefix' => 'craft',

);


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

    // 'lebridge.co.uk' => array(
    //     'server' => 'localhost',
    //     'user' => 'lebridge_staging',
    //     'password' => 'REZLVsJJYVERyJTuPN3VvGxBZnNd2h',
    //     'database' => 'lebridge_staging',
    // ),

);


