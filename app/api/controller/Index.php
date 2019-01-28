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

/**
 * 首页控制器
 */
class Index extends ControllerBase  
{

    // /**
    //  * 首页轮播图
    //  * picture为图片表
    //  */
    // public function banner1()   //$uid=false,$project_id=false
    // {
    //     $uid = isset($_GET['uid']) ? $_GET['uid'] : false;   
    //     $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

    //     // if (!isset($uid) || !isset($project_id)) {  
    //     //     return show('500' ,'','系统异常');
    //     // }
        
        
    //     if(!($uid && $project_id))//登陆前条件 
    //     {
             
    //         $where_banner['b.status'] =1;
    //         $where_banner['b.cate_affiliation'] =0;  //登录前的轮播图商品链接对应为无项目(数据库里默认为0)
            
    //     }
    //     else//登陆后条件  
    //     {

    //         $where_banner['b.status'] =1;
    //         $where_banner['b.cate_affiliation'] =$project_id;

    //     }
    //     //轮播图数据(要求排序)
    //     $row=db('banner')->alias('b')->field('b.img_name,b.jump,b.sort,p.path')->join('picture p','b.thumb=p.id','left')->where($where_banner)->where('b.status = 1')->order('b.sort asc,b.id desc')->select();
    //     //dump($where);

    //     // dump($row);
    //     // exit;   

    //     if(!$row)
    //     {
    //         return show('500' ,'','系统异常');
    //     }
    //     return show('200' ,$row);
    // }




    public function banner2()
    {

        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;   
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        // if (!isset($uid) || !isset($project_id)) {  
        //     return show('500' ,'','系统异常');
        // }
       
        $data=[];

        // //轮播图数据(要求排序)
        // $row=db('banner')->alias('b')->field('b.img_name,b.jump,b.sort,p.path')->join('picture p','b.thumb=p.id','left')->where('b.status = 1')->order('b.sort asc,b.id desc')->select();  
        
        
        //分类数据
        if(!($uid && $project_id))//登陆前条件 
        {
            //分类条件
        
            $where_banner['b.status'] =1;
            $where_banner['b.cate_affiliation'] =0;  //登录前的轮播图商品链接对应为无项目(数据库里默认为0)

            // //商品条件
            // $goods_where['g.status']=1;
            // $goods_where['g.is_boutique']=0;       //$goods_where['g.is_boutique']=1;
            // $goods_where['c.goods_category_default']=0;
            // echo 11;
        }
        else//登陆后条件
        {
            $where_banner['b.status'] =1;
            $where_banner['b.cate_affiliation'] =$project_id;

            // //商品条件
            // $goods_where['g.status']=1;
            // $goods_where['g.is_boutique']=1;
            // $goods_where['c.goods_category_default']=1;
            // $goods_where['c.cate_affiliation']=$project_id;//项目id
            // echo 22;
        }

        //轮播图数据(要求排序)
        $row=db('banner')->alias('b')->field('b.img_name,b.jump,b.sort,p.path')->join('picture p','b.thumb=p.id','left')->where($where_banner)->order('b.sort asc,b.id desc')->select();

       
        // //精品数据
       // $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,goods_price,goods_img,goods_content')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->select();  //->limit($index, $n)

       

        if(!$row)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' ,$row);


    }
    
    /**
     * 首页展示数据(轮播图和顶级分类列表)
     * picture为图片表
     */
    public function banner()   //$uid=false,$project_id=false
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;   
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        // if (!isset($uid) || !isset($project_id)) {  
        //     return show('500' ,'','系统异常');
        // }
       
        $data=[];

        // //轮播图数据(要求排序)
        // $row=db('banner')->alias('b')->field('b.img_name,b.jump,b.sort,p.path')->join('picture p','b.thumb=p.id','left')->where('b.status = 1')->order('b.sort asc,b.id desc')->select();  
        
        
        //分类数据
        if(!($uid && $project_id))//登陆前条件 
        {
            //分类条件
            $where['g.status']=1;
         	$where['g.tid']=0;   //tid为零的是顶级分类                 
            $where['g.goods_category_default']=0; //0为登录前显示的分类
            $where['g.is_recommend']=1;  //1为推荐
            // $where['g.cate_affiliation']=0;//项目id
             
            $where_banner['b.status'] =1;
            $where_banner['b.cate_affiliation'] =0;  //登录前的轮播图商品链接对应为无项目(数据库里默认为0)

            // //商品条件
            // $goods_where['g.status']=1;
            // $goods_where['g.is_boutique']=0;       //$goods_where['g.is_boutique']=1;
            // $goods_where['c.goods_category_default']=0;
            // echo 11;
        }
        else//登陆后条件
        {
            //分类条件
            $where['g.status']=1;
            $where['g.tid']=0;//分类上级id
            $where['g.goods_category_default']=1;//找登陆后显示的数据
            $where['g.is_recommend']=1;  //1为推荐
            $where['g.cate_affiliation']=$project_id;//项目id

            $where_banner['b.status'] =1;
            $where_banner['b.cate_affiliation'] =$project_id;

            // //商品条件
            // $goods_where['g.status']=1;
            // $goods_where['g.is_boutique']=1;
            // $goods_where['c.goods_category_default']=1;
            // $goods_where['c.cate_affiliation']=$project_id;//项目id
            // echo 22;
        }

        //轮播图数据(要求排序)
        $row=db('banner')->alias('b')->field('b.img_name,b.jump,b.sort,p.path')->join('picture p','b.thumb=p.id','left')->where($where_banner)->order('b.sort asc,b.id desc')->select();

        //dump($where);
        $goods_category_list=db('goods_category')
        ->alias('g')   
        ->field('g.*,p.path')
        ->join('picture p','g.cate_thumb=p.id','left')  
        ->where($where)
        ->order('g.sort', 'asc')  //后加的分类排序功能
        ->limit(10)  //首页顶级分类只显示10个
        ->select();
      
        // //精品数据
       // $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,goods_price,goods_img,goods_content')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->select();  //->limit($index, $n)

        $data=[$row,$goods_category_list];  //$data=[$row,$goods_category_list,$goods_list];

        // dump($goods_category_list);
        // exit;   

        if(!$data)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' ,$data);
    }


    /**
     * 首页展示精选商品列表(懒加载)
     * 
     */
    public function goods()  //$uid=false,$project_id=false
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;
        
        //用于懒加载(分页)
        $index = $_GET['start'];
        $n = $_GET['count'];

        // if (!isset($index) || !isset($n) || !isset($uid) || !isset($project_id)) {  
        //     return show('500' ,'','系统异常');
        // }

        // $p = isset($_GET['p']) ? $_GET['p'] : 1;
        
        // $n = 4; //每页个数
        // $index = ($p - 1) * $n; //查询序号
        // if($index < 0)
        // {
        //     $index = 0;
        // }
        
        
        if(!($uid && $project_id))//登陆前条件
        {
            // //分类条件
            // $where['g.status']=1;
            // $where['g.tid']=0;                    
            // $where['g.goods_category_default']=0;  
            // $where['g.cate_affiliation']=0;//项目id
            
            //商品条件
            $goods_where['g.status']=1;  //商品为上架状态
            $goods_where['c.status']=1;  //二级分类为上架状态
            $goods_where['g.is_boutique']=0;  //0为精品
            // $goods_where['g.is_boutique']!=1;  //1为正常
            $goods_where['c.goods_category_default']=0;  //登录前显示
            // $goods_where['c.cate_affiliation']=0;//项目id
            //echo 11;
            // $data = '走登录前';
            
            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=0;  //登录后显示

        }
        else//登陆后条件  
        {
            // //分类条件
            // $where['g.status']=1;
            // $where['g.tid']=0;//分类上级id
            // $where['g.goods_category_default']=1;//找登陆后显示的数据
            // $where['g.cate_affiliation']=$project_id;//项目id
            
            //商品条件
            $goods_where['g.status']=1;  //商品为上架状态
            $goods_where['c.status']=1;  //二级分类为上架状态
            $goods_where['g.is_boutique']=0;  //登录后的精选商品
            $goods_where['c.goods_category_default']=1;  //登录后显示
            $goods_where['c.cate_affiliation']=$project_id;//项目id
            //echo 22;
            // $data = '走登录后';
             
            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=1;  //登录后显示
            $project_where['c.cate_affiliation']=$project_id;//项目id
            
        }

        $project_status = db('project')->alias('p')->field('p.show_price')->join('goods_category c','p.id=c.cate_affiliation','left')->where($project_where)->where('c.tid != 0')->find();
        
        
        // if ($index < 16) {  //前两次查询让$n为8
        //     $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, $n)->select(); //->order('g.sort', 'asc')
        // }elseif ($index == 16) {  //第三次查询时让$n为4(目的让总数为20条商品)
        //     $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, 4)->select();  //->order('g.sort', 'asc')
        // }else{
        //     return show('500' ,'','系统异常');  
        //     // exit();
        // }

        //精品数据
        $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit(20)->select();  //  ->limit($index, $n)  ->fetchSql(true)   ->where('g.status != -1')   ->join('goods_category c','g.category_id=c.id','left')
 

        //$sql = "SELECT `g`.`goods_name`,`g`.`id`,`g`.`goods_price`,`g`.`goods_img`,`g`.`goods_content` FROM `klp_goods` `g` LEFT JOIN `klp_goods_category` `c` ON `g`.`category_id`=`c`.`id` WHERE `g`.`status` = 1 AND `g`.`is_boutique` = 0 AND `c`.`goods_category_default` = 0 LIMIT 0,6";

        
        // dump($goods_list);  
        // exit();
        
        if(!$goods_list)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' ,$goods_list,$project_status);

    }



    /**
     * 购物车页展示"为您推荐"商品列表
     * 
     */
    public function shopGoods()  
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        $index = $_GET['start'];
        $n = $_GET['count'];

        // if (!isset($index) || !isset($n) || !isset($uid) || !isset($project_id)) {  
        //     return show('500' ,'','系统异常');
        // }

       
        if(!($uid && $project_id))//登陆前条件
        {
            // //分类条件
            // $where['g.status']=1;
            // $where['g.tid']=0;                    
            // $where['g.goods_category_default']=0;  
            // $where['g.cate_affiliation']=0;//项目id
            
            //商品条件
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            $goods_where['c.goods_category_default']=0;
            $goods_where['g.is_boutique']=0; //0为精选商品作为购物车里的推荐商品
            // $goods_where['c.is_recommend']=1;  //1为推荐 (暂不让生效)
            // $goods_where['c.cate_affiliation']=0;//项目id
            // echo 11;
            
            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=0;  //登录后显示
           

        }
        else//登陆后条件   
        {
            // //分类条件
            // $where['g.status']=1;
            // $where['g.tid']=0;//分类上级id
            // $where['g.goods_category_default']=1;//找登陆后显示的数据
            // $where['g.cate_affiliation']=$project_id;//项目id
            
            //商品条件
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            $goods_where['c.goods_category_default']=1;
            $goods_where['g.is_boutique']=0;  //0为精选商品作为购物车里的推荐商品
            // $goods_where['c.is_recommend']=1;  //1为推荐商品 (暂不让生效)
            $goods_where['c.cate_affiliation']=$project_id;//项目id
            // echo 22;
            
            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=1;  //登录后显示
            $project_where['c.cate_affiliation']=$project_id;//项目id
            
        }

        $project_status = db('project')->alias('p')->field('p.show_price')->join('goods_category c','p.id=c.cate_affiliation','left')->where($project_where)->where('c.tid != 0')->find();
        
        // if ($index < 16) {  //前两次查询让$n为8
        //    $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, $n)->select();  //->order('g.sort', 'asc') 
        // }elseif ($index == 16) {  //第三次查询时让$n为4(目的让总数为20条商品) 
        //    $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, 4)->select();
        // }else{
        //     return show('500' ,'','系统异常');
        //     // exit();
        // }

        //"为你推荐"商品数据
        $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit(20)->select();  //->where($goods_where)  ->limit($index, $n)  ->fetchSql(true)  ->where('c.tid != 0')


        // dump($goods_list);
        // exit();
        
        if(!$goods_list)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' ,$goods_list,$project_status);

    }
    

    /**
     *关键字搜索商品   
     */

    public function search()  
    {
        $item_code = $_GET['item_code']; //用于判断用户点击事件,$item_code为1表示点击"销量"按钮,2为点击价格"低~高"按钮,3为点击价格"高~低"按钮 , 默认''为没点击任何一个

        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        $index = $_GET['start'];
        $n = $_GET['count'];

        // if (!isset($index) || !isset($n) || !isset($uid) || !isset($project_id)) {

        //     return show('500' ,'','系统异常');
        // }


        $keyword = !empty($_GET['keyword']) ? $_GET['keyword'] : '';
        // $keyword = !empty($_POST['keyword']) ? $_POST['keyword'] : '';

        if(!($uid && $project_id))//登陆前条件 
        {
            //商品条件  
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            // $goods_where['g.is_boutique']=0;       //搜索出来的商品无需是精选
            $goods_where['c.goods_category_default']=0;

            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=0;  //登录后显示
             

        }
        else//登陆后条件
        {
            //商品条件
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            // $goods_where['g.is_boutique']=1;  //搜索出来的商品无需是精选
            $goods_where['c.goods_category_default']=1;
            $goods_where['c.cate_affiliation']=$project_id;//项目id

            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=1;  //登录后显示
            $project_where['c.cate_affiliation']=$project_id;//项目id    
            
        }
   
        if (empty($item_code)) {  //默认显示被搜索的商品数据
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where('g.goods_name','like','%'.$keyword.'%')->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }elseif ($item_code == 1) { //为1时按销量倒序查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('g.goods_name','like','%'.$keyword.'%')->where($goods_where)->where('c.tid != 0')->order('g.goods_volume', 'desc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }elseif ($item_code == 2) { //为2时按价格从低到高查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('g.goods_name','like','%'.$keyword.'%')->where($goods_where)->where('c.tid != 0')->order('g.goods_price', 'asc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }elseif ($item_code == 3) { //为3时按价格从高到低查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('g.goods_name','like','%'.$keyword.'%')->where($goods_where)->where('c.tid != 0')->order('g.goods_price', 'desc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }

        $project_status = db('project')->alias('p')->field('p.show_price')->join('goods_category c','p.id=c.cate_affiliation','left')->where($project_where)->where('c.tid != 0')->find();

        // //被搜索的商品数据  
        // $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content')->join('goods_category c','g.category_id=c.id','left')->where('g.goods_name','like','%'.$keyword.'%')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)

        // dump($goods_list); 
        // exit();
        // array_push($goods_list, $project_status);

        if(!$goods_list)
        {
            return show('500' ,$goods_list,'系统异常');
        }
        return show('200' ,$goods_list,$project_status);
    }



    /**
     *分类页展示一级分类及对应二级分类列表
     */

     public function classify_1() //$uid=false, $project_id=false
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
            // $where['g.cate_affiliation']=0;//项目id  
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
        ->where('g.status != 0')  //后台下架二级分类时小程序中对应删除其二级分类(即下架后的不显示)
        ->order('g.sort asc,g.id asc')  //后加的排序功能
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
            ->where('g.status = 1')    //二级分类为1才显示出来---后台下架(status为0)二级分类时小程序中对应删除其二级分类(即下架后的不显示)
            ->where('g.status != 0')   //后台下架二级分类时小程序中对应删除其二级分类(即下架后的不显示)  
            ->order('g.id asc')   //后加的排序功能
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
       
       for ($n=0; $n < count($arr3) ; $n++) { 
           
         array_multisort(array_column($arr3[$n],'sort'),$arr3[$n]);
         
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
        
    
        // dump($arr4['banner']);
        
        // dump($arr4);
        // echo '<hr />';
        // dump($arr3);
        // echo '<hr />';
        // dump($arr2);
        // exit();
        $class_data=[$goods_category_list,$arr4];
        if(!$goods_category_list || !$arr4)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' , $class_data);
    }


    /**
     *分类页点击二级分类时展示该分类下的所有商品 
     */

    public function classify2Goods()  
    {
        $item_code_classify = $_GET['item_code_classify'];

        // $uid = $_GET['uid'];
        // $project_id = $_GET['project_id'];
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        $index = $_GET['start'];
        $n = $_GET['count'];

        // if (!isset($index) || !isset($n) || !isset($uid) || !isset($project_id)) {

        //     return show('500' ,'','系统异常');
        // }


        $classify2_id = !empty($_GET['classify2_id']) ? $_GET['classify2_id'] : '';
        // $keyword = !empty($_POST['keyword']) ? $_POST['keyword'] : '';

        if(!($uid && $project_id))//登陆前条件
        {
            //商品条件
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            // $goods_where['g.is_boutique']=0; //无需关心是否为精品    
            $goods_where['c.goods_category_default']=0;

            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=0;  //登录后显示
            

        }
        else//登陆后条件
        {
            //商品条件
            $goods_where['g.status']=1;
            $goods_where['c.status']=1;  //二级分类为上架状态
            // $goods_where['g.is_boutique']=1; //无需关心是否为精品
            $goods_where['c.goods_category_default']=1;
            $goods_where['c.cate_affiliation']=$project_id;//项目id

            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=1;  //登录后显示
            $project_where['c.cate_affiliation']=$project_id;//项目id   
            
        }

        if (empty($item_code_classify)) {  //默认显示点击某二级分类时该二级分类下的商品数据
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit,g.sort')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->where($goods_where)->where('c.tid != 0')->order('g.sort asc,g.id desc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }else if ($item_code_classify == 1) { //为1时按销量倒序查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->where($goods_where)->where('c.tid != 0')->order('g.goods_volume', 'desc')->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)
        }else if ($item_code_classify == 2) { //为2时按价格从低到高查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->where($goods_where)->where('c.tid != 0')->order('g.goods_price', 'asc')->limit($index, $n)->select();  //  //->where($goods_where)  ->limit($index, $n)
            // $item= $classify2_id;
        }else if ($item_code_classify == 3){ //为3时按价格从高到低查询商品
            //被搜索的商品数据
            $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->where($goods_where)->where('c.tid != 0')->order('g.goods_price', 'desc')->limit($index, $n)->select();  //  //->where($goods_where)  ->limit($index, $n)
            // $item= $classify2_id;
        }

        $project_status = db('project')->alias('p')->field('p.show_price')->join('goods_category c','p.id=c.cate_affiliation','left')->where($project_where)->where('c.tid != 0')->find();


        //  $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content,g.goods_volume')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->order('g.goods_price', 'asc')->select(); 

        // dump($goods_list);
        // //被搜索的商品数据
        // $goods_list=db('goods')->alias('g')->field('g.goods_name,g.id,g.goods_price,g.goods_img,g.goods_content')->join('goods_category c','g.category_id=c.id','left')->where('c.id', $classify2_id)->limit($index, $n)->select();  //->where($goods_where)  ->limit($index, $n)


        // dump($goods_list);  
        // exit();

        if(!$goods_list)
        {
            return show('500' ,$goods_list,'系统异常');
        }    
        return show('200' ,$goods_list,$project_status);
    }

    

    // /**
    //  * 商品二级分类列表
    //  */

    // public function classify_2()   //$uid=false, $project_id=false
    // {
    //     $uid = $_GET['uid'];
    //     $project_id = $_GET['project_id'];

    //     if (!isset($uid) || !isset($project_id) || !isset($_GET['classify_id'])) {
    //         return show('500' ,'','系统异常');
    //     }
        
    //     // if (!isset($_GET['classify_id'])) {
    //     //     return show('500' ,'','系统异常');
    //     // }

    //     if(!($uid && $project_id))//登陆前条件
    //     {
    //         //分类条件
    //         $where['g.status']=1;
    //         $where['g.tid']=$tid;      //$where['g.tid']=0;               
    //         $where['g.goods_category_default']=0;  
    //         $where['g.cate_affiliation']=0;//项目id

    //         //$where['g.id']=$tid;
    //     }
    //     else//登陆后条件
    //     {
    //         //分类条件
    //         $where['g.status']=1;
    //         $where['g.tid']=$tid;//分类上级id
    //         $where['g.goods_category_default']=1;//找登陆后显示的数据
    //         $where['g.cate_affiliation']=$project_id;//项目id

    //         //$where['g.id']=$tid;
    //     }

    //     //分类列表
    //     $goods_category_list=db('goods_category')
    //     ->alias('g')   
    //     ->field('g.*,p.path')
    //     ->join('picture p','g.cate_thumb=p.id','left')
    //     ->where($where)
    //     ->select();

    //     // dump($goods_category_list);
    //     // exit();

    //     if(!$goods_category_list)
    //     {
    //         return show('500' ,'','系统异常');
    //     }
    //     return show('200' ,$goods_category_list);
    // }




    /**
     * 商品详情
     */
    
    public function goodsDetails()   //$uid=false, $project_id=false
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : false;
        $project_id = isset($_GET['project_id']) ? $_GET['project_id'] : false;

        $goods_id = $_GET['goods_id'];

        // if (!isset($uid) || !isset($project_id) || !isset($goods_id)) {
        //     return show('500' ,'','系统异常');
        // }
        if (!isset($goods_id)) {
            return show('500' ,'','系统异常');
        }
          
        $data = [];
        if(!($uid && $project_id))//登陆前条件
        {
            //商品条件
            $goods_where['g.status']=1;  //1为上架商品
            $goods_where['c.status']=1;  //1为上架分类
            // $goods_where['g.is_boutique']=0;  //0为精品,1为正常(登录后)
            $goods_where['c.goods_category_default']=0;  //0为登录前显示(查看商品详情页本无需再判断登录前后,因为呈现商品列表时已作判断, 但考虑到轮播图和广告位可添加商品详情链接, 所以要加)
            // echo 11;
            $goods_where['g.id']=$goods_id;


            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=0;  //登录后显示
            
        }         
        else//登陆后条件  
        {
            //商品条件
            $goods_where['g.status']=1;  //1为上架商品
            $goods_where['c.status']=1;  //1为上架分类
            // $goods_where['g.is_boutique']=1; //0为精品,1为正常
            $goods_where['c.goods_category_default']=1;  //1为登录后显示(查看商品详情页本无需再判断登录前后,因为呈现商品列表时已作判断, 但考虑到轮播图和广告位可添加商品详情链接, 所以要加)       
            $goods_where['c.cate_affiliation']=$project_id;  //查看商品详情页本无需判断项目归属,因为呈现商品列表时已作判断, 但考虑到轮播图和广告位可添加商品详情链接, 所以为避免后台如果添加登录前的商品链接在登录后也能买的问题, 得加此判断
            // echo 22;
            $goods_where['g.id']=$goods_id;


            $project_where['c.status']=1;  //二级分类为上架状态
            $project_where['c.goods_category_default']=1;  //登录后显示
            $project_where['c.cate_affiliation']=$project_id;//项目id  
        }

        //商品详细数据(总) 只有一条数据
        $goods_details=db('goods')->alias('g')->field('g.goods_name, g.id, g.goods_price, g.goods_img, g.goods_content, g.goods_erp, g.goods_clp, g.goods_description, g.img_ids, g.goods_repertory, g.goods_volume,g.goods_unit')->join('goods_category c','g.category_id=c.id','left')->where($goods_where)->where('c.tid != 0')->find(); //->where($goods_where)   
        
        if ($goods_details['img_ids']) {        
            $arr = explode(',', $goods_details['img_ids']);
            // $arr1 =[];

            foreach ($arr as $value) {
                $img_ids=db('picture')->field('path')->where($value.'=id')->where('status = 1')->select();
                // $img_ids=db('picture')->alias('p')->field('p.path')->where($value.'=p.id')->where('p.status = 1')->select();
                // $img_ids=db('goods')->alias('g')->field('p.path')->join('picture p','g.'.$value.'=p.id','left')->where($goods_where)->select();
                $arr1[] = $img_ids;
                // dump($img_ids);
                // array_push($arr1, $img_ids) 
            }

            if(!$arr1)
            {
                $arr1[]="";
            }
            $data = [$goods_details, $arr1];
        }else{
            $data = [$goods_details, ""];
        }


        $project_status = db('project')->alias('p')->field('p.show_price')->join('goods_category c','p.id=c.cate_affiliation','left')->where($project_where)->where('c.tid != 0')->find();
        
        
        
        //商品详细数据(商品多图)
        //$img_ids=db('goods')->alias('g')->field('g.img_ids, p.path')->join('picture p','g.category_id=c.id','left')->where($goods_where)->select(); //->where($goods_where)
        
        
        // dump($arr1);
        // exit();

        if(!$data)
        {
            return show('500' ,'','系统异常');
        }
        return show('200' ,$data,$project_status);  
    }


    
  
}
  