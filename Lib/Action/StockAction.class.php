<?php
import("ORG.Util.Page");
class StockAction extends BaseAction {


    private $eps_g_y;
    private $m;
	private $cache_time;
	private $x;

    public function _init(){
        $this->m=M('stock');
        $this->eps_g_y=0.07;
		$this->cache_time=12*3600;
		$this->x=8.5;
    }
	//精选股票
    public function top(){
		$data = D('Stock')->topStockList(100);
        $this->assign('title',"精选股票_".$this->site_name);
        $this->assign('total',count($data));
        $this->assign('data',$data);
        $this -> display();
    }
	//热门股票
    public function hot(){
		$data = D('Stock')->hotStockList(280);
        $this->assign('title',"热门股票_".$this->site_name);
        $this->assign('total',count($data));
        $this->assign('data',$data);
        $this -> display();
    }
	//高分推荐
    public function best(){
		$data = D('Stock')->bestStockList(100);
        $this->assign('title',"高分推荐_".$this->site_name);
        $this->assign('total',count($data));
        $this->assign('data',$data);
        $this -> display('top_m');
    }
	//个股累积涨幅
    public function change(){
		$type = t($_GET['type']);
		if($type=='down'){
			$order='asc';
			$data = D('Stock')->DayChgStockList(30,$order);
		}elseif($type=='up'){
			$order='desc';
			$data = D('Stock')->DayChgStockList(30,$order);
		}else{
			$data = D('Stock')->changeStockList(30);
		}
        $this->assign('title',"股票涨幅榜_".$this->site_name);
        $this->assign('days',D('Stock')->getChangeDay());
        $this->assign('data',$data);
		$this->assign('type',$type);
        $this -> display();
    }
	//板块累积涨幅
    public function change_trade(){
		$data = D('Stock')->changeTradeList(100);
        $this->assign('title',"板块涨幅榜_".$this->site_name);
        $this->assign('days',D('Stock')->getChangeDay());
        $this->assign('data',$data);
        $this -> display();
    }
    public function index(){
        $s=t($_GET['keyword']);
        $ord_str=array('pb'=>array('ord'=>1),'psr'=>array('ord'=>1),'cap'=>array('ord'=>1),'pgr'=>array('ord'=>1),'alr'=>array('ord'=>1));
        if(isset($_GET['type'])){
            $ord_name=t($_GET['type']);
            if(!in_array($ord_name,array('pb','psr','cap','pgr','alr'))){
                redirect('/stock');
            }
			$ord_value=intval($_GET['ord']);
			$ord_str[$ord_name]['ord'] = $ord_value==1 ? 0:1;
			$ord_str[$ord_name]['img'] = $ord_value==1 ? "down":"up";
			$ord_value = $ord_value==1? "desc": "asc";
			
        }else{
            $ord_name='chg';
			$ord_value='desc';
        }
		$order = $ord_name." ".$ord_value;
        $this->assign('ord_str',$ord_str);
        if(strlen($s)>30){
            redirect('/stock');
        }
        $map['price']=array('gt',0);
		$map['status']=1;
        if(!empty($s)){
            if(preg_match('/[\d]+/',$s)){
                $st_map['code']=$s;
                if(D('Stock')->findStock($st_map['code'])){
                    redirect('/'.$st_map['code'].'.html');
                }
            }else{
                $st_map=array();
                $st_map['name']=$s;

                if($st=D('Stock')->findStock($st_map)){
                    redirect('/'.$st['code'].'.html');
                }
                unset($st_map);
                $where['name']= array('like',"%".$s."%");
                $where['hy_name']= array('like',"%".$s."%");
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }
        }
        $page_num = 50;
        $p = intval ( $_GET ['p'] ) == 0 ? 1 : intval ( $_GET ['p'] );
        //$m = M ( 'es_stock' );
        $count = $this->m->where($map)->count ();
		if($count <=0){
			redirect404();
		}
        $data = $this->m->where($map)->order($order)->page ( $p . ',' . $page_num )->select ();
        $Page = new Page ( $count, $page_num );
        $Page->setConfig('prev', '«');
        $Page->setConfig('next', '»');
        $Page->setConfig ('theme', '<li>%upPage%</li><li>%linkPage%</li><li>%downPage%</li>' );
        $show = $Page->show (); // 分页显示输出
        $this->assign('title','筛选股票_'.$this->site_name);
        $this->assign ( 'page', $show ); // 赋值分页输出
        $this->assign ( 'data', $data );
        $this->assign ('keyword',$s);
        $this->assign('total',$count);
        $this -> display();
    }
    public function view(){
        if($_GET['code']==''){
			redirect404();
        }
        $code=t($_GET['code']);
        $map['code']=$code;
        $data = $this->m->where($map)->find();
        $id=$data['id'];
		D('Stock')->updateCap($code);
        //D('Stock')->value($code);
        $data=array();
        $data = D('Stock')->relation(true)->find($id);
        $chart = $this->pchart($code);
		$relate = D('Stock')->related_stock($code);

        $this->assign('title',$data['name']." ".$data['code']."_股票价值分析_".$this->site_name);
		$this->assign('keywords',$data['name']." ".$data['code']);
        $this->assign('data',$data);
		$this->assign('relate',$relate);
        $this->assign('chart',$chart);
		$this->assign('days',D('Stock')->getChangeDay());
        $this->assign('eps_g_y',$this->eps_g_y);
        $this->display();
    }
    //每股收益调和平均
    public function eps_avg($arr){
        return $arr[0];
        //去掉最小值和最大值
        if($arr[0]>=5){
            //高成长低估
            $eps_avg=$arr[0]/2;
        }elseif($arr[0]>=3 && $arr[0]<5){
            //低成长高估
            $eps_avg=sqrt($arr[0]);
        }else{
            $eps_avg=$arr[0];
        }
        return $eps_avg;
    }
    //每股收益年增长率
    public function epsg($arr){
        $c = count($arr);
        $eps_g=pow($arr[0]/$arr[$c-1],1/($c-1))-1;
        return $eps_g;
    }
    //年均净利润增长率
    public function np_avg($arr){
        $c=count($arr);
        $np_sum=0;
        foreach($arr as $k=>$v){
            if($k<$c-1){
                $grap[]=($arr[$k]-$arr[$k+1])/$arr[$k+1];
            }
        }
        //如果有5年的数据去掉最大值最小值
        if($c>4){
            $max=array_search(max($grap),$grap);
            $min=array_search(min($grap),$grap);
            unset($grap[$max]);
            unset($grap[$min]);
            foreach($grap as $v){
                $new_grap[]=$v;
            }
            $grap=$new_grap;
        }
        $np_sum=array_sum($grap);
        $np_avg=$np_sum/(count($grap)-1);
        return $np_avg;
    }
    //净利润增长率
    public function pgr(){
        $map['id'] = $_REQUEST['id'];
        if($_POST['p1']){
            $p1 = $_POST['p1'];
            $p2 = $_POST['p2'];
            $c=$_POST['year'];
            $g=pow($p1/$p2,1/($c))-1;
            $this->m->where($map)->setField('pgr',$g);
            redirect('/stock/');
        }
        $data = $this->m->where($map)->find();
        $this->assign('data',$data);
        $this->assign('action',ACTION_NAME);
        $this->display();
    }
    //上传首页图片
    public function picupload()
    {
        $id = $_POST['id'];
        $img_id = $_POST['img_id'];
        $error = 1;
        if(isset($_FILES[$id]))
        {
            $attach = $_FILES[$id];
            if($attach['error']!=4)
            {
                $attachment = $attach['name'];
                $tmp_attachment  = $attach['tmp_name'];
                $attachment_size = $attach['size'];
                $return = _uploadfile($attachment, $tmp_attachment, ROOT_PATH . C("UPLOAD_PIC"));
                $picture = '/'.date("Ym")."/". $return;
                $error = '';
                echo $picture;
                exit;
            }
        }
        echo $error;
    }
	//财报曲线JSON
    public function pchart($code){

        //$id = $_GET['id'];
        $res = M('finance')->where('code='.$code)->order('year asc')->select();
        if(!$res){
            return;
        }
        foreach($res as $v){
            $eps[]=$v['eps'];
            $year[]=$v['year'];
            $price[]=$v['price_max'];
            $price_min[]=$v['price_min'];
            $np[]=$v['np'];
        }
        $priceList = D('Stock')->getPriceList($code);
        foreach($priceList as $v){
            $day_price['day'][]="'".$v['day']."'";
            $day_price['price'][]=$v['price'];
        }
        $day_price['day']=implode(',',$day_price['day']);
        $day_price['price']=implode(',',$day_price['price']);
        $data['eps']=implode(',',$eps);
        $data['year']='"'.implode('","',$year).'"';
        $data['price']=implode(',',$price);
        $data['p_y']=$res;
        $data['day_price']=$day_price;
        $data['np']=implode(',',$np);
        return $data;
    }
	//价格曲线JSON
	public function json(){
        $code = $_GET['code'];
        $priceList = D('Stock')->getPriceList($code);
		$priceList = $this->_find_no_empty($priceList);
        foreach($priceList as $k=>$v){
			$day_price['data'][$k][0]=strtotime($v['day'])."000";
			$day_price['data'][$k][1]=$v['price'];
        }
		$day_price_json=str_replace('"','',json_encode($day_price));
		$day_price_json=str_replace('data','"data"',$day_price_json);

		echo $day_price_json;
	}
	//即时股票数据
	public function realTimeData(){
		$code = $_GET['code'];
		$news_data=D('Stock')->realTimeData($code);
		$news_data_json=json_encode($news_data);
		echo $news_data_json;
	}
	
	public function autoComplete(){
	    $s = t($_POST['keyword']);
	    $where['name']= array('like',"%".$s."%");
	    $where['code']= array('like',"%".$s."%");
	    $where['_logic'] = 'or';
	    $map['_complex'] = $where;
	
	    $limit = 10;
	    //$m = M ( 'es_stock' );
	    $count = $this->m->where($map)->count ();
	    $data = $this->m->field('id,code,name')->where($map)->limit($limit)->select ();
	    if ($data) {
	    	foreach ($data as $k => $v) {
	    		$json_data[]=$v['name'].'('.$v['code'].')';
	    	}
	    	echo json_encode($json_data);
	    }
	}
	private function _find_no_empty($arr){
		foreach($arr as $k=>$v){
			if($v['price']=='0.00' || empty($v['price'])){
				if(isset($arr[$k+1]) && intval($arr[$k+1]['price'])>0){
					$arr[$k]['price']=$arr[$k+1]['price'];
				}
				if(isset($arr[$k-1]) && intval($arr[$k-1]['price'])>0){
					$arr[$k]['price']=$arr[$k-1]['price'];
				}				
			}
		}
		return $arr;
	}
    public function clear(){
		S('top',null);
		S('best',null);
		S('changeStockList_10',null);
		S('changeTradeList_10',null);
		S('changeTradeList_100',null);
		S('bestStockList_10',null);
		echo "<br>OK";
    }
	public function test(){

		$code='002431';
		$np_avg= D('Finance')->npAvgByYear($code);
		var_dump($np_avg);
		/*$m = new Memcached();
		$m->addServer('127.0.0.1', 11211);
		$m->set('foo', 200);
		var_dump($m->get('foo'));*/
	}
}