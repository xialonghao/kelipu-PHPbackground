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

use \Firebase\JWT\JWT;

use think\Cache;









/**
 *
 * 刷新支付页的erp
 *
 */
function refresh_order_goods($order_id){
    $row = db('order_goods')->where('goodsid="'.$order_id.'"')->select();
    for($i=0;$i<count($row);$i++){
        $update = db('goods')->where('id="'.$row[$i]['goodsid'].'"')->find();
        if($update){
            db('order_goods')->where('goodsid="'.$row[$i]['goodsid'].'"')->update(['goods_erp' =>$update['goods_erp']]);
        }

    }
}

/**
 * 
* 刷新购物车的价格
* 
*/
function refresh_cart($card_id)
{
  $row=db('cart')->where('card_id = "'.$card_id.'"')->select();
   // dump($row);

  for($i=0;$i<count($row);$i++)
  {
      // dump(count($row));
     // echo '--1';
      $num = 0;
      $update=db('goods')->where('id = "'.$row[$i]['goodsid'].'"')->find();
      // dump($update);
      if($update)
      {
        //echo 1;
        //   if($row[$i]['goods_price'] != $update['goods_price'])
        // {
            $num = ($update['goods_price'] * 100) * $row[$i]['goods_num'];
            $num = $num / 100;
           db('cart')->where('card_id = "'.$card_id.'" and goods_erp = "'.$row[$i]['goods_erp'].'" and goodsid= "'.$row[$i]['goodsid'].'"')->update(['goods_price' =>$update['goods_price'] , 'goods_num_price'=>$num]);
          
        // } 
      }

      
  }

    return 1;
}
/**
 * +积分   登录一开始异步生成
 * @param  int   card_id 卡id
 * @return bool  
*/
function integral_start($card_id)
{
   $onfi = db('card')->where('id="'.$card_id.'"')->find();

     if($onfi['money_start'] == 0)
    {
         
        db('card')->where('id="'.$card_id.'"')->update(['money_start' => 1]);
        $data=[
            'card_id' =>$card_id,
            'marked' =>'绑卡:(面值:"'.$onfi['bei_money'].'")',
            'integral_before' =>$onfi['bei_money'],
            'integral' =>$onfi['bei_money'],    
            'integral_stop'=>$onfi['bei_money'],
            'create_time' =>time(),
            'money_start' => 1,
            'is_add' => 0
        ];

        db('card_running')->insert($data);
        return true;
     }
     return false;
}

/**
 * 获取邮费方法
 * @param  Array   address_str 地址字符串
 * @param  Array   project_id  项目id
 * @return Array   return_arr  城市区数字数组
*/
 function get_postage($address_str,$project_id)
{

        if(!$address_str)
        {
            return 0;
        }
        $sd = $address_str;

        $diqu = explode(',',$sd);

        if($diqu[1]=='市辖区' || $diqu[1]=='县'){

            $ss = $diqu[0].','.$diqu[2];

        }else{

            $ss = $diqu[0].','.$diqu[1];

        }

        $yf = db('freight')->where('project_id="'.$project_id.'" and status=1 and site  like  "'.$ss.'%"')->find();

        if(!$yf)
        {
            $st=$diqu[0].',所有';
            
            $yf=db('freight')->where('project_id="'.$project_id.'" and status=1 and site like "'.$st.'%"')->find();

            if(!$yf)
            {
                return 0;
            }
        }

        return $yf['price'];
}  

/**
 * @param  Array   address_str 地址字符串
 * @return Array   return_arr  城市区数字数组
*/
function get_address_number($address_str)
{
     
       $add=$address_str;
       $str='';
       $arr=explode(',', $add);

       //城
       $prov=db('address_k')->where('name ="'.$arr[0].'"')->find();
        if(strpos($arr[0],'省') !== false)
       {
            $provs= explode('省',$arr[0]);
            $prov=db('address_k')->where('name ="'.$provs[0].'"')->find();
       }
        if(strpos($arr[0],'市')!==false)
       {

            $provs= explode('市',$arr[0]);
            $prov=db('address_k')->where('name ="'.$provs[0].'"')->find();
       }
      
       //二级
       if(mb_strlen($prov['number'],"UTF-8")==2)
       {

            if($arr[1]!='市辖区' || $arr[1]!='县')
            {
           
                //市
                $city=db('address_k')->where('name ="'.$arr[1].'"')->find();
                if($arr[1] == '自治区直辖县级行政区划' || $arr[1] == '省直辖县级行政区划' )
                {    

                        if(strpos($arr[2],'县')!==false)
                       {


                           $erji= explode('县',$arr[2]);
                         $city=db('address_k')->where('name ="'.$erji[1].'"')->find();

                       }else if(strpos($arr[2],'区')!==false)
                       {

                          $erji= explode('区',$arr[2]);
                            $city=db('address_k')->where('name ="'.$erji[1].'"')->find();
                       }
                       else
                       {

                            $erji= explode('市',$arr[2]);
                             $city=db('address_k')->where('name ="'.$erji[1].'"')->find();
                       }
                }
                else 
                {
                    if(strpos($arr[1],'市')!==false)
                    {

                         $citys= explode('市',$arr[1]);
                         $city=db('address_k')->where('name ="'.$citys[0].'"')->find();
                       
                        
                    }else
                    {
                          $city=db('address_k')->where('name ="'.$arr[1].'"')->find();
                    }
                   
                }

                if(mb_strlen($city['number'],"UTF-8")<=2)
                {
                    
                     $str+=$prov['number'].$city['number'];
                }else
                {
                     $str+=$city['number'];
                }
               
            }
            else 
            {
                 $str+=$prov['number'].'01';
            } 
       }
       else
       {
            $str=$prov['number'];
       }
      
      
       $str1=str_split($str,4);

       $str=$str1[0];
    
       //三级
       if(strpos($arr[2],'县')!==false)
       {
           $nodata= explode('县',$arr[2]);
           $where['name']    = ['like',$nodata[0]."%"];

       }else if(strpos($arr[2],'区')!==false)
       {
          $nodata= explode('区',$arr[2]);
           $where['name']    = ['like',$nodata[0]."%"];
       }
       else
       {
            $nodata= explode('市',$arr[2]);
           $where['name']    = ['like',$nodata[0]."%"];
       }


      $where['number']  = ['like',$str."%" ];
     
  
     //区
      $row=db('address_k')->where($where)->find();

      $sheng=[];
      $shi=[];
      $qu=[];
      $return_arr=[];
      $sheng=str_split($row['number'],2);
      $shi=str_split($row['number'],4);
      $qu=str_split($row['number'],6);
        
      $return_arr=[$sheng[0],$shi[0],$qu[0]];
         // dump($return_arr);
      if($return_arr[0]=='')
      {

        $return_arr=[
            0=>'10',
            1=>'01',
            2=> '01'
        ];
      }
     // dump($return_arr);
     //  exit();
      return $return_arr;
    }    




// 解密user_token
function decoded_user_token($token = '')
{
    
    try {
        
        $decoded = JWT::decode($token, API_KEY . JWT_KEY, array('HS256'));

        return (array) $decoded;
        
    } catch (Exception $ex) {
        
        return $ex->getMessage();
    }
}

// 获取解密信息中的data
function get_member_by_token($token = '')
{
    
    $result = decoded_user_token($token);

    return $result['data'];
}

// 数据验签时数据字段过滤
function sign_field_filter($data = [])
{
    
    $data_sign_filter_field_array = config('data_sign_filter_field');
    
    foreach ($data_sign_filter_field_array as $v)
    {
        
        if (array_key_exists($v, $data)) {
            
            unset($data[$v]);
        }
    }
    
    return $data;
}

// 过滤后的数据生成数据签名
function create_sign_filter($data = [], $key = '')
{
    
    $filter_data = sign_field_filter($data);
    
    return empty($key) ? data_md5_key($filter_data, API_KEY) : data_md5_key($filter_data, $key);
}
