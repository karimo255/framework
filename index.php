<?php

session_start();
header('Content-Type: text/html; charset=utf-8');


require_once 'config/paths.php';
require_once 'app/core/autoloader.php';



//new registry
$registry = new Registry();

//lang 
$lang = new Lang('header');
$spr = $lang->autoDetectSprache();

//tools
$request = new Request();
$session = new Session();
$template = new Template();
$db = DB::getInstance();

//atache lang to registry
$registry->set('lang', $spr);

//atache tools to registry
$registry->set('request', $request);
$registry->set('session', $session);
$registry->set('template', $template);
$registry->set('db', $db);

//server info
$host = $request->server['HTTP_HOST'];
$uri = $request->server['REQUEST_URI'];
$downloadHost = "http://assalam.ddns.net/public/quran/";



//download 
if (isset($request->get['fileName'])) {
    $fileName = $request->get['fileName'];
    $path = $request->get['path'];
    $erlaubtePfads = array('ghamdi', 'afasy');
    if (!in_array($path, $erlaubtePfads)) {
        return;
    }
    $down = new Download($downloadHost . $path . '/' . $fileName . '.mp3');
    $down->exportData();
    return;
}



//post requests
if (count($request->post) > 0) {
    $sichertheit = new Security();
    $cleanedPost = $sichertheit->clean($request->post);
    if (array_key_exists('email', $cleanedPost) && array_key_exists('pass', $cleanedPost)  ) {
        $_SESSION['userData'] = $cleanedPost;
    } else {
        $_SESSION['Postdata'] = $cleanedPost;
    }
     header("HTTP/1.1 303 See Other");
     header("Location:http://$host$uri");
} elseif (count($_SESSION['userData']) > 0) {//login if needed
    $user = new user($registry);
    $user->LogMeIn();
} else {
    $app = new Bootstrap($registry); //app start
}









