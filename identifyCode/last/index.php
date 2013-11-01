<?php
require_once ('GetCookiesAndIdentifyCodeClass.php');
require_once ('IdentifyCodeClass.php');
require_once ('ReLoginClass.php');
require_once ('ConnectDatabaseClass.php');
require_once ('GetUserInfoClass.php');
require_once ('GetCurriculumClass.php');


//获取登陆令牌
$GetLogInfo = new GetCookieAndIdentifyCodeClass();
$filename = $GetLogInfo -> getCookiesAndIdentifyCode();

//第三步，识别验证码
$valid = new IdentifyCodeClass();
$valid->setImage($filename);
$valid->getHec();
$validCode = $valid->run();

// $reLogin = new ReLoginClass();
// $response = $reLogin -> reLogin( $validCode );
// $interceptstring = explode("</TABLE>", $response);
// /*$interceptstring这个数组0包含学生信息1包含课程信息*/


// $Db = new ConnectDatabaseClass();
// $conn = $Db -> startConnect();

// //$userinfo = new GetUserInfoClass();
// //$userinfo -> resolveUserInfo( $interceptstring[0] );

// $curriculum = new GetCurriculumClass();
// $curriculum -> resolveCurriculumInfo( $interceptstring[1] );

// $Db -> closeConnect($conn);
