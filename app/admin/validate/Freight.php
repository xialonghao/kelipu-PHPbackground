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
 * 运费配置验证器
 */
class Freight extends AdminBase
{

    // 验证规则
    protected $rule =   [
        // 'cate_id'          => 'require',
        'project_id'       => 'require',
        'price'       => 'require|number',
    ];

    // 验证提示
    protected $message  =   [
        'project_id.require'         => '项目必须选择',
        'price.require'       => '运费不能为空',
        'price.require'       => '运费必须为数值',
    ];

    // 应用场景
    protected $scene = [
        'edit'  =>  ['project_id','price'],

    ];
}
