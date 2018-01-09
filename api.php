<?php
header("Content-type:text/html;charset=utf-8");
error_reporting( E_ERROR | E_WARNING | E_PARSE);
ini_set("display_errors","On");
if(isset($_GET['a'])){
	$act=$_GET['a'];
}else{
	die("\nmiss argv\n");
}
$app = new api();
if (method_exists($app,$act)) {
	// 执行前置操作
	call_user_func(array($app,$act));
}else{
	die("\nmiss argv\n");
}

class api{
	
	
	public function app(){
		$result['status']=1;
		$result['error']='';
		$data = array(
				array('id'=>1,'name'=>'CMS碎片系统','role_list'=>array('id'=>1,'name'=>'地方站管理员')),
				array('id'=>2,'name'=>'CMS碎片系统','role_list'=>array('id'=>2,'name'=>'地方站管理员')));
		$result['data']=$data;
		print_r($result);
		echo json_encode($result);
		
	}
	public function pm_list(){
		$result['status']=1;
		$result['error']='';
		$data = array('id'=>'1','name'=>'地方站管理员','permission_list'=>array(array('id'=>1,'name'=>'新闻删除','value'=>'2'),
				array('id'=>1,'name'=>'新闻删除','value'=>'test_url'))
				);
		$result['data']=$data;
		print_r($result);
		echo json_encode($result);
	}
}