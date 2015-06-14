<?php

session_start();
header('Content-Type: text/html; charset=utf-8');

require_once '../admin/app/core/autoloader.php';
require_once '../admin/config/paths.php';

$registry = new Registry();
$request = new Request();
$session = new Session();
$template = new Template();
$output = new Output();
$db = DB::getInstance();
$registry->set('request', $request);
$registry->set('session', $session);
$registry->set('template', $template);
$registry->set('db', $db);
$registry->set('output', $output);


$host = $request->server['HTTP_HOST'];
$uri = $request->server['REQUEST_URI'];

//files upload
if (count($request->files) > 0) {
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        return false;
    }

    //posts bild
    if (isset($request->files['bild1']) && !empty($request->files['bild1'])) {
        Upload::init(
                array('png', 'jpeg', 'gif', 'jpg'), array('min-width' => 400, 'min-height' => 350, 'max-width' => 600, 'max-height' => 520), 2000000, BILDER . 'posts/l'
        );

        $Upload = Upload::progress('bild1');
        $registry->get('session')->data['upload_error'] = $Upload;
        $registry->get('session')->data['bild1'] = Upload::$uploededFilesName;

       // echo $Upload;
        //exit();
    }

    //banner bild

    if (isset($request->files['bild']) && !empty($request->files['bild'])) {
        Upload::init(
                array('png', 'jpeg', 'gif', 'jpg'), array('min-width' => 980, 'min-height' => 340, 'max-width' => 1200, 'max-height' => 415), 2000000, BILDER . 'banner/'
        );

        $Upload = Upload::progress('bild');
        $registry->get('session')->data['bannerUpload'] = $Upload;
        $registry->get('session')->data['bild'] = Upload::$uploededFilesName;
    }
    //veranstaltungen bild

    if (isset($request->files['veranst_bIld']) && !empty($request->files['veranst_bIld'])) {
        Upload::init(
                array('png', 'jpeg', 'gif', 'jpg'), array('min-width' => 495, 'min-height' => 555, 'max-width' => 600, 'max-height' => 675), 2000000, BILDER . 'veranstaltungen/'
        );

        $Upload = Upload::progress('veranst_bIld');
        $registry->get('session')->data['veranst_Upload'] = $Upload;
        $registry->get('session')->data['veranst_bIld'] = Upload::$uploededFilesName;
    }

    //gebetszeiten txt
    if (isset($request->files['gebetszeiten']) && !empty($request->files['gebetszeiten'])) {
        $gebetszeiten = Upload::progressFile('gebetszeiten', 'gebetszeiten', PUBLI . 'gebetszeiten/');
        $registry->get('session')->data['gebet_zeiten_upload'] = $gebetszeiten;
    }
}


if (count($request->post) > 0) {
    $sichertheit = new Security();
    $request->post = array_filter($request->post);
    $cleanedPost = $sichertheit->clean($request->post);
    $cleanedPostKeys = array_keys($cleanedPost);
    if (in_array('email', $cleanedPostKeys) && in_array('pass', $cleanedPostKeys)) {
        $_SESSION['userData'] = $cleanedPost;
    } else {
        $_SESSION['Postdata'] = $cleanedPost;
    }
    header("HTTP/1.1 303 See Other");
    header("Location:http://$host$uri");
} elseif (count($_SESSION['userData']) > 0) {
    $user = new Login_mod($_SESSION['userData']);
    $user->LogMeIn();
} elseif (isset($_SESSION['login']) && $_SESSION['login'] == true) {
    $app = new Bootstrap($registry);
} else {
    $registry->get('template')->set_template('login/form.tpl', 'admin');
    $registry->get('template')->set('test', 'null');
    echo $registry->get('template')->render();
} 






