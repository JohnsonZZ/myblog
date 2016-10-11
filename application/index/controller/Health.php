<?php
namespace app\index\controller;
use think\Request;
use app\index\controller\Com;
use app\index\model\Articles;
use app\index\model\Comments;
class health extends Com
{
	public function index()
    {
		$articles = new Articles();
		$article = $articles
					->alias('a')
					->field('a.id,photo,brief,title,a.time,pcount,like,count(c.id) as review')
					->where('cid','2')
					->join('comments c','a.id=c.aid','LEFT')
					->group('a.id') 
					->paginate(5);
		$this->assign('articles',$article);
		return $this->fetch();
    }
    public function health($id)
    {
		$articles = new Articles();
		$article = $articles->field('id,title,article,time,like')->find($id);
		$this->assign('article', $article);
		//�Ƿ����$id������
		if($article)
			$this->assign('article', $article);
		else
			$this->redirect('/health');
		//�������������
		if(!cookie("?article$id")){
			$articles->where('id',$id)->setInc('pcount');
			cookie("article$id","active");
		}
		
		$tree = array();
		$Comments = new Comments();
		$count = $Comments ->where('pid',0) ->where('aid',$id) ->count();
		$comments = $Comments
						->where('pid',0)
						->where('aid',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,pid,fromuid,a.time,b.nick as send,b.avatar as asend')
						->limit(10)
						->select();
		foreach($comments as $key => $data){
			$tree[$key]=$data;
			$child = $Comments
						->where('pid',$data['id'])
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,pid,fromuid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->select();
			if($child){
				$tree[$key]['child'] = $child;
			}
		}
		$this->assign('tree', $tree);//������
		$this->assign('count', $count);//��������
		
		//$id ��һ��
		$latter = $articles->field('id,title')->order('id asc')->where('id','gt',$id)->where('cid',2)->find();
		//$id ��һ��
		$former = $articles->field('id,title')->order('id desc')->where('id','lt',$id)->where('cid',2)->find();
		$this->assign('former', $former);
		$this->assign('latter', $latter);
		
		$articleList = $articles->field('id,title,photo')->where('id','neq',$id)->where('cid',2)->order('id desc')->limit(8)->select();
		$this->assign('articleList', $articleList);
		return $this->fetch();
    }
}
