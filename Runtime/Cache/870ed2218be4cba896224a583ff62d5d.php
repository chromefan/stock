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
        $('.nav').find('li.two').addClass( 'active')
    })
</script>
<p><strong>市销率</strong>:流通市值除以去年12个月的销售收入</p>
<div id="tablescroll">
<table class="table table-bordered table-hover definewidth m10" id="thetable">
    <thead>
    <tr>
        <th>序号</th>
        <th>股票代码</th>
        <th>股票名称</th>
       
        <th>股价</th>
		<th>涨幅</th>
        <th><a href="/stock/?type=pb&ord=<?php echo (intval($ord_str["pb"]["ord"])); ?>">市净率<?php if(($ord_str["pb"]["img"]) != ""): ?><img src="__IMG__/<?php echo ($ord_str["pb"]["img"]); ?>.gif"><?php endif; ?></a> </th>
        <th><a href="/stock/?type=psr&ord=<?php echo (intval($ord_str["psr"]["ord"])); ?>">市销率<?php if(($ord_str["psr"]["img"]) != ""): ?><img src="__IMG__/<?php echo ($ord_str["psr"]["img"]); ?>.gif"><?php endif; ?></a> </th>
        <th><a href="/stock/?type=cap&ord=<?php echo (intval($ord_str["cap"]["ord"])); ?>">流通市值<?php if(($ord_str["cap"]["img"]) != ""): ?><img src="__IMG__/<?php echo ($ord_str["cap"]["img"]); ?>.gif"><?php endif; ?></a></th>
        <th><a href="/stock/?type=pgr&ord=<?php echo (intval($ord_str["pgr"]["ord"])); ?>">净利润增长率<?php if(($ord_str["pgr"]["img"]) != ""): ?><img src="__IMG__/<?php echo ($ord_str["pgr"]["img"]); ?>.gif"><?php endif; ?></a></th>
    </tr>
    </thead>
    <?php if(is_array($data)): $k = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($k % 2 );++$k;?><tr>
            <td><?php echo ($k); ?></td>
            <td><?php echo ($d["code"]); ?></td>
            <td><a href="/<?php echo ($d["code"]); ?>.html" target="_blank"><?php echo ($d["name"]); ?></a></td>
           
            <td><?php echo ($d["price"]); ?></td>
			<td <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo $d['chg']*100; ?>%</td>
			<td><?php echo ($d["pb"]); ?></td>
            <td><?php echo ($d["psr"]); ?></td>
            <td><?php echo (intval($d["cap"])); ?>亿</td>
            <td><?php echo sprintf('%.2f',$d['pgr'])*100; ?>%</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
</div>
<br />

<div class="pagination pagination-centered"><ul><?php echo ($page); ?></ul></div>
<script type="text/javascript" src="__STATIC__/Js/jquery.tablescroll.js"></script>

<script>

    /*<![CDATA[*/
    jQuery(document).ready(function($)
    {
        $('#thetable').tableScroll({height:700});
    });


    /*]]>*/
</script>
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