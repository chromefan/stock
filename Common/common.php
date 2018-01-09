<?php
// 跳转到404 结束掉程序
function redirect404() {
	header ( 'HTTP/1.1 404 Not Found' );
	header ( 'Status: 404 Not Found' );
	echo @file_get_contents ( SITE_PATH . '/404.html' );
	exit ();
}
// 实例化插件
function plugin($name, $params = array()) {
	return X ( $name, $params, 'Plugin' );
}

// 实例化服务
function service($name, $params = array()) {
	return X ( $name, $params, 'Service' );
}

// 调用接口服务
function X($name, $params = array(), $domain = 'Service') {
	
	$class = $name . 'Service';
	$classfile = LIB_PATH . $domain . '/' . $class . '.class.php';
	if (file_exists ( $classfile )) {
		require_cache ( $classfile );
		$pass = new $class ();
		return $pass;
	}

}
//随机数 
function random($length) {
	$hash = '';
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
	$max = strlen($chars) - 1;
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
/**
 * 转换为安全的纯文本
 *
 * @param string $text        	
 * @param boolean $parse_br
 *        	是否转换换行符
 * @param int $quote_style
 *        	ENT_NOQUOTES:(默认)不过滤单引号和双引号 ENT_QUOTES:过滤单引号和双引号
 *        	ENT_COMPAT:过滤双引号,而不过滤单引号
 * @return string null null:参数错误
 */
function t($text, $parse_br = false, $quote_style = ENT_NOQUOTES) {
	if (is_numeric ( $text ))
		$text = ( string ) $text;
	
	if (! is_string ( $text ))
		return null;
	
	if (! $parse_br) {
		$text = str_replace ( array (
				"\r",
				"\n",
				"\t" 
		), ' ', $text );
	} else {
		$text = nl2br ( $text );
	}
	
	// $text = stripslashes($text);
	$text = htmlspecialchars ( $text, $quote_style, 'UTF-8' );
	
	return $text;
}
// 加密函数
function jiami($txt, $key = null) {
	if (empty ( $key ))
		$key = C ( 'SECURE_CODE' );
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
	$nh = rand ( 0, 64 );
	$ch = $chars [$nh];
	$mdKey = md5 ( $key . $ch );
	$mdKey = substr ( $mdKey, $nh % 8, $nh % 8 + 7 );
	$txt = base64_encode ( $txt );
	$tmp = '';
	$i = 0;
	$j = 0;
	$k = 0;
	for($i = 0; $i < strlen ( $txt ); $i ++) {
		$k = $k == strlen ( $mdKey ) ? 0 : $k;
		$j = ($nh + strpos ( $chars, $txt [$i] ) + ord ( $mdKey [$k ++] )) % 64;
		$tmp .= $chars [$j];
	}
	return $ch . $tmp;
}

// 解密函数
function jiemi($txt, $key = null) {
	if (empty ( $key ))
		$key = C ( 'SECURE_CODE' );
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
	$ch = $txt [0];
	$nh = strpos ( $chars, $ch );
	$mdKey = md5 ( $key . $ch );
	$mdKey = substr ( $mdKey, $nh % 8, $nh % 8 + 7 );
	$txt = substr ( $txt, 1 );
	$tmp = '';
	$i = 0;
	$j = 0;
	$k = 0;
	for($i = 0; $i < strlen ( $txt ); $i ++) {
		$k = $k == strlen ( $mdKey ) ? 0 : $k;
		$j = strpos ( $chars, $txt [$i] ) - $nh - ord ( $mdKey [$k ++] );
		while ( $j < 0 )
			$j += 64;
		$tmp .= $chars [$j];
	}
	return base64_decode ( $tmp );
}
/**
 * 检查Email地址是否合法
 *
 * @return boolean
 */
function isValidEmail($email) {
	return preg_match ( "/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email ) !== 0;
}
/**
 * 检查邮政编码
 * 
 * @param
 *        	$str
 * @return boolean
 */
function isPostalcode($str) {
	return preg_match ( "/^[1-9]\d{5}$/i", $str ) !== 0;
}
/**
 * 检查手机号码
 * 
 * @param string $str        	
 * @return boolean
 */
function isValidMobile($str) {
	return preg_match ( "/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $str ) !== 0;
}
/**
 * 检查Email是否可用
 *
 * @return boolean
 */
function isEmailAvailable($email, $uid = false) {
	return D ( 'User', 'home' )->isEmailAvailable ( $email, $uid );
}
/**
 * DES加密函数
 *
 * @param string $input        	
 * @param string $key        	
 */
function desencrypt($input, $key) {
	$size = mcrypt_get_block_size ( 'des', 'ecb' );
	$input = pkcs5_pad ( $input, $size );
	
	$td = mcrypt_module_open ( 'des', '', 'ecb', '' );
	$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
	@mcrypt_generic_init ( $td, $key, $iv );
	$data = mcrypt_generic ( $td, $input );
	mcrypt_generic_deinit ( $td );
	mcrypt_module_close ( $td );
	$data = base64_encode ( $data );
	return $data;
}

/**
 * DES解密函数
 *
 * @param string $input        	
 * @param string $key        	
 */
function desdecrypt($encrypted, $key) {
	$encrypted = base64_decode ( $encrypted );
	$td = mcrypt_module_open ( 'des', '', 'ecb', '' ); // 使用MCRYPT_DES算法,cbc模式
	$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
	$ks = mcrypt_enc_get_key_size ( $td );
	@mcrypt_generic_init ( $td, $key, $iv ); // 初始处理
	
	$decrypted = mdecrypt_generic ( $td, $encrypted ); // 解密
	
	mcrypt_generic_deinit ( $td ); // 结束
	mcrypt_module_close ( $td );
	
	$y = pkcs5_unpad ( $decrypted );
	return $y;
}

/**
 *
 * @see desencrypt()
 */
function pkcs5_pad($text, $blocksize) {
	$pad = $blocksize - (strlen ( $text ) % $blocksize);
	return $text . str_repeat ( chr ( $pad ), $pad );
}

/**
 *
 * @see desdecrypt()
 */
function pkcs5_unpad($text) {
	$pad = ord ( $text {strlen ( $text ) - 1} );
	
	if ($pad > strlen ( $text ))
		return false;
	if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
		return false;
	
	return substr ( $text, 0, - 1 * $pad );
}

/**
 * 关键字过滤
 */
function keyWordFilter($content) {
	$audit = model ( 'Xdata' )->lget ( 'audit' );
	if ($audit ['open'] && $audit ['keywords']) {
		$replace = $audit ['replace'] ? $audit ['replace'] : '[和*谐]';
		$arr_keyword = explode ( '|', $audit ['keywords'] );
		foreach ( $arr_keyword as $k => $v ) {
			$content = str_replace ( $v, $replace, $content );
		}
		return $content;
	} else {
		return $content;
	}
}

/**
 * 检测内容是否含有关键字
 */
function checkKeyWord($content) {
	return false; // 关闭
	$audit = model ( 'Xdata' )->lget ( 'audit' );
	if ($audit ['open'] && $audit ['keywords']) {
		$arr_keyword = explode ( '|', $audit ['keywords'] );
		foreach ( $arr_keyword as $k => $v ) {
			$num = stristr ( $content, $v ) ? $num + 1 : $num;
		}
		if ($num) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * 获取用户的绑定状态
 *
 * @param int $uid
 *        	用户ID
 * @param string $type
 *        	平台类型
 */
function bindstate($uid, $type) {
	return M ( "login" )->where ( "uid=$uid AND type='{$type}'" )->count ();
}

/**
 * 将给定用户设为在线
 *
 * @param int $uid        	
 */
function setOnline($uid) {
	$cookie_name = 'login_time_' . $uid;
	$cookie_time = intval ( cookie ( $cookie_name ) );
	$now = time ();
	$expire = 5 * 60; // 有效期: 5min
	if ($cookie_time < ($now - $expire)) {
		// 删除7天前登录的用户
		M ( '' )->execute ( 'DELETE FROM ' . C ( 'DB_PREFIX' ) . 'user_online where ctime<' . ($now - 3600 * 24 * 7) );
		cookie ( $cookie_name, $now, $expire );
		$sql = 'REPLACE INTO ' . C ( 'DB_PREFIX' ) . 'user_online (`uid`,`ctime`) VALUES ("' . $uid . '", "' . $now . '")';
		return M ( '' )->execute ( $sql );
	} else {
		return null;
	}
}

/**
 * 获取当前在线用户数(有效期15分钟)
 *
 * @return int
 */
function getOnlineUserCount() {
	$time = time () - 15 * 60;
	$sql = "SELECT COUNT(*) AS count FROM " . C ( 'DB_PREFIX' ) . "user_online WHERE `ctime` > " . $time;
	$res = M ( '' )->query ( $sql );
	return $res [0] ['count'];
}

/**
 * 根据access检查是否有权访问当前节点(APP_NAME/MODULE_NAME/ACTION_NAME)
 * 
 * @return boolean
 */
function canAccess() {
	$acl = C ( 'access' );
	return $acl [APP_NAME . '/' . MODULE_NAME . '/' . ACTION_NAME] === true || $acl [APP_NAME . '/' . MODULE_NAME . '/*'] === true || $acl [APP_NAME . '/*/*'] === true;
}

/**
 * Navigates through an array and removes slashes from the values.
 *
 * If an array is passed, the array_map() function causes a callback to pass the
 * value back to the function. The slashes from this value will removed.
 *
 * @since 2.0.0
 *       
 * @param array|string $value
 *        	The array or string to be striped.
 * @return array string array (or string in the callback).
 */
function stripslashes_deep($value) {
	if (is_array ( $value )) {
		$value = array_map ( 'stripslashes_deep', $value );
	} elseif (is_object ( $value )) {
		$vars = get_object_vars ( $value );
		foreach ( $vars as $key => $data ) {
			$value->{$key} = stripslashes_deep ( $data );
		}
	} else {
		$value = stripslashes ( $value );
	}
	
	return $value;
}

/**
 * 通过循环遍历将对象转换为数组
 *
 * @param object $var        	
 * @return array
 */
function object_to_array($var) {
	if (is_object ( $var ))
		$var = get_object_vars ( $var );
	
	if (is_array ( $var ))
		$var = array_map ( 'object_to_array', $var );
	
	return $var;
}

/**
 * 根据给定的省市的代码获取实际地址
 *
 * @param int $province        	
 * @param int $city        	
 */
function getLocation($province, $city) {
	$l = model ( 'Area' )->getAreaTree ();
	
	foreach ( $l ['provinces'] as $key => $value ) {
		$arr ['province'] [$value ['id']] = $value ['name'];
		if ($value ['citys']) {
			foreach ( $value ['citys'] as $k => $v ) {
				foreach ( $v as $kk => $vv ) {
					$arr ['city'] [$value ['id']] [$kk] = $vv;
				}
			}
		}
	}
	if ($province) {
		$return = $arr ['province'] [$province];
		if ($city) {
			$return = $return . ' ' . $arr ['city'] [$province] [$city];
		}
	}
	return $return;
}

/**
 * 锁定表单
 *
 * @param int $life_time
 *        	表单锁的有效时间(秒). 如果有效时间内未解锁, 表单锁自动失效.
 * @return boolean 成功锁定时返回true, 表单锁已存在时返回false
 */
function lockSubmit($life_time = 30) {
	if (isset ( $_SESSION ['LOCK_SUBMIT_TIME'] ) && intval ( $_SESSION ['LOCK_SUBMIT_TIME'] ) > time ()) {
		return false;
	} else {
		$_SESSION ['LOCK_SUBMIT_TIME'] = time () + intval ( $life_time );
		return true;
	}
}

/**
 * 检查表单是否已锁定
 *
 * @return boolean 表单已锁定时返回true, 否则返回false
 */
function isSubmitLocked() {
	return isset ( $_SESSION ['LOCK_SUBMIT_TIME'] ) && intval ( $_SESSION ['LOCK_SUBMIT_TIME'] ) < time ();
}

/**
 * 表单解锁
 *
 * @return void
 */
function unlockSubmit() {
	unset ( $_SESSION ['LOCK_SUBMIT_TIME'] );
}

/**
 * 对strip_tags函数的扩展, 可以过滤object, param, embed等来自编辑器的标签
 * 
 * @param unknown_type $str        	
 * @param unknown_type $allowable_tags        	
 */
function real_strip_tags($str, $allowable_tags) {
	$str = stripslashes ( htmlspecialchars_decode ( $str ) );
	return strip_tags ( $str, $allowable_tags );
}

/**
 * 检查是否是以手机浏览器进入(IN_MOBILE)
 */
function isMobile() {
	$mobile = array ();
	static $mobilebrowser_list = 'Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
	// note 获取手机浏览器
	if (preg_match ( "/$mobilebrowser_list/i", $_SERVER ['HTTP_USER_AGENT'], $mobile )) {
		return true;
	} else {
		if (preg_match ( '/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			return false;
		} else {
			if ($_GET ['mobile'] === 'yes') {
				return true;
			} else {
				return false;
			}
		}
	}
}

function isiPhone() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iPhone' ) !== false;
}

function isiPad() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iPad' ) !== false;
}

function isiOS() {
	return isiPhone () || isiPad ();
}

function isAndroid() {
	return strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Android' ) !== false;
}

/**
 * 获取用户浏览器型号。新加浏览器，修改代码，增加特征字符串.把IE加到12.0 可以使用5-10年了.
 */
function getBrowser() {
	if (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Maxthon' )) {
		$browser = 'Maxthon';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 12.0' )) {
		$browser = 'IE12.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 11.0' )) {
		$browser = 'IE11.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 10.0' )) {
		$browser = 'IE10.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 9.0' )) {
		$browser = 'IE9.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 8.0' )) {
		$browser = 'IE8.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 7.0' )) {
		$browser = 'IE7.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'MSIE 6.0' )) {
		$browser = 'IE6.0';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'NetCaptor' )) {
		$browser = 'NetCaptor';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Netscape' )) {
		$browser = 'Netscape';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Lynx' )) {
		$browser = 'Lynx';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Opera' )) {
		$browser = 'Opera';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Chrome' )) {
		$browser = 'Google';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Firefox' )) {
		$browser = 'Firefox';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'Safari' )) {
		$browser = 'Safari';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'iphone' ) || strpos ( $_SERVER ['HTTP_USER_AGENT'], 'ipod' )) {
		$browser = 'iphone';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'ipad' )) {
		$browser = 'iphone';
	} elseif (strpos ( $_SERVER ['HTTP_USER_AGENT'], 'android' )) {
		$browser = 'android';
	} else {
		$browser = 'other';
	}
	return $browser;
}
/**
 * 检查给定的用户名是否合法
 *
 * 合法的用户名由2-10位的中英文/数字/下划线/减号组成
 *
 * @param string $username        	
 * @return boolean
 */
function isLegalUsername($username) {
	// GB2312: preg_match("/^[".chr(0xa1)."-".chr(0xff)."A-Za-z0-9_-]+$/",
	// $username)
	
	return preg_match ( "/^[\x{4e00}-\x{9fa5}A-Za-z0-9_-]+$/u", $username ) && mb_strlen ( $username, 'UTF-8' ) >= 2 && mb_strlen ( $username, 'UTF-8' ) <= 10;
}

function isValidUname($uname) {
	return isLegalUsername ( $uname );
}

function isUnameAvailable($uname) {
	return D ( 'User' )->isUnameAvailable ( $uname );
}

function isValidPassword($password) {
	return strlen ( $password ) >= 6 && strlen ( $password ) < 16;
}
/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * +----------------------------------------------------------
 * 
 * @param string $len
 *        	长度
 * @param string $type
 *        	字串类型
 *        	0 字母 1 数字 其它 混合
 * @param string $addChars
 *        	额外字符
 *        	+----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { // 位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	$chars = str_shuffle ( $chars );
	$str = substr ( $chars, 0, $len );
	return $str;
}

function ts_auto_charset($content) {
	return ts_change_charset ( $content, 'UTF8', C ( 'DB_CHARSET' ) );
}

function uc_auto_charset($content) {
	return ts_change_charset ( $content, C ( 'DB_CHARSET' ), 'UTF8' );
}
/* 字符、数组串编码转换 */
function ts_change_charset($fContents, $from = 'UTF8', $to = 'GBK') {
	$from = strtoupper ( $from ) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper ( $to ) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper ( $from ) === strtoupper ( $to ) || empty ( $fContents ) || (is_scalar ( $fContents ) && ! is_string ( $fContents ))) {
		// 如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string ( $fContents )) {
		if (function_exists ( 'mb_convert_encoding' )) {
			return mb_convert_encoding ( $fContents, $to, $from );
		} elseif (function_exists ( 'iconv' )) {
			return iconv ( $from, $to, $fContents );
		} else {
			return $fContents;
		}
	} elseif (is_array ( $fContents )) {
		foreach ( $fContents as $key => $val ) {
			$_key = ts_change_charset ( $key, $from, $to );
			$fContents [$_key] = ts_change_charset ( $val, $from, $to );
			if ($key != $_key)
				unset ( $fContents [$key] );
		}
		return $fContents;
	} else {
		return $fContents;
	}
}
function mkdir_by_uid($uid, $dir = '.') {
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	!is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
	return $dir1.'/'.$dir2.'/'.$dir3;
}
// 将用户ID转换为二级路径
function convertUidToPath($uid)
{
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	return $dir1.'/'.$dir2.'/'.$dir3.'/';
}
function get_avatar($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}
function getUserFace($uid, $size) {
	require_once SITE_PATH.'/api/uc_client/uc_config.inc.php';
	$size = ($size) ? $size : 'm';
	if ($size == 'm') {
		$type = 'middle';
	} elseif ($size == 's') {
		$type = 'small';
	} else {
		$type = 'big';
	}
	$userface = C('UC_AVATAR_PATH').'/'.get_avatar($uid,$type) ;
	if (is_file ( $userface )) {
		return UC_API.'/data/avatar/'.get_avatar($uid,$type);
	} else {
		return C ( 'BASE_URL' ) . "/Public/images/user_pic_{$type}.gif";
	}
}
// Cookie 设置、获取、删除
function ssetcookie($prefix,$domain,$name, $value='', $option=null) {
	// 默认设置
	$config = array(
			'prefix' => $prefix, // cookie 名称前缀
			'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
			'path' => C('COOKIE_PATH'), // cookie 保存路径
			'domain' => $domain, // cookie 有效域名
	);
	// 参数设置(会覆盖黙认设置)
	if (!empty($option)) {
		if (is_numeric($option))
			$option = array('expire' => $option);
		elseif (is_string($option))
		parse_str($option, $option);
		$config = array_merge($config, array_change_key_case($option));
	}
	// 清除指定前缀的所有cookie
	if (is_null($name)) {
		if (empty($_COOKIE))
			return;
		// 要删除的cookie前缀，不指定则删除config设置的指定前缀
		$prefix = empty($value) ? $config['prefix'] : $value;
		if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
			foreach ($_COOKIE as $key => $val) {
				if (0 === stripos($key, $prefix)) {
					setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
					unset($_COOKIE[$key]);
				}
			}
		}
		return;
	}
	$name = $config['prefix'] . $name;
	if ('' === $value) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
	} else {
		if (is_null($value)) {
			setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
			unset($_COOKIE[$name]); // 删除指定cookie
		} else {
			// 设置cookie
			$expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
			setcookie($name, $value, $expire, $config['path'], $config['domain']);
			$_COOKIE[$name] = $value;
		}
	}
}
function _authcode($string,$p){
	return $string;
}
// 字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	
	$ckey_length = 4; // 随机密钥长度 取值 0-32;
	                  // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	                  // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	                  // 当此值为 0 时，则不产生随机密钥
	
	$key = md5 ( $key ? $key : UC_KEY );
	$keya = md5 ( substr ( $key, 0, 16 ) );
	$keyb = md5 ( substr ( $key, 16, 16 ) );
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
	
	$cryptkey = $keya . md5 ( $keya . $keyc );
	$key_length = strlen ( $cryptkey );
	
	$string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
	$string_length = strlen ( $string );
	
	$result = '';
	$box = range ( 0, 255 );
	
	$rndkey = array ();
	for($i = 0; $i <= 255; $i ++) {
		$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
	}
	for($j = $i = 0; $i < 256; $i ++) {
		$j = ($j + $box [$i] + $rndkey [$i]) % 256;
		$tmp = $box [$i];
		$box [$i] = $box [$j];
		$box [$j] = $tmp;
	}
	
	for($a = $j = $i = 0; $i < $string_length; $i ++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box [$a]) % 256;
		$tmp = $box [$a];
		$box [$a] = $box [$j];
		$box [$j] = $tmp;
		$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
	}
	
	if ($operation == 'DECODE') {
		if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
			return substr ( $result, 26 );
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
	}
}
/**
 * 输出消息
 *
 * @param unknown_type $msg
 * @param unknown_type $gourl
 * @param unknown_type $target
 * @param unknown_type $exit
 */
function showmassage($msg,$gourl=0,$target='',$exit=1){
	header("Content-type:text/html;charset=utf-8");
	$js = "<script language='javascript'>alert('$msg');";
	$js.=empty($gourl)?"history.back(-1)":"{$target}location.href='$gourl'";
	$js.="</script>";
	echo $js;
	if($exit)
	{
		exit();
	}
}
/**
 * ****************************************************************
 * PHP截取UTF-8字符串，解决半字符问题。
 * 英文、数字（半角）为1字节（8位），中文（全角）为3字节
 *
 * @return 取出的字符串, 当$len小于等于0时, 会返回整个字符串
 * @param $str 源字符串
 * $len 左边的子串的长度
 * **************************************************************
 */
function utf_substr($str, $len) {
	for($i = 0; $i < $len; $i ++) {
		$temp_str = substr ( $str, 0, 1 );
		if (ord ( $temp_str ) > 127) {
			$i ++;
			if ($i < $len) {
				$new_str [] = substr ( $str, 0, 3 );
				$str = substr ( $str, 3 );
			}
		} else {
			$new_str [] = substr ( $str, 0, 1 );
			$str = substr ( $str, 1 );
		}
	}
	return join ( $new_str );
}
/**
 * 获取cookie中记录的用户ID
 */
function getCookieUser() {
	$cookie = cookie ( 'admin_auth' );
	$cookie = explode ( "\t", authcode ( $cookie, 'DECODE', C ( 'SECURE_CODE' ) ) );
	return $cookie;
}
function get_post_data(){
	$res=array();
	if (is_array($_POST)&&!empty($_POST)) {
		unset($_POST['toSave']);
		unset($_POST['__hash__']);
		foreach ($_POST as $k => $v) {
			if (is_numeric($v)) {
				$res[$k]=intval($v);
			}else{
				$res[$k]=t($v);
			}
		}
		return $res;
	}
	return false;
}
//获取 年月数据
function year_month(){
	foreach ( range ( date('Y'),"1999" ) as $k => $v ){
		$date['year'][]=$v;
	}
	foreach ( range ('1','12') as $k => $v ){
		$date['month'][]=$v;
	}
	return $date;
}
//组合表格数据
function table_data($data,$m=8){
	$j = 0;
	$i = 0;
	$list=array();
	foreach ( $data as $k => $v ) {
		$list [$j] [$i] = $data [$k];
		$i ++;
		if ($i % $m == 0) {
			$i = 0;
			$j ++;
		}
	}
	return $list;
}
//数字转换为英文月份
function enmonth($k){
	$k=intval($k);
	$month=array(
			1  => "January",
			2  => "February",
			3  => "March",
			4  => "April",
			5  => "May",
			6  => "June",
			7  => "July",
			8  => "August",
			9  => "September",
			10 => "October",
			11 => "November",
			12 => "December");
	return $month[$k];
}
function is_hanzi($chinese)     {
	$regex = '/([\x81-\xfe][\x40-\xfe])/';
	$matches = array();
	return preg_match($regex, $chinese);
}
// 当前时辰
function shichen() {
	$now = intval ( date ( 'H' ) );
	$shichen_num = '';
	switch ($now) {
		case $now == 23 || $now == 0 || $now == 24 :
			$shichen_num = 1;
			break;
		case $now == 1 || $now == 2 :
			$shichen_num = 2;
			break;
		case $now == 3 || $now == 4 :
			$shichen_num = 3;
			break;
		case $now == 5 || $now == 6 :
			$shichen_num = 4;
			break;
		case $now == 7 || $now == 8 :
			$shichen_num = 5;
			break;
		case $now == 9 || $now == 10 :
			$shichen_num = 6;
			break;
		case $now == 11 || $now == 12 :
			$shichen_num = 7;
			break;
		case $now == 13 || $now == 14 :
			$shichen_num = 8;
			break;
		case $now == 15 || $now == 16 :
			$shichen_num = 9;
			break;
		case $now == 17 || $now == 18 :
			$shichen_num = 10;
			break;
		case $now == 19 || $now == 20 :
			$shichen_num = 11;
			break;
		case $now == 21 || $now == 22 :
			$shichen_num = 12;
			break;
		default :
			$shichen_num = 1;
			break;
	}
	return $shichen_num;
}
function shichen_name($num){
	$data=M('shichen')->select();
	if($num>0){
		foreach($data as $v){
			$shichen[$v['id']]=$v['name'];
		}
		return $shichen[$num];
	}
	return $data;
}
	/**
 * google api 二维码生成【QRcode可以存储最多4296个字母数字类型的任意文本，具体可以查看二维码数据格式】
 * @param string $chl 二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded.如果需要传递的信息超过2K个字节，请使用POST方式
 * @param int $widhtHeight 生成二维码的尺寸设置
 * @param string $EC_level 可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
 *                         L-默认：可以识别已损失的7%的数据
 *                         M-可以识别已损失15%的数据
 *                         Q-可以识别已损失25%的数据
 *                         H-可以识别已损失30%的数据
 * @param int $margin 生成的二维码离图片边框的距离
 */ 
function get_QRcode($chl,$widhtHeight ='150',$EC_level='L',$margin='0') 
{ 
		$chl = urlencode($chl); 
		echo '<img src="http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" widhtHeight="'.$widhtHeight.'" widhtHeight="'.$widhtHeight.'"/>'; 
}
function get_lunar($date=''){
	import ( 'ORG.Util.Lunar');
	import ( 'ORG.Util.Date');
	$lunar = new Lunar();
	//公历转农历
	if(empty($date)){
		$date=date('Y-m-d',time());
	}
	$n = $lunar->S2L($date);
	$d = new Date();
	$gz = $d->magicInfo('GZ');
	return $n;
}
function checkReferer(){
	if(isset($_SERVER['HTTP_REFERER'])){
		//针对部分浏览器可能无HTTP_REFERER,所以做这么一个判断  
		$servername=$_SERVER['SERVER_NAME'];
		$sub_from=$_SERVER["HTTP_REFERER"];
		$sub_len=strlen($servername);
		$checkfrom=substr($sub_from,7,$sub_len);
		if($checkfrom!=$servername){
			return false;
		}else{
			return true;
		}
	}
	return false;
}
/**
 * Author:Zero
 * 1.上传文件类型错误
 * 2.无法创建目录
 * 3.上传失败
 */
function _uploadfile($filename, $tmpfile, $uploadroot = '', $is_new_name = true) {
	$extension = strtolower ( substr ( strrchr ( $filename, "." ), 1 ) );
	if (! in_array ( $extension, C ( 'UPLOAD_TYPE' ) )) {
		exit ( '1' ); // 上传文件类型错误
	}
	
	if ($uploadroot == '')
		$uploadroot = ROOT_PATH . '/public/data/temp/';
	
	$uploadpath = $uploadroot . date ( "Ym" ) . "/";
	
	if ($is_new_name) {
		$filename = time () . str_pad ( mt_rand ( 1, 9999 ), 4, '0', STR_PAD_LEFT ) . '.' . $extension;
	}
	
	$attachpath = $uploadpath . $filename;
	
	if (! is_dir ( $uploadroot ))
		@mkdir ( $uploadroot, 0777 ) or die ( "2" );
	if (! is_dir ( $uploadpath ))
		@mkdir ( $uploadpath, 0777 ) or die ( "2" );
	if (@is_uploaded_file ( $tmpfile )) {
		if (! @move_uploaded_file ( $tmpfile, $attachpath )) {
			@unlink ( $tmpfile ); // 删除临时文件
			exit ( '3' );
		}
	}
	return $filename;
}
/**
 * 判断远程图片地址是否存在
 * @param unknown_type $url
 * @return boolean
 */
function curl_url_exist($url){

	$ch = curl_init();
	$timeout = 30000;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$contents = curl_exec($ch);
	if (preg_match("/404/", $contents) || empty($contents)){
		return false;
	}else{
		return true;
	}
}
function do_post($url, $data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30000);
	$ret = curl_exec($ch);

	curl_close($ch);
	return $ret;
}

function get_url_contents($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30000);
	$result =  curl_exec($ch);
	curl_close($ch);

	return $result;
}
function get_url_contents_curl($url,$is_cookie=false) {
		
		$c = curl_init ();
		if($is_cookie){
			$cookie_file="cookie.txt";
			curl_setopt($c, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookie_file); //使用cookies
		}
		curl_setopt ( $c, CURLOPT_URL, $url );
		//curl_setopt ( $c, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; MyIE2; InfoPath.1)" );
		curl_setopt ( $c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1" );
		curl_setopt ( $c, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $c, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $c, CURLOPT_CONNECTTIMEOUT, 300 );
		curl_setopt ( $c, CURLOPT_TIMEOUT, 300 );
		$contents = curl_exec ( $c );
		//$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		curl_close ( $c );
		return $contents;
}
/**
 * Balances tags of string using a modified stack.
 *
 * @since 2.0.4
 *
 * @author Leonard Lin <leonard@acm.org>
 * @license GPL
 * @copyright November 4, 2001
 * @version 1.1
 * @todo Make better - change loop condition to $text in 1.2
 * @internal Modified by Scott Reilly (coffee2code) 02 Aug 2004
 *		1.1  Fixed handling of append/stack pop order of end text
 *			 Added Cleaning Hooks
 *		1.0  First Version
 *
 * @param string $text Text to be balanced.
 * @return string Balanced text.
 */
function force_balance_tags( $text ) {
	$tagstack = array();
	$stacksize = 0;
	$tagqueue = '';
	$newtext = '';
	// Known single-entity/self-closing tags
	$single_tags = array( 'area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source' );
	// Tags that can be immediately nested within themselves
	$nestable_tags = array( 'blockquote', 'div', 'object', 'q', 'span' );

	// WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);
	// WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

	while ( preg_match("/<(\/?[\w:]*)\s*([^>]*)>/", $text, $regex) ) {
		$newtext .= $tagqueue;

		$i = strpos($text, $regex[0]);
		$l = strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';
		// Pop or Push
		if ( isset($regex[1][0]) && '/' == $regex[1][0] ) { // End Tag
			$tag = strtolower(substr($regex[1],1));
			// if too many closing tags
			if( $stacksize <= 0 ) {
				$tag = '';
				// or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			else if ( $tagstack[$stacksize - 1] == $tag ) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop( $tagstack );
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ( $j = $stacksize-1; $j >= 0; $j-- ) {
					if ( $tagstack[$j] == $tag ) {
					// add tag to tagqueue
						for ( $k = $stacksize-1; $k >= $j; $k--) {
							$tagqueue .= '</' . array_pop( $tagstack ) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// If it's an empty tag "< >", do nothing
			if ( '' == $tag ) {
				// do nothing
			}
			// ElseIf it presents itself as a self-closing tag...
			elseif ( substr( $regex[2], -1 ) == '/' ) {
				// ...but it isn't a known single-entity self-closing tag, then don't let it be treated as such and
				// immediately close it with a closing tag (the tag will encapsulate no text as a result)
				if ( ! in_array( $tag, $single_tags ) )
					$regex[2] = trim( substr( $regex[2], 0, -1 ) ) . "></$tag";
			}
			// ElseIf it's a known single-entity tag but it doesn't close itself, do so
			elseif ( in_array($tag, $single_tags) ) {
				$regex[2] .= '/';
			}
			// Else it's not a single-entity tag
			else {
				// If the top of the stack is the same as the tag we want to push, close previous tag
				if ( $stacksize > 0 && !in_array($tag, $nestable_tags) && $tagstack[$stacksize - 1] == $tag ) {
					$tagqueue = '</' . array_pop( $tagstack ) . '>';
					$stacksize--;
				}
				$stacksize = array_push( $tagstack, $tag );
			}

			// Attributes
			$attributes = $regex[2];
			if( ! empty( $attributes ) && $attributes[0] != '>' )
				$attributes = ' ' . $attributes;

			$tag = '<' . $tag . $attributes . '>';
			//If already queuing a close tag, then put this tag on, too
			if ( !empty($tagqueue) ) {
				$tagqueue .= $tag;
				$tag = '';
			}
		}
		$newtext .= substr($text, 0, $i) . $tag;
		$text = substr($text, $i + $l);
	}

	// Clear Tag Queue
	$newtext .= $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while( $x = array_pop($tagstack) )
		$newtext .= '</' . $x . '>'; // Add remaining tags to close

	// WP fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}
/**
 * Replaces double line-breaks with paragraph elements.
 *
 * A group of regex replaces used to identify text formatted with newlines and
 * replace double line-breaks with HTML paragraph tags. The remaining
 * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
 * or 'false'.
 *
 * @since 0.71
 *
 * @param string $pee The text which has to be formatted.
 * @param bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
 * @return string Text which has been converted into correct paragraph tags.
 */
function wpautop($pee, $br = true) {

	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	$pee = preg_replace('/<img.+?"\s\/>/is', '', $pee);
	$pee = preg_replace('/<a\shref=".+?"><\/a>/is', '', $pee);
	return $pee;
}
?>