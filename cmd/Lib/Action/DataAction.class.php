<?php
class DataAction extends Action {


	public function test(){
		echo time();
	}
	public function run (){
		$this->stock_update();
		$this->value();
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
	public function trade(){
		$sql = 'TRUNCATE table trade';
		$ret = M('trade')->execute($sql);
		$res = M('es_stock')->field('code,trade')->group('trade')->select();
		foreach($res as $v){
			if(empty($v['trade'])){
				$add['name']='B股';
			}elseif($v['trade']=='行业'){
				$add['name']='未知行业';
			}else{
				$add['name']=$v['trade'];
			}
			M('trade')->add($add);
		}
		echo '<br>all done';
	}
	public function trade_chg(){
		$res = M('trade')->select();
		foreach($res as $v){
				$map['trade']=$v['name'];
				$stock=M('es_stock')->field('code,trade')->where($map)->select();
				if(!$stock){
					continue;
				}
				$chg=array();
				$avg_chg=0;
				foreach($stock as $s){
					$chg[]=D('Stock')->getChange($s['code']);
				}
				$avg_chg=array_sum($chg)/count($chg);
				$add_data['chg']=$avg_chg;
				M('trade')->where('hy_id='.$v['hy_id'])->save($add_data);
		}
		echo '<br>all done';
	}
	public function curl_caibao(){
		echo '\nStart\n';
		$res = M('es_stock')->field('code')->select();
		foreach($res as $v){
			 $id=D('Stock')->curl_get_data($v['code']);
			 echo "{$v['code']}:{$id}\t";
		}
		echo '<br>all done';
	}
	//采集数据更新
	public function stock_update(){
		echo '\nStock update Start\n';
		//$m['code']='601318';
		$stock_list= M('es_stock')->select();
		$c=0;
		foreach($stock_list as $v){
			if(empty($v['trade'])){
				continue;
			}
			$trade=M('trade')->where("name='".$v['trade']."'")->find();

			$save_data['name']=$v['name'];		
			$save_data['rev']=$v['rev'];
			$save_data['hy_id']=$trade['hy_id'];
			$save_data['hy_name']=$v['trade'];
			$save_data['price']=$v['price'];
			$save_data['alr']=$v['alr'];
			if($v['url']=='sh'){
				$code_type='1';
			}else{
				$code_type='2';
			}
			$url='http://nufm.dfcfw.com/EM_Finance2014NumericApplication/JS.aspx?type=CT&cmd='.$v['code'].$code_type.'&sty=FDT&st=z&sr=&p=&ps=&lvl=&cb=&js=var%20jsquote=(x);&token=beb0a0047196124721f56b0f0ff5a27c';
			$json=get_url_contents_curl($url,true);
			$pattern='/\"(.+?)\"/';
			$res=array();
			$data=array();
			preg_match($pattern,$json,$res);
			if(!empty($res[1])){
				$data = explode(',',$res[1]);
				$save_data['price']=$data[26];
				$save_data['pe']=$data[38];
				$save_data['pb']=$data[39];
				if(intval($data[40])>0){
					$data[40]=$data[40]/100000000;
				}
				if(intval($data[41])>0){
					$data[41]=$data[41]/100000000;
				}
				$save_data['cap_all']=$data[40];
				$save_data['cap']=$data[41];
				if($save_data['rev']*10000 ==0 || $save_data['cap']*10000==0){
					$save_data['status']=0;
				}else{
					$save_data['psr']=$save_data['cap']/$save_data['rev'];
				}
			}
			$map['code']=$v['code'];
			if( M('stock')->where($map)->find()){
				$res=M('stock')->where($map)->save($save_data);
			}else{
				$save_data['code']=$v['code'];
				$res=M('stock')->where($map)->add($save_data);
			}
			/*if(!$res){
				$error_sql=M('stock')->getLastSql();
				echo "\n {$v['code']}: \t $error_sql \n";
			}*/
			if(!isset($save_data['cap'])){
				var_dump($url);
				var_dump($json);
			}
			$save_data=null;
			$data=null;
			$res=null;
			$c++;
			echo "$c \t";
		}
		echo "\nEnd:Stock update all'.$c.'done \n";
	}
	//对所有股票进行估值
	public function value(){
		echo "\n Start: Stock Value\n";
		$map['status']=1;
		$stock = M('stock')->where($map)->select();

		foreach($stock as $k=>$v){
			if(intval($v['cap'])==0){
				continue;
			}
			$res=D('Stock')->value($v['code']);
			if($res){
				echo "{$v['code']}:\t";
				M('stock')->where('code='.$v['code'])->setField('status',1);
			}else{
				M('stock')->where('code='.$v['code'])->setField('status',0);
			}
		}
		echo "\n End:Stock Value all done \n";
	}
}