<?php
/** 
 * * +-----------------+ **产品模型 * +-----------------+ 
 * * @author luohj * */
class StockModel extends RelationModel{
	
	
	//var $uid;
	//protected $tableName='stock';

	protected $_link = array(
		'Finance' => array(
			'mapping_type'  => HAS_MANY,
			'class_name'    => 'Finance',
			'foreign_key'   => 's_id',
			'mapping_name'  => 'finance_data',
			'mapping_order' => 'year desc',
		),
     );
	public function getStock($map){
		$stock = $this->where($map)->find();
		return $stock;
	}
    public function findStock($map){
        $stock = M('es_stock')->where($map)->find();
        return $stock;
    }
	public function getList(){
		$res = $this->order('id desc')->select();
		return $res;
	}
	//最新股价
	public function getNewPrice($code){
		$map['code']=$code;
		$map['day']=date("Y-m-d");
		$res=M('price_log')->where($map)->find();
		if($res){
			return $res['price'];
		}
		return false;
	}
    public function getPriceList($code){
		$map['code']=$code;
        $res=M('price_log')->where($map)->order('day asc')->select();
        return $res;
    }
	//获取个股涨幅 默认100个交易日
	public function getChange($code,$limit=200){
		$map['code']=$code;
		$map['price']=array('gt','0');
		$res=M('price_log')->where($map)->order('day desc')->limit($limit)->select();
		$new_price=(float)$res[0]['price'];
		$old_price=(float)$res[(count($res)-1)]['price'];
		$chg=($new_price-$old_price)/$old_price;
		return $chg;
	}
	//板块涨幅
	public function getTradeChg($limit='50'){
		$res = M('trade')->select();
		foreach($res as $v){
				$map['hy_id']=$v['hy_id'];
				$stock=$this->field('code,hy_name')->where($map)->select();
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
		$res = M('trade')->limit($limit)->order('chg desc')->select();
		return $res;
	}
	//个股涨幅数据
	public function changeStockList($limit=10){
		$data = S('changeStockList_'.$limit);
		if(empty($data)){
			$data = $this->order('chg desc')->limit($limit)->select();
			S('changeStockList_'.$limit,$data,C('CACHE_TIME'));
		}
		return $data;
	}
	//板块涨幅数据
	public function changeTradeList($limit=10){
		$data = S('changeTradeList_'.$limit);
		if(empty($data)){
			$data = $this->getTradeChg($limit);
			S('changeTradeList_'.$limit,$data,C('CACHE_TIME'));
		}
		return $data;
	}
	//高分推荐
	public function bestStockList($limit=10){
		//$data = S('bestStockList_'.$limit);
		if(empty($data)){
			$map['price']=array('gt',3);
			$map['status']=1;
			$data = $this->where($map)->order('score desc,roi_mid desc')->limit($limit)->select();
			//S('bestStockList_'.$limit,$data,C('CACHE_TIME'));
		}
		return $data;
	}
	//精选股票
	public function topStockList($limit=10){
		$data = S('topStockList'.$limit);
		if(empty($data)){
			$map['roi_mid']=array('egt',0.1);
			$map['price']=array('gt',3);
			$map['status']=1;
			/*$data = $this->where($map)->order('roi_mid desc,roi desc ')->limit(100)->select();
			foreach($data as $k =>$v){
				$this->value($v['code']);
			}*/
			$data = $this->where($map)->order('chg desc,roi_mid desc')->limit($limit)->select();
			S('topStockList'.$limit,$data,C('CACHE_TIME'));
		}
		return $data;
	}
	//计算新旧数据相隔天数
	public function getChangeDay(){
		$res=M('price_log')->order('day asc')->find();
		$old_day=$res['day'];
		$res=M('price_log')->order('day desc')->find();
		$new_day=$res['day'];
		$date1 = strtotime($old_day);
		$date2 = strtotime($new_day);
		$days = ceil(abs($date2 - $date1)/86400);
		return $days;
	}
	//指数数据
	public static function marketList(){
		$market_code=array('s_sh000001','s_sz399001','s_sz399300','b_HSI');
		$market_code_str=implode(',',$market_code);
		$url="http://hq.sinajs.cn/?list=".$market_code_str;
		$json=get_url_contents($url);
		$pattern='#\"(.+?)\"#';
		preg_match_all($pattern,$json,$res);
		if(!empty($res[1])){
			foreach($res[1] as $k=> $v){
				$data= explode(',',$v);
				$market[$k]['name']=ts_change_charset($data[0],'GBK','utf-8');
				$market[$k]['code']=$market_code[$k];
				$market[$k]['index']=$data[1];
				$market[$k]['change']=$data[2];
				$market[$k]['chg']=$data[3]."%";
			}
		}
		return $market;
	}
	//相关股票
	public  function related_stock($code){
		$map['code']=$code;
		$the_one=$this->where($map)->find();
		$map=array();
		$map['hy_id']=$the_one['hy_id'];
		$related=$this->where($map)->limit('20')->order('rand()')->select();
		return $related;
	}
    //计算价值
    public function value($code){
        $eps_g_y=0.07;
		$x=8.5;
        if(!empty($_GET['code'])){
            $code = $_GET['code'];
        }
        if(empty($code)){
            return false;
        }
        $map['code']=$code;
        $res=M('finance')->where($map)->order('year desc')->select();
		if(empty($res)){
			return false;
		}
        /*if(empty($res) && $all==false){
            D('Stock')->curl_get_data($code);
            redirect('/'.$code.'.html');
        }*/
        $stock=$this->getStock($map);
        $price=$this->getNewPrice($stock['code']);
        $chg=$this->getChange($stock['code']);
		$chg_10=$this->getChange($stock['code'],10);
        $price = $price? $price:$stock['price'];
		$data['psr']=$stock['cap']/$stock['rev'];
        $year=7;
        $mid_year=3;
        if($res){
			$eps_avg= D('Finance')->epsNew($code);
			if(($eps_avg*100)==0){
				return false;
			}
			$data['eps']=$eps_avg;
            //净利润增长率
            $data['pgr']=$np_avg= D('Finance')->npAvgByYear($code);
            if($data['pgr'] < 0){
                $pgr=0;
            }else{
                $pgr=$data['pgr'];
            }
            $g = sqrt($pgr*100)/100;
			$N=$x+2*$g*100;
            $eps_g_y=$eps_g_y;
            $data['value_min']=$N*$eps_avg;
            $data['pv']=$price/$data['value_min'];
            $data['mos']=$data['value_min']-($data['value_min']*0.2);

            //三年价值
            $eps_y_mid= $eps_avg*pow((1+$g),$mid_year);
            $data['value_mid']=$N*$eps_y_mid;
            $data['roi_mid']=pow($data['value_mid']/$price,1/$mid_year)-1;
            if(is_nan($data['roi_mid'])){
                $data['roi_mid']=0;
            }
            //七年后投资回报

            //七年价值
            $eps_y= $eps_avg*pow((1+$eps_g_y),$year);
            $data['value_max']=($x+2*$eps_g_y*100)*$eps_y;
            $data['roi']=pow($data['value_max']/$price,1/$year)-1;
            $data['roi'] = is_nan($data['roi'])? 0 :$data['roi'];
            $data['status']=1;
            $data['price']=$price;
            $data['chg']=$chg;
			$data['chg10']=$chg_10;
            $data['g']=$g;
            
			$score=$this->summary($data);
			$data['score']=$score['score'];
			$data['score_msg']=$score['score_msg'];
			M('stock')->where($map)->save($data);
            return true;
        }
    }
	    //系统提醒
    public function summary($stock){
        if($stock['pv']>0 && $stock['pv']<=0.6){
            $msg[1]['text']="明显被低估,买入增持";
            $msg[1]['score']=3;
        }elseif($stock['pv']>0.6 && $stock['pv']<2){
            $msg[1]['text']="股价在价值附近浮动，观望";
            $msg[1]['score']=2;
        }else{
            $msg[1]['text']="股价已超出当前价值，减持";
            $msg[1]['score']=0;
        }
        if($stock['g']>0.1 && $stock['cap']<800){
            $msg[2]['text']="高成长股";
            $msg[2]['score']=2;
        }elseif($stock['g']<0.1 && $stock['g']>0.01){
            $msg[2]['text']="低成长股";
            $msg[2]['score']=1;
        }else{
            $msg[2]['text']="常年负增长，注意风险";
            $msg[2]['score']=0;
        }
        if($stock['psr']<=0.75){
            $msg[3]['text']="低市销率,安全性较高";
            $msg[3]['score']=3;
        }elseif($stock['psr']>0.75 && $stock['psr']<=3 ){
            $msg[3]['text']="市销率不是很高,还有机会，参考其他参数操作";
            $msg[3]['score']=2;
        }else{
            $msg[3]['text']="高市销率,谨慎操作可减持";
            $msg[3]['score']=0;
        }
        if($stock['price']<$stock['mos']){
            $msg[4]['text']="股价低于安全边际，放心买入";
            $msg[4]['score']=2;
        }else{
            $msg[4]['text']="股价高于安全边际，谨慎操作";
            $msg[4]['score']=0;
        }
        $score_sum=0;
        foreach($msg as $v){
            $score_sum +=$v['score'];
        }
		$score['score']= $score_sum;
		$score['score_msg']=serialize($msg);
		return $score;
    }
	
}
?>