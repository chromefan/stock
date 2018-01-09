<?php
/** 
 * * +-----------------+ ** 用户模型 * +-----------------+ 
 * * @author luohj * */
class UserModel extends Model{
	
	
	var $uid;
	protected $tableName='user';
	public static $nameHash = array ();
	
	
	public function isEmailAvailable($email){
		$map['email']=$email;
		$res=$this->where($map)->find();
		if(empty($res)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 根据查询条件查询用户
	 *
	 * @param array|string $map
	 *        	查询条件
	 * @param string $field
	 *        	字段
	 * @param int $limit
	 *        	限制条数
	 * @param string $order
	 *        	结果排序
	 * @param boolean $is_find_page
	 *        	是否分页
	 * @return array
	 */
	public function getUserByMap($map = array(), $field = '*', $limit = '', $order = '', $is_find_page = true) {
		if ($is_find_page) {
			return $this->where ( $map )->field ( $field )->order ( $order )->findPage ( $limit );
		} else {
			return $this->where ( $map )->field ( $field )->order ( $order )->limit ( $limit )->findAll ();
		}
	}
	/**
	 * 获取单个用户名
	 *
	 * @param array $map
	 */
	public function getUsername($uid) {
		if (empty ( $uid )) {
			return '';
		}
		$res = $this->where ( 'uid=' . $uid )->field ( 'username' )->find ();
		return $res ['username'];
	}
	/**
	 * 更新操作
	 *
	 * @param string $type
	 *        	操作
	 * @return boolean
	 */
	function upDate($type) {
		return $this->$type ();
	}
	
	/**
	 * 根据标示符(uid或uname或email或domain)获取用户信息
	 *
	 * 首先检查缓存(缓存ID: user_用户uid / user_用户uname), 然后查询数据库(并设置缓存).
	 *
	 * @param string|int $identifier
	 *        	标示符内容
	 * @param string $identifier_type
	 *        	标示符类型. (uid, uname, email, domain之一)
	 */
	public function getUserByIdentifier($identifier, $identifier_type = 'uid') {
		if ($identifier_type == 'uid' && ! is_numeric ( $identifier ))
			return false;
		else if (! in_array ( $identifier_type, array (
				'uid',
				'username',
				'email',
				'domain'
		) ))
			return false;
		$user = $this->getUserInfoCache ( $identifier, $identifier_type );
		return $user;
	}
	
	/**
	 * 缓存用户列表
	 *
	 * 缓存key的格式为: user_用户uid 和 user_用户昵称
	 *
	 * @param array $user_list
	 *        	用户ID列表, 或者用户详情列表. 如果为用户ID列表时, 本方法会首先获取用户详情列表, 然后缓存.
	 * @return boolean true:缓存成功 false:缓存失败
	 */
	public function setUserObjectCache($user_list) {
		if (! is_array ( $user_list ))
			return false;
		if (! is_array ( $user_list [0] ) && ! is_numeric ( $user_list [0] ))
			return false;
	
		if (is_numeric ( $user_list [0] )) {
			foreach ( $user_list as $val ) {
				$user = $this->getUserInfoCache ( $val );
			}
		} else {
			foreach ( $user_list as $val ) {
				$this->getUserInfoCache ( $val ['uid'] );
			}
		}
	
		return true;
	}
	
	
	public function isUnameAvailable($uname) {
		if (! isValidUname ( $uname )) // 格式错误
			return false;
		else if ($this->getUserByIdentifier ( $uname, 'username' )) // 在UC已存在
			return false;
	
		return true;
	}
	
	/**
	 * 获取用户基本信息缓存
	 *
	 * @param int $uid
	 *        	用户UID，string $type 查询类型(uid,uname,email,domain)
	 * @return array 用户基本信息
	 */
	public function getUserInfoCache($uid, $type = 'uid') {
		// 如果为空，则直接退出
		$defaultValue = $uid;
		if (empty ( $uid )) {
			return false;
		}
	
		if (! in_array ( $type, array (
				'uid',
				'username',
				'email',
				'domain'
		) )) {
			return false;
		}
	
		// 获取用户的UID
		if (! is_numeric ( $uid ) && $type != 'uid') {
			if (isset ( self::$nameHash [$type] [$defaultValue] )) {
				$uid = self::$nameHash [$type] [$defaultValue];
			} else {
				$map [$type] = $uid;
				$uid = $this->where ( $map )->getField ( 'uid' );
				self::$nameHash [$type] [$defaultValue] = $uid;
			}
			if (empty ( $uid )) {
				return false;
			}
		}
/* 		$userInfo = $this->where ( 'uid=' . $uid )->find ();
		S ( 'S_userInfo_' . $uid, $userInfo );
		return $userInfo; */
		$userInfo = S ( 'S_userInfo_' . $uid );
		// 获取用户基本信息缓存
		if (empty ( $userInfo )) {
			// 姓名
			$userInfo = $this->where ( 'uid=' . $uid )->find ();
				
			S ( 'S_userInfo_' . $uid, $userInfo );
		}
		return $userInfo;
	}

}