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
 * 商品分类验证器
 */
class goodsCategory extends AdminBase
{

    // 验证规则
    protected $rule =   [
        // 'cate_id'          => 'require',
        'cate_name'       => 'require|max:50',
         // 'cate_thumb'   => 'require',
    ];

    // 验证提示
    protected $message  =   [
        'cate_name.require'         => '分类名称不能为空',
        'cate_name.max'         => '分类名称最大长度为50位',
        // 'cate_thumb.require'      => '不能为空',
        // 'category_id.require'  => '文章分类必须选择',
    ];

    // 应用场景
    protected $scene = [
        'edit'  =>  ['cate_name'],
        'editS'  =>  ['cate_name']
    ];
}
