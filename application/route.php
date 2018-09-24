<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//
//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//    //'goodsInfo/[:id]' => ['Home/Goods/goodsInfo',['method' => 'get', 'ext' => 'html'],'cache'=>3600]
//    //Home/Goods/goodsInfo/id/104.html
//];
use think\Route;
// 注册路由到index模块的News控制器的read操作
//Route::get('goodsInfo/:id','api/goods/goodsInfo',['cache'=>['Home/Goods/goodsInfo',300]]);// 访问方式 http://www.tpshop2.0.com/goodsInfo/77.html

Route::post('api/:version/user/regist','api/:version.User/regist');// 访问方式 http://www.tpshop2.0.com/goodsInfo/77.html
Route::post('api/:version/user/login','api/:version.User/login');
Route::post('api/:version/user/login2','api/:version.User/login2');
Route::post('api/:version/user/password/reset','api/:version.User/reset');
Route::post('api/:version/user/feedback','api/:version.FeedBack/feedback');   //消息反馈
Route::get('api/:version/user/:user_id/getuserinfo','api/:version.User/getUserInfo');    //获取用户信息接口
Route::put('api/:version/user/:user_id/updatesex','api/:version.User/updateSex');    //修改用户性别
Route::put('api/:version/user/:user_id/updateregion','api/:version.User/updateRegion');    //修改用户地区
Route::get('api/:version/user/:user_id/subordinates','api/:version.User/getSubordinates');    //我的下级
Route::get('api/:version/user/agreements','api/:version.User/agreements');    //用户协议
Route::get('api/:version/user/aboutus','api/:version.User/aboutus');    //关于我们
Route::put('api/:version/user/:user_id/quit','api/:version.User/quit');    //退出登录接口


Route::get('api/:version/ad/getlist','api/:version.Ad/getlist');

//-----------------
Route::get('api/:version/ad/getadlist','api/:version.Ad/getadlist');
Route::get('api/:version/ad/getad','api/:version.Ad/getad');   //获取启动页广告
//-------------------
Route::get('api/:version/user/news/getnews','api/:version.News/getnews');   //查询我的消息列表
Route::get('api/:version/user/news/:news_id/newsdetail','api/:version.News/newsdetail');   // 消息详情
Route::post('api/:version/user/recomregist','api/:version.User/recomregist');   //推荐注册



Route::post('api/:version/user/:user_id/uploadimg','api/:version.User/uploadimg');



Route::get('api/:version/order/getnews','api/:version.Order/getnews');   //获取最新快报接口
Route::get('api/:version/good/gethots','api/:version.Good/gethots');   //获取热门推荐接口


Route::get('api/:version/good/:good_id/getdetails','api/:version.Good/getdetails');   //获取商品详情接口


Route::get('api/:version/attr/:good_id/getattr','api/:version.Attr/getattr');   //获取商品属性接口

Route::get('api/:version/region/getlist','api/:version.Region/getRegionList');   //获取地区列表
Route::get('api/:version/version/:type/checkversion','api/:version.Version/checkVersion');   //版本检测接口

//汽车列表  接口
Route::get('api/:version/good/getrecomlist','api/:version.Good/getRecomList');   //获取精品推荐列表接口


//新车  页面接口
Route::get('api/:version/good/getallist','api/:version.Good/getAllList');   //查询所有商品列表
Route::get('api/:version/category/getallcat','api/:version.Category/getAllCats');   //查询所有分类接口
Route::get('api/:version/good/getdatelist','api/:version.Good/getDateList');   //查询按时间分类接口



//任务接口
Route::get('api/:version/task/:user_id/gettasklist','api/:version.Task/getTaskList');   //查询任务列表
Route::get('api/:version/user/:user_id/task/:task_id/getdetail','api/:version.Task/getDetail');   //查询任务详情


Route::get('api/:version/order/:user_id/getlist','api/:version.Order/getOrderList');   //获取用户订单列表接口
Route::get('api/:version/order/:rec_id/getorderdetail','api/:version.Order/getOrderDetail');   //查询订单详情接口
Route::post('api/:version/user/:user_id/good/:good_id/order/submitorder','api/:version.Order/submitOrder');   //提交订单接口
Route::post('api/:version/user/:user_id/material','api/:version.User/submitMaterial');   //提交用户资料接口
Route::put('api/:version/order/:order_id/cancelorder','api/:version.Order/cancelOrder');   //取消订单

Route::post('api/:version/message/sendsms','api/:version.SendSms/sendCode');    //发送验证码

//汽车整车、产品品牌     全套接口
Route::get('api/:version/nav/:nav_id/home/gethomelist','api/:version.HomeList/getHomeList');

//搜索接口
Route::get('api/:version/home/search','api/:version.HomeList/searchList');


//-----------------------版本2-------------------------
Route::get('api/:version/goodspayment/:good_id/ratio','api/:version.GoodsPayment/getPayRatio');   //查询首付比例接口
Route::get('api/:version/goodspayinfo/:good_id/ration/:ration_id','api/:version.GoodsPayInfo/getPayInfo');   //查询商品付款信息
Route::post('api/:version/Address/:user_id/addaddress','api/:version.Address/add_address');   //添加收货地址接口
Route::get('api/:version/Address/:user_id/addresslist','api/:version.Address/getaddrlist');   //添加收货地址接口
Route::put('api/:version/Address/:address_id/deladress','api/:version.Address/deladress');   //删除收货地址接口
Route::put('api/:version/Address/:address_id/defaultadress','api/:version.Address/defaultadress');   //设为默认收货地址
Route::post('api/:version/Address/:address_id/updateadress','api/:version.Address/updateadress');   //修改收货地址
Route::get('api/:version/GoodCollect/:good_id/user/:user_id/getcollect','api/:version.GoodCollect/getcollect');   //查询收藏接口
Route::put('api/:version/GoodCollect/:good_id/user/:user_id/collect','api/:version.GoodCollect/collect');   //收藏和取消收藏接口
Route::get('api/:version/GoodCollect/user/:user_id/collectlist','api/:version.GoodCollect/getcollectlist');   //查询我的收藏列表接口
Route::get('api/:version/SearchWord/hotsearch','api/:version.SearchWord/gethotsearch');   //热门搜索
// Route::get('api/:version/GoodVisit/user/:user_id/looklist','api/:version.GoodVisit/getlooklist');   //查询我的浏览历史
Route::put('api/:version/User/signame','api/:version.User/signame');   //用户签名

Route::get('api/:version/Article/articlelist','api/:version.Article/getlist');   //资讯