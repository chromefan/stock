<?php
/**
 * 
 * @author luohj
 *
 */

 
class UserAction extends BaseAction {

    public function index(){

        $this -> display();
    }

    //帐号设置
    public function set(){
    	$this->display();
    }
    //投资组合列表
    public function portfolio(){
    	$this->display();
    }
    //投资组合列表
    public function watchlist(){
        $this->display();
    }
    //我的自选股列表
    public function choicelist(){
        $this->display();
    }
    // 统一删除
    public function delete() {
    	$param = $_POST ['param'];
    	$map [$param] = intval ( $_POST ['id'] );
    	$table = t ( $_POST ['name'] );
    	if (empty ( $map [$param] ) || empty ( $table )) {
    		echo 0;
    	}
    	M ($table )->where ( $map )->delete ();
    	echo 1;
    }
	//仅获取改变的POST
	private function get_change_post(){
		$data=get_post_data();
		if(!$data){
			return false;
		}
		$input=array();
		$org=array();
		foreach($data as $k=>$v){
			if(!strpos($k,'_org')){
				$input[$k]=$v;
			}else{
				$org[$k]=$v;
			}
		}
		$result=array();
		$i=0;
		foreach($input as $k=>$v){
			$org_key=$k.'_org';
			if(isset($org[$org_key])){
				if(strcmp($v,$org[$org_key]) != 0){
					$result[$k]=$v;
					$i++;
					continue;
				}
			}else{
				$result[$k]=$v;
			}
		}
		if($i==0){
			return false;
		}
		return $result;
	}
}
