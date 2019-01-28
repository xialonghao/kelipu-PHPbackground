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

namespace app\admin\validate;

/**
 * 商品验证器
 */
class Goods extends AdminBase
{

    // 验证规则
    protected $rule =   [
    // 'cate_id'          => 'require',
        'goods_name'       => 'require|max:100',
        'goods_unit'       => 'require|max:10',
        'goods_price'       => 'require|number|max:10',
        'goods_market_price' => 'length:0,10',
        'goods_erp'       => 'require|max:10',
        'goods_clp'       => 'require|max:10',
        'goods_img'       => 'require',
        'outer_goods_id' =>'max:10',
        'goods_description'=> 'max:100',
        'goods_content' => 'require',
        'goods_repertory'=>'require|number|max:10'
      //  'goods_more_img' => 'require',

    ];

    // 验证提示
    protected $message  =   [
        'goods_name.require'         => '商品名称名称不能为空',
        'goods_unit.require'       => '商品单位不能为空',
        'goods_price.require'       => '商品出售价格不能为空',
        'goods_price.number'       => '商品出售价格为数字',
         'goods_market_price.length' => '商品市场价最大值10位',
        'goods_repertory.require'       => '库存不能为空',
        'goods_repertory.number'       => '库存请添写数字',
        'goods_repertory.max'       => '库存最大值10位',
        'goods_erp.require'       => 'ERP商品号不能为空',
        'goods_clp.require'       => '外部商品号不能为空',
        'goods_img.require'       => '商品图片url不能为空',
       'goods_description.max'=> '商品简介请填写少于100位',
           'goods_name.max'         => '商品名称名称请填写少于100位',
        'goods_content.require' => '商品内容不能为空',
        'outer_goods_id.max'       => '外部商品id请填写少于10位',
        'goods_unit.max'   =>'商品单位请填写少于10位',
        'goods_price.max'  =>'商品出售价格最大值10位',
        'goods_erp.max'    =>'ERP商品号请填写少于10位',
        'goods_clp.max'    =>'外部商品号请填写少于10位',
    ];

    // 应用场景
    protected $scene = [
        'edit'  =>  ['goods_name','goods_unit','goods_price','goods_repertory','goods_erp','goods_clp','goods_img','goods_content','goods_description','outer_goods_id'],

    ];
}
