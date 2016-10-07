<?php
namespace app\admin\controller;
use app\admin\controller\Com;
use think\Db;
use think\Request;
use think\Loader;
use app\admin\model\Messages;
class message extends Com
{
    public function index()
    {
		
		$messages =	Messages::alias('a')
						->order('a.time desc')
						->join('users b','a.fromuid = b.id','LEFT')
						->join('users c','a.touid = c.id','LEFT')
						->field('a.id,message,a.time,b.nick as send,c.nick as get')
						->paginate(12);
		$this->assign('messages',$messages);
		return $this->fetch();
    }
	public function add(){   
		$data = Request::instance()->param();
		if(sendMail($data['mail'],$data['title'],$data['content']))
			$this->success('发送成功！');
		else
			$this->error('发送失败');
	}
	public function del($id){
		db('messages')->delete($id);
		$this->redirect('/admin/message');
	} 
	public function excel($id){
		Loader::import('PHPExcel.PHPExcel',EXTEND_PATH);
		Loader::import('PHPExcel.PHPExcel.IOFactory',EXTEND_PATH);
		if(is_array($id)){
			$id = implode(',',$id);
			$map['id']  = array('in',$id);
		}else{
			$map['id'] = $id;
		}
		$log = Db::name('logs')->where($map)->select();
		$i=1;
		$objPHPExcel = new \PHPExcel();  		
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");
		$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '用户')
					->setCellValue('B1', '日志内容')	
					->setCellValue('C1', '时间');
							
		foreach($log as $value){
			$i++;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A$i", $value['user'])
						->setCellValue("B$i", $value['log'])
						->setCellValue("C$i", $value['time']);
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');  
        header('Content-Disposition: attachment;filename="用户日志.xlsx"');  //日期为文件名后缀  
        header('Cache-Control: max-age=0');  
  
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  //excel5为xls格式，excel2007为xlsx格式  
        $objWriter->save('php://output');  
	}
}
