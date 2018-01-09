<?php

class FinanceAction extends BaseAction {

	private $m;
	private $stock;
	
	public function _init(){
		if(!empty($_GET['code'])){
			$map['code']=$_GET['code'];
			$this->stock=D('Stock')->getStock($map);
			session('stock',$this->stock);
		}else{
			$this->stock=session('stock');
		}
		$this->assign('stock',$this->stock);
		$this->m=M('finance');
	}
	public function index(){
		if(!empty($this->stock['code'])){
			$map['code']=$this->stock['code'];	
		}else{
			$map='';
		}
		$data = $this->m->where($map)->order('year desc')->limit(20)->select();
		$this->assign('total',count($data));
		$this->assign('data',$data);
		$this -> display();
	}
	
	public function getYear(){
		$arr=range('2005',date('Y')-1);
		return $arr;
	}
	public function get_fdata(){
		$id = intval($_GET['id']);
		$data = D('Stock')->relation(true)->find($id);
		if(empty($data['finance_data'])){
			$fdata=D('Stock')->curl_get_data($id);
		}
		redirect(U('/finance/index/code/'.$data['code']));
	}	
	public function add(){
		if(!empty($_POST['name'])){
			$this->m->create();
			$this->m->price=($this->m->price_min+$this->m->price_max)/2;
			$this->m->s_id=$this->stock['id'];
			$this->m->add();
			redirect(U('/finance/index'));
		}
		$year=$this->getYear();
		$this->assign('year',$year);
		$this->assign('action',ACTION_NAME);
		$this->display();
	}
	public function edit(){
		if($_GET['id']>0){
			$data = $this->m->where('id='.intval($_GET['id']))->find();
			$cate =$this->getYear();
			$this->assign('data',$data);
			$this->assign('year',$cate);
			$this->assign('action',ACTION_NAME);
			$this->display('add');
			exit;
		}elseif($_POST['id']>0){
			$this->m->create();
			$this->m->price=($this->m->price_min+$this->m->price_max)/2;
			$this->m->s_id=$this->stock['id'];
			$this->m->save();
		}
		redirect('/finance/');
		
	}
	public function del(){
			$id = $_POST['id'];
			$type = $_POST['type'];
			if($_POST['id']>0){
				M($type)->where('id='.$id)->delete();
			}
			echo 1;
	}
}