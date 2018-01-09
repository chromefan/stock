<?php

/**
 * 第三方登录
 * @author luohj
 *
 */
class OauthAction  extends Action{
	
	private $type;
	private $platform;
	private $call_back;
	private $m;
	private $baseurl;
	private $refer_url;
	
	
	public function _initialize(){
		$this->m=M ( 'user_oauth_login' );
		$this->baseurl=C ( 'BASE_URL' );
		
		if (!empty($_SESSION ['callback'])) {
			// 跳转至登录前输入的url
			$this->refer_url = $_SESSION ['callback'];
		} else {
			$this->refer_url = U ( '/User/index' );
		}
	}
	
	public function setType($type = '') {
		if (isset ( $_GET ['type'] )) {
			$this->type = $_GET ['type'];
		} elseif (! empty ( $type )) {
			$this->type = $type;
		}
		if (! $this->_checkLoginType ( $this->type )) {
			$this->error ( L ( 'parameter_error' ) );
			return;
		} else {
			$this->_loadTypeLogin ( $this->type );
			$this->platform = new $this->type ();
			$this->call_back = C ( 'BASE_URL' ) . U("/Oauth/callback/type/{$this->type}");
			return $this->type;
		}
	}
	
	// 第三方回调处理
	public function callback() {
		if (!empty ( $_SESSION)) {
			 $_SESSION=null;
		}
		$this->setType ();
		$res = $this->platform->checkUser ( $this->call_back );
		if ($res) {
			$this->oauthlogin ();
		} else {
			$this->error ( L ( 'not_authorised' ) );
		}
	}
	
	// 外站帐号登录成功处理
	public function oauthlogin() {
		$type = $this->type;
		$userinfo = $this->platform->userInfo ();
		// 检查是否成功获取用户信息
		if (empty ( $userinfo ) || empty ( $userinfo ['id'] ) || empty ( $userinfo ['username'] )) {
			$this->assign ( 'jumpUrl', $this->baseurl );
			$this->error ( L ( 'user_information_filed' ) );
		}
		if (service ( 'Passport' )->isLogged ()) {
		    $this->success(L ( 'login_success' ))  ;
		}
		if ($info = $this->m->where ( "`type_uid`='" . $userinfo ['id'] . "' AND type='{$type}'" )->find ()) {
			$user = D ( 'User' )->where ( "uid=" . $info ['uid'] )->find ();
			if (empty ( $user )) {
				// 未在本站找到用户信息, 删除用户站外信息,让用户重新登录
				$this->m->where ( "type_uid=" . $userinfo ['id'] . " AND type='{$this->type}'" )->delete ();
			} else {
				$now = time ();
				$ctime = $info ['ctime'];
				$expires_in = intval ( $info ['expires_in'] );
				// 超过生命周期更新
				if (($now - $ctime) > $expires_in) {
					$syncdata ['id'] = $info ['id'];
					$syncdata ['ctime'] = $now;
					$syncdata ['nickname'] = $userinfo ['username'];
					$syncdata ['userface'] = $userinfo ['userface'];
					if (isset ( $_SESSION [$type] ['openid'] )) {
						$syncdata ['openid'] = $_SESSION [$type] ['openid'];
					}
					$syncdata ['access_token'] = $_SESSION [$type] ['access_token'];
					$syncdata ['expires_in'] = $_SESSION [$type] ['expires_in'];
					$this->m->save ( $syncdata );
				}
				$result = service ( 'Passport' )->registerLogin ( $user, C('COOKIE_EXPIRE'));

				$this->assign ( 'jumpUrl', $this->refer_url );
				$this->success ( $user ['username'] . L ( 'login_success' ) . $result ['login'] );
				exit;
			}
			redirect(U('/Public/login'));
		}else{
			$this->_register($type, $userinfo);
		}
	}
	// 登录后绑定其他帐号处理
	private function bindother($type = '', $userinfo = array()) {
		global $_SGLOBAL;
		$user = D ( 'User' )->getUserInfoCache ( $_SGLOBAL ['mid'] );
		// 对原用户名进行保留
		// 如果该类型的绑定已经进行过，则是系统异常。正确流程并不会进行两次绑定
		$sync ['uid'] = $user ['uid'];
		$sync ['type'] = $type;
		$jumpUrl = U ( '/User/bind' );
		if ($this->m->where ( $sync )->count ()) {
			$this->error ( L ( 'bind_error' ), $jumpUrl );
			exit ();
		}
		S ( 'user_login_' . $user ['uid'], null );
		if ($this->_addOauthData($user['uid'], $userinfo, $type)) {
			$this->success ( L ( 'bind_success' ), $jumpUrl );
		} else {
			$this->error ( L ( 'bind_error' ), $jumpUrl );
		}
	}

	// 第三方无本站帐号登录注册
	private function _register($type,$userinfo) {

		$result=array();
		// 初使化用户信息, 激活帐号
		//系统自动分配帐号
		$auto_username=$userinfo ['username'];
		$data ['username'] =   $auto_username;
		$data ['userface'] = $userinfo ['userface'];
		//$data ['email'] = t ($email);
		$salt = substr ( uniqid ( rand () ), - 6 );
		$password = C('PUB_PASSWORD');
		$data ['password'] = md5 ( md5 ( $password ) . $salt );
		$data ['salt'] = $salt;
		$data ['regtime'] = time ();
		$data ['regip'] = get_client_ip ();
        $data ['source'] = $type;
        
		if ($uid = D ( 'User' )->add ( $data )) {
			// 记录至同步登录表
			$this->_addOauthData($uid, $userinfo, $type);
			$res=service ( 'Passport' )->loginLocal ( $uid, $password, C('COOKIE_EXPIRE') );
			$result ['status'] = 1;
			$result ['jumpUrl'] = $this->refer_url;
			$result ['info'] = "同步登录成功";
			$result ['success']=true;
		} else {
			$result ['status'] = 0;
			$result ['info'] = "同步帐号发生错误";
			$result ['success']=false;
		}
		var_dump($result);
		exit;
		return $result;
	}
	//记录OAUTH
	private function _addOauthData($uid,$userinfo,$type){
		$syncdata ['type_uid'] = $userinfo ['id'];
		$syncdata ['type']=$type;
		$res =  $this->m->where($syncdata)->find();
		if($res){
			$this->m->where($syncdata)->delete();
		}
		$syncdata ['uid'] =  $uid;
		$syncdata ['ctime'] = time();
		$syncdata ['nickname'] = $userinfo ['username'];
		$syncdata ['userface'] = $userinfo ['userface'];
		$syncdata ['sex'] = $userinfo ['sex'];
		if(isset($_SESSION [$type] ['openid'])){
			$syncdata ['openid'] = $_SESSION [$type] ['openid'];
		}
		$syncdata ['is_rsync'] = 1;
		$syncdata ['access_token'] = $_SESSION [$type] ['access_token'];
		$syncdata ['expires_in'] = $_SESSION [$type] ['expires_in'];
		return $this->m->add ( $syncdata );
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
	private function _loadTypeLogin() {
		$classfile = SITE_PATH . "/Lib/Plugin/Login/lib/{$this->type}.class.php";
		if (file_exists ( $classfile )) {
			include_once (SITE_PATH . "/Lib/Plugin/Login/lib/{$this->type}.class.php");
		}
	}
	// 设置标题
	private function setTitle($lang) {
		$title = L ( $lang ) . '-' . L ( 'site_name' );
		$this->assign ( 'title', $title );
	}
}

?>