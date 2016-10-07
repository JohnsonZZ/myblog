<?php
namespace app\index\controller;
use think\Db;
use think\Request;
use think\Controller;
use app\index\model\Messages;
class message extends Controller
{
	public function index()
    {	
		$tree = array();
		$Messages = new Messages();
		$count = $Messages ->count();
		$messages = $Messages
						->where('pid',0)
						->order('a.time desc')
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend')
						->paginate(3);
		foreach($messages as $key => $data){
			$tree[$key]=$data;
			$child = $Messages
						->where('pid',$data['id'])
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->select();
			if($child){
				$tree[$key]['child'] = $child;
			}
		}
		$this->assign('count', $count);
		$this->assign('tree', $tree);
		$this->assign('messages', $messages);
		return $this->fetch();
    }
    public function comment(Request $request)
	{
		$data = $request->param();
		$Messages = new Messages($data);
		$Messages->save();
		$id = $Messages->id;
		if($data['pid'] == 0){
			$message = $Messages
						->where('a.id',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend')
						->find();
			
		}else{
			$message = $Messages
						->where('a.id',$id)
						->alias('a')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,fromuid,pid,a.time,b.nick as send,b.avatar as asend,c.nick as get,c.avatar as aget')
						->find();
			$result = $Messages->where('id','neq',$id)->where('pid',$data['pid'])->find();
			if($result){
				$message['child'] = true;
			}
		}
		return $message;
	}
}
