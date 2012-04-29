<?php
$_dev_servers = array('localhost','127.0.0.1','www.yeloworks.com','yeloworks.com');
define('SERVER_DEV', (in_array($_SERVER['SERVER_NAME'], $_dev_servers)));
define('SERVER_PROD',!SERVER_DEV);

global $project;
$_project_dir = rtrim(str_replace('\\','/',dirname(__FILE__)),'/').'/';
$project = 'mysite';
$project_name = 'Adaimy Studios';
$theme_name = 'adaimy';
$_project_log = dirname($_project_dir).'/log/'.$project.'-'.date('H-i-s').'.log';
$_project_url = 'http://'.(SERVER_PROD ? $project.'.com' : 'localhost');

global $databaseConfig;
#$databaseConfig = SERVER_PROD ? include 'db_prod.php':include 'db_dev.php';
$databaseConfig = include 'db_dev.php';

MySQLDatabase::set_connection_charset('utf8');
SSViewer::set_theme($theme_name);
i18n::set_locale('en_US');
SiteTree::enable_nested_urls();

Director::set_dev_servers($_dev_servers);

if(SERVER_DEV){
	ini_set('log_errors', 'On');
	ini_set('error_log', 'log');
	Director::set_environment_type('dev');
	ini_set('display_errors', E_ALL);
	ini_set('display_startup_errors', TRUE);
	error_reporting(E_ALL);
	Security::setDefaultAdmin('admin','password');
}


//SS_Log::add_writer(new SS_LogFileWriter($_project_log),SS_LOG::ERR);
LeftAndMain::setApplicationName($project_name,$project_name,$_project_url);
LeftAndMain::setLogo('zzz_admin/images/logo_small.png','width:24px;height:24px;display:inline-block;position: relative; left:-3px; margin-top: 3px; padding-left: 0;');
LeftAndMain::set_loading_image('zzz_admin/images/logo.gif');
Object::add_extension('File','FileDecorator');
Object::add_extension('Image','ImageDecorator');
Object::add_extension('Page','AdaimyDecorator');
Object::add_extension('SiteConfig','CustomSiteConfig');
SortableDataObject::add_sortable_class('ParralaxImage');
Requirements::set_write_js_to_body(false); 
File::$allowed_extensions[] = 'svg';
?>
