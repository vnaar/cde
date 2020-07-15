<?php declare(strict_types=1);

namespace cdetoolclient;


/**
** //目标 
** //实现明文密码的混淆 之后 传递；增加密码的安全性。
**
** 使用方法    
	// 命令行 php ； 
	// php pwdrnd.php 222ccc3333 
	// 或
	// php pwdrnd.php 323223 >a.txt  
	// 输出提示：1）原始  2）完成混淆后 带前缀  3）需要剔除的字符   4)可以在通信工具里传递的字符串【传递并告知解密方式】
	// 输出：  self.php 232323  
	// 232323	LOWtest3252323	删除字母  test3252323
** 
*/


if(count($argv)==2&&isset($argv['1'])&&trim($argv['1'])!=''){
		$origin=trim($argv['1']);
		
	}else {
		if($argc<=1){
			exit('1001: need an argument');
		}else if($argc==2){
			exit('1002: the argument is invalid');		
		}else{
			exit('1003: only support one argument');
		}
		
}
//自测试
//==================================
$obj=new PwdRand();
for($i=0;$i<3;$i++){
	$obj->GetPwdRnd($origin);	
}
exit('');
//==================================
//==================================
//==================================
//==================================

Class PwdRand{
	private $dic=array(
		'LOW'=>'abcdefghijklmnopqrstuvwxyz',
		'NUM'=>'1234567890',
		'UPP'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		'SPE'=>'!@#~$%^&*()-_+,./[]|\\:;\'\"<>?',//保留=号
		'HAN'=>''
	);

	private $blockcontinuous=1;//开启连续块；（3-8个之间）并保证唯一性。 BLC0标识。
	
	
	public function GetPwdRnd($origin=''){
		if(empty($origin)){exit('');}
		$mblen=mb_strlen($origin,'utf8');
		if($mblen<3){exit('str is too short !');}
		$numlen=0;
		$originright='';//追加
		if(rand(1,10)>5){
			$numlen=rand(0,3);//个数
			if($numlen>0){
				$tmp=$origin;// tmp
				$origin=mb_substr($tmp,0,$mblen-$numlen);			
				$originright=mb_substr($tmp,$mblen-$numlen);
			}
		}		
		$numlen=0;
		//准备字段
		$dic=$this->dic;
		//1、获取参数
		//2、分析组成
		//3、确定准备加入待混淆的类型；
		$newstr='';
		$oldgr=$this->getGroup($origin,$dic);

		//差集
		$newgr=array_diff(array_keys($dic),array_keys($oldgr));
		$tmpstr='';//插入的值
		$dotype='';
		$replacestr='';
		
		if(count($newgr)>0){
			$dotype='NUL';
			$replacestr='未知';
			
			//乱插方式
			$newgr=array_values($newgr);
			//var_dump(array_keys($dic),$oldgr,$newgr,array_values($newgr));
			$numlen=rand(0,count($newgr)-1);//选择一种方式
			$strlen=rand(5,8);
			switch($newgr[$numlen]){
				case 'LOW':
					$dotype=$newgr[$numlen];//
					$replacestr='删除所有小写字母';
					$tmpstr=$this->make_small($strlen);
					break;
				case 'UPP':
					$dotype=$newgr[$numlen];//
					$replacestr='删除所有大写字母';
					$tmpstr=$this->make_large($strlen);
					break;
				case 'HAN':
					$dotype=$newgr[$numlen];//
					$replacestr='删除所有汉字';
					$tmpstr=$this->make_han($strlen);
					break;
				case 'SPE':
					$dotype=$newgr[$numlen];//
					$replacestr='删除所有特殊字符';
					$tmpstr=$this->make_spe($strlen);
					break;		
				case 'NUM':
					$dotype=$newgr[$numlen];//
					$replacestr='删除所有数字';
					$tmpstr=$this->make_num($strlen);
					
					//var_dump(' --- in num  ---'.$numlen.'-'.$strlen,'---',$tmpstr);
					break;
				default:
					break;
				
				
			}
			
			if($tmpstr!=''){
				$newstr=$this->insertShunxu($origin,$tmpstr);				
			}else{				
				exit('9002 str is too strong please try later !');
			}
			
			//output
			$this->showOuputStr($dotype,$origin.$originright,$newstr,$replacestr.($originright?' 追加 '.$originright:''));
			
		}else if($this->blockcontinuous==1){
			$dotype='CON';//连续字符串
			$replacestr='删除连续字符';
			$numcount=0;
			$tmpstr='';
			for($i=0;$i<30;$i++){
				$numcount++;
				
				$numlen=rand(3,8);
				$tmpstr=$this->make_blockcontinuous($numlen);
				if(strpos('======================'.$origin,$tmpstr)>0){
					$newstr='';
				}else{
					$newstr=$tmpstr;
					break;
				}
				
				
			}
			if($newstr==''){
				exit('9001 please try later !');
			}else{				
				//随即位插入连续的字符
				$numlen=rand(0,mb_strlen($origin,'utf8'));
				$newstr=mb_substr($origin,0,$numlen).$tmpstr.mb_substr($origin,$numlen);
				
			}			
			
			// 
			$replacestr=$replacestr.$tmpstr.($originright?' 追加 '.$originright:'');//		
			//output
			$this->showOuputStr($dotype,$origin.$originright,$newstr,$replacestr);
			
		}else{
			exit('9000 str is too strong !');
		}

		//使用方式 php pwdrnd.php 222ccc3333 
		//output
		echo "--- SUCCESS --- \t";
		
		
	}

	
	//=====================================================
    // 命令行 php ； php pwdrnd.php 222ccc3333 
	// 输出提示：1）原始  2）完成混淆后 带前缀  3）需要剔除的字符   4)可以在通信工具里传递的字符串【传递并告知解密方式】
	// 输出：  self.php 232323  
	// 232323	LOWtest3252323	删除字母  test3252323
	protected function showOuputStr($dotype,$origin,$newstr,$replacestr){
		echo "\r\n\r\n---------------------------\r\n";
		echo $origin."\t".$dotype.$newstr."\t  ||复制箭头后面的内容->  ".$replacestr."\t".$newstr;
		echo "\r\n";
	}

	protected function getGroup($str,$dic){
		$gr=array();
		foreach($dic as $k=>$v){
			if($k=='LOW' &&$this->is_str_small($str)){
				$gr[$k]=1;
			}else if($k=='UPP' &&$this->is_str_large($str)){
				$gr[$k]=1;
			}else if($k=='NUM' &&$this->is_str_num($str)){
				$gr[$k]=1;
				
			}else if($k=='SPE' &&$this->is_str_spec($str,$this->mb_str_split($v))){
				$gr[$k]=1;
			}else if($k=='HAN' &&$this->is_str_han($str)){
				$gr[$k]=1;
			}else{
				continue;
			}
			
			
		}	
		return $gr;	
	}


	protected function is_str_spec($str,$tmpAr){
		//var_dump($str,$tmpAr);
		foreach($tmpAr as $k=>$v){
			//var_dump($str,$k,$v,strpos($str,$v));
			if(strpos('='.$str,$v)>0){
				return true;
			}	
		}
		return false;
	}


	protected function is_str_num($str){
		$ar0=array(1,2,3,4,5,6,7,8,9,0);
		$ar1=$this->mb_str_split(trim($str));
		
		$ar3 = array_intersect($ar0,$ar1);
		if(count($ar3)>0){
			return true;
		}else{
			return false;
		}  
	}

	protected function is_str_han($str){
		if (preg_match("/[\x7f-\xff]/", $str)) {
			return true; //"含有中文";
		}else{
			return false;//echo "没有中文";
		}
	}



	protected function is_str_large($str){
		$ar=$this->mb_str_split($str);
		$num=count($ar);
		for ($i = 0; $i < $num; $i++) {
			if(in_array($ar[$i],range('A','Z'))){
				return true;
			}        
		}
		return false;
	}



	protected function is_str_small($str){
		$ar=$this->mb_str_split($str);
		$num=count($ar);
		for ($i = 0; $i < $num; $i++) {
			if(in_array($ar[$i],range('a','z'))){
				return true;
			}        
		}
		return false;
	}



	/** 
	 * 将字符串分割为数组     
	 * @param  string $str 字符串 
	 * @return array       分割得到的数组 
	 */  
	protected function mb_str_split($str){  
		return preg_split('/(?<!^)(?!$)/u', $str );  
	}  

	//php随机生成汉字 $num为生成汉字的数量
	protected function make_han($num){
		$b = '';
		for ($i=0; $i<$num; $i++) {
			// 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
			$a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
			// 转码
			$b .= mb_convert_encoding($a, 'UTF-8', 'GB2312'); 
		}
		return $b;
	}

	protected function make_num($num){
		$b = '';
		for ($i=0; $i<$num; $i++) {
			
			$a = rand(0,9);
			// 转码
			$b .= ''.strval($a);
		}
		return $b;
	}

	protected function make_small($num){
		$b = '';
		$ar=range('a','z');
		for ($i=0; $i<$num; $i++) {
			$a=array_rand($ar,1);
			$b.=$ar[$a];
		}
		return $b;
	}


	protected function make_large($num){
		$b = '';
		$ar=range('A','Z');
		for ($i=0; $i<$num; $i++) {
			$a=array_rand($ar,1);
			$b.=$ar[$a];
		}
		return $b;
	}
	protected function make_spe($num){
		$b = '';
		
		$ar=$this->mb_str_split($this->dic['SPE']);
		for ($i=0; $i<$num; $i++) {
			$a=array_rand($ar,1);
			$b.=$ar[$a];
		}
		return $b;
	}

	//按顺序插入 异类插入
	protected function insertShunxu($old,$tmpstr){
		
		$ar=$this->mb_str_split($tmpstr);
		$oldar=$this->mb_str_split($old);
		$c=0;
		$num=count($ar);
		for ($i=0; $i<$num; $i++) {		
			$c=array_rand($oldar,1);//取随机位置
			array_splice($oldar, $c, 0, array($ar[$i])); // 插入到位置3且删除0个
			
		}	
		return join('',$oldar);
		
	}
	protected function make_blockcontinuous($length = 8)
	{
		// 密码字符集，可任意添加你需要的字符 保留=号
		$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
		'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
		't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
		'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
		'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', 
		'@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_', 
		'[', ']', '{', '}', '<', '>', '~', '`', '+',  ',', 
		'.', ';', ':', '/', '?', '|');

		// 在 $chars 中随机取 $length 个数组元素键名
		$keys = array_rand($chars, $length); 
		$password = '';
		for($i = 0; $i < $length; $i++)
		{
			// 将 $length 个数组元素连接成字符串
			$password .= $chars[$keys[$i]];
		}

		return $password;
	}

}

