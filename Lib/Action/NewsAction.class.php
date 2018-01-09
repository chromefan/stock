<?php
class NewsAction extends BaseAction {



    public function _init(){
        $News=D('Article');
		$list = $News->hot();
		$this->assign('hot',$list);
    }

	public function index(){
		$p=intval($_GET['p']);
		$data = D('Article')->getList($p);
		$this->assign('title',"知股财经_".$this->site_name);
		$this->assign('data',$data);
		$this->display();
	}
    public function content(){
		$article = D('Article')->content(t($_GET['id']));
		if(!$article){
			redirect404();
		}
        $this->assign('title',$article['title']."_".$this->site_name);
		$this->assign('article',$article);
		$this->assign('keywords',$article['title']." | 知股财经_".$this->site_name);
		$this->assign('description',$article['description']);
		$this->display();
    }
}