<?php declare(strict_types=1);
//$path 图片地址
//$start 是图片地址中的可变部分 默认是 .png的前面一位
//$pre 是域名的前缀
//$domain是主host部分
//$https 默认http 即返回的是Http地址
//使用  $img=GetImgDomain('/upload/a.png'); 返回 http://img0.cdet.cn/upload/a.png
function GetImgDomain($path='',$start=-5,$pre='img',$domain='cdet.cn',$https='http'){      
	if(stripos(',,'.$path,'http')===2){return $path;} 
    return $https.'://'.$pre.(strrpos('0123456789abcdefghijklmnopqrstuvwxyz._-=+[],;#@~',strtolower(substr($path,$start,1)))%3).'.'.$domain.'/'.trim($path,'/');
}

