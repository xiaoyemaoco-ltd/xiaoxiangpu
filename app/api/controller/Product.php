<?php
namespace app\api\controller;
use app\admin\model\Category;
use app\BaseController;
//use app\common\SearchBuilders\ProductSearchBuilder;
//use app\Exceptions\InvalidRequestException;
//use Illuminate\Http\Request;
//use Illuminate\Pagination\LengthAwarePaginator;
use think\facade\Db;
use app\Request;
use think\facade\Query;
use app\common\model\Product as PModel;
class Product extends BaseController{
    //产品列表
    public function productlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $type = $request->post('type');
        $where = "status=1";
        $limit = "";
        switch ($type){
            case 'ms':
                $where.=" and is_rush=1";
                break;
            case 'jmj':
                $where.=" and category_id=2";
                break;
            case 'xjtcp':
                $where.=" and category_id=3";
                break;
        }
        $productsfild = 'id,name,images,price,discount_price,shop_id,category_id,sales';
        $list = (new PModel)::with('shops')->where($where)
            ->field($productsfild)
            -> order('createtime desc')
            ->  select()  -> toArray();
        return apiBack('success', '成功', '10000', $list);
    }

    //商品详情
    public function productdetails(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $productid = $request->post('id');
        $details = (new PModel)::with(['shops'=>function(Query $query){
            $query -> field('shop_id,title');
        }])
            -> fieldRaw('id,shop_id')
            ->where('id',$productid)
//            -> field('id,name,images,price,discount_price,shop_id,sales,rating,review,introduce,product_spec_info,parea')
            -> select() -> toArray();
        dump($details);die;
        $details['product_spec_info'] = json_decode($details['product_spec_info'],true);
        return apiBack('success', '成功', '10000', $details);
    }

    //产品评价
    public function productevaluation(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $productid = $request->post('uid');
        $productid = $request->post('id');
        $details = (new PModel)::with('skus')->where('id',$productid)
            -> field('id,name,images,price,discount_price,shop_id,sales,rating,review,introduce,product_spec_info,parea')
            -> find() -> toArray();
        $details['product_spec_info'] = json_decode($details['product_spec_info'],true);
        return apiBack('success', '成功', '10000', $details);
    }

}
