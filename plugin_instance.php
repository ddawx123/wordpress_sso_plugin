<?php
/**
 * @package DingStudio_SSO
 * @version 2.0
 */
/*
Plugin Name: DingStudio_SSO
Plugin URI: http://www.dingstudio.cn/
Description: This is a single sign on plugin for WordPress by dingstudio passport SSO System.
Author: David Ding（alone◎浅忆）
Version: 2.0
Author URI: http://www.dingstudio.cn/
*/

/**
 * 显示插件启用提示语
 * @return string 返回字符串提示句
 */
function example() {
	echo "<p id='dsso_header_tip'>温馨提示：已使用小丁工作室统一身份认证系统接管账户系统。</p>";
}

/**
 * 用户登录拦截器（用于阻断wordpress原生登录接口）
 * @return mixed 返回具体动作类型
 */
function login_interceptor() {
	//$page_viewed = basename($_SERVER['REQUEST_URI']);
	$page_viewed = basename($_SERVER['SCRIPT_NAME']);
	if ($page_viewed === "wp-login.php" && $_SERVER['REQUEST_METHOD'] === 'GET'){
		if(is_user_logged_in()) {
			if(!isset($_REQUEST['redirect_to']) || @$_REQUEST['redirect_to'] == '') {
				wp_redirect(home_url().'/wp-admin/index.php');
			}
			else {
				wp_redirect($_REQUEST['redirect_to']);
			}
		}
		else {
			redirectSSO();
		}
	}
}

/**
 * 重定向到统一身份认证平台
 * @return mixed 重定向到SSOPassport页面
 */
function redirectSSO() {
	wp_redirect(plugins_url('',__FILE__).'/wp-sso/ssologin.htm?redirect_to='.urlencode($_SERVER['REQUEST_URI']));
}

/**
 * 登出JS注入过程
 * @return string 返回JavaScript代码
 */
function logout_jsloader() {
	echo '<script>var custom_logout = true;</script>';
}

/**
 * 登出过程拦截器
 * @return mixed 返回具体动作类型
 */
function logout_interceptor() {
	$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to']:$_SERVER['REQUEST_URI'];
	wp_redirect(plugins_url('',__FILE__).'/wp-sso/ssologout.htm?redirect_to='.urlencode($redirect_to));
	exit();
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'example' ); //挂钩后台的提示区 div，用于注入显示自定义提示语
add_action( 'init', 'login_interceptor' ); //挂钩系统全局init函数，用于控制全局登录过程
add_action( 'admin_head', 'logout_jsloader' ); //挂钩后台的head标签组，注入自定义登出js
add_action( 'wp_logout', 'logout_interceptor' ); //挂钩登出过程，使登出时自动同步退出通行证

?>
