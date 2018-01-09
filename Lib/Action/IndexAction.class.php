<?php
class IndexAction extends BaseAction {

    public function index(){

		$stock=D('Stock');
		$news =D('Article');

		$list['changeStockList'] = $stock->changeStockList();
		$list['changeTradeList'] = $stock->changeTradeList(10);
		$list['bestStockList']   = $stock->bestStockList(20);
		$list['hotStockList']    = $stock->hotStockList(12);
		//财经新闻
		$list['newsList'] = $news->hot('id desc');
		$this->assign('title',$this->site_name."：股票真实价值分析、财报统计分析、股票筛选、财经新闻");
		$this->assign('list',$list);
		$this->display();
    }
    public function index2(){

		$stock=D('Stock');
		$news =D('Article');

		$list['changeStockList'] = $stock->changeStockList();
		$list['changeTradeList'] = $stock->changeTradeList(10);
		$list['bestStockList']   = $stock->bestStockList(20);
		$list['hotStockList']    = $stock->hotStockList(12);
		//财经新闻
		$list['newsList'] = $news->hot('id desc');
		$this->assign('title',$this->site_name."：股票真实价值分析、财报统计分析、股票筛选、财经新闻");
		$this->assign('list',$list);
		$this->display();
    }
	public function mchart(){
		$type=$_GET['type'];
		if($type==1){
			$data_type='turnover';
		}else{
			$data_type='index';
		}
		$market=M('market_log')->order('day asc')->select();
		$mchart=array();
		foreach($market as $k=>$v){
            $mchart['data'][$k][0]=strtotime($v['day'])."000";
			if($type==1){
				$v[$data_type]=$v[$data_type]/10000;
			}
            $mchart['data'][$k][1]=$v[$data_type];
        }
		$mchart_json=str_replace('"','',json_encode($mchart));
		$mchart_json=str_replace('data','"data"',$mchart_json);
		echo $mchart_json;
	}
    public function nopage(){
        //处理生成HTML
        $title="页面未找到_".$this->site_name;
        $this->assign('title',$title);
        $this->assign('waitSecond',3);
        $this->assign ( 'jumpUrl',C('BASE_URL'));
        $path=SITE_PATH;
        $this->buildHtml("404",$path."/", '404');
        echo 'ok';
    }
	public function test(){
		$code=$_GET['code'];
		if(substr($code,0,1)=='6'){
			$code_type='1';
		}else{
			$code_type='2';
		}
		$url='http://nufm.dfcfw.com/EM_Finance2014NumericApplication/JS.aspx?type=CT&cmd='.$code.$code_type.'&sty=FDT&st=z&sr=&p=&ps=&lvl=&cb=&js=var%20jsquote=(x);&token=beb0a0047196124721f56b0f0ff5a27c';
		$json=get_url_contents($url);
		$pattern='/\"(.+?)\"/';
		preg_match($pattern,$json,$res);
		if(!empty($res[1])){
			$data = explode(',',$res[1]);
		}

		print_r($data);
		/*$data[1]//code
		$data[2]//name
		$data[26]//当前价
		$data[27]//当日涨幅
		$data[28]//当日涨跌幅%
		$data[38]//市盈率
		$data[39]//市净率
		$data[40]//总市值
		$data[41]//流通市值*/
	}

}