<?php
/**
 * 基类
 * @author luohj
 *
 */
class BaseAction extends Action {

	protected  $uid;
	private $username;
	private $userinfo;
	public $site_name;

	public function _initialize() {
		// 控制器初始化
		$this->checkLogin();
		if (method_exists ( $this, '_init' ))
			$this->_init ();
		$this->site_name=C('SITE_NAME');
		$market_data=D('Stock')->marketList();

		$this->assign('market_data',$market_data);
		$this->assign('keywords',L('keywords'));
		$this->assign('description',L('description'));
		$this->assign('img_url',C('IMG_URL'));
		$this->assign ( "user", $this->userinfo );
	}

	// 控制菜单显示
	public function getMenu() {
		$user = array ();
		$user ['menu'] = S ( 'userMenuList_' . $this->uid );
		$user ['module'] = S ( 'userModuleName_' . $this->uid );
		if (empty ( $user ['menu'] ) || empty ( $user ['module'] )) {
			$user = $this->menu ();
		}
		$this->assign('menu', $user['menu']);
		return $user ['module'];
	}
	//设置菜单
	public function setMenu(){
		$this->menu();
	}

	// 根据权限列出菜单
	public function menu() {
		$roleid = $this->userinfo ['roleid'];
		$sql="select menulist from cz_admin_role where id={$roleid}";
		$res = M ( '' )->query ($sql);
		$sql = "select * from cz_admin_menu where id in ({$res[0]['menulist']}) ORDER BY id ASC";
		$root = M ( '' )->query ( $sql );
		$result = array ();
		$piv_str = '';
		foreach ( $root as $i => $x ) {
			$result [$i] ['pid'] ['id'] = $x ['id'];
			$result [$i] ['pid'] ['name'] = $x ['name'];
			$result [$i] ['pid'] ['url'] = $x ['url'];
			$result [$i] ['sid'] = M ( 'admin_menu' )->where ( 'parentid=' . $x ['id'] )->order('ord asc')->select ();
		}
		foreach ( $root as $k => $r ) {
			$module [] = $r ['module'];
		}
		unset ( $root );
		$res = array ();
		// 设置缓存
		S ( 'userMenuList_' . $this->uid, $result, 14400 );
		$res ['menu'] = $result;
		$module = explode ( ",", implode ( ",", $module ) );
		S ( 'userModuleName_' . $this->uid, $module, 14400 );
		$res ['module'] = $module;
		return $res;
	}
	public function checkLogin(){
	
		global $_SGLOBAL;
		// 验证登录
		if (service ( 'Passport' )->isLogged ()) { // 未登录
			$this->uid=$_SGLOBAL['mid'];
			$this->username=$_SGLOBAL['username'];
			$this->userinfo=$_SGLOBAL['userinfo'];

		}else{
			$this->uid=0;
		}
	}
}