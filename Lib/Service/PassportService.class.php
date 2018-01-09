<?php
/**
 * 通行证服务
 *
 * @author luohj
 */

class PassportService {
	var $_error;
	
	/**
	 * 获取错误信息
	 *
	 * @return string 返回具体的错误信息
	 */
	
	public function getLastError() {
		return $this->_error;
	}
	/**
	 * 验证用户是否已登录
	 *
	 * 按照session -> cookie的顺序检查是否登录
	 *
	 * @return boolean 登录成功是返回true, 否则返回false
	 */
	public function isLogged() {
		global $_SGLOBAL;
		// 检查session
		$this->checkSession ();
		// 验证本地系统登录
		if (intval ( $_SGLOBAL ['mid'] ) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 根据标示符(email或uid)和未加密的密码获取本地用户 (密码为null时不参与验证)
	 *
	 * @param string $identifier
	 *        	标示符内容 (为数字时:标示符类型为uid, 其他:标示符类型为email)
	 * @param string|boolean $password
	 *        	未加密的密码
	 * @return array boolean 否则返回false
	 */
	public function getLocalUser($identifier, $password = null) {
		
		if (empty ( $identifier ))
			return false;
		if (isValidEmail ( $identifier )) {
			$identifier_type = 'email';
		} else if (is_numeric ( $identifier )) {
			$identifier_type = 'uid';
		} else {
			$identifier_type = 'username';
		}
		$user = D ( 'User' )->getUserByIdentifier ( $identifier, $identifier_type );
		$passwordmd5 =md5 ( C ( 'SECURE_CODE' ) . md5 ( $password ) );

		if (! $user) {
			$this->_error = 'Nothing';
			return false;
		} else if ($user ['password'] !=  $passwordmd5 ) {
			$this->_error = 'Username or Password error';
			return false;
		} else {
			return $user;
		}
	}
	
	/**
	 * 使用本地账号登录 (密码为null时不参与验证)
	 *
	 * @param string $email        	
	 * @param string|boolean $password        	
	 * @return boolean
	 */
	public function loginLocal($identifier, $password = null, $is_remember_me = false) {
		$user = $this->getLocalUser ( $identifier, $password );
		return $user ? $this->registerLogin ( $user, $is_remember_me, $password ) : false;
	}
	
	/**
	 * 注册用户的登录状态 (即: 注册cookie + 注册session + 记录登录信息)
	 *
	 * @param array $user        	
	 * @param boolean $is_remeber_me        	
	 */
	public function registerLogin(array $user, $is_remeber_me = false, $password = null) {
		if (empty ( $user ))
			return false;
		return $this->_recordLogin ( $user, $is_remeber_me );
	}
	
	/**
	 * 设置登录状态、记录登录日志
	 */
	private function _recordLogin($user, $is_remeber_me = false) {
		global $_SGLOBAL;
		$_SGLOBAL ['mid'] = $user ['uid'];
		//$_SGLOBAL ['email'] = $user ['email'];
		if (! cookie (C('COOKIE_NAME'))) {
			// 创建cookie
			$expire = $is_remeber_me ? (3600 * 24 * 365) : (3600 * 1);
			cookie ( C('COOKIE_NAME'), authcode ( "{$user['uid']}\t{$user['password']}\t{$user['username']}", 'ENCODE', C ( 'SECURE_CODE' ) ), $expire );
		}
		return $user;
		//$this->recordLogin ( $user );
	}
	/**
	 * 注销本地登录
	 */
	public function logoutLocal() {
		global $_SGLOBAL;
		$_SGLOBAL=array();
		$_SESSION=array();
		// 注销cookie
		cookie (C('COOKIE_NAME'), NULL );
		// 注销管理员
		// unset($_SESSION['cngusersAdmin']);
	}
	
	public function checkSession() {
		global $_SGLOBAL;
		if (cookie ( C('COOKIE_NAME') )) {
			$cookie = $this->getCookieUser ();
			$uid = $cookie ['0'];
			$password = $cookie ['1'];
			$res = D ( 'User' )->where ( "uid={$uid}" )->find ();
			if ($res) {
				if ($res ['password'] == $password) {
					$_SGLOBAL ['mid'] = $uid;
					$_SGLOBAL ['username'] = $res ['username'];
					$_SGLOBAL ['userinfo'] = $res ;
				} else {
					$_SGLOBAL ['mid'] = 0;
				}
			} else {
				$_SGLOBAL ['mid'] = 0;
			}
		}
		if (empty ( $_SGLOBAL ['mid'] )) {
			$this->logoutLocal ();
		}
	}
	/**
	 * 获取cookie中记录的用户ID
	 */
	public function getCookieUser() {
		$cookie = cookie ( C('COOKIE_NAME'));
		$cookie = explode ( "\t", authcode ( $cookie, 'DECODE', C ( 'SECURE_CODE' ) ) );
		return $cookie;
	}
	
	/**
	 * 记录登录session信息
	 *
	 * @param array $user
	 *        	用户登录信息
	 */
	public function recordLogin($user) {
		
		$now = time ();
		M ( '' )->execute ( 'DELETE FROM ' . C ( 'DB_PREFIX' ) . 'user_login_log where uid=' . $user ['uid'] . ' OR ctime<' . ($now - 3600 * 24 * 7) );
		$data ['uid'] = $user ['uid'];
		$data ['ip'] = get_client_ip ();
		$data ['ctime'] = time ();
		$data ['client']=getBrowser();
		M ( 'user_login_log' )->add ( $data);
	}
	
	// 检查Email地址是否合法
	public function isValidEmail($email) {
		
		return preg_match ( "/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email ) !== 0;
	}
	
	// 检查Email是否可用
	public function isEmailAvailable($email = null) {
		$return_type = empty ( $email ) ? 'ajax' : 'return';
		$email = empty ( $email ) ? $_POST ['email'] : $email;
		
		$res = M ( 'members' )->where ( '`email`="' . $email . '"' )->find ();
		
		if (! $res) {
			if ($return_type === 'ajax')
				echo 'success';
			else
				return true;
		} else {
			if ($return_type === 'ajax')
				echo L ( 'email_used' );
			else
				return false;
		}
	}
	
	/* 后台管理相关方法 */
	
	// 运行服务，系统服务自动运行
	public function run() {
		return;
	}
}
//uc_sync();