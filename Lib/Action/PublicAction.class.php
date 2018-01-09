<?php
/**
 * +--------------------------------
 * 登录注册等公共模块类操作
 * 无需继承基类
 * +--------------------------------
 * @author luohj
 *
 */
class PublicAction extends Action {

	private $baseurl;
	private $call_back;
	private $refer_url;

	public function _initialize() {

		$this->baseurl = C ( 'BASE_URL' ); // 设置全站首页地址
		$this->assign ( 'site', L ( 'site_name' ) ); // 输出站点名称
		if (!empty($_SESSION ['callback'])) {
			// 跳转至登录前输入的url
			$this->refer_url = $_SESSION ['callback'];
		} else {
			$this->refer_url = U ( '/User/index' );
		}
	}

	// 登录页面
	public function login() {

		if(isset($_GET ['callback'])){
			$_SESSION ['callback'] =  $_GET ['callback'] ;
		}else{
			unset($_SESSION ['callback']);
		}
		if (service ( 'Passport' )->isLogged ()) {
			redirect ( U ( '/User/index' ) );
			exit ();
		}
		// 验证码
		$data ['email'] = t ( $_REQUEST ['email'] );
		$data ['uid'] = t ( $_REQUEST ['uid'] );
		$uids = array ();
		// 生成登录缓存
		D ( 'User' )->setUserObjectCache ( array_flip ( array_flip ( $uids ) ) );
		// 第三方平台
		$this->assign ( $data );
		$this->setTitle ( 'login' );
		$this->display ();
	}
	// 普通登录
	public function doLogin() {
		// 检查密码帐号
		$username = t ( $_POST ['username'] );
		if (strlen ( $username ) < 1 || strlen ( $username ) > 32) {
			$this->error ( L ( 'username_format_error' ) );
		}
		$password = t ( $_POST ['password'] );
		if (strlen ( $password ) < 6 || strlen ( $password ) > 16) {
			$this->error ( L ( 'password_rule' ) );
		}
		$result = service ( 'Passport' )->loginLocal ( $username, $password, C('COOKIE_EXPIRE') );
		if ($result) {
			$this->assign ( 'jumpUrl', $this->refer_url );
			$this->success ( L ( 'login_success' ) . $result ['login'] );
		} else {
			$this->error ( L ( 'login_error' ) );
		}
	}
	// 注册页面
	public function register() {
		if(isset($_GET ['callback'])){
			$_SESSION ['callback'] =  $_GET ['callback'] ;
		}else{
			unset($_SESSION ['callback']);
		}
		// 验证码
		$opt_verify = $this->_isVerifyOn ( 'register' );
		if ($opt_verify)
			$this->assign ( 'register_verify_on', 1 );
		$this->setTitle ( L ( 'reg' ) );
		$this->display ();
	}
	// 注册
	public function doRegister() {
		// 验证码
		if (!M('')->autoCheckToken($_POST)) {
			$this->error ( L ( 'error_security_code' ) );
			return false;
		}
		$verify_option = $this->_isVerifyOn ( 'register' );
		if ($verify_option && md5 ( $_POST ['verify'] ) != $_SESSION ['verify'])
			$this->error ( L ( 'error_security_code' ) );
			// 参数合法性检查
		$required_field = array (
				'email' => 'Email',
				'username' => L ( 'username' ),
				'password' => L ( 'password' ),
				'repassword' => L ( 'retype_password' )
		);
		foreach ( $required_field as $k => $v ) {
			if (empty ( $_POST [$k] )) {
				$this->error ( $v . L ( 'not_null' ) );
			}
		}
		if (! $this->isValidEmail ( $_POST ['email'] ))
			$this->error ( L ( 'email_format_error_retype' ) );
		if (! $this->isValidNickName ( $_POST ['username'] ))
			$this->error ( L ( 'username_format_error' ) );
		if (strlen ( $_POST ['password'] ) < 6 || strlen ( $_POST ['password'] ) > 16 || $_POST ['password'] != $_POST ['repassword'])
			$this->error ( L ( 'password_rule' ) );
		if (! $this->isEmailAvailable ( $_POST ['email'] ))
			$this->error ( L ( 'email_used_retype' ) );

			// 注册
		$data ['email'] = $_POST ['email'];
		$data ['username'] = ts_auto_charset ( t ( $_POST ['username'] ) );
		$data ['regip'] = get_client_ip ();
		$salt = substr ( uniqid ( rand () ), - 6 );
		$password = $_POST ['password'];
		$data ['password'] = md5 ( md5 ( $password ) . $salt );
		$data ['regdate'] = time ();
		$data ['salt'] = $salt;

		// $data['is_active'] = $need_email_activate ? 0 : 1;
		if (! ($uid = M ( 'members' )->add ( $data ))){
			$this->error ( L ( 'reg_filed_retry' ) );
		}
		$data['uid']=$uid;
		//同步注册
		service ( 'Passport' )->authRegister($data);
		// 置为已登录, 供完善个人资料时使用
		$result = service ( 'Passport' )->loginLocal ( $uid, $password, C('COOKIE_EXPIRE') );
		$this->assign ( 'jumpUrl', $this->refer_url);
		$this->success ( L ( 'login_success' ) . $result ['login'] );
	}
	// 设置标题
	private function setTitle($lang) {
		$title = L ( $lang ) . '-' . L ( 'site_name' );
		$this->assign ( 'title', $title );
	}
	// 第三方帐号登录入口
	public function auth_login() {
		$type = $_GET ['type'];
		unset($_SESSION[$type] );
		if (! $this->_checkLoginType ( $type )) {
			$this->error ( L ( 'parameter_error' ) );
		}
		$this->_loadTypeLogin ( $type );
		$platform = new $type ();
		$call_back = $this->baseurl . U("/Oauth/callback/type/{$type}");
		$url = $platform->getUrl ( $call_back );
		redirect ( $url );
		exit ();
	}
	// 检查第三方类型
	private function _checkLoginType($type) {
		if (! in_array ( $type, C ( 'BIND_TYPE' ) )) {
			return false;
		} else {
			return true;
		}
	}
	// 加载同步登录各类的类文件
	private function _loadTypeLogin($type) {
		$classfile = SITE_PATH . "/Lib/Plugin/Login/lib/{$type}.class.php";
		if (file_exists ( $classfile )) {
			include_once (SITE_PATH . "/Lib/Plugin/Login/lib/{$type}.class.php");
		}
	}

	// 注销退出
	public function logout() {
		if(isset($_GET ['callback'])){
			$_SESSION ['callback'] =  $_GET ['callback'] ;
		}else{
			unset($_SESSION ['callback']);
		}
		$return=service ( 'Passport' )->logoutLocal ();
		$this->assign ( 'jumpUrl',$this->refer_url);
		$this->success ( L ( 'exit_success' ).$return );
	}
	// 找回密码页面
	public function sendPassword() {
		$this->display ('sendPassword');
	}

	// 发送修改密码邮件
	public function doSendPassword() {
		$_POST ["email"] = t ( $_POST ["email"] );
		if (! $this->isValidEmail ( $_POST ['email'] )){
			$this->error ( L ( 'email_format_error' ) );
		}
		$user = M ( "members" )->where ( '`email`="' . $_POST ['email'] . '"' )->find ();
		$site_name =L ( 'site_name' );
		//$user["username"]= uc_auto_charset($user["username"]);
		if (! $user) {
			if(isset($_POST['userapi'])&& intval($_POST['userapi'])==1){
				return false;
			}
			$this->error ( L ( "email_not_reg" ) );
		} else {
			$code = base64_encode ( $user ["uid"] . "." . md5 ( $user ["uid"] . '+' . $user ["password"] ).".".time() );
			$url = $this->baseurl . U ( '/Public/resetPassword', array (
					'code' => $code,
			) );
			$body = <<<EOD
<strong>{$user["username"]}，你好: </strong><br/><br/>
这封信是由 <strong>{$site_name}</strong> 发送的。<br/>
您收到这封邮件，是因为在<strong>{$site_name}</strong>上这个邮箱地址被登记为用户邮箱，且该用户请求使用 Email 密码重置功能所致。<br/><br/>
----------------------------------------------------------------------<br/>
重要！<br/>

如果您没有提交密码重置的请求或不是我们地理网的注册用户，请立即忽略并删除这封邮件。只在您确认需要重置密码的情况下，才继续阅读下面的内容。<br/>
----------------------------------------------------------------------<br/><br/>
密码重置说明:<br/><br/>

您只需在提交请求后的三天之内，通过点击下面的链接重置您的密码：<br/>
您只需通过点击下面的链接重置您的密码: <br/><br/>

<a href="$url">$url</a><br/><br/>

如果通过点击以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。<br/>
上面的页面打开后，输入新的密码后提交，之后您即可使用新的密码登录网站了。您可以在用户控制面板中随时修改您的密码。<br/>
EOD;
			$email_sent = service ( 'Mail' )->send_email ( $user ['email'], L ( 'reset' ) . L ( 'site_name' ) . L ( 'password_zh' ), $body );
			if(isset($_POST['userapi'])&& intval($_POST['userapi'])==1){
				return $email_sent;
			}
			if ($email_sent) {
				$this->assign ( 'jumpUrl', $this->baseurl );
				$this->success ( L ( 'send_you_mailbox' ) . L ( 'notice_accept' ) );
			} else {
				$this->error ( L ( 'email_send_error_retry' ) );
			}
		}
	}
	// 重设密码页面
	public function resetPassword() {
		$code = explode ( '.', base64_decode ( $_GET ['code'] ) );
		if(!empty($code[2])){
			if((time()-$code[2])>3*24*3600){
				$this->error(L ( 'link_expired' ));
			}
		}else{
			$this->error ( L ( "link_error" ) );
		}
		$user = M ( 'members' )->where ( '`uid`=' . $code [0] )->find ();

		if ($code [1] == md5 ( $code [0] . '+' . $user ["password"] )) {
			$this->assign ( 'email', $user ["email"] );
			$this->assign ( 'code', $_GET ['code'] );
			$this->display ('resetPassword');
		} else {
			$this->error ( L ( "link_error" ) );
		}
	}

	// 重设密码
	public function doResetPassword() {
		if ($_POST ["password"] != $_POST ["repassword"]) {
			$this->error ( L ( "password_same_rule" ) );
		}
		$code = explode ( '.', base64_decode ( $_POST ['code'] ) );
		if(!empty($code[2])){
			if((time()-$code[2])>3*24*3600){
				$this->error(L ( 'link_expired' ));
			}
		}else{
			$this->error ( L ( "link_error" ) );
		}
		$user = M ( 'members' )->where ( '`uid`=' . $code [0] )->find ();
		if ($code [1] == md5 ( $code [0] . '+' . $user ["password"] )) {
			$save ['password'] = md5 ( md5 ( $_POST ['password'] ) . $user ['salt'] );
			$res = M ( 'members' )->where ( '`uid`=' . $code [0] ) -> save ( $save );
			// 去掉用户缓存信息
			S ( 'S_userInfo_' . $code [0], null );
//			if ($res) {
				$this->assign ( 'jumpUrl', U ( '/Public/login' ) );
				$this->success ( L ( 'save_success' ) );
//			} else {
//				$this->error ( L ( 'save_error_retry' ) );
//			}
		} else {
			$this->error ( L ( "safety_code_error" ) );
		}
	}
	// 检查Email地址是否合法
	public function isValidEmail($email) {

		return preg_match ( "/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email ) !== 0;
	}
	// 检查Email是否可用
	public function isEmailAvailable($email = null) {
		$return_type = empty ( $email ) ? 'ajax' : 'return';
		$email = empty ( $email ) ? $_POST ['email'] : $email;

		$res = D ( 'User', 'home' )->where ( '`email`="' . $email . '"' )->find ();
		if (! $res) {
			if ($return_type === 'ajax')
				echo 1;
			else
				return true;
		} else {
			if ($return_type === 'ajax')
				echo - 6;
			else
				return false;
		}
	}
	// 检查用户名是否符合规则，且是否为唯一
	public function isValidNickName($name) {

		$return_type = empty ( $name ) ? 'ajax' : 'return';
		$name = empty ( $name ) ? t ( $_POST ['username'] ) : $name;

		if (! isLegalUsername ( $name )) {
			if ($return_type === 'ajax') {
				echo - 1;
				return;
			} else
				return false;
		} else if (checkKeyWord ( $name )) {
			if ($return_type === 'ajax') {
				echo - 2;
				return;
			} else {
				return false;
			}
		}
		// var_dump($this->mid);
		$res = M ( 'members' )->where ( "username='{$name}'" )->count ();
		if (! $res) {
			if ($return_type === 'ajax')
				echo 1;
			else
				return true;
		} else {
			if ($return_type === 'ajax')
				echo - 3;
			else
				return false;
		}
	}
	// 检查验证码
	public function isVerify($verify = '') {
		$return_type = empty ( $verify ) ? 'ajax' : 'return';
		if (md5 ( $_POST ['verify'] ) == $_SESSION ['verify']) {
			if ($return_type === 'ajax')
				echo 1;
			else
				return true;
		} else {
			if ($return_type === 'ajax')
				echo 0;
			else
				return false;
		}
	}
	// 验证码
	public function verify() {
		import ( 'ORG.Util.Image' );
		Image::buildImageVerify ();
	}
	public function error404() {
		$this->display ( '404' );
	}

	private function _isVerifyOn($type = 'login') {
		// 检查验证码
		if ($type != 'login' && $type != 'register')
			return false;
		$opt_verify = $GLOBALS ['ts'] ['site'] ['site_verify'];
		return in_array ( $type, $opt_verify );
	}
	//手动生成密码
	public function password(){
		$password='123456';
		$salt='267b04';
		$str = md5(md5($password).$salt);
		var_dump($str);
		exit;
	}
}