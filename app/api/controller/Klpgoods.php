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

namespace app\api\controller;

use app\common\controller\ControllerBase;
use think\Cache;
/**
 * 接口请求更新商品控制器
 */
class Klpgoods extends ControllerBase
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
		$sign=md5(API_USERNAME.API_PASSWORD.$timestamp.API_PASSWORD);

		//链接地址+请求参数
		$url=KLP_API."/giftcard/api/restful/auth2/access_token?username=".API_USERNAME."&password=".API_PASSWORD."&timestamp=$timestamp&sign=$sign";

		//定义header
		$header = [
		    'ContentType:application/json',
			];

		$result=juhecurl_get($url,$header);


		if($result['success']==true)
		{
			Cache('get_access_token_goods', $result['result']);
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
		$url    = KLP_API."/giftcard/api/restful/auth2/refresh_token?refresh_token=$refresh_token";
		$header = [
		    'ContentType:application/json',
		];

		$result = juhecurl_get($url,$header);

		if($result['success'] == true)
		{

			Cache('get_access_token_goods', $result['result']);
			//session('access_token_time', time());
			return true;
		}
		else
		{
			$this->get_access_token(API_USERNAME,API_PASSWORD);
			return $result;
		}

	}

	//判断token有效期 , 过期后自动更新
	public function judgeValidity()
	{
		//获取到正确的access_token
		if(!empty(Cache::get('get_access_token_goods')))
		{
			//获取当前session
			$result        = Cache::get('get_access_token_goods');
			$time          = time();
			
			//echo $time .'*---*'.strtotime($result['refresh_expires_at']);
		
			if($time > strtotime($result['expires_at']))
			{
				$refresh_token = $result['refresh_token']; 
				//刷新token
				$row=$this->get_refresh_token($refresh_token);	
			}
		
			$result = session('get_access_token_goods');
		}else 
		{	
			$status = $this->get_access_token(API_USERNAME,API_PASSWORD);
			if($status !== true)
			{
				return false;
			}
			$result = Cache::get('get_access_token_goods');
		}
		return true;
	}

	/** 
	*@param get_access_token_goods array
	*@param reresult  array
	*@检查商品，更新商品状态  全量访问
	*/
	public function upldate_goods()
	{
		//验证token有效期
		$this->judgeValidity();

		//token数组
		$result    = Cache::get('get_access_token_goods');
		//dump($result);

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

		// dump($result);
		$headers = array(
    	"Colipu-Token:".$result['access_token'],	  
    	); 
		//开始请求接口
		
		 for ($k=0; $k < count($str) ; $k++) { 

		 	//组成url	
			$url     = KLP_API."/giftcard/api/restful/products/status?skus=".$str[$k];
			$results = $this->juhecurl_gets($url,$headers);

			// dump($results);
			// exit();
			//更新数据
			if($results['success']==true)
			{
				for ($l=0; $l < count($results['result']) ; $l++) { 
				
					
					$row=db('goods')->where('goods_erp = "'.$results['result'][$l]['sku'].'" and status != -1')->update(['status' => $results['result'][$l]['state']]);
					if(!$row)
					{
						echo $results['result'][$l]['sku'].'商品更新有问题，更新时间为：'.date('Y-m-d H:i:s', time())."----------";
					}

				}
				
				
			}
			else
			{
				echo $results['result'].'更新有问题，更新时间为：'.date('Y-m-d H:i:s', time())."----------";
			}
		 }
			if($results['errorcode'] == '-1')
			{
				 $this->get_access_token(API_USERNAME,API_PASSWORD);
				 $this->upldate_goods();
			}
		return show('200','','成功');
		}

	/** 
	*@param get_access_token_goods array
	*@param reresult  array
	*@检查商品，更新商品状态  增量接口
	*/
	public function upldate_goods_increment()
	{

		//验证token有效期
		$this->judgeValidity();

		//token数组
		$result    = Cache::get('get_access_token_goods');
		//dump($result);

		// dump($result);
		$headers = array(
    	"Colipu-Token:".$result['access_token'],	   
    	); 

    	$url     = KLP_API."/giftcard/api/restful/messages?type=204&del=0";

		$results = $this->juhecurl_gets($url,$headers);

		if($results['success'] == true)
		{
			for ($i=0; $i < count($results['result']) ; $i++) { 
			
			$row=db('goods')->where('goods_erp = "'.$results['result'][$i]['result']['skuId'].'" and status != -1')->update(['status' => $results['result'][$i]['result']['state']]);
	

			}
		}
		if($results['errorcode'] == '-1')
			{
				 $this->get_access_token(API_USERNAME,API_PASSWORD);
				 $this->upldate_goods_increment();
			}
		
		return show('200','','成功');

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

	public function ceshi()
	{
		db('ceshi')->insert(['name'=>1]);
		
		echo '成功';
	}
}