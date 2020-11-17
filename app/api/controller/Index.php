<?php

namespace app\api\controller;


use app\admin\model\User;
use app\BaseController;
use app\common\model\Adv;
use app\common\model\Config;
use app\Request;
use app\common\model\Product;
class Index extends BaseController
{
    public function userRegister (Request $request)
    {
        if (!$request->isPost()) return apiBack('ok', '请求方式错误', '10004');
    }

    /**
     * 首页配置
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function config (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');

        //轮播图
        $swiper = Adv::where('title', '首页轮播')->field('advurl')->select()->toArray();
        $swiper = array_column($swiper, 'advurl');

        $productsfild = 'id,name,images,price,discount_price,shop_id,category_id,sales';
        //秒杀商品
        $skiimiao = (new Product)::where(['status'=>1,'is_rush'=>1])
            -> field($productsfild)
            -> order('createtime desc')
            -> limit(0,3)
            -> select() -> toArray();
        //店长推荐chanpin
        $shopproducts =  (new Product)::where(['status'=>1,'is_recommend'=>1,'category_id'=>2])
            -> field($productsfild)
            -> order('createtime desc')
            -> limit(0,6)
            -> select() -> toArray();
        //新疆特产
        $xjtcp =  (new Product)::where(['status'=>1,'category_id'=>3])
            -> field($productsfild)
            -> order('createtime desc')
            -> limit(0,6)
            -> select() -> toArray();
        $roll = ['xxx用户购买两份椒麻鸡', '王小二购买新疆特产一份', '张三购买椒麻鸡套餐两份', '李四购买椒麻鸡两份'];
        $data = [
            'secskill' => ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end'))] ,
            'swiper' => $swiper,
            'skillmiao' => ['type'=>'ms','data'=>$skiimiao],//$skiimiao,
            'dztj' => ['type'=>'jmj','data'=>$shopproducts],
            'xjtcp' => ['type'=>'xjtcp','data'=>$xjtcp],
            'roll' => $roll,
        ];
        return apiBack('success', '成功', '10000', $data);
    }

    /**
     * 签到
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sign (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $openId = $request->post('openId');
        $userModel = new User();
        $user = $userModel->where('openid', $openId)->find();
        $end_time = strtotime(date("Y-m-d",time())) + 60*60*24;
        $sign_time = $end_time - $user->last_sign_time;
        if ($sign_time < 86400) return  apiBack('fail', '今日已签到，请明天再来', '10004');
        $config = new Config();
        $score = $config->where('name', 'sign_score')->value('value');
        $user->score += $score;
        $user->last_sign_time = time();
        $user->save();
        return apiBack('success', '签到成功，获得' . $score . '积分', '10000');
    }



}
