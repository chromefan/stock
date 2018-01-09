<?php
if (!defined('SITE_PATH'))	exit();

return array
	(
		'URL_MODEL'=>2,
		// 数据库常用配置
		'DB_TYPE'			=>	'mysql',			// 数据库类型
		'DB_HOST'			=>	'localhost',			// 数据库服务器地址
		'DB_NAME'			=>	'stock',			// 数据库名
		'DB_USER'			=>	'root',		// 数据库用户名
		'DB_PWD'			=>	'Luohj@2007',		// 数据库密码
		'DB_PORT'			=>	3306,				// 数据库端口
		'DB_PREFIX'			=>	'',		// 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
		'DB_CHARSET'		=>	'utf8',				// 数据库编码
		'DB_FIELDS_CACHE'	=>	false,				// 启用字段缓存
		'SHOW_PAGE_TRACE'   =>	false,  
		'DEFAULT_TIMEZONE'  =>	'Asia/Chongqing',
		'COOKIE_DOMAIN'	=>	'www.evalstock.com',	//cookie域,请替换成你自己的域名 以.开头
		'COOKIE_PREFIX' =>'stock_',
		'COOKIE_EXPIRE'=>'31536000',
		'COOKIE_PATH'=>'/',
		'COOKIE_NAME'=>'login_auth',
		//缓存时间
		'CACHE_TIME'=>43200,
		//Cookie加密密码
		'SECURE_CODE'    =>  'stock',
		'SITE_NAME' =>'知股网',
		// 是否开启URL Rewrite
		'URL_ROUTER_ON'		=> true,
        'URL_ROUTE_RULES'=>array(
            'stock/:code\d' =>  'Stock/view',
			'pages/:id\d'=>'Pages/content',
        ),
		'SHOW_ERROR_MSG' =>true,
		'BASE_URL'     =>'http://www.evalstock.com',
		'IMG_URL'   =>'http://img.ceziling.com/data/stock',
		//定义错误页面
		'TMPL_ACTION_SUCCESS'=>'Public:success',
		'TMPL_ACTION_ERROR'=>'Public:error',
		//'ERROR_PAGE'=>'./Tpl/Public/error.html',
		'TMPL_EXCEPTION_FILE'=>'./404.html',
		//'EXCEPTION_TMPL_FILE' => './Tpl/Public/error.html',
		//设置语言匹配
		'LANG_SWITCH_ON' => true,
		'DEFAULT_LANG' => 'zh-cn', // 默认语言
		'LANG_AUTO_DETECT' => false, // 自动侦测语言
		'LANG_LIST'=>'en-us,zh-cn,zh-tw',//必须写可允许的语言列表
		'SCORE_RATE'=>500,
		//'UC_AVATAR_PATH'=>'/home/www/wwwroot/uc/data/avatar',
		//第三方登录网站
		//'BIND_TYPE'=>array('sina','qzone','tqq'),
		//'email'=>require_once SITE_PATH.'/home/email.inc.php',
		'UPLOAD_PIC'=>'/public/data/',
		'UPLOAD_TYPE'   => array('gif','jpeg','jpg','swf','apk','png'),
		'TOKEN_ON'=>false,  // 是否开启令牌验证	
		'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称			
		'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
		'TOKEN_RESET'=>true,  //令牌验证出stock错后是否重置令牌 默认为true
		'TMPL_PARSE_STRING'  =>array(
							'__STATIC__'=>'http://img.ceziling.com/stock',
					    	'__JS__' => 'http://img.ceziling.com/js',
							'__IMG__' => 'http://img.ceziling.com/stock/Images',
							'__CSS__' => 'http://img.ceziling.com/stock/Css',
			),
);

?>
