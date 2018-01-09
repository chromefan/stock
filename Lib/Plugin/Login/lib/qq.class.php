<?php
/**
 * QQ授权操作
 * @author luohj
 */
include_once 'config.inc.php';
include_once ('qq/qqoauth.php');
class qq {
	
	var $loginUrl;
	
	function __construct() {
		OAuth::init ( QQ_KEY, QQ_SECRET );
	}
	
	function getUrl($url) {
		$this->loginUrl = OAuth::getAuthorizeURL ( $url );
		return $this->loginUrl;
	}
	
	// 用户资料
	function userInfo() {
		$this->doClient ();
		$me = json_decode ( Tencent::api ( 'user/get_user_info' ), true );
		$user ['id'] = $_SESSION ['qq'] ['openid'];
		$user ['username'] = $me ['nickname'];
		$user ['userface'] = $me ['figureurl_2'];
		$user ['sex'] =  $me['gender']=='男'?1:0;
		return $user;
	}
	// 验证用户
	function checkUser($call_back) {
		// 验证授权
		if ($_GET ['code']) { // 应用频道
			$code = $_GET ['code'];
			$url = OAuth::getAccessToken ( $code, $call_back );
			$r = Http::request ( $url );
			if (! $r) {
				$r = file_get_contents ( $url );
			}
			parse_str ( $r, $out );
			// 存储授权数据
			if ($out ['access_token']) {
				$_SESSION ['qq'] ['access_token'] = $out ['access_token'];
				$_SESSION ['qq'] ['openid'] = OAuth::getOpenid ( $out ['access_token'] );
				$_SESSION ['qq'] ['expires_in'] = $out ['expires_in'];
				return $out;
			} else {
				return false;
			}
		
		}
	}
	
	private function doClient($opt) {
		$access_token = ($opt ['access_token']) ? $opt ['access_token'] : $_SESSION ['qq'] ['access_token'];
		$_SESSION ['q_access_token'] = $access_token;
		$_SESSION ['q_openid'] = ($opt ['openid']) ? $opt ['openid'] : $_SESSION ['qq'] ['openid'];
	}
	
	// 发布一条说说
	function update($text, $opt) {
		$this->doClient ( $opt );
		$params = array (
				'content' => $text 
		);
		$r = Tencent::api ( 't/add_t', $params, 'POST' );
		return $r;
	
	}
	
	// 上传一个照片，并发布一条说说
	function upload($text, $pic, $opt) {
		//使用分享
		return $this->share($text,$pic,$opt);
		$this->doClient ( $opt );
		$params = array (
				'con' => $text,
				'richtype'=> 1,
				'richval' =>'url='.$pic."&width=250&height=320",
				'third_source' => 1,
		);
	    $params=array();
		//$r = Tencent::api ( 'shuoshuo/add_topic', $params, 'POST' );
		$params = array (
				'content' => $text,
				'pic_url' => $pic,
				'syncflag'=>0,
		);
		$r = Tencent::api('t/add_pic_url', $params, 'POST');
		return $r;
	}
	//同步分享
	function share($text,$pic,$opt){
		$this->doClient($opt);
		preg_match('#《(.+?)》(http://.+?)，#is',$text,$res);
		$title = $res[1];
		$url = $res[2];
		$params = array (
				'title' => $title,
			    'url'=>$url,
				'site'=>'中国国家地理客户端',
				'fromurl' => 'http://download.dili360.com',
				'comment'=>$text,
				'images'=>$pic."?".time().rand(0,999999),
				'nswb'=>1,
		);
		$r = Tencent::api('share/add_share', $params, 'POST');
		return $r;
	}
	//同步分享2
	function shareText($text,$pic,$opt,$title,$url){
		$this->doClient($opt);
		$params = array (
				'title' => $title,
			    'url'=>$url,
				'site'=>'中国国家地理客户端',
				'fromurl' => 'http://download.dili360.com',
				'comment'=>$text,
				'images'=>$pic."?".time().rand(0,999999),
				//'nswb'=>1,
		);
		$r = Tencent::api('share/add_share', $params, 'POST');
		return $r;
	}
}
?>
