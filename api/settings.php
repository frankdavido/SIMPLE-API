<?php

#region
/*
 * NOTE: Do not change anything in this region
 * */
if (!defined('DOCUMENT_ROOT')) {
    define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']); # document root
}
if (!defined('HOSTNAME')) {
    define('SERVER_NAME', str_replace('www.', '', $_SERVER['SERVER_NAME']));
    define('HTTP_HOST', $_SERVER['HTTP_HOST']);
    define('HOSTNAME', explode('.', SERVER_NAME, 2)[0]);
    define('REQUEST_URI', $_SERVER['REQUEST_URI']);
}
/*
 * Get URI elements.
 * @return array
 */
function getUriSegments():array
{
    $uri = parse_url(REQUEST_URI, PHP_URL_PATH);
    $uri = explode('/', $uri);
    return $uri;
}
#endregion

#region
/*
 * You may alter this constants below as per your needs
 */
/*
 * Class register for constants used in this site
 * */
class CONSTANTS
{
    /*
     * Database connection Parameters
    */
    public const DATABASE_HOST  = 'localhost';              // May be localhost or IP of the database server.
    public const DATABASE_NAME  = 'testapi';                // Change to real database name
    public const DATABASE_USER  = 'user';                   // Change to real database user
    public const DATABASE_PASSWORD  = 'pass';// Change to real database password
    /*
     * Whether to use persistent connection to database. Makes the application faster.
    */
    public const PERSISTENT_DATABASE_CONNECTION = false;    // Boolean [true of false]. Default is false
    /*
     * Do you want my script to automatically create and populate some Test data for you inside the database?
     */
    public const CREATE_TEST_DATA = true;                   // Boolean [true of false].
    /*
     * Default Timezone
     * For more list of timezones,
     * SEE: https://www.w3schools.com/php/php_ref_timezones.asp
     */
    public const TIMEZONENAME = 'Africa/Lagos';             // String. More examples See comment above. PHP and DB will use zone
}


#endregion


date_default_timezone_set(constants::TIMEZONENAME);
require_once DOCUMENT_ROOT . '/db/dbconn.php';
