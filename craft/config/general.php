<?php

/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/general.php
 */

return array(

    '*' => array(
        'omitScriptNameInUrls' => true,
        'siteRoutesSource'   => 'file',
    ),

    'supertone.craft.dev' => array(
    	'siteUrl' => 'http://supertone.craft.dev',
        'devMode' => true,
    ),

    'supertone.sitestaged.com' => array(
    	'siteUrl' => 'http://supertone.sitestaged.com',
        'cooldownDuration' => 0,
    ),

    'supertonerecords.com' => array(
        'siteUrl' => 'http://supertonerecords.com',
        'cooldownDuration' => 0,
    )
);
