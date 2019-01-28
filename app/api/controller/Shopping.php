<?php
    namespace   app\api\controller;
    use think\Controller;
    use think\Db;
    use think\db\saveAll;
    use think\Cache;
    class Shopping extends Controller{
        public function sels(){
            $info=db('goods')->where('id=28')->select();
            return json(array(
                'code'=>200,
                'data'=>$info,
            ));
        }
        //获取商品id
        public function readcommodity(){
                $data= input('post.');
                $info = db('goods')->where('id="'.$data['id'].'"')->find();
                return json(
                  array(
                      'code'=>200,
                      'msg'=>'成功',
                      'data'=>$info,
                  )
                );
        }
        //加入购物车
        public function gaincomm(){
            $data = input('post.');
            $goodsid=$data['goodsid'];
            $info = db('cart')->where('goodsid="'.$goodsid.'" and card_id="'.$data['card_id'].'"')->select();
            $shopinventory=db('goods')->where('id="'.$data['goodsid'].'"')->find();
            $goods_xinxi=db('goods')->where('id = "'.$goodsid.'" and status = 1')->find();
            //套餐
            $setmeal = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=1')
                ->find();
            $ss = db('card')->where('id="'.$data['card_id'].'"')->select();
            if($setmeal){
                $dingdan = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
                if($dingdan){
                    return json(array(
                        'code'=>360,
                        'msg'=>'该卡已兑换套餐',
                        'data'=>$setmeal,
                        'unit'=>$ss,
                        'dingdan'=>$dingdan,
                    ));
                }
            }
            //中型
            $neutral = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=2')
                ->find();
            $neutral_id = db('card')->where('id="'.$data['card_id'].'"')->select();
            if($neutral){
                $neutral_indent = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
                if($neutral_indent){
                    return json(array(
                        'code'=>360,
                        'msg'=>'该卡已兑换过',
                        'data'=>$neutral,
                        'unit'=>$neutral_id,
                        'dingdan'=>$neutral_indent,
                    ));
                }
            }
            //
            if($setmeal){
                $ssss = db('cart')->where('card_id="'.$data['card_id'].'" and goodsid="'.$data['goodsid'].'"')->select();
                if($ssss){
                    return json(array(
                        'code'=>280,
                        'data'=>$setmeal,
                        'unit'=>$ss,
                        'dingdan'=>$dingdan,
                    ));
                }
            }
            if($setmeal){
                $ding = db('cart')->where('card_id="'.$data['card_id'].'"')->count();
                if($ding){
                    return json(array(
                        'code'=>378,
                        'msg'=>'只能添加一种套餐',
                        'data'=>$setmeal,
                        'unit'=>$ss,
                        'dingdan'=>$dingdan,
                    ));
                }
            }
            if(!$goods_xinxi)
            {
                    return json(array(
                    'code'=>270,
                    'data'=>'宝贝补货中'
                ));exit;
            }
            $cate_=db('goods_category')->where('id = "'.$goods_xinxi['category_id'].'" and status = 1 ')->find();
            if(!$cate_)
            {
                    return json(array(
                    'code'=>270,
                    'data'=>'宝贝补货中'
                ));exit;
            }
            $cha_project=db('goods_category')->where('id = "'.$cate_['tid'].'"')->find();
                if($data['project_id'] != $cha_project['cate_affiliation'])
            {
            	 return json(array(
                    'code'=>261,
                    'data'=>'宝贝补货中'
                ));exit;
            }
            if(empty($shopinventory['goods_repertory'])){
                return json(array(
                    'code'=>261,
                    'data'=>'宝贝补货中'
                ));exit;
            }
            if($info){
                return json(array(
                    'code'=>220,
                    'data'=>'已加入购物车'
                ));exit;
            }else{
                $insert_data=[
                    'card_id'=>$data['card_id'],
                    'goodsid'=>$goodsid,
                    'goods_name'=>$data['goods_name'],
                    'goods_img'=>$data['goods_img'],
                    'goods_price'=>$data['goods_price'],
                    'goods_erp'=>$data['goods_erp'],
                    'goods_num_price'=>$data['goods_price'],
                    'goods_num'=>$data['goods_num'],
                    'goods_unit'=>$data['goods_unit'],
                    'checked'=>1,
                ];
                $info = db('cart')->insert($insert_data);
                return json(array(
                    'code'=>200,
                    'data'=>'$info',
                ));
            }
        }
        //goods表删除和购物车同步
        public function delet(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'"and checked=1')->select();
            $sp_id='';
            for($i=0;$i<count($info);$i++){
                $sp_id .= $info[$i]['goodsid'].',';
            }
            $dids = substr($sp_id,0,-1);
            $delsp =  db('goods')->where('id in ('.$dids.') and status=-1')->select();
            $idids="";
            for( $i=0; $i<count($delsp); $i++){
                dump($delsp[$i]['id']);
                $idids .=$delsp[$i]['id'].',';
            }
            $ides = substr($idids,0,-1);
            $delte = db('cart')->where('goodsid in('.$ides.')')->delete();
            if($delsp){
                return json(array(
                    'code'=>200,
                    'msg'=>'商品不存在',
                    'data'=>$delte,
                ));
            }
        }
        //读取购物车内容
        public function  cartshow(){
            $data = input('post.');
            $change = refresh_cart($data['card_id']);
            if(!empty($change)){
                $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
                $sp_id='';
                for($i=0;$i<count($info);$i++){
                    $sp_id .= $info[$i]['goodsid'].',';
                }
                $sp_sold = db('goods')->where(array('id'=>['in',$sp_id]))->field('id,goods_repertory,status')->select();
                return json(array(
                    'code'=>200,
                    'msg'=>'成功',
                    'data'=>$info,
                ));
            }
        }
//        套餐
        public function setmeal(){
            $data = input('post.');
            $setmeal = db('project')->alias('p')
                ->field('c.*')
                ->join('card c','p.id= c.project_id','right')
                ->where('p.id="'.$data['project_id'].'" and c.id="'.$data['card_id'].'" and p.show_price=1')
                ->count();
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
            if(empty($setmeal)){
            }else{
                return json(array(
                    'code'=>260,
                    'msg'=>'套餐',
                    'data'=>$setmeal,
                    'unit'=>$info,
                ));
            }
        }
        //套餐加入购物车只能加入一个
       public function setmealshop()
       {
           $data = input('post.');
           $setmeal = db('project')->alias('p')
               ->field('c.*')
               ->join('card c', 'p.id= c.project_id', 'right')
               ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=1')
               ->find();
            $info = db('cart')->where('card_id="' . $data['card_id'] . '"')->select();
            if ($setmeal) {
                $info = db('cart')->where('card_id="' . $data['card_id'] . '"')->count();
                return json(array(
                    'code'=>360,
                    'data'=>$info,
                ));
            }
        }
    public function setmealka(){
        $data = input('post.');
        $setmeal = db('project')->alias('p')
            ->field('c.*')
            ->join('card c','p.id= c.project_id','right')
            ->where('p.id="'.$data['project_id'].'" and c.id="'.$data['card_id'].'" and p.show_price=1')
            ->find();
        $info = db('card')->where('id="'.$data['card_id'].'"')->select();
        if($setmeal){
            $dingdan = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
            if($dingdan){
                return json(array(
                    'code'=>260,
                    'msg'=>'套餐',
                    'data'=>$setmeal,
                    'unit'=>$info,
                    'dingdan'=>$dingdan,
                ));
            }
        }
    }
        //删除购物车数据
        public function cartdel(){
            $data = input('post.');
            $info = db('cart')->where('id="'.$data['id'].'"')->delete();
            return json(array(
                'code'=>200,
                'msg'=>'删除成功',
            ));
        }
        //购物车相加
        public function bindPlus(){
            $data = input('post.');
             $change = refresh_cart($data['card_id']);
                if(!empty($change)) {
                    $ses = db('cart')->where('id="' . $data['ids'] . '" and card_id="' . $data['card_id'] . '"')->field('goods_num,goods_price,goods_num_price')->select();
                    $goods_num_price = $ses[0]['goods_price'] * $data['goods_num'];
                    $nums = db('cart')->where('id="' . $data['ids'] . '" and card_id="' . $data['card_id'] . '"')->update(['goods_num_price' => $goods_num_price, 'goods_num' => $data['goods_num']]);
                    return json(array(
                        'code' => 200,
                    ));
                }
        }
        //购物车相减
        public function bindMinus(){
            $data = input('post.');
            $change = refresh_cart($data['card_id']);
            if(!empty($change)) {
                $ses = db('cart')->where('id="' . $data['ids'] . '" and card_id="' . $data['card_id'] . '"')->field('goods_num,goods_price,goods_num_price')->select();
                $goods_num_price = $ses[0]['goods_num_price'] - $ses[0]['goods_price'];
                $nums = db('cart')->where('id="' . $data['ids'] . '" and card_id="' . $data['card_id'] . '"')->update(['goods_num_price' => $goods_num_price, 'goods_num' => $data['goods_num']]);
                return json(array(
                    'code' => 200,
                ));
            }
        }
        //输入相加
        public function bindinput(){
            $data = input('post.');
            $info = db('cart')->where(array('id'=>$data['ids'],'card_id'=>$data['card_id']))->field('goods_num,goods_price,goods_num_price')->select();
            $goods_num_price = $info[0]['goods_price']*$info[0]['goods_num'];
            $nums = db('cart')->where(array('id'=>$data['ids'],'card_id'=>$data['card_id']))->update(['goods_num_price'=>$goods_num_price,'goods_num' => $data['goods_num']]);
        }
        public function total(){
            $data = input('post.');
            $ses = db('cart')->where();
        }
        public function shows(){
            $data = input('post.');

            $inof = db('cart')->where( 'card_id="'.$data['card_id'].'"');
        }
        public function spxj(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
            $sp_id='';
            for($i=0;$i<count($info);$i++){
                $sp_id .= $info[$i]['goodsid'].',';
            }
            $spsds = db('goods')->where(array('id'=>['in',$sp_id],'status'=>0))->field('id,goods_repertory,status')->select();
            if($spsds){
                return json(array(
                    'code'=>500,
                    'sp'=>$spsds,
                ));
            }else{
                return json(array(
                    'code'=>200,
                    'sp'=>$spsds,
                ));
            }
        }
        public function spsl(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'" ')->select();
            $sp_id='';
            for($i=0;$i<count($info);$i++){
                $sp_id .= $info[$i]['goodsid'].',';
            }
            $spsd = db('goods')->where(array('id'=>['in',$sp_id]))->field('id,goods_repertory,status')->select();
            if($spsd){
                return json(array(
                    'code'=>500,
                    'sp'=>$spsd,
                ));
            }else{
                return json(array(
                    'code'=>200,
                    'sp'=>$spsd,
                ));
            }
        }
        public function close(){
            $data = input('post.');
             refresh_cart($data['card_id']);




            $thonkens  = Cache::get('thoken');
            $timestamp = time();
            $tiems     = strtotime($thonkens['result']['expires_at']);
//            dump($thonkens['result']['expires_at']);
            if($timestamp>$tiems){
                $this->thoken();
            };
            //中性
            $neutral = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=2')
                ->find();
            $neutral_taocan = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=1')
                ->find();
            $neutral_id = db('card')->where('id="'.$data['card_id'].'"')->select();
            if($neutral_taocan)
            {

                $neutral_indent = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
                if($neutral_indent){
                    return json(array(
                        'code'=>360,
                        'msg'=>'该卡已兑换过',
                        'data'=>$neutral,
                        'unit'=>$neutral_id,
                        'dingdan'=>$neutral_indent,
                    ));
                }
                else
                {
                    Cache($data['card_id'].'1',1);
                }
            }

            if($neutral){
                $neutral_indent = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
                if($neutral_indent){
                    return json(array(
                        'code'=>360,
                        'msg'=>'该卡已兑换过',
                        'data'=>$neutral,
                        'unit'=>$neutral_id,
                        'dingdan'=>$neutral_indent,
                    ));
                }
                else
                {
                    Cache($data['card_id'].'2',1);
                }
            }

            $inof = db('cart')->where( array('card_id'=>$data['card_id'],'checked' => 1))->select();
            if(!$inof)
            {
                return json(array(
                    'code'=>800,
                    'msg'=>'请勾选商品',
                ));
            }
            $info = db('cart')->where('card_id="'.$data['card_id'].'"and checked=1')->select();
            //删除商品
            $sp_id='';
            for($i=0;$i<count($info);$i++) {
                $sp_id .= $info[$i]['goodsid'] . ',';
            }
            $goods_del_id = substr($sp_id,0,-1);
            $goods_del =  db('goods')->where('id in ('.$goods_del_id.') and status=-1')->select();
            if($goods_del)
            {
                $goods_del_id_one="";
                for( $i=0; $i<count($goods_del); $i++){
                    $goods_del_id_one .=$goods_del[$i]['id'].',';
                }
                $goods_del_id_two = substr($goods_del_id_one,0,-1);
                $goods_del_s = db('cart')->where('goodsid in('.$goods_del_id_two.')')->delete();
                if($goods_del_s){
                    return json(array(
                        'code'=>290,
                        'msg'=>'商品不存在',
                    ));
                }
            }
            $sp_id='';
            for($i=0;$i<count($info);$i++){
                $sp_id .= $info[$i]['goodsid'].',';
                $spsds = db('goods')->where(array('id'=>['in',$sp_id],'status'=>0))->field('id,goods_repertory,status')->select();
                if($spsds){
                    return json(array(
                        'code'=>500,
                        'msg'=>'宝贝补货中',
                        'sp'=>$spsds,
                    ));
                }
                $spsd = db('goods')->where(array('id'=>$info[$i]['goodsid']))->field('id,goods_repertory,status')->find();
                if($spsd['goods_repertory']<$info[$i]['goods_num']){
                    return json(array(
                        'code'=>500,
                        'msg'=>'宝贝补货中',
                        'sp'=>$spsd,
                    ));
                }
            }
            $inventory = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>1))->select();
            	for($i=0;$i<count($inventory);$i++){
            	    $goods = db('goods')->where(array('id'=>$inventory[$i]['goodsid']))->find();;
                    $sp_sold=db('goods')->where(array('id'=>$inventory[$i]['goodsid'],'status'=>0))->find();
                    if($sp_sold){
                        return json(array(
                            'code'=>500,
                            'msg'=>'宝贝补货中',
                        ));
                    }
            	    if($inventory[$i]['goods_num']>$goods['goods_repertory']){
            	        return json(array(
            	            'code'=>500,
                            'msg'=>'宝贝补货中',
                        ));
                    }
                }
            $goodsids=[];
            $goods_pice=0;
            $buy_amount=0;
            for ($u=0; $u < count($inof) ; $u++) {
                $goodsids[]=$inof[$u]['goodsid'];
                $goods_pice+=$inof[$u]['goods_num_price'];
                $buy_amount+=$inof[$u]['goods_num'];
            }
            $data['goods_pice']=$goods_pice;
            $data['buy_amount']=$buy_amount;
            $kahao = db('card')->where('id="'.$data['card_id'].'"')->find();
            $times = "M".time().rand(1,9).$kahao['gift_number'];
            $data['order_sn']=$times;
            $data['newtime']=time();
            $project = db('project')->where('id="'.$data['project_id'].'"')->field('invoice,invoice_gift,telephone,site,start_bank,bank_id,add_auty')->find();;
            $insert_data=[
                'card_id'=>$data['card_id'],
                'order_sn'=>$data['order_sn'],
                'order_price'=>$data['goods_pice'],
                'order_pay'=>'0',
                'order_status'=>'-2',
                'buy_amount'=>$data['buy_amount'],
                'create_time'=>$data['newtime'],
                'order_invoice'=>$project['invoice'],
                'order_invoice_gift'=>$project['invoice_gift'],
                'order_telephone'=>$project['telephone'],
                'order_site'=>$project['site'],
                'order_start_bank'=>$project['start_bank'],
                'order_bank_id'=>$project['bank_id'],
                'order_add_auty'=>$project['add_auty']
            ];
            $ids ='';
            foreach($goodsids as $k=>$v){
                $ids.=$v.',';
            }
            //商品id
            $spid = substr($ids,0,-1);
            //添加order
            $info = db('order')->insertGetId($insert_data);
            //显示订单表
            $infos = db('order')->find();
            $dingdan = db('cart')->where(['goodsid'=>['in',$spid],'card_id'=>$data['card_id'],'checked' =>1])->select();

            $lists=array();
            foreach($dingdan as $k=>$v){
                $pl = $v;
//                print_r($pl['goodsid']);256,261
                $lists[]=[
                    'order_id'=>$info,
                    'order_sn'=>$infos['order_sn'],
                    'goodsid'=>$pl['goodsid'],
                    'goods_price'=>$pl['goods_price'],
                    'goods_amount'=>$pl['goods_num'],
                    'goods_name'=>$pl['goods_name'],
                    'goods_img'=>$pl['goods_img'],
                    'goods_erp'=>$pl['goods_erp'],
                    'goods_unit'=>$pl['goods_unit'],
                ];
            }
            $orgd = db('order_goods')->insertAll($lists);
            if($info){
                return json(array(
                    'code'=>200,
                    'msg'=>'成功',
                    'data'=>$info
                ));
            }else{
               return json(array(
                    'code'=>600,
                    'msg'=>'提交订单失败',
                    'data'=>$info
                ));
            }
        }
        public function pay(){
            $data = input('post.');
            $info = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=0 and id ="'.$data['order_id'].'"')->find();
            $foni = db('order_goods')->where('order_id="'.$info['id'].'" and order_id ="'.$data['order_id'].'"')->select();
            return json(array(
                'code'=>200,
                'msg'=>'成功',
                'data'=>$foni,
                'stat'=>$info
            ));
        }
        //地址
        public function address(){
            $data = input('post.');
            if(isset($data['id'])){
                    $fon = db('card_address')->where('card_id="'.$data['card_id'].'"  and id="'.$data['id'].'"')->find();
                     if($fon){
                        return json(array(
                            'code'=>200,
                            'msg'=>'成功',
                            'data'=>$fon,
                        ));
                    }else{
                        return json(array(
                            'code'=>220,
                            'msg'=>'成功',
                            'data'=>$fon,
                        ));
                    }
            }else{
                    $info = db('card_address')->where('card_id="'.$data['card_id'].'"  and is_default=1')->find();
                    if($info){
                        return json(array(
                            'code'=>200,
                            'msg'=>'成功',
                            'data'=>$info,
                        ));
                    }else{
                        return json(array(
                            'code'=>220,
                            'msg'=>'成功',
                            'data'=>$info,
                        ));
                    }
            }
        }
        //显示积分
        public function cardka(){
            $data = input('post.');
//            print_r($data['card_id']);exit;
            $info = db('card')->where('id="'.$data['card_id'].'"')->find();
            return json(array(
                'code'=>200,
                'msg'=>'成功',
                'data'=>$info,
            ));
        }
        //邮费
        public function youfei(){
            $data = input('post.');
            $setmeal = db('project')->alias('p')
                ->field('c.*')
                ->join('card c','p.id= c.project_id','right')
                ->where('p.id="'.$data['project_id'].'" and c.id="'.$data['card_id'].'" and p.show_price=1')
                ->find();
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
            if($setmeal){
                if(empty($data['id'])){
                    $info = db('card_address')->where('card_id="'.$data['card_id'].'" and is_default="1" ')->find();
                    $youfei =  get_postage($info['address'],$data['project_id']);
                    if($youfei==0){
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }
                    if($youfei){
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }else{
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }
                }else{
                    $info = db('card_address')->where('card_id="'.$data['card_id'].'" and id="'.$data['id'].'"')->find();
                    $youfei =  get_postage($info['address'],$data['project_id']);
                    if($youfei==0.00){
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }
                    if($youfei){
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }else{
                        return json(array(
                            'code'=>240,
                            'msg'=>'已享受免运费',
                            'data'=>$setmeal,
                            'unit'=>$info,
                            'postage'=>$youfei,
                        ));
                    }
                }
            }
            if(empty($data['id'])){
                $info = db('card_address')->where('card_id="'.$data['card_id'].'" and is_default="1" ')->find();
                $youfei =  get_postage($info['address'],$data['project_id']);
                if($youfei==0){
                    return json(array(
                        'code'=>230,
                        'msg'=>'已享受免运费',
                        'data'=>$youfei,
                    ));
                }
                if($youfei){
                    return json(array(
                        'code'=>200,
                        'msg'=>'成功',
                        'data'=>$youfei,
                    ));
                }else{
                    return json(array(
                        'code'=>230,
                        'msg'=>'已享受免运费',
                        'data'=>$youfei,
                    ));
                }
            }else{
                    $info = db('card_address')->where('card_id="'.$data['card_id'].'" and id="'.$data['id'].'"')->find();
                    $youfei =  get_postage($info['address'],$data['project_id']);
                    if($youfei==0.00){
                        return json(array(
                            'code'=>230,
                            'msg'=>'已享受免运费',
                            'data'=>$youfei,
                        ));
                    }
                    if($youfei){
                        return json(array(
                            'code'=>200,
                            'msg'=>'成功',
                            'data'=>$youfei,
                        ));
                    }else{
                        return json(array(
                            'code'=>230,
                            'msg'=>'已享受免运费',
                            'data'=>$youfei,
                        ));
                    }
            }
        }
        public function payment(){
            $data = input('post.');
            $info = db('order')->where('card_id="'.$data['card_id'].'" and id = "'.$data['id'].'"')->update($data);
            return json(array(
                'code'=>200,
                'msg'=>'成功'
            ));
        }
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

            if($out['success']==true){
                Cache('thoken',$out);
            }else{
                return"false";
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
        public function yincang(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'" ')->select();
            $sp_id='';
            for($i=0;$i<count($info);$i++){
                $sp_id .= $info[$i]['goodsid'].',';
            }
            $sp_id=substr($sp_id,0,-1);
            $spsds = db('goods')->where(array('id'=>['in',$sp_id],'status'=>1))->field('id,goods_repertory,status')->select();
            $spsd = db('goods')->where(array('id'=>['in',$sp_id],'goods_repertory'=>0))->whereOr('id in ('.$sp_id.') and status = 0')->field('id,goods_repertory,status')->select();
            if($spsd){
                return json(array(
                    'code'=>500,
                    'msg'=>'宝贝补货中',
                    'sp'=>$spsd,
                ));
            }else{
                return json(array(
                    'code'=>200,
                    'msg'=>'宝贝补货中',
                    'sp'=>$spsd,
                ));
            }
        }
        public function indent(){
            $data = input('post.');
            //中型
            $neutral = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=2')
                ->find();
            $neutral_taocan = db('project')->alias('p')
                ->field('c.*')
                ->join('card c', 'p.id= c.project_id', 'right')
                ->where('p.id="' . $data['project_id'] . '" and c.id="' . $data['card_id'] . '" and p.show_price=1')
                ->find();
            if($neutral_taocan)
            {
                if(Cache::get($data['card_id'].'1') == 100861)
                {
                    return json(array(
                        'code'=>370,
                        'msg'=>'该卡已兑换套餐'
                    ));
                }


                $shus=Cache($data['card_id'].'1',100861);

            }
            if($neutral)
            {
               if(Cache::get($data['card_id'].'2') == 100862)
               {
                   return json(array(
                       'code'=>370,
                       'msg'=>'该卡已兑换过'
                   ));
               }


                $shus=Cache($data['card_id'].'2',100862);
            }
            $thonkens  = Cache::get('thoken');
            $timestamp = time();
            $tiems     = strtotime($thonkens['result']['expires_at']);
//            if($timestamp>$tiems){
//                $this->thoken();
//            };
            if(empty(Cache::get('thoken'))){
                $this->thoken();
            }
            $thonkens  = Cache::get('thoken');

            // if($thonkens['success']==false){
            //     $this->thoken();
            // }
            if(!empty($thonkens['errormsg'])){
                $this->thoken();
            }
                $key = $thonkens['result']['access_token'];

                $neutral_id = db('card')->where('id="'.$data['card_id'].'"')->select();
                if($neutral){
                    $neutral_indent = db('order')->where('card_id="'.$data['card_id'].'" and order_pay=1 and status=1')->select();
                    if($neutral_indent){
                        return json(array(
                            'code'=>360,
                            'msg'=>'该卡已兑换过',
                            'data'=>$neutral,
                            'unit'=>$neutral_id,
                            'dingdan'=>$neutral_indent,
                        ));
                    }
                }
                //

                $imputed = db('card')->where('id="'.$data['card_id'].'"')->find();
                 $order_pricess = db('order')->where('id="'.$data['order_id'].'"')->find();
              if($imputed['status']!=1)
              {
              $shus=Cache($data['card_id'].'1',1);
              $shus=Cache($data['card_id'].'2',1);
              	return json(array(
                        'code'=>261,
                        'msg'=>'卡已被禁用',
                    ));
              }
                if($imputed['money']<$order_pricess['order_price']){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>260,
                        'msg'=>'余额不足',
                    ));
                }
                //goods表删除结算页不能购买
                $jiesuandel = db('order_goods')->where('order_id="'.$data['order_id'].'"')->select();
                $jsdelid = '';
                for($i=0;$i<count($jiesuandel);$i++){
                   $jsdelid .= $jiesuandel[$i]['goodsid'].',';
                }
                $jsdelids = substr($jsdelid,0,-1);
                $goods_del = db('goods')->where('id in('.$jsdelids.') and status=-1')->select();
                if($goods_del){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(

                        'code'=>264,
                        'msg'=>'商品不存在',
                        'data'=>$goods_del,
                    ));
                }
                //goods表erp商品号修改结算页也跟着修改
                refresh_order_goods($jsdelid);
                //结算页判断下架补货
                $info = db('cart')->where('card_id="'.$data['card_id'].'"and checked=1 ')->select();
                $sp_id='';
                for($i=0;$i<count($info);$i++){
                    $sp_id .= $info[$i]['goodsid'].',';
                }
                $spsds = db('goods')->where(array('id'=>['in',$sp_id],'status'=>0))->field('id,goods_repertory,status')->select();
                if($spsds){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>500,
                        'msg'=>'宝贝补货中',
                        'sp'=>$spsds,
                    ));
                }
                $spsd = db('goods')->where(array('id'=>['in',$sp_id],'goods_repertory'=>0))->field('id,goods_repertory,status')->select();
                if($spsd){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>500,
                        'msg'=>'宝贝补货中',
                        'sp'=>$spsd,
                    ));
                }
                $info = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->find();
                $inf = db('card_address')->where('card_id="'.$data['card_id'].'" and id="'.$data['zhi_id'].'" ')->find();
                $erp = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->find();
                $erps = db('order_goods')->where('order_id="'.$erp['id'].'" and order_id ="'.$data['order_id'].'"')->select();
                $sp_id= '';
                for($i=0; $i<count($erps);$i++){
                    $goo_id = $erps[$i]['goodsid'];
                    $sp_id.= $goo_id.',';
                }
                $sp_ids = substr($sp_id,0,-1);
                $sp_xiajia=db('goods')->where(array('id'=>['in',$sp_ids]))->select();
                for($i=0; $i<count($sp_xiajia);$i++){
                    if($sp_xiajia[$i]['status']==0){
                        $shus=Cache($data['card_id'].'1',1);
                        $shus=Cache($data['card_id'].'2',1);
                        return json(array(
                            'code'=>500,
                            'msg'=>'宝贝补货中',
                        ));
                    }
                }
                //判断库存
                $order_goods_id = substr($data['good_id'],0,-1);
                $order_goods = db('order_goods')->where('order_id="'.$data['order_id'].'"and goodsid in('.$order_goods_id.')')->select();
                $repe=[];
                for($i=0;$i<count($order_goods);$i++){
                    $goods = db('goods')->where('id='.$order_goods[$i]['goodsid'].' and  goods_repertory < '.$order_goods[$i]['goods_amount'].'')->find();
                    if($goods){
                       $repe[]=$goods;
                    }
                }
                if($repe)
                {
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                    'code'=>7481,
                    'msg'=>'宝贝补货中',
                    'data'=>$repe,
                ));
                }
                //判断库存不出
                $inventory = db('goods')->where(array('id'=>['in',$sp_ids]))->select();
                for($i=0;$i<count($inventory);$i++){
                    if($inventory[$i]['goods_repertory']==0){
                        $shus=Cache($data['card_id'].'1',1);
                        $shus=Cache($data['card_id'].'2',1);
                        return json(array(
                            'code'=>500,
                            'msg'=>'宝贝补货中',
                        ));
                    }
                }
                $url = KLP_API."/giftcard/api/restful/extorder";
                //初始化
                $headers = array(
                    "Colipu-Token:".$key,
                    "Content-Type:application/json",
                );

                $injson = array();
                for($i=0; $i<count($erps);$i++){
                $injson[]=[
                    'sku'=>$erps[$i]['goods_erp'],
                    'num'=>$erps[$i]['goods_amount'],
                    'price'=>$erps[$i]['goods_price'],
                ];
                }

                $post_data=array();
                $dizhi = get_address_number($inf['address']);
                $post_data['yggc_order']=$info['order_sn'];
                $post_data['sku']=$injson;
                $post_data['name']=$info['order_name'];
                $post_data['province']=intval($dizhi[0]);
                $post_data['city']=intval($dizhi[1]);
                $post_data['county']=intval($dizhi[2]);
                $post_data['address']=$info['address'];
                $post_data['zip']='999001';
                $post_data['phone']='';
                $post_data['mobile']=$info['mobile'];
                $post_data['email']='summer.xia@izhihuo.com';
                $post_data['remark']=$info['order_remark'];
//                $post_data['invoice_type']=$info['order_add_auty'];
                $post_data['invoice_type']=$info['order_invoice'];
                $post_data['invoice_title']=$info['order_invoice_gift'];
                $post_data['invoice_tax_num']=$info['order_add_auty'];
                $post_data['invoice_bank']=$info['order_start_bank'];
                $post_data['invoice_bank_account']=$info['order_bank_id'];
                $post_data['invoice_address']=$info['order_site'];
                $post_data['invoice_phone']=$info['order_telephone'];
                $post_data['payment']=9;
                $post_data['order_price']=intval($info['order_price']);
                $post_data['freight']=intval($info['freight']);
                $str=json_encode($post_data);
//                dump($str);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);
                $message = json_decode($output,true);
//                dump($message);die;
                if($message['errorcode']== -1){
                    $this->thoken();
                }
                if(!empty($message['errormsg'])){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>510,
                        'msg'=>'商品信息有误请联系客服400-118-8366'
                    ));
                }
                if($message['errorcode']==500){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>510,
                        'msg'=>'商品信息有误请联系客服400-118-8366'
                    ));
                }

                if(empty($message['success'])){
                    $shus=Cache($data['card_id'].'1',1);
                    $shus=Cache($data['card_id'].'2',1);
                    return json(array(
                        'code'=>500,
                        'msg'=>'宝贝补货中'
                    ));
                }
                if($message['success']==true) {
                    $url = KLP_API."/giftcard//api/restful/order/{$info['order_sn']}/confirmation";
                   // print_r($url);die;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);     //20170611修改接口，用/id的方式传递，直接写在url中了
                    $ots = curl_exec($ch);
                    curl_close($ch);
                    $wuliao=json_decode($ots,true);
                }
                if($wuliao['success']==true){
                    $zhifusj = time();
                    $zhif = '1';
                    $shohuo ='0';
                    $kahao = db('card')->where('id="'.$data['card_id'].'"')->find();
                    $zhifu = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->update(['order_pay'=>$zhif,'order_pay_time' =>$zhifusj,'order_status'=>$shohuo,'card_sn'=>$kahao['gift_number']]);
                    $jian = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->find();
                    $cardd =db('card')->where('id="'.$data['card_id'].'"')->find();
                    $jianfan = $cardd['money'] - $jian['order_price'];
                    $deduction = db('card')->where('id="'.$data['card_id'].'"')->update(['money'=>$jianfan]);
                    $kucun = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->find();
                    //有可能俩条数据
                    $kucuns = db('order_goods')->where('order_id="'.$kucun['id'].'"')->select();
                    foreach($kucuns as $k=>$v){
                        $gdds = db('goods')->where('id="'.$v['goodsid'].'"')->setDec('goods_repertory',$v['goods_amount']);
                    };
                    $sgw = db('cart')->where('card_id="'.$data['card_id'].'" and checked=1')->delete();
                    $this->sales($data['order_id']);
                    //积分流水
                        $lisst=[
                            'card_id'=>$cardd['id'],
                            'order_id'=>$data['order_id'],
                            'order_sn'=>$kucun['order_sn'],
                            'marked'=>'消费:订单号("'.$kucun['order_sn'].'")',
                            'integral_before'=>$cardd['money'],
                            'integral'=>$jian['order_price'],
                            'integral_stop'=>$jianfan,
                            'create_time'=>time(),
                        ];
                    $orgd = db('card_running')->insert($lisst);
                    return json(array(
                        'code'=>200,
                        'msg'=>'成功'
                    ));
                }else{
                    echo "订单不存在";
                }
        }
        public function sales($order_id){
            $xiaoliang = db('order_goods')->where('order_id="'.$order_id.'"')->select();
            for($i=0;$i<count($xiaoliang);$i++){
                $x = db('goods')->where('id="'.$xiaoliang[$i]['goodsid'].'"')->find();
                $sl = $x['goods_volume']+$xiaoliang[$i]['goods_amount'];
                $xl = db('goods')->where('id="'.$xiaoliang[$i]['goodsid'].'"')->update(['goods_volume'=>$sl]);
            }
            return true;
        }
        //购物图标显示
            public function gwxs(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->find();
                if($info){
                    return json(array(
                        "code"=>200,
                        "data"=>$info,
                    ));
                }else{
                    return json(array(
                        "code"=>251,
                        "data"=>$info,
                    ));
                }
            }
        //购物图标推荐
        public function gwxss(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->find();
            if($info){
                return json(array(
                    "code"=>200,
                    "data"=>$info,
                ));
            }else{
                return json(array(
                    "code"=>251,
                    "data"=>$info,
                ));
            }
        }
            //地址显示隐藏
        public function siteout(){
            $data = input("post.");
            $info = db('card_address')->where('card_id="'.$data['card_id'].'" and is_default=1')->find();
            if($info){
                return json(array(
                    "code"=>200,
                    "data"=>$info,
                ));
            }else{
                return json(array(
                    "code"=>220,
                    "data"=>$info,
                ));
            }
        }
        public function integral(){
            $data = input('post.');
            $onfi = db('card')->where('id="'.$data['card_id'].'"')->find();
            if($onfi['money_start'] == 0)
            {
            	db('card')->where('id="'.$data['card_id'].'"')->update(['money_start' => 1]);
            	$data=[
            		'card_id' =>$data['card_id'],
            		'integral_before' =>$onfi['bei_money'],//备用积分面值
            		'integral' =>$onfi['bei_money'], 	
            		'integral_stop'=>$onfi['bei_money'],
            		'create_time' =>time(),
            		'money_start' => 1
            	];
            	db('card_running')->insert($data);
            }
            $ifno = db('card_running')->where('card_id="'.$data['card_id'].'"')->order('id asc')->select();
            /* 规避项目套餐形式的*/
            $card_info=db('card')->where('id  = "'.$data['card_id'].'"')->find();
            $order_info=db('order')->where('card_id  = "'.$data['card_id'].'" and status = 1 and order_pay = 1')->select();
            $project_info=db('project')->where('id = "'.$card_info['project_id'].'"')->find();
            if(count($order_info)>=1 && $project_info['show_price'] == 1 )
            {
                $onfi['money']= 0;
            }
            /*结束*/
             for ($i=0;$i<count($ifno);$i++){
                $ifno[$i]['create_time'] = date("Y-m-d H:i:s",$ifno[$i]['create_time']);
            }
            return json(array(
                'code'=>200,
                'data'=>$ifno,
                'project_info'=>$project_info,
                'onfi'=>$onfi,
            ));
        }
        public function jiag(){
            $data= input('post.');
            $info = db('order')->where("card_id='".$data['card_id']."'")->find();
            return json(array(
                'code'=>200,
                'data'=>$info,
            ));
        }
        public function integrals(){
            $data= input('post.');
            $integral=0;
            $quan = db('cart')->where("card_id='".$data['card_id']."'")->select();
            for($i=0;$i<count($quan); $i++){
                $zhi = $quan[$i]['checked'];
                if($data['checkAll'] == true)
                {
                    $checkalldef= db('cart')->where("card_id='".$data['card_id']."'")->update(['checked'=>0]);
                }
               else{
                   $checkalldef= db('cart')->where("card_id='".$data['card_id']."'")->update(['checked'=>1]);
               }
            }
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
        public function shuliang(){
             $data = input('post.');
             $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
             return json(array(
                 'code'=>200,
                 'data'=>$info,
             ));
        }
        public function dizhis(){
            $data = input('post.');
            $info = db('card_address')->where('id="'.$data['id'].'" and card_id="'.$data['card_id'].'"')->find();
            return json(array(
                'code'=>200,
                'data'=>$info,
            ));
        }
        //显示手机号
        public function mobileshow(){
            $data = input('post.');
            $info = db('card')->where('id = "'.$data['card_id'].'"')->find();
            $ss =db('card_running')->where('card_id = "'.$data['card_id'].'"')->find();
            return json(array(
                'code'=>200,
                'data'=>$info,
                'ss'=>$ss,
            ));
        }
        //购物图标展示
        public function shopshow(){
            $data = input('post.');
            $info = db('cart')->where('card_id="'.$data['card_id'].'"')->select();
            $nums = 0;
            foreach($info as $k=>$v){
                 $nums+=$v['goods_num'];
            }
            return json(array(
                'code'=>200,
                'data'=>$nums,
            ));
        }
        public function zongjifen(){
            $data = input('post.');
            $info = db('order')->where('card_id="'.$data['card_id'].'"')->order('id desc')->find();
            $sum = $info['order_price']+$info['freight'];
            return json(array(
                'code'=>200,
                'data'=>$sum,
            ));
        }
        public function cancelpay(){
            $data = input('post.');
            $info = db('order')->where('card_id="'.$data['card_id'].'" and id ="'.$data['order_id'].'"')->delete();
            $infos =db('order_goods')->where('order_id="'.$data['order_id'].'"')->delete();
            if($info){
                return json(array(
                    'code'=>200,
                    'msg'=>'成功',
                    'data'=>$info,
                ));
            }else{
                return json(array(
                    'code'=>240,
                    'msg'=>'成功',
                    'data'=>$info,
                ));
            }
        }
        //购物车单选
        public function checkboxChange(){
            $data = input('post.');
            $spid = substr($data['ids'],0,-1);
            $ff = db('cart')->where(array('card_id'=>$data['card_id'],'id'=>$spid))->find();
            if($ff['checked']=='1' ){
                $data['checked']=0;
            }else if($ff['checked']=='0'){
                $data['checked']=1;
            }
            $info = db('cart')->where(array('card_id'=>$data['card_id'],'id'=>$spid))->update(['checked'=>$data['checked']]);
        }
        public function shopadding(){
            $data = input('post.');
            $heji = refresh_cart($data['card_id']);


            if(!empty($heji)){
            $info = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>1))->select();
            $shopjige=0;
            foreach($info as $k=>$v){
               $shopjige+=($v['goods_price']*100) * ($v['goods_num']*100);
            }
            return json(array(
                'code'=>200,
                'data'=>$shopjige / 10000,
            ));
            }
        }
        public function quanxuan(){
            $data = input('post.');
            $change = refresh_cart($data['card_id']);
            if(!empty($change)) {
                $gh = db('cart')->where(array('card_id' => $data['card_id']))->select();
                for ($i = 0; $i < count($gh); $i++) {
                    $df = $gh[$i]['checked'];
                    if ($df == 1) {
                        $info = db('cart')->where(array('card_id' => $data['card_id'], 'checked' => 1))->select();
                        $shopjige = 0;
                        foreach ($info as $k => $v) {
                            $shopjige += $v['goods_num_price'];
                        }
                        return json(array(
                            'code' => 200,
                            'data' => $shopjige,
                        ));
                    } else if ($df == 0) {
                        $info = db('cart')->where(array('card_id' => $data['card_id'], 'checked' => 0))->select();
                        $shopjige = 0;
                        foreach ($info as $k => $v) {
                            $shopjige += 0;
                        }
                        return json(array(
                            'code' => 200,
                            'data' => $shopjige,
                        ));
                    }
                }
            }
        }
        //默认全选
        public function defqx(){
            $data=input('post.');
            //数量
            $info = db('cart')->where(array('card_id'=>$data['card_id']))->count();
            $inf = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>1))->count();
            if($inf>0&&$inf==$info){
                return json(array(
                    'code'=>200,
                    'data'=>1,
                ));
            }else{
                return json(array(
                    'code'=>212,
                    'data'=>0,
                ));
            }
        }
        public function cunzhi(){
                $data = input('post.');
                $info = db('cart')->where(array('card_id'=>$data['card_id'],'checked'=>1))->select();
                $pdid = '';
                for($i=0;$i<count($info); $i++){
                    $onid = $info[$i]['id'];
                    $pdid.= $onid.',';
                }
                return json(array(
                    'code'=>200,
                    'data'=>$pdid,
                ));
        }
        //待收货显示数量;
        public function received()
        {
            $data = input('pos.');
            $info = db('order')->where(array('card_id' => $data['card_id'], 'order_pay' => 1))->count();
            return json(array(
                'code' => 200,
                'data' => $info,
            ));
        }
    }
?> 