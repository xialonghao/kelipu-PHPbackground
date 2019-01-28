<?php
namespace app\api\controller;
use app\common\controller\ControllerBase;
use think\Cache;
use think\Session;
class Login extends ControllerBase
{
//请求thonk
    public function thoken(){

        // 时间戳
        $timestamp=time();

        //加密
        $sign=md5(API_USERNAME.API_PASSWORD.$timestamp.API_PASSWORD);

        $url=KLP_API."/giftcard/api/restful/auth2/access_token?username=".API_USERNAME."&password=".API_PASSWORD."&timestamp=$timestamp&sign=$sign";

        $headers = [
            'ContentType:application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        $out = json_decode($output,true);
//        print_r($out);
        // $tiems = strtotime($out['result']['refresh_expires_at']);
        //     $url="http://uatopenapi.colipu.com/giftcard/api/restful/auth2/access_token?username=colipu&password=colipu_uat&timestamp=$timestamp&sign=$sign";
        //     $headers = array(
        //         "Content-Type:application/json",
        //     );
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL,$url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //     $output = curl_exec($ch);
        //     curl_close($ch);
        //     $out = json_decode($output,true);
            if($out['success']==true){
                Cache('thoken',$out);
                //session('thoken',$out);
            }else{
                dump($out);
                echo "false";
            }

    }
    //刷新thoken
    public function thokens(){
            $out = session('thoken');
            $timestamp=time();
            $thien = $out['result']['refresh_token'];
            $url=KLP_API."/giftcard/api/restful/auth2/refresh_token?refresh_token=$thien";
            $headers = array(
                "Content-Type:application/json",
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $output = curl_exec($ch);
            curl_close($ch);
            $out = json_decode($output,true);
             if($out['success']==true){
                Cache('thoken',$out);
             }else{
                $this->thoken();
            }
    }
    //登陆
    public function login(){
        $thonkens  = Cache::get('thoken');
        $timestamp = time();
        $tiems     = strtotime($thonkens['result']['expires_at']);
        if($timestamp>$tiems){
            $this->thoken();
        };
            if(empty(Cache::get('thoken'))){
//                echo 11;
                $this->thoken();
            }
            $thonkens  = Cache::get('thoken');

            $timestamp = time();

            $tiems     = strtotime($thonkens['result']['refresh_expires_at']);
            if($thonkens['success']==false){
                $this->thoken();
//                echo 222;
            }
            if($thonkens['errormsg']==-1){
//                echo 333;
                $this->thoken();
            }
                    $key = $thonkens['result']['access_token'];
                    $gift_number =input('post.gift_number');
                    $password  = input('post.password');
                    $news_time =  time();
                    $url = KLP_API."/giftcard/api/restful/api/Encrypt";
                    //初始化
                    $headers = array(
                        "Colipu-Token:".$key,
                        "Content-Type:application/json",
                    );

                    $post_data = array();
                    $post_data['cardId']=$gift_number;
                    $post_data['cardPass']=$password;
                    $str=json_encode($post_data);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_URL,$url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $output = curl_exec($ch);
                    $message = json_decode($output,true);
//                    dump($message);die;
                    if($message['errorcode']==-1){
                        $this->thoken();
                    }
                    //   dump($message);
                    // dump($thonkens);die;
                    $pass = $message['result'];
                    $loginfo   = db('card')->alias('c')->field('c.*')->JOIN('project p', 'c.project_id = p.id ','right')->where('c.gift_number="'.$gift_number.'" and c.status !=-1')->find();//and c.tie_time<"'.$news_time.'"

                    if($loginfo){

//                        print_r(strtotime($loginfo['tie_time']));
//                        echo "bbb";
//                        print_r($news_time);die;
                        if(strtotime($loginfo['tie_time']."+1 day") <$news_time){
                            return json(array(
                                'code'=>230,
                                'msg'=>'积分卡已过期'
                            ));
                        }if($news_time<strtotime($loginfo['open_time'])){
                            return json(array(
                                'code'=>230,
                                'msg'=>'积分卡没生效'
                            ));
                        }else if($loginfo['status'] == 0){
                            return json(array(
                                'code'=>223,
                                'msg'=>'卡号已禁用'
                            ));
                        }
                         if($loginfo['password'] == $pass){
                            //生成一开始的积分数据
                            integral_start($loginfo['id']);
                            return json(array(
                                'code'=>200,
                                'msg'=>'登陆成功',
                                'id'=>$loginfo['id'],
                                'project_id'=>$loginfo['project_id']
                            ));
                        }else{
                            return json(array(
                                'code'=>201,
                                'msg'=>'卡密不正确'
                            ));
                        }

                    }else{
                                return json(array(
                                    'code'=>202,
                                    'msg'=>'卡号不存在'
                                ));
                    }



    }
    //手机登录
    public function tellogin(){
        $mobile = input('post.mobile');//手机
        $verify = input('post.content');
//        echo"$mobile";能接收到
//        echo"$verify";能接收到
        $news_time = time();
        $forn=Cache::get($verify);
//        print_r($forn);die;
        if(!empty($verify)){
            if($mobile==$forn[0]){
                $data['mobile'] = $mobile;
                $loginfo   = db('card')->alias('c')->field('c.*')->JOIN('project p', 'c.project_id = p.id ','right')->where('c.mobile="'.$mobile.'"  and c.status !=-1')->find();
                if($loginfo['status']!= 1){
                    return json(array(
                        'code'=>223,
                        'msg'=>'卡号禁用'
                    ));
                }else if($loginfo['mobile']!=$forn[0]){
                    return json(array(
                        'code'=>230,
                        'msg'=>'此卡未绑定'
                    ));
                }
                if(strtotime($loginfo['tie_time']."+1 day")<=$news_time){
                    return json(array(
                        'code'=>230,
                        'msg'=>'积分卡号过期'
                    ));
                }
                if($news_time<strtotime($loginfo['open_time'])){
                    return json(array(
                        'code'=>230,
                        'msg'=>'积分卡没生效'
                    ));
                }
                if($news_time<strtotime($loginfo['open_time'])){
                    return json(array(
                        'code'=>230,
                        'msg'=>'积分卡没生效'
                    ));
                }
                if(!Cache::get($verify)){
                    return json(array(
                        'code'=>280,
                        'msg'=>'验证码过期',
                        // 'wawa'=>$verify
                    ));
                }
                    //生成一开始的积分数据
                    integral_start($loginfo['id']);
                    return json(array(
                    'code'=>206,
                    'msg'=>'验证码正确',
                    'id'=>$loginfo['id'],
                    'project_id'=>$loginfo['project_id'],
                ));
            }else{
                return json(array(
                    'code'=>205,
                    'msg'=>'手机号错误'
                ));
            }
        }else{
            return json(array(
                'code'=>207,
                'msg'=>'验证码有误'
            ));
        }

    }
    //卡片显示
    public function cardread(){
        $id   = input('post.cardid');
        $info = db('card')->where('id="'.$id.'"')->find();
        $project_info = db('project')->where('id = "'.$info['project_id'].'"')->find();
        $order = db('order')->where('card_id = "'.$info['id'].'" and status = 1  and order_pay = 1 ')->select();
        return json(array(
            'code'=>200,
            'data'=>$info,
            'project' =>$project_info,
            'order_num' =>count($order)
        ));
    }
    //更换手机号
    public function changenumber(){
        $mobile = input('post.mobile');//手机
        $id     = input('post.cardid');
        $verify = input('post.content');
        $forn=Cache::get($verify);
        if(!empty($forn)){
            if(!Cache::get($verify)){
                return json(array(
                    'code'=>280,
                    'msg'=>'验证码过期',
                    // 'wawa'=>$forn,
                   
                ));
            }
            if($mobile==$forn[0]){
                $data['mobile'] = $mobile;
                $infos = db('card')->where('mobile = "'.$data['mobile'].'" and status != -1')->find();
                if(!empty($infos))
                {

                    if(time()> strtotime($infos['tie_time']."+1 day") && $infos['status'] != 0)
                    {
                        db('card')->where('id ="'.$infos['id'].'"')->update(['mobile'=>'']);
                    }
                    // if($infos['status'] == 1 && time()<strtotime($infos['tie_time']))
                    // {
                    //     return json(array(
                    //         'code'=>809,
                    //         'msg'=>'手机号已被绑定'
                    //     ));
                    // }
                    if($infos['status'] == 0 && time()<strtotime($infos['tie_time']))
                    {
                        return json(array(
                            'code'=>809,
                            'msg'=>'积分卡已被禁用'
                        ));
                    }
                }
                $info   = db('card')->where('id="'.$id.'"')->update($data);
                return json(array(
                    'code'=>206,
                    'msg'=>'验证码正确'
                ));
            }else{
                return json(array(
                    'code'=>205,
                    'msg'=>'手机号错误'
                ));
            }
        }else{
            return json(array(
                'code'=>207,
                'msg'=>'验证码有误'
            ));
        }

    }
    public function telverify(){
        $mobile = input('post.mobile');//手机
        $info = db('card')->where('mobile="'.$mobile.'" and status != -1')->find();
        if($info){
                return json(array(
                    'code'=>256,
                    'msg'=>'已绑定'
                ));
        }else{
                return json(array(
                    'code'=>266,
                    'msg'=>'未绑定'
                ));
        }
////        echo"<pre>";
//        print_r($mobile);
//        for($i=0;$i<count($info);$i++){
//            $mobils = $info[$i]['mobile'];
//            print_r($mobile);
//            if($mobile!=$mobils){
//                print_r(11111);
//                return json(array(
//                    'code'=>266,
//                    'msg'=>'未绑定'
//                ));
//            }else if($info['mobile']==$mobile){
//                print_r(123123);
//                return json(array(
//                    'code'=>256,
//                    'msg'=>'已绑定'
//                ));
//            }
//        }

    }
//提交短信
    public  function qingqiu(){
        session_start();
        $mobile = input('post.mobile');//手机
        $news_time = time();
        $info = db('card')->where('mobile="'.$mobile.'"and status!=-1')->find();
        if($info){
            if($info['status']==0) {
                return json(array(
                    'code' => 260,
                    'msg' => '积分卡已被禁用'
                ));
            }else if(strtotime($info['tie_time'])<=$news_time){
                return json(array(
                    'code'=>230,
                    'msg'=>'积分卡已过期'
                ));
            } if($news_time<strtotime($info['open_time'])){
                return json(array(
                    'code'=>230,
                    'msg'=>'积分卡没生效'
                ));
            }
        $timestamps = time()*1000;
        $post_data = array();
        $post_data['account'] = '27075526564794548';
        $post_data['password'] = md5('klp123'.$mobile.$timestamps);
        $post_data['content'] ='【晨光科力普】尊敬的客户，请输入手机动态验证码："'.urlencode(rand(100000,999999)).'" 。该验证码有效期30分钟，只可输入3次，转发无效'; //短信内容需要用urlencode编码下
        $auth = explode('"',$post_data['content']);
        Cache::set($auth[1],array($mobile,$auth[1]),1800);
        $post_data['mobile'] = $mobile;
        $post_data['timestamps'] = $timestamps; //时间戳 单位毫秒$post_data);
//      dump($post_data);
        $url='http://sapi.appsms.cn:8088/msgHttp/json/mt';
        $o='';
        foreach ($post_data as $k=>$v)
        {
            $o.="$k=".urlencode($v).'&';
        }
        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
        $result = curl_exec($ch);
        $ses = json_decode($result,true);
//        dump($ses);

        return json(array(
            'code'=>200,
            'stuts'=>$ses,
            'wawa'=>$auth[1]
        ));
        }else{
            return json(array(
                'code'=>266,
                'msg'=>'未绑定'
            ));
        }

    }
    public  function cardqingqiu(){
        session_start();
        $mobile = input('post.mobile');//手机
        $is_mobile_c=db('card')->where('mobile = "'.$mobile.'" and status = 0')->find();
         $is_mobile_cs=db('card')->where('mobile = "'.$mobile.'" and status = 1')->find();
        if($is_mobile_c)
        {
            return json(array(
                'code'=>201,
                'msg'=>'手机号已被绑定',
            ));
        }
         if($is_mobile_cs['status'] == 1 && time()<strtotime($is_mobile_cs['tie_time']))
           {
                        return json(array(
                            'code'=>809,
                            'msg'=>'手机号已被绑定'
                        ));
           }
//        dump($is_mobile_c);
//        exit;
//        print_r($mobile);
            $timestamps = time()*1000;
            $post_data = array();
            $post_data['account'] = '27075526564794548';
            $post_data['password'] = md5('klp123'.$mobile.$timestamps);
            $post_data['content'] ='【晨光科力普】尊敬的客户，请输入手机动态验证码："'.urlencode(rand(100000,999999)).'" 。该验证码有效期30分钟，只可输入3次，转发无效'; //短信内容需要用urlencode编码下
            //Session::set($post_data['content'],$post_data['content'],'think');
            $auth = explode('"',$post_data['content']);
//                print_r($auth);
            Cache::set($auth[1],array($mobile,$auth[1]));
//        print_r(Cache::get($post_data['content']));
            $post_data['mobile'] = $mobile;
//        $post_content['content']='【晨光科力普】尊敬的客户，请输入手机动态验证码： 。该验证码有效期30分钟，只可输入3次，转发无效';
            $post_data['timestamps'] = $timestamps; //时间戳 单位毫秒
//            print_r($post_data);
            $url='http://sapi.appsms.cn:8088/msgHttp/json/mt';
            $o='';
            foreach ($post_data as $k=>$v)
            {

                $o.="$k=".urlencode($v).'&';
            }
            $post_data=substr($o,0,-1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
            $result = curl_exec($ch);
            $ses = json_decode($result,true);
//                if($is_mobile_c['status']==0) {
//                    return json(array(
//                        'code' => 260,
//                        'msg' => '卡号禁用'
//                    ));
//                }
            return json(array(
                'code'=>200,
                'stuts'=>$ses,
            ));


    }
    //地址添加
    public function addsite(){
        $alls = input('post.');
        //检测地址是否足够五条
         // $num = db('card_address')->where('card_id="'.$alls['card_id'].'"')->select();
         // if(count($num)>=5)
         // {
         // 	 return json(array(
         //        'code'=>209,
         //        'msg'=>'地址最多5条'
         //    ));
         // }
         if($alls['is_default'] != 0)
       	{
       		 $info =db('card_address')->where('card_id="'.$alls['card_id'].'"')->update(['is_default'=>'0']);
       	}
        // $info =db('card_address')->where('card_id="'.$alls['card_id'].'"')->update(['is_default'=>'0']);
        $inpt = db('card_address')->where('card_id="'.$alls['card_id'].'"')->insert($alls);
        if($inpt){
            return json(array(
                'code'=>200,
                'msg'=>'保存成功'
            ));
        }else{
            return json(array(
                'code'=>208,
                'msg'=>'添加失败'
            ));
        }
    }
    //地址显示
    public function siteshow(){
        $cadid = input('post.');
        $info = db('card_address')->where('card_id="'.$cadid['card_id'].'"')->select();
        if($info){
            return json(array(
                'code'=>200,
                'msg'=>'成功',
                'data'=>$info,
            ));
        }else{
            return json(array(
                'code'=>210,
                'msg'=>'地址不存在',
                'data'=>$info,
            ));
        }
    }
    //地址修改
    public function uppsite(){
        $data = input('post.');
       	 if($data['is_default'] != 0)
       	 {
             $is_checked =db('card_address')->where('id="'.$data['id'].'"')->find();
             if($is_checked['is_default'] !=$data['is_default'] )
             {
                 $infos =db('card_address')->where('card_id="'.$data['card_id'].'"')->update(['is_default'=>'0']);
             }	
       		 // $info =db('card_address')->where('id="'.$data['id'].'"')->update(['is_default'=>1]);
       	 }
        // $info =db('card_address')->where('card_id="'.$data['card_id'].'"')->update(['address'=>'']);
        $info = db('card_address')->where('id="'.$data['id'].'" and card_id="'.$data['card_id'].'"')->update($data);
        if($info){
            return json(array(
                'code'=>200,
                'msg'=>'修改成功'
            ));
        }else{
            return json(array(
                'code'=>209,
                'msg'=>'修改失败'
            ));
        }
    }
    //地址删除
    public function delet(){
        $data = input('post.');
        $info = db('card_address')->where('card_id="'.$data['card_id'].'" and id="'.$data['id'].'"')->delete();
        return json(array(
            "code"=>200,
            'msg'=>"删除成功",
        ));
    }

    //
    public function is_status_ok()
    {
        $data = input('post.');

        $info = db('card')->where('id = "'.$data['card_id'].'"')->find();

         if($info)
         {
            if($info['status']!=1)
            {
                return json(array(
                    "code"=>200,
                    'msg'=>"退出登录",
                ));
            }
            return json(array(
                    "code"=>203,
                    'msg'=>"ok",
                ));
         }
            return json(array(
                    "code"=>200,
                    'msg'=>"退出登录",
                ));


    }
    public function Cardexpired(){
        $data = input('post.');
        $new_time = time();
        $info = db('card')->where('id="' . $data['card_id'] . '"')->find();
        if($info){
            if (empty($data['card_id'])) {
                return json(array(
                    'code' => 220,
                ));
            } else if (strtotime($info['tie_time']."+1 day") <= $new_time) {
                return json(array(
                    'code' => 200,
                    'msg' => '积分卡已过期'
                ));
            }else if($info['status']==0){
                return json(array(
                    'code' => 200,
                    'msg' => '积分卡已被禁用'
                ));
            }elseif ($info['status'] == -1) {
                return json(array(
                    'code' => 200,
                    'msg' => '积分卡已被销毁'
                ));
            }
        }    
        
    }
    
    // public function Cardexpired(){
    //     $data = input('post.');
    //     $new_time = time();
    //     $info = db('card')->where('id="' . $data['card_id'] . '"')->find();
    //     if(!$info){
    //         if (empty($data['card_id'])) {
    //             return json(array(
    //                 'code' => 220,
    //             ));
    //         }
    //     } else if (strtotime($info['tie_time']) <= $new_time) {
    //             return json(array(
    //                 'code' => 200,
    //                 'msg' => '积分卡已过期'
    //             ));
    //         }else if($info['status']==0){
    //             return json(array(
    //                 'code' => 200,
    //                 'msg' => '积分卡已被禁用'
    //             ));
    //         }
    // }

 public function navingforbid(){
        $data = input('post.');
        $new_time = time();
        $info = db('card')->where('id="' . $data['card_id'] . '"')->find();
        if($info){
            if (empty($data['card_id'])) {
                return json(array(
                    'code' => 220,
                ));
            } else if (strtotime($info['tie_time']."+1 day") <= $new_time) {
                return json(array(
                    'code' => 200,
                    'msg' => '积分卡已过期'
                ));
            }else if($info['status']==0){
                return json(array(
                    'code' => 200,
                    'msg' => '积分卡已被禁用'
                ));
            }
        }
    }

}

?>