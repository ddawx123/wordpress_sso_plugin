<?php
require_once(dirname(__FILE__).'/../../../../wp-load.php');
require_once(dirname(__FILE__).'/libs/common.class.php');
$common = Common::getInstance();
if (isset($_REQUEST['token'])) {
	$userinfo = $common->getUserInfo($_REQUEST['token']);
	if (!isset($userinfo->usermail) || !isset($userinfo->username)) {
		exit($common->generateMsg(403, '当前会话密钥无效，请尝试重新登录平台。', null));
	}
	else {
		$ud = get_userdatabylogin($userinfo->username);
		if (!$ud) {
			$ud = array(
				'user_login'	=>	$userinfo->username,
				'user_pass'	=>	'dingstudio@'.rand(100, 999),
				'user_nicename'	=>	$userinfo->username,
				'user_email'	=>	$userinfo->usermail
			);
			if (wp_insert_user($ud)) {
				$newud = get_userdatabylogin($userinfo->username);
				$uid = $newud->ID;
				wp_set_current_user($uid, $userinfo->username);
				wp_set_auth_cookie($uid);
				exit($common->generateMsg(200, '首次登录系统，已完成用户资料的自动迁入，并成功登录。待前端重定向至后台！', null));
			}
			else {
				exit($common->generateMsg(500, '身份验证通过，但您首次登录本系统，在尝试迁入您的用户资料时发生了异常。建议稍后再次尝试，或联系管理员解决该问题！', null));
			}
		}
		else {
			$uid = $ud->ID;
			wp_set_current_user($uid, $userinfo->username);
			wp_set_auth_cookie($uid);
			exit($common->generateMsg(200, '登录成功，待前端发起重定向进入指定页面。', null));
		}
	}
	//header('Location: ../?sso&hash='.CApi::GenerateSsoToken('someone@example.org', 'example_password_plaintext'));
}
else {
	exit('Single Sign On API isn\'t available');
}