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
    <link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="/static/css/stock.css" />
    <script type="text/javascript" src="__STATIC__/Js/jquery.js"></script>
    <script type="text/javascript" src="__STATIC__/Js/jquery.sorted.js"></script>
    <script type="text/javascript" src="__STATIC__/Js/bootstrap.js"></script>
    <script type="text/javascript" src="__STATIC__/Js/ckform.js"></script>
    <script type="text/javascript" src="__STATIC__/Js/common.js"></script>
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
                <form class="navbar-form pull-right" action="/stock/index" method="get">
                    <input class="span2" type="text" name="keyword"  placeholder="股票名称/代码/行业名称">
                    <button type="submit" class="btn">搜索</button>
                </form>
            </div><!--/.nav-collapse -->
        </div>
    </div>
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

<script>
    $(function(){
        $('.nav').find('li').eq(0).removeClass('active')
		$('.nav').find('li.six').addClass( 'active')
    })
</script>
	<div class="news-list">
	<?php if(is_array($data["article"])): $i = 0; $__LIST__ = $data["article"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$a): $mod = ($i % 2 );++$i;?><div class="media">
              <a class="pull-left" href="/news/<?php echo ($a["id"]); ?>.html">
                <img class="media-object" data-src="<?php echo ($a["img_url"]); ?>" alt="<?php echo ($a["title"]); ?>" title="<?php echo ($a["title"]); ?>" style="width: 200px; height: 140px;" src="<?php echo ($a["img_url"]); ?>">
              </a>
              <div class="media-body">
                <h4 class="media-heading"><a href="/news/<?php echo ($a["id"]); ?>.html" style="color:#000"/><?php echo ($a["title"]); ?></a>  </h4>		
				<span style=" margin-left: 20px;"><small><?php echo ($a["date"]); ?></small></span>
               <p><?php echo ($a["content"]); ?></p>
              </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
		<div class="pagination pagination-centered">
			<ul><?php echo $data['show'] ?></ul>
		</div>
	</div>
	<div class="sub">
<div class="hd"><h2>新闻<span>热度排行</span></h2></div>
<div class="rank">
		<ul>
			<?php if(is_array($hot)): $key = 0; $__LIST__ = $hot;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$h): $mod = ($key % 2 );++$key;?><li><a href="/news/<?php echo ($h["id"]); ?>.html" title="<?php echo ($h["title"]); ?>" target="_blank"><?php echo ($h["short_title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
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
                <div>				
				<script type="text/javascript">
					var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
					document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F1f13e36e3a00778475d115a0420bc4fb' type='text/javascript'%3E%3C/script%3E"));
				</script>
				<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1254479627'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/stat.php%3Fid%3D1254479627%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
				</div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>