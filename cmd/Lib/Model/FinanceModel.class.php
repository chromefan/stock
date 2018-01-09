<?php
/**
 * Created by PhpStorm.
 * 财务模型
 * User: huanjunluo
 * Date: 15-1-26
 * Time: 上午9:54
 */

class financeModel extends Model{



    /**
     * 净利润增长率年平均
     */
    public function npAvgByYear($code){
        $condition='';
        $condition['code']=$code;
        $new_finance = $this->where($condition)->order('year desc')->find();
        $new_date = $new_finance['year'];
        $month=date('m-d',strtotime($new_date));
        $where='date_format(year,\'%m-%d\')=\''.$month.'\' and code='.$code.' and id<>'.$new_finance['id'];
        $last_finance=$this->where($where)->order('year desc')->find();
        $np_avg = ($new_finance['np']-$last_finance['np'])/$last_finance['np'];
        return $np_avg;
    }
	/**
     * 最新EPS
     */
    public function epsNew($code){
        $condition='';
        $condition['code']=$code;
        $new_finance = $this->where($condition)->order('year desc')->find();
        return $new_finance['eps'];
    }
	/**
	* 股票财务数据抓取
	*/
	public function curl_get_data($id){
		if(is_string($id)){
			$map['code']=$id;
		}elseif(is_numeric($id)){
			$map['id']=$id;
		}
        $s=$this->field('id,code,name')->where($map)->find();
        $res=M('es_stock')->field('url')->where($map)->find();
        $real_url=$res['url'];
        if(strstr($real_url,'sh')){
            $code_type='01';
        }else{
            $code_type='02';
        }
		//$url="http://f10.eastmoney.com/f10_v2/BackOffice.aspx?command=RptF10MainTarget&code={$s['code']}{$code_type}&num=9&spstr=&n=1";
		$url="http://f10.eastmoney.com/f10_v2/BackOffice.aspx?command=RptF10MainTarget&code={$s['code']}{$code_type}&num=9&spstr=&n=0";
		$json= get_url_contents($url);
		$p1='#\);">(.+?)</tr>#';
        $p2='#<td class="tips-data-Right"><span>(.[^>]+?)</span></td>#';
        $p_date="#\d{2}-\d{2}-\d{2}#";
        preg_match_all($p_date, $json,$res_date);
        $res_date=$res_date[0];
        $key = $res_date[0];
		$max = 10;
		
		for ($i = 0; $i < $max; $i++) {
			if($res_date[$i]==$key && $i<>0){
				break;
			}else{
				$year[]=$res_date[$i];
			}
		}		
		preg_match_all($p1, $json,$res);
		$fdata=$res[1];
		if(empty($fdata)){
			return;
		}
		foreach ($fdata as $k =>$v){
			if($k==0 || $k == 9){
				preg_match_all($p2, $v,$sub);
				if($k==0){
					$data['eps']=$sub[1];
				}else{
					$data['vps']=$sub[1];
				}
			}
		}
		foreach ($data['eps'] as $k =>$v){
			$add['s_id']=$s['id'];
			$add['name']=$s['name'];
			$add['code']=$s['code'];
			$add['eps']=$v;
			if(preg_match('/万$/',$data['vps'][$k])){
				$data['vps'][$k]=sprintf("%f",$data['vps'][$k])/10000;
			}
			$add['np']=sprintf("%f",$data['vps'][$k]);
			$add['year']=date('Y-m-d',strtotime($year[$k]));
			$id=M('finance')->add($add);
		}
		return $id;
	}
}