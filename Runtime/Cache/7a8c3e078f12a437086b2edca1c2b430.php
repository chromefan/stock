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

<style>
<!--
.typeahead strong {
	color: red;
	font-weight: normal;
}
-->
</style>
<div class="po-content" >

<form class="form-horizontal">
<h4 class="title">基本信息</h4>
  <div class="control-group">
    <label class="control-label" for="inputEmail">组合名称</label>
    <div class="controls">
      <input type="text" id="inputEmail" placeholder="Email">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputPassword">组合介绍</label>
    <div class="controls">
      <textarea rows="3"></textarea>
    </div>
  </div>
  <h4 class="title">组合股票 
  	<div class="input-append pull-right"><input id="product_search" type="text" data-provide="typeahead"><a href="#" class="btn"><i  class="icon-plus">添加</i></a></div>
  		
  		</h4>
  <div class="stock-list">
  <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">Panel heading</div>
  <h5><span>总市值：600340.04 </span><span class="red">持仓盈亏：+2000.33</span>  </h5>
  <table class="table table-hover">
              <thead>
                <tr>
                  <th>股票代码</th>
                  <th>股票名称</th>
                  <th>当前价格</th>
                  <th>买入价格</th>
                  <th>买入日期</th>
                  <th>股票数量</th>
                  <th>股票市值</th>
                  <th>持仓盈亏</th>
                  <th>盈亏比例</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                <tr class="red">
                  <td>008636</td>
                  <td>美的集团</td>
                  <td>39.90</td>
                  <td>2015-04-07</td>
                  <td>38.90</td>
                  <td>1000</td>
                  <td>399000</td>
                  <td>1000</td>
                  <td>3.3%</td>
                </tr>
                <tr class="green">
                  <td>008636</td>
                  <td>美的集团</td>
                  <td>39.90</td>
                  <td>2015-04-07</td>
                  <td>38.90</td>
                  <td>1000</td>
                  <td>399000</td>
                  <td>-1000</td>
                  <td>-3.3%</td>
                  <td><a href="#">移除</a></td>
                </tr>
              </tbody>
   </table>
   </div>
   </div>
   <div class="form-actions">
	  <button type="submit" class="btn btn-large btn-block btn-success">完成创建投资组合</button>
	</div>
</form>

</div>
<script type="text/javascript" src="/static/js/bootstrap-typeahead.js"></script> 
<script>
$(document).ready(function($) {
   // Workaround for bug in mouse item selection
   $.fn.typeahead.Constructor.prototype.blur = function() {
      var that = this;
      setTimeout(function () { that.hide() }, 250);
   };
 
   $('#product_search').typeahead({
	    source: function (query, process) {
	        var parameter = {keyword: query};
	        $.post('/stock/autoComplete', parameter, function (data) {
	            process(data);
	        },'json');
	    },
	    highlighter: function (item) {
	        var query = this.query.replace(/[\-\[\]{}()*+?.,\^$|#s]/g, '\$&')
	        return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
	            return '<strong>' + match + '</strong>'
	        })
	    }
	});
})
</script>
 
<!-- Modal -->
<div id="at_stock" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Modal header</h3>
  </div>
  <div class="modal-body">

  </div>
  <div class="modal-footer">
  	<button class="btn btn-primary">确认 </button>
    <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
  </div>
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