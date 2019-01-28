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

use app\api\model\Order as orderModel;

use think\Cache;
/**
 * 订单控制器
 */
class Order extends ControllerBase
{

    /**
     * @param  card_id      str
     * @param  order_status str
     * @param  orderModel   model
     * @return order_list   json  
     * 请求订单列表接口
     */
    public function order_list($card_id ='',$order_status='')
    {
    	
    	if($order_status == ''|| $card_id == ''){ return show('10001','','暂无数据');}

    	$where['o.card_id'] = $card_id;
    	$where['o.status'] = 1;

    	//全部查询的条件
    	if($order_status == 0){ $where['o.order_status'] = ['in', '0,1,5,-3,-2,4']; $where['o.order_pay'] = 1;}  

    	//已完成
    	if($order_status == 1){ $where['o.order_status'] = 1; 			  $where['o.order_pay'] = 1;}

    	//待收货
    	if($order_status == 5){ $where['o.order_status'] = ['in', '0,5']; $where['o.order_pay'] = 1;}

    	//查询数据  
    	$arr= orderModel::alias('o')->where($where)->order('o.id desc')->select();

  		foreach ($arr as $vo)
  		{
  			$vo->orderGoods;
  		}
    	if(!empty($arr))
    	{
    		$card_info=db('card')->where('id  = "'.$card_id.'"')->find();
    		$project_info=db('project')->where('id = "'.$card_info['project_id'].'"')->find();
    		$he=[$project_info,$arr];
    		return show('200',$he);
    	}
    	else
    	{
    		return show('10001','','暂无数据');
    	}	
    	
    }


    /**
     * 订单详情
     * @return [json]   $data
     */
    public function order_details($card_id='',$order_id='')
    {

      if($card_id == ''|| $order_id == ''){ return show('10001','','暂无数据');}

      //查询订单信息
      $data=db('order')->where('card_id= "'.$card_id.'" and id="'.$order_id.'" and status = 1 ')->find();

      //查询订单商品信息
      $data_goods=db('order_goods')->where('order_id="'.$order_id.'"  and status = 1 ')->select();

      $arr=[];
   
      if(!$data || !$data_goods)
      {
         return show('500','','暂无数据');
      }else
      {
        $data['create_time'] = date("Y-m-d H:i:s",$data['create_time']);
        if($data['order_status'] == 1 )
        {
          $data['order_status'] = '已签收';
        }
        else if($data['order_status'] == 0 )
        {
          $data['order_status'] = '待收货';
        }
        else if($data['order_status'] == 4 )
        {
          $data['order_status'] = '退换货中';
        }
        else if($data['order_status'] == 5 )
        {
          $data['order_status'] = '已发货';
        }
        else if($data['order_status'] == '-3')
        {
          $data['order_status'] = '拒收';
        }
        else if($data['order_status'] == '-2')
        {
          $data['order_status'] = '取消';
        }
        $str='';
        for ($i=0; $i < count($data_goods) ; $i++) { 
              $str=$str+$data_goods[$i]['goods_amount'];
        }
        

        $card_info=db('card')->where('id  = "'.$card_id.'"')->find();
    	$project_info=db('project')->where('id = "'.$card_info['project_id'].'"')->find();
    	$arr=[$data,$data_goods,$str,$project_info];
        return show('200',$arr);
      }

    }

    /**
     * @param  int  card_id   积分卡id
     * @param  int  order_id  订单id  
     * @return json data      物流信息
     * 请求物流信息接口
     */
    public function logistics($card_id ='',$order_id='')
    {


    	if($order_id == ''|| $card_id == ''){ return show('300','','暂无数据');}

    	//调用接口更新物流状态  实时调用
    	$this->order_logistics($order_id);

    	//查询数据
    	$logistics_list=db('order')->field('order_sn,logistics_json,buy_amount')->where(['card_id'=>$card_id,'id'=>$order_id])->find();

    	//dump($logistics_list);
    	//物流数组数据
    	if($logistics_list)
    	{

    		$goods_img     = db('order_goods')->field('goods_img')->where('order_id = "'.$order_id.'"')->find();

    		$logistics_arr = json_decode($logistics_list['logistics_json'],true);

    		//dump($logistics_arr);

    							//物流数组                       //物流基本信息  	//随机订单内的商品图片
    		$data          = [$logistics_arr , $logistics_list , $goods_img['goods_img']];
    		// dump($data);

    		return show('200',$data);
    	}
    	else
    	{
    		return show('10001','','暂无数据');
    	}

    } 

    /**
     * 
     * @param  [int] $order_id 订单id
     * @return [Boole]   处理完不返回任何信息
     */
    public function order_logistics($order_id)
    {
		//判断token有效期 , 过期后自动更新
    	judgeValiditys();

    	$access_token = Cache::get('get_access_token_goods');
    	//获取订单号
    	$order_sn     = db('order')->field('order_sn')->where('id = "'.$order_id.'"')->find();
    //	dump($order_sn );

    	$url          = KLP_API."/giftcard/api/restful/order/".$order_sn['order_sn']."/logistics";

    	$herders      = $headers = array(

    	"Content-Type : application/json",
    	"Colipu-Token :".$access_token['access_token'],	

    	); 

    	$result       = juhecurl_gets($url,$herders);
    	
   
 
    	if( $result['success'] == true )
    	{
    		
    		// echo $order_id;
    		 // dump($result['result']['orderTrack'][0] );
    		// $rows=db('order')->where('id = "'.$order_id.'"')->select();
    		if($result['result']['orderTrack'][0]['content']!='暂无该物流信息,请稍后查询。')
    		{
    			

    			$data=[
    			'logistics_json' => json_encode($result['result']['orderTrack'])
	    		];
	    		
	    		$row=db('order')->where('id = "'.$order_id.'"')->update($data);
    		}
    		
    		

    	}
      if($result['errorcode'] == '-1')
      {
         get_access_tokens(API_USERNAME,API_PASSWORD);
         $this->order_logistics();
      }

    	// return true;
    	
    }

    /**
     * 更新所有用户订单状态   定时任务跑
     * @return [bool]   true
     */
    public function get_order_status()
    {
        //判断token有效期 , 过期后自动更新
        judgeValiditys();

        $access_token = Cache::get('get_access_token_goods');

        //获取未到货订单号
        $map['order_pay']     = 1;
        $map['order_status']  = ['in','0,5,1,4,-2,-3'];

        $order_data = db('order')->field('order_sn,id')->where($map)->select();
       
        $herders    = array(

        "Content-Type : application/json",
        "Colipu-Token :".$access_token['access_token'], 

        ); 

        $datas=[];
        $order_status = [];
        for ($i = 0; $i < count($order_data); $i++) { 

            $url     = KLP_API."/giftcard/api/restful/order/".$order_data[$i]['order_sn']."/status";

            $result  = juhecurl_gets($url,$herders);

            if($result['success'] == true)
            {
              if($result['result']['state']=='-1')
              {
                $datas=[
                   'order_status' =>'-3'
                 ];
              }
              else
              { 
                 $datas=[
                   'order_status' =>$result['result']['state'] 
                 ]; 
              }
              
            	// dump($result);
            $row=db('order')->where('order_sn = "'.$result['result']['order_id'].'"')->update($datas);
      

    //             if(!$row)
				// {
				// 		echo $result['result']['order_id'].'订单更新失败，更新时间为：'.date('Y-m-d H:i:s', time())."----------";
				// }
            }else
            {
            	echo $result['result']['order_id'].'订单有问题，更新时间为：'.date('Y-m-d H:i:s', time())."----------";
            }
        }
        
        if($result['errorcode'] == '-1')
        {
         get_access_tokens(API_USERNAME,API_PASSWORD);
         $this->get_order_status();
        }


      return show('200','','成功');

    }



/** 
  *@param get_access_token_goods array
  *@param reresult  array
  *@检查订单，更新订单状态  增量接口
  */
  public function get_order_increment()
  {

    //判断token有效期 , 过期后自动更新
        judgeValiditys();

    //token数组
    $result    = Cache::get('get_access_token_goods');
    //dump($result);

    // dump($result);
    $headers = array(
      "Colipu-Token:".$result['access_token'],     
      ); 

      $url     = KLP_API."/giftcard/api/restful/messages?type=302&del=0";

    $results =juhecurl_gets($url,$headers);
    // dump($results);
    if($results['success'] == true)
    {
      for ($i=0; $i < count($results['result']) ; $i++) { 
      
    
    
            if($results['result'][$i]['result']['state']=='-1')
              {
                $datas=[
                   'order_status' =>'-3'
                 ];
              }
              else
              { 
                 $datas=[
                   'order_status' =>$results['result'][$i]['result']['state']
                 ];
              }
       $row=db('order')->where('order_sn = "'.$results['result'][$i]['result']['orderId'].'"')->update( $datas );

      }
    }
     if($results['errorcode'] == '-1')
      {
         get_access_tokens(API_USERNAME,API_PASSWORD);
         $this->upldate_order_increment();
      }
    
    return show('200','','成功');

  }





}
