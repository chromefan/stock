<?php
import("ORG.Util.Page");
class ArticleModel extends Model{
	
	

	//获取文章列表
	public function getList($p){
		//return time();
		$m=M ( 'news' );
		$pageCount=10;
		$count =  $m->count();// 查询满足要求的总记录数
		$Page  = new Page($count,$pageCount);// 实例化分页类 传入总记录数和每页显示的记录数
		$result['article']= $m->order('ID DESC')->page($p.",{$pageCount}")->select();
		foreach($result['article'] as $k =>$v){
			$result['article'][$k]['content']=utf_substr(strip_tags($v['content']),180)."......";
		}
		$Page->setConfig ('theme', '%upPage%%prePage%%first%%linkPage%%nextPage%%downPage%' );
		$result['show']  =  $Page->show();// 分页显示输出
		return $result;
	}
	//热门文章
	public function hot($order='rand()'){
		$m=M ( 'news' );
		$list = $m->limit(10)->order($order)->select();
		foreach($list as $k =>$v){
			$list[$k]['short_title']=utf_substr(strip_tags($v['title']),42);
		}
		return $list;
	}
	//文章内容
	public function content($id){
		$map['id']=$id;
		$m=M ( 'news' );
		$data= $m->where($map)->find();
		if (!$data) {
			return false;
		}
		$data['description']=utf_substr(strip_tags($data['content']),180)."......";
		//$data['content']=$this->_replace($data['content']);
		return $data;		
	}
}