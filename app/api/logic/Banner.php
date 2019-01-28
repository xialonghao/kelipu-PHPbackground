<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+s
namespace app\api\logic;

use app\common\logic\Article as CommonArticle;

/**
 * 轮播图逻辑
 */
class Banner extends ApiBase
{
    
  public static $commonBannerLogic = null;

     /**
     * 基类初始化
     */
  public function __construct()
    {
        // 执行父类构造方法
        parent::__construct();
        
        empty(static::$commonBannerLogic) && static::$commonBannerLogic = get_sington_object('Banner', CommonArticle::class);
    }

   /**
   * 获取轮播图列表
   */
  public function getBannerList()
  {
    return static::$commonBannerLogic->getBannerList();
  }
}