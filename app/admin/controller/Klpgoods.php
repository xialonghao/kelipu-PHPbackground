<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;
/**
 * 接口请求更新商品控制器
 */
class Klpgoods extends AdminBase
{
	public function get_access_token($username='',$password='')
	{	
		if($username==''||$password=='')
		{
			return '系统出错';
		}
		
		// 时间戳
		$timestamp=time();

		//加密
		$sign=md5('GiftCard'.'colipu_uat'.$timestamp.'colipu_uat');

		//链接地址+请求参数
		$url="http://api.colipu.com:10092/api/restful/auth2/access_token?username=GiftCard&password=colipu_uat&timestamp=$timestamp&sign=$sign";

		//定义header
		$header = [
		    'ContentType:application/json',
			];

		$result=juhecurl_get($url,$header);
		if($result['success']==true)
		{
			session('get_access_token_goods', $result['result']);
			//session('access_token_time', time());
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get_refresh_token($refresh_token)
	{
		$url    = "http://api.colipu.com:10092/api/restful/auth2/refresh_token?refresh_token=$refresh_token";
		$header = [
		    'ContentType:application/json',
		];

		$result = juhecurl_get($url,$header);

		if($result['success'] == true)
		{

			session('get_access_token_goods', $result['result']);
			//session('access_token_time', time());
			return true;
		}
		else
		{
			$this->get_access_token('GiftCard','colipu_uat');
			return $result;
		}

	}

	//判断token有效期 , 过期后自动更新
	public function judgeValidity()
	{
		//获取到正确的access_token
		if(!empty(session('get_access_token_goods')))
		{
			//获取当前session
			$result        = session('get_access_token_goods');
			$time          = time();
			//echo $time .'*---*'.strtotime($result['refresh_expires_at']);
			if($time > strtotime($result['refresh_expires_at']))
			{
				
				$refresh_token = $result['refresh_token'];
				//刷新token
				$row=$this->get_refresh_token($refresh_token);	
			}
			
			$result = session('get_access_token_goods');
		}else 
		{	
			$status = $this->get_access_token('GiftCard','colipu_uat');
			if($status !== true)
			{
				return false;
			}
			$result = session('get_access_token_goods');
		}
		return true;
	}

	/* 
	** @param get_access_token_goods array
	** @param reresult  array
	** @检查商品，更新商品状态
	 */
	public function upldate_goods()
	{
		//验证token有效期
		$this->judgeValidity();

		//token数组
		$result    = session('get_access_token_goods');
		$arr       = [];
		// 查询所有的商品
		$goods_arr = db('goods')->field('goods_erp')->group('goods_erp')->select();
		
		for ($i=0; $i < count($goods_arr) ; $i++) { 

			$arr[].=$goods_arr[$i]['goods_erp'];			

		} 
		//分组
		$str  = [];
		$arrt = array_chunk($arr,6);
		//循环分组组成字符串
		for ($j=0; $j < count($arrt) ; $j++) { 
			
			$str[] = implode(',',$arrt[$j]);
		}	

		$headers = array(
    	"Colipu-Token:".$result['access_token'],	  
    	); 
		//开始请求接口
		
		 for ($k=0; $k < count($str) ; $k++) { 

		 	//组成url	
			$url     = "http://api.colipu.com:10092/api/restful/products/status?skus=".$str[$k];
			$results = $this->juhecurl_gets($url,$headers);
			//更新数据
			if($results['success']==true)
			{
				for ($l=0; $l < count($results['result']) ; $l++) { 
					
					db('goods')->where('goods_erp = "'.$results['result'][$l]['sku'].'"')->update(['status' => $results['result'][$l]['state']]);
				}
				
				
			}
		 }
		 return '成功';

	}


 	public function juhecurl_gets($url,$headers,$params=false,$ispost=0){
		// $headers = array(
		//   	"Colipu-Token:".$header,	  
		//  );
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$output = curl_exec($ch);
		curl_close($ch);
		return json_decode($output,true);
	}
}