<?php
    $path = $_SERVER['DOCUMENT_ROOT'];
    include $path . "/src/autoload.php";
    $WowSessionName = md5("WowFramework" . "_" . preg_replace('/(?:www\.)?(.*)\/?$/i', '$1', $_SERVER["HTTP_HOST"])  . "_" . $_SERVER['HTTP_USER_AGENT']);
    session_name($WowSessionName);
    session_start();
    /**
     *    Filemanager PHP connector configuration
     *
     *    filemanager.config.php
     *    config for the filemanager.php connector
     *
     * @license       MIT License
     * @author        Riaan Los <mail (at) riaanlos (dot) nl>
     * @author        Simon Georget <simon (at) linea21 (dot) com>
     * @copyright     Authors
     */


    /**
     *    Check if user is authorized
     *
     * @return boolean true is access granted, false if no access
     */
    function auth() {
        // You can insert your own code over here to check if the user is authorized.
        // If you use a session variable, you've got to start the session first (session_start())
        if(!isset($_SESSION["AdminLogonPerson"])){
            return FALSE;
        }
        /**
         * @var \App\Models\LogonPerson $logonPerson
         */
        $logonPerson = $_SESSION["AdminLogonPerson"];

        return $logonPerson->isAdmin();
    }


    /**
     *    Language settings
     */
    $config['culture'] = 'en';

    /**
     *    PHP date format
     *    see http://www.php.net/date for explanation
     */
    $config['date'] = 'd M Y H:i';

    /**
     *    Icons settings
     */
    $config['icons']['path']      = 'images/fileicons/';
    $config['icons']['directory'] = '_Open.png';
    $config['icons']['default']   = 'default.png';

    /**
     *    Upload settings
     */
    $config['upload']['overwrite']  = FALSE; // true or false; Check if filename exists. If false, index will be added
    $config['upload']['size']       = FALSE; // integer or false; maximum file size in Mb; please note that every server has got a maximum file upload size as well.
    $config['upload']['imagesonly'] = TRUE; // true or false; Only allow images (jpg, gif & png) upload?

    /**
     *    Images array
     *    used to display image thumbnails
     */
    $config['images'] = array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    );


    /**
     *    Files and folders
     *    excluded from filtree
     */
    $config['unallowed_files'] = array('.htaccess','.php','.inc','.dat','.cnf');
    $config['unallowed_dirs']  = array(
        '_thumbs',
        '.CDN_ACCESS_LOGS',
        'cloudservers'
    );

    /**
     *    FEATURED OPTIONS
     *    for Vhost or outside files folder
     */
// $config['doc_root'] = '/home/user/userfiles'; // No end slash


    /**
     *    Optional Plugin
     *    rsc: Rackspace Cloud Files: http://www.rackspace.com/cloud/cloud_hosting_products/files/
     */
    $config['plugin'] = NULL;
//$config['plugin'] = 'rsc';


//	not working yet
//$config['upload']['suffix'] = '_'; // string; if overwrite is false, the suffix will be added after the filename (before .ext)

?>