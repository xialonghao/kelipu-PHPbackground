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
 * 文章接口控制器
 */
class Ceshi extends ControllerBase{



public function wxfuserinfo() {

  return 1;

}






  public function integrals(){
            $data= input('post.');
            $integral=0;
            $quan = db('cart')->where("card_id='".$data['card_id']."'")->select();
            for($i=0;$i<count($quan); $i++){
                $zhi = $quan[$i]['checked'];
             
                if($zhi==1)
                {

                    $checkalldef= db('cart')->where("card_id='".$data['card_id']."'")->update(['checked'=>0]);
                   
                }
               else
               {
                   $checkalldef= db('cart')->where("card_id='".$data['card_id']."'")->update(['checked'=>1]);
                   break;

               }

            }
//            $checkalldef= db('cart')->where("card_id='".$data['card_id']."'")->update(['checked'=>1]);
            if($data['checkAll'] == false)
            {
                $info = db('cart')->where("card_id='".$data['card_id']."'")->select();


                for ($i=0; $i < count($info); $i++) {

                    $integral+=$info[$i]['goods_num_price'];
                }
            };

            $info = db('cart')->where(array('card_id'=>$data['card_id']))->count();

            $inf = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>1))->count();

            $infs = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>0))->count();

            if($inf==$info){
                return json(array(
                    'code'=>200,
                    'data'=>1,
                ));
            }else{
                return json(array(
                    'code'=>212,
                    'data'=>0,
                ));
            };
            return json(array(
                'code'=>200,
                'data'=>$integral,
            ));
        }



    public function classify_1s() //$uid=false, $project_id=false
    {
        // $uid = $_GET['uid'];
        // $project_id = $_GET['project_id'];

        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        // if (!isset($uid) || !isset($project_id)) {
        //     return show('500' ,'','系统异常');
        // }

        // isset($uid) ? $uid : false;
        // isset($project_id) ? $project_id : false;
        

        if(!($uid && $project_id))//登陆前条件  !($uid && $project_id)
        {
            //分类条件
            $where['g.status']=1;
            $where['g.tid']=0;                    
            $where['g.goods_category_default']=0;      
            // $where['g.cate_affiliation']='0';//项目id  
            // echo 11;
        }
        else//登陆后条件
        {
            //分类条件
            $where['g.status']=1;
            $where['g.tid']=0;//分类上级id
            $where['g.goods_category_default']=1;//找登陆后显示的数据
            $where['g.cate_affiliation']=$project_id;//项目id
            // echo 22;
        }

        //分类列表
        $goods_category_list=db('goods_category')
        ->alias('g')   
        ->field('g.*,p.path,ps.path as jump_thumbs')
        ->join('picture p','g.cate_thumb=p.id','left')
        ->join('picture ps','g.jump_thumb=ps.id','left')
        ->where($where)
        ->where('g.status != -1')  //后台删除二级分类时小程序中对应删除(即删除后的不显示)
        ->order('g.sort', 'asc')  //后加的排序功能
        ->select();
        
     
        $str='';
        $name='';
        $jump_thumb='';
        $jump_url='';
         // 0:2 1:3  2:5 
         for($i=0;$i<count($goods_category_list);$i++)
         {
            $str.=','.$goods_category_list[$i]['id'];
            $name.=$goods_category_list[$i]['cate_name'].',';
            $jump_thumb.=$goods_category_list[$i]['jump_thumbs'].',';
            $jump_url.=$goods_category_list[$i]['jump_url'].',';
            // $datass=db('goods_category')
            // ->alias('g')
            // ->field('g.*,p.path')
            // ->join('picture p','g.cate_thumb=p.id','left')
            // ->where('tid= "'.$goods_category_list[$i]['id'].'" ')
            // ->select();
            // for ($j=0; $j < count($datass) ; $j++) { 
            //      $list[][]=[
            //         'tid'=> $datass[$j]['tid'],
            //         'img'=> $datass[$j]['path'],
            //         'name'=> $datass[$j]['cate_name'],
            //         'id'=> $datass[$j]['id']
            //     ];
            // }
           
         }
         $arr=[];
         $str1=explode(',',$str);
         unset($str1[0]);
         $str1=implode(',',$str1);
         $map['tid']=['in',$str1];
         $datass=db('goods_category')
            ->alias('g')
            ->field('g.*,p.path')
            ->join('picture p','g.cate_thumb=p.id','left')
            ->where($map)
            ->where('g.status != -1')  //后台删除某二级分类时小程序中对应不显示(即删除后的不显示)
            ->order('g.sort', 'asc')  //后加的排序功能
            ->select();
        for ($l=0; $l < count($datass); $l++) { 
               $arr[][]= $datass[$l];
         for ($p=0; $p < count($datass); $p++) {                
                if($datass[$l]['tid'] == $datass[$p]['tid'] && $datass[$l]['id'] != $datass[$p]['id'] )
                {
                    unset($arr[$p]);
                    $arr[$l][]=$datass[$p]; 
                }                
            }
           
        }
         $arr3=[];
        foreach ($arr as $key => $value) {
           $arr3[]=$arr[$key];
        }
       $arr4=[];
       $name=explode(',',$name);
       unset($name[count($name)]);

     
       $jump_thumb=explode(',',$jump_thumb);
       unset($jump_thumb[count($jump_thumb)]);

        $jump_url=explode(',',$jump_url);
        unset($jump_url[count($jump_url)]);
       
      
       $arr2=explode(',',$str1);
  

        for ($q=0; $q < count($arr2); $q++) { 
                    $arr4[$q]['goods_category_list']='';
                     $arr4[$q]['name']=$name[$q];
                    $arr4[$q]['jump_thumb']=$jump_thumb[$q];
                    $arr4[$q]['jump_url']=$jump_url[$q];
            for ($e=0; $e < count($arr3) ; $e++) { 

                if($arr2[$q] == $arr3[$e][0]['tid'])
                {                  
                    
                	$arr4[$q]['goods_category_list']=$arr3[$e];
                    $arr4[$q]['name']=$name[$q];
                    $arr4[$q]['jump_thumb']=$jump_thumb[$q];
                    $arr4[$q]['jump_url']=$jump_url[$q];
                }

                
            }

        }
        
    
      //	 dump($arr4);
       // exit();
        $class_data=[$goods_category_list,$arr4];
        if(!$goods_category_list || !$arr4)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' , $class_data);
    }



	public function index()
	{
	   $add='上海,市辖区,杨浦区';

	   $str='';
	   $arr=explode(',', $add);

	   //城
	   $prov=db('address_k')->where('name ="'.$arr[0].'"')->find();

	   if(mb_strlen($prov['number'],"UTF-8")==2)
	   {
	   		if($arr[1]!='市辖区' ||$arr[1]!='县') //2513
	   		{
	   			//市
	   			$city=db('address_k')->where('name ="'.$arr[1].'"')->find();

	   			$str+=$prov['number'].$city['number'];
	   		}
	   		$str+=$prov['number'].'01';
	   }
	   else
	   {
	   		$str=$prov['number'];
	   }
	  $nodata= explode('区',$arr[2]);

	  $where['number']  = ['like',$str."%" ];
	  $where['name']    = ['like',$nodata[0]."%"];
	 
	  $row=db('address_k')->where($where)->find();
	  $return_arr=[];
	  $return_arr=str_split($row['number'],2);

	  return $return_arr;
	}  

	 //邮费
	public function youfei()
	{
	   // $data = input('post.');//card_id//prio_id

	  //  $info = db('card_address')->where('card_id=1')->find();

    // refresh_cart('2062');
       $a=get_address_number('湖北省,武汉市,汉阳区');
		// $str = "上海市,县,崇明县";
 
		   dump($a);



       // dump(Cache::get('get_access_token_goods')); 
		// $a=$this->get_address_numberss($str);
		// dump($a);
		//$project_id=6;
	//     $sd =  '上海市,市辖区,奉贤区';

	//     $diqu = explode(',',$sd);

	//     if($diqu[1]=='市辖区' || $diqu[1]=='县'){

	//         $ss = $diqu[0].','.$diqu[2];

	//     }else{

	//         $ss = $diqu[0].','.$diqu[1];

	//     }

	//     $yf = db('freight')->where('project_id="'.$project_id.'" and status=0 and site ="'.$ss.'"')->find();

 //        if(!$yf)
 //        {
 //            $st=$diqu[0].',所有';
 //            $yf=db('freight')->where('project_id="'.$project_id.'" and status=0 and site ="'.$st.'"')->find();

 //            if(!$yf)
 //            {
 //                return 0;
 //            }
 //        }
 //        dump($yf['price']);
	 }  



/**
 * @param  Array   address_str 地址字符串
 * @return Array   return_arr  城市区数字数组
*/
public function get_address_numberss($address_str)
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
                   		 $city=db('address_k')->where('name ="'.$citys[1].'"')->find();
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

      $return_arr=[];
      $return_arr=str_split($row['number'],2);
      
          if(!$return_arr)
      {

        $return_arr=[
            0=>'10',
            1=>'01',
            2=> '01'
        ];
      }
//     dump($return_arr);
//      exit();
      return $return_arr;
    }    


    public function dingshi(){

    	$return =refresh_cart('2062');
//      dump($return);
    }
}