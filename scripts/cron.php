<?php
require_once dirname(__DIR__).'/bootstrap.php';

use GitWrapper\Event\GitLoggerListener;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Log to a file named "git.log"
$log = new Logger('git');
$log->pushHandler(new StreamHandler(dirname(__DIR__).'/logs/git.log', Logger::DEBUG));

// Instantiate the listener, add the logger to it, and register it.
$listener = new GitLoggerListener($log);

use GitWrapper\GitWrapper;
use es\eucm\xapi\Profile2Html;
use es\eucm\xapi\XapiCiConfig;

// http://stackoverflow.com/questions/1707801/making-a-temporary-dir-for-unpacking-a-zipfile-into/1707859#1707859
function createTempFolder ($dir=false, $prefix='php')
{
    $tempfile=tempnam(sys_get_temp_dir(),'');
    if (file_exists($tempfile)) { unlink($tempfile); }
    mkdir($tempfile);
    if (is_dir($tempfile)) { return $tempfile; }
}

$profile2Html = new Profile2Html();
$wrapper = new GitWrapper();
$wrapper->addLoggerListener($listener);

$taskDir=dirname(__DIR__).'/cron';
foreach(glob($taskDir.'/*.profile') as $profile) {
    $githubUrl=file_get_contents("$profile");
    $githubUrl=explode("\n",$githubUrl)[0];
    $shaCommit=mb_split('\\.', basename($profile))[0];
    $tempdir = createTempFolder();
    chdir($tempdir);
    $git = $wrapper->clone($githubUrl, $tempdir);
    $git->checkout($shaCommit);
    
    $ciConfig = new XapiCiConfig($tempdir);
    $profilesPaths = $ciConfig->getProfilesPaths();
    
    foreach($prfilesPaths as $path) {
        $path = realpath($path);
        $profileName=basename($path);
        $profileName=mb_split('\\.', basename($profileName))[0];
        if (mb_strpos($path, $tempdir) !== 0) {
            echo "path '$path' not relative to repository";
            exit(1);
        }
        $html = $profile2Html->generate($path);
        $profileHtmlPath = realpath(XAPI_PROFILE_PUBLIC_SITE.'/'.$profileName);
        if (mb_strpos($profileHtmlPath, XAPI_PROFILE_PUBLIC_SITE) !== 0) {
            echo "path '$profileHtmlPath' not relative to publication site";
            exit(1);
        }
        
        file_put_contents(XAPI_PROFILE_PUBLIC_SITE.'/'.$profileName);
    }
    rmdir($tempdir);
    //ulink($profile);
}
