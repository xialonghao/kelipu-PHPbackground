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
 * 积分卡验证器
 */
class Card extends AdminBase
{

    // 验证规则
    protected $rule =   [
        'gift_number'       => 'require|max:20',
        // 'gift_number'       => 'require',
        'gift_id'       => 'require|max:20',
        'exchange' => 'require|max:5',
        'open_time'       => 'require',
        'tie_time'       => 'require',

    ];

    // 验证提示
    protected $message  =   [
        'gift_number.require'         => '积分卡号不能为空',
        'gift_number.max'         => '积分卡号最大长度20位',
        'gift_id.require'       => '批次号不能为空',
        'gift_id.max'       => '批次号最大长度20位',
        'exchange.require' => '兑换规则不能为空',
        'exchange.require' => '兑换规则最大长度5位',
        'open_time.require'       => '开始时间不能为空',
        'tie_time.require'       => '结束时间不能为空',
       
    ];

    // 应用场景
    protected $scene = [
        'edit'  =>  ['gift_number','gift_id','exchange','open_time','tie_time'],

    ];
}
