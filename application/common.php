<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

	/*
	 * 精确时间间隔函数
	 * $time 发布时间 如 1356973323
	 * $str 输出格式 如 Y-m-d H:i:s
	 * 半年的秒数为15552000，1年为31104000，此处用半年的时间
	 */
	function from_time($time,$str='m-d'){
		
		$way = time() - strtotime($time);
		$r = '';
		if($way < 60){
			$r = '刚刚';
		}elseif($way >= 60 && $way <3600){
			$r = floor($way/60).'分钟前';
		}elseif($way >=3600 && $way <86400){
			$r = floor($way/3600).'小时前';
		}elseif($way >=86400 && $way <2592000){
			$r = floor($way/86400).'天前';
		}elseif($way >=2592000 && $way <15552000){
			$r = floor($way/2592000).'个月前';
		}else{
			$r = date($str,strtotime($time));
		}
		return $r;
	}		

	/**
	* 函数：日志记录
	* @param  string $log   日志内容。
	* @return 无返回值
	*/
	function addlog($log){
		
		$data['user'] = session('name');
		$data['log'] = $log;
		db('logs')->insert($data);
	}
	
	
	/**
	 * 邮件发送函数
	 * @param string $to 收件人
	 * @param string $title 标题
	 * @param string $content 内容
	 */
    function sendMail($to, $title, $content) {
     
        $mail = new PHPMailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host = 'smtp.163.com'; //smtp服务器的名称
        $mail->SMTPAuth = TRUE; //启用smtp认证
        $mail->Username = 'best_log@163.com'; //你的邮箱名
        $mail->Password = 'zhang123'; //邮箱密码
        $mail->From = 'best_log@163.com'; //发件人地址（也就是你的邮箱地址）
        $mail->FromName = '肇庆市华创信息科技公司'; //发件人姓名
        $mail->AddAddress($to,"尊敬的客户");
        $mail->WordWrap = 50; //设置每行字符长度
        $mail->IsHTML(TRUE); // 是否HTML格式邮件
        $mail->CharSet='utf-8'; //设置邮件编码
        $mail->Subject =$title; //邮件主题
        $mail->Body = $content; //邮件内容
        $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
        return($mail->Send());
    }