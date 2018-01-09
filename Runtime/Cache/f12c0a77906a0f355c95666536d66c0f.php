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
<script>
    $(function(){
        $('.nav').find('li').eq(0).removeClass('active')
		$('.nav').find('li.one').addClass( 'active')
    })
</script>
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
	<div class="index-left-list">
	<h4><a href="/stock/change_trade">板块涨幅榜</a></h4>
	<table class="table">
    <thead>
    <tr>
		<th>排名</th>
        <th>板块名称</th>
        <th>6月涨幅</th>
    </tr>
    </thead>
     <tbody>
     <?php if(is_array($list['changeTradeList'])): $k = 0; $__LIST__ = $list['changeTradeList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($k % 2 );++$k;?><tr>
			<td><?php echo ($k); ?></td>
            <td><a href="/stock/index/?keyword=<?php echo ($d["name"]); ?>"><?php echo ($d["name"]); ?></a></td>
			<td <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo $d['chg']*100; ?>%</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?> 
       </tbody>
	   </table>
	<h4><a href="/stock/change">个股涨幅榜</a></h4>
	<table class="table">
    <thead>
    <tr>
		<th>排名</th>
        <th>股票名称</th>
        <th>6月涨幅</th>
    </tr>
    </thead>
     <tbody>
     <?php if(is_array($list['changeStockList'])): $k = 0; $__LIST__ = $list['changeStockList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($k % 2 );++$k;?><tr>
			<td><strong><?php echo ($k); ?></strong></td>
            <td><a href="/<?php echo ($d["code"]); ?>.html"><?php echo ($d["name"]); ?></a></td>
			<td <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo $d['chg']*100; ?>%</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?> 
       </tbody>
	  </table>
	</div>
    <div class="index-main">
    <h3>上证指数</h3>
    <div id="mchart"></div>
	<div id="mchart2"></div>
	<!--<h4><a href="/stock/best">高分股票</a></h4>
	<table class="table">
    <thead>
    <tr>
        <th>股票代码</th>
        <th>股票名称</th>
		<th>当前股价</th>
		<th>涨跌幅</th>
		<th>推荐指数</th>
    </tr>
    </thead>
     <tbody>
     <?php if(is_array($list['bestStockList'])): $k = 0; $__LIST__ = $list['bestStockList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($k % 2 );++$k;?><tr>
            <td><?php echo ($d["code"]); ?></td>
            <td><a href="/<?php echo ($d["code"]); ?>.html"><?php echo ($d["name"]); ?></a></td>
			<td><?php echo ($d["price"]); ?></td>
			<td <?php if(($d["chg"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo $d['chg']*100; ?>%</td>
            <td>
				<strong><em style="color:red;"><?php echo ($d["score"]); ?></em></strong>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?> 
       </tbody>
	   </table>
	 -->
    </div>
	<div class="sub">
	<h4><a href="/news">财经要闻</a></h4>
	<div class="rank">
		<ul>
			<?php if(is_array($list['newsList'])): $key = 0; $__LIST__ = $list['newsList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$h): $mod = ($key % 2 );++$key;?><li><a href="/news/<?php echo ($h["id"]); ?>.html" title="<?php echo ($h["title"]); ?>" target="_blank"><?php echo ($h["short_title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div>
	<h4><a href="/stock/hot">热门股票</a></h4>
	<table class="table">
    <thead>
    <tr>
		<th>股票代码</th>
        <th>股票名称</th>
        <th>10日涨幅</th>
    </tr>
    </thead>
     <tbody>
     <?php if(is_array($list['hotStockList'])): $k = 0; $__LIST__ = $list['hotStockList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($k % 2 );++$k;?><tr>
			<td><?php echo ($d["code"]); ?></td>
            <td><a href="/<?php echo ($d["code"]); ?>.html"><?php echo ($d["name"]); ?></a></td>
			<td <?php if(($d["chg10"]) > "0"): ?>class="red"<?php endif; ?> <?php if(($d["chg10"]) < "0"): ?>class="green"<?php endif; ?> ><?php echo $d['chg10']*100; ?>%</td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?> 
       </tbody>
	  </table>
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
<script type="text/javascript">
$(function() {

	$.getJSON('/index/mchart', function(result) {
		Highcharts.setOptions({
			global: { useUTC: false  },
			lang: {
				months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
				//shortMonths : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
				weekdays: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
			},
		});
		// Create the chart
		$('#mchart').highcharts('StockChart', {

			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {
					second: '%Y-%m-%d<br/>%H:%M:%S',
					minute: '%Y-%m-%d<br/>%H:%M',
					hour: '%Y-%m-%d<br/>%H:%M',
					day: '%Y<br/>%m-%d',
					week: '%Y<br/>%m-%d',
					month: '%Y-%m',
					year: '%Y'
				}
			},
			tooltip:{
           // 日期时间格式化
                   xDateFormat: '%Y-%m-%d'
            },
			rangeSelector : {
				selected : 1
			},

			title : {
				text : '上证指数'
			},
			
			series : [{
				name : '收盘价',
				type : 'area',
				data : result.data,
				tooltip: {
					valueDecimals: 2
				},
				threshold: null
			}]
		});
	});

});	
$(function() {

	$.getJSON('/index/mchart/?type=1', function(result) {
		Highcharts.setOptions({
			global: { useUTC: false  },
			lang: {
				months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
				//shortMonths : ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
				weekdays: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
			},
		});
		// Create the chart
		$('#mchart2').highcharts('StockChart', {

			xAxis: {
				type: 'datetime',
				dateTimeLabelFormats: {
					second: '%Y-%m-%d<br/>%H:%M:%S',
					minute: '%Y-%m-%d<br/>%H:%M',
					hour: '%Y-%m-%d<br/>%H:%M',
					day: '%Y<br/>%m-%d',
					week: '%Y<br/>%m-%d',
					month: '%Y-%m',
					year: '%Y'
				}
			},
			tooltip:{
           // 日期时间格式化
                   xDateFormat: '%Y-%m-%d'
            },
			rangeSelector : {
				selected : 1
			},

			title : {
				text : '沪市A股成交量(亿)'
			},
			
			series : [{
				name : '成交量',
				type : 'area',
				data : result.data,
				tooltip: {
					valueDecimals: 2
				},
				threshold: null
			}]
		});
	});

});	
//双线
/*$(function() {
	var seriesOptions = [],
		yAxisOptions = [],
		seriesCounter = 0,
		names = ['指数', '成交量'],
		colors = Highcharts.getOptions().colors;
	
	var path = "/index/mchart/";
	$.each(names, function(i, name) {
	
		$.getJSON(path+ '?type=' +i ,	function(result) {
			var data = result.data;
			seriesOptions[i] = {
				name: name,
				data: data
			};

			// As we're loading the data asynchronously, we don't know what order it will arrive. So
			// we keep a counter and create the chart when all the data is loaded.
			seriesCounter++;

			if (seriesCounter == names.length) {
				createChart();
			}
		});
	});



	// create the chart when all data is loaded
	function createChart() {

		$('#mchart2').highcharts('StockChart', {
		    chart: {
		    },

		    rangeSelector: {
		        selected: 4
		    },

		    yAxis: {
		    	labels: {
		    		formatter: function() {
		    			return (this.value > 0 ? '+' : '') + this.value + '%';
		    		}
		    	},
		    	plotLines: [{
		    		value: 0,
		    		width: 2,
		    		color: 'silver'
		    	}]
		    },
		    
		    plotOptions: {
		    	series: {
		    		compare: 'percent'
		    	}
		    },
		    
		    tooltip: {
		    	pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.change}%)<br/>',
		    	valueDecimals: 2
		    },
		    
		    series: seriesOptions
		});
	}

});	*/		
</script>

<script type="text/javascript" src="http://cdn.hcharts.cn/highstock/2.0.1/highstock.js"></script>
<script src="/hchart/js/highcharts-more.js"></script>
<script src="/hchart/js/modules/exporting.js"></script>
<script type="text/javascript" src="http://cdn.hcharts.cn/highcharts/4.0.1/modules/exporting.js"></script>