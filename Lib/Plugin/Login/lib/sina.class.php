<?php
include_once 'config.inc.php';
include_once( 'sina/weibooauth.php' );
class sina{

	var $loginUrl;
	private $_sina_akey;
	private $_sina_skey;

	public function __construct() {
		$this->_sina_akey = SINA_WB_AKEY;
		$this->_sina_skey = SINA_WB_SKEY;
	}

    function getUrl($call_back = null) {
		$o = new SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey  );
		$this->loginUrl = $o->getAuthorizeURL( $call_back);
		return $this->loginUrl;
	}

	function getJSON($userid,$passwd){
		$o = new SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey  );
		$keys = $o->getRequestToken();
		$return = $o->getAuthorizeJSON( $keys['oauth_token'] ,false , $userid,$passwd);
		if($return){
			$return = json_decode($return);
			$o = new SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey ,$keys['oauth_token'] , $keys['oauth_token_secret']  );
			$access_token = $o->getAccessToken(  $return->oauth_verifier ) ;
			return $access_token;
		}
	}

	//用户资料
	function userInfo(){
		if(isset($_SESSION['sina']['uid'])){
			$uid = $_SESSION['sina']['uid'];
		}
		$me = $this->doClient()->show_user_by_id($uid);
		$user['id']          = $me['id'];
		$user['username']    = $me['name'];
		$user['province']    = $me['province'];
		$user['city']        = $me['city'];
		$user['location']    = $me['location'];
		$user['userface']    = str_replace(  $user['id'].'/50/' , $user['id'].'/180/' ,$me['profile_image_url'] );
		$user['sex']         = ($me['gender']=='m')?1:0;
		return $user;
	}

    private function doClient($opt){
		$access_token = ( $opt['access_token'] )? $opt['access_token']:$_SESSION['sina']['access_token'];
		return new SaeTClientV2( $this->_sina_akey , $this->_sina_skey ,  $access_token );
	}

	//验证用户
    function checkUser($call_back){
    	$o = new SaeTOAuthV2( $this->_sina_akey , $this->_sina_skey );
    	if (isset($_REQUEST['code'])) {
    		$keys = array();
    		$keys['code'] = $_REQUEST['code'];
    		$keys['redirect_uri'] = $call_back;
    		try {
    			$token = $o->getAccessToken( 'code', $keys ) ;
    		} catch (OAuthException $e) {
    		}
    	}
		 $_SESSION['sina'] = $token;
		 return $token;
	}

	//发布一条微博
	function update($text,$opt){
		return $this->doClient($opt)->update($text);
	}

	//上传一个照片，并发布一条微博
	function upload($text,$pic,$opt){
		return $this->doClient($opt)->upload($text,$pic);
	}


}
?>
