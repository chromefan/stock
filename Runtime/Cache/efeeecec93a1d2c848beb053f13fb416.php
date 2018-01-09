<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title><?php echo ($title); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta property="wb:webmaster" content="90d78890f31f82c4" />
	<meta property="qc:admins" content="2755126167656143473363757" />
	<meta name="keywords" content="<?php echo ($keywords); ?>" />
    <meta name="description" content="<?php echo ($description); ?>" />
    
	<link rel="shortcut icon" href="http://img.ceziling.com/stock/Images/favicon.jpg"/> 
    <link rel="stylesheet" type="text/css" href="__STATIC__/Css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/Css/bootstrap-responsive.css" />
    <link rel="stylesheet" type="text/css" href="__STATIC__/Css/style.css" />
    <link rel="stylesheet" type="text/css" href="/static/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/static/css/select2.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/stock.css" />
	<script type="text/javascript" src="/static/js/jquery.min.js"></script>
    <style type="text/css">
        body {
            padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
        }
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="__STATIC__/assets/js/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner" style="padding: 11px 0; font-size: 14px;">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="/" style="color: #ffffff;font-size:25px; font-weight: bold;">知 股</a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li  class="one"><a href="/">首页</a></li>
					<li  class="hot"><a href="/stock/hot/">热门股票</a></li>
                    <li  class="three"><a href="/stock/top/">精选股票</a></li>
                    <li  class="four"><a href="/stock/best/">高分推荐</a></li>
					<li  class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">涨跌幅榜 <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                          <li><a href="/stock/change">个股涨幅榜</a></li>
                          <li><a href="/stock/change_trade">板块涨幅榜</a></li>
                        </ul>
                     </li>
					<li  class="two"><a href="/stock/">筛选股票</a></li>
                    <li  class="six"><a href="/news/">知股财经</a></li>
                </ul>
                <form class="nav navbar-form navbar-left" action="/stock/index" method="get">
                    <input class="span2" type="text" name="keyword"  placeholder="股票名称/代码/行业名称">
                </form>
                <ul class="nav navbar-nav navbar-right">      
               		  <?php if(($user["uid"]) < "0"): ?><li><a href="#login"  data-toggle="modal">登录</a></li> 
               		  <li class="divider-vertical"></li><?php endif; ?>
                     
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?php echo ($user["userface"]); ?>" alt="<?php echo ($user["username"]); ?>"  style="height:30px; margin-right:10px"  class="img-circle"><?php echo ($user["username"]); ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                          <li><a href="/user/set">我的帐号</a></li>
                          <li><a href="/user/watchlist">投资组合</a></li>
                          <li class="divider"></li>
                          <li><a href="/public/logout">注销退出</a></li>
                        </ul>
                      </li>
                    </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>

<div  id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <form class="form-signin" method="post" action="/public/doLogin" >
         <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		    <h3 id="myModalLabel">欢迎登录<?php echo (C("SITE_NAME")); ?></h3>
  		</div>
  		<div class="modal-body">
  		<div class="main">
       <div class="main-social">
		  <div class="login-weibo">
		    <a href="/public/auth_login/type/sina">
		    <img src="/static/img/weibo.png" alt="48.png" width="140">
			</a>  
			</div>
		  <div class="login-qq">
		    <a    href="/public/auth_login/type/qq">
		    	<img src="/static/img/qq.png" alt="48.png" width="140">
			</a>  
		  </div>
		</div>

      </div>
        </div>

    </form>
</div>


<div class="security-banner">
    <div class="security-banner-inner">
		<?php if(is_array($market_data)): $i = 0; $__LIST__ = $market_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?><span><?php echo ($d["name"]); ?></span>
			<span <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> > <?php echo ($d["index"]); ?></span>
			<span <?php if(($d["change"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["change"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo ($d["change"]); ?></span>
			<span <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo ($d["chg"]); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<div class="container">


<div class="user-left-menu">
<ul class="nav nav-list">
  <li class="nav-header"><i class="icon-cog"></i><span>设置</span></li>
  <li class="active"><a href="#">帐号设置</a></li>
</ul>
</div>

<div class="user-form">

<div class="toolbar border_bottom clearfix new">
	<div class="pull-right">
	<ul class="breadcrumb">
	  <li><i class="icon-map-marker"></i><span class="divider">/</span></li>
	  <li class="active">编辑用户信息</li>
	</ul>
	</div>
</div>
<form accept-charset="UTF-8" action="/krypton/users/322732" class="form-horizontal" id="edit_user_322732" method="post" novalidate="novalidate">
	<div style="display:none"><input name="utf8" type="hidden" value="✓"><input name="_method" type="hidden" value="patch"><input name="authenticity_token" type="hidden" value="VLgK3+k9HeETzDTupUUscdHSr8SObAreSShHyhuXhVg="></div>
	<div class="control-group string optional user_name"><label class="string optional control-label" for="user_name">用户昵称</label>
	<div class="controls"><input class="string optional" id="user_name" name="user[name]" type="text" value="<?php echo ($user["username"]); ?>"></div></div>
	<div class="control-group email optional user_email"><label class="email optional control-label" for="user_email">邮件</label>
	<div class="controls"><input class="string email optional" id="user_email" name="user[email]" type="email"></div></div>
	<div class="control-group tel optional user_phone"><label class="tel optional control-label" for="user_phone">联系方式</label>
	<div class="controls"><input class="string tel optional" id="user_phone" name="user[phone]" type="tel"></div></div>
	<div class="control-group text optional user_tagline"><label class="text optional control-label" for="user_tagline">一句话介绍自己</label>
	<div class="controls"><textarea class="text optional" id="user_tagline" name="user[tagline]"></textarea></div></div>
	<div class="control-group integer optional disabled user_id"><label class="integer optional control-label" for="user_id">用户ID</label>
	<div class="controls"><input class="numeric integer optional disabled" disabled="disabled" id="user_id" name="user[id]" step="1" type="number" value="<?php echo ($user["uid"]); ?>"></div></div>
	<div class="form-actions"><input class="btn btn btn-primary" data-disable-with="Submitting..." name="commit" type="submit" value="更新用户"></div>
</form>
</div>

</div>
<footer>
    <div class="container">
        <div class="row">
            <div class="span3">
                <h5>功能介绍</h5>
                <a href="/" target="_blank">牛股筛选</a><br>
                <a href="/">投资组合</a><br>
                <a href="/">股票打分</a><br>
                <a href="/">财报分析</a>
            </div>
            <div class="span3">
                <h5>集大成者</h5>
                <a href="/stock/about/">格雷厄姆</a> <br>
                <a href="/stock/about//">肯尼思·费雪</a> <br>
                <a href="/stock/about/">巴菲特</a> <br>
                <a href="/stock/about/">彼得林奇</a>
            </div>
            <div class="span3">
                <h5>社交媒体</h5>
                <a href="http://zhizi.ceziling.com/">个人博客</a> <br>
                <a href="https://weibo.com/etalk">新浪微博</a> <br>
            </div>
            <div class="span3 lovingly">
                <p>Copyright &copy;2014 知股</p>
				<p>备案号：京ICP备14012910号-2</p>

            </div>
        </div>
    </div>
</footer>
    <script type="text/javascript" src="__STATIC__/Js/jquery.sorted.js"></script>
    <script type="text/javascript" src="/static/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="__STATIC__/Js/ckform.js"></script>
    <script type="text/javascript" src="/static/js/jquery.uniform.js"></script> 
	<script type="text/javascript" src="__STATIC__/Js/common.js"></script>
</body>
</html>