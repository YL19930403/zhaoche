<?php


namespace    app\api\model  ;

use think\Db;
use think\Model;

class  Order  extends  Model {
    protected   $table = 'tp_order' ;

    /*
     *  获取最新订单信息接口
     *  $timeArr   :  当前时间戳往下往上加7天后返回的时间戳数组
     * */
    public  function  getLastsList($timeArr){

       return   Db::table('tp_order')->alias('o')
                                       ->where('o.order_status', 'in', [0, 1])
                                       ->where('o.add_time','between time', [$timeArr['bottom'] , $timeArr['top']])
                                       ->distinct(true)
                                       ->join('tp_users u', 'u.user_id = o.user_id', 'left')
                                       ->join('tp_user_good_image ug', 'u.user_id = ug.user_id', 'left')
                                       ->field('o.order_id, o.order_sn, o.order_status, o.shipping_status, o.pay_status, o.user_id , u.mobile , u.review ,
                                           CASE  ug.view  when   0 then "资料待审核" when 1 then "资料审核通过"  when 2 then "资料审核未通过" else "资料审核被拒" END materials ')
                                       ->limit(ORDER_COUNT)
                                       ->order('o.add_time desc')
                                       ->select();


        //  $sql =  ' select   DISTINCT  o.order_id , o.order_sn, o.order_status, o.shipping_status, o.pay_status ,o.user_id , ug.view , u.mobile  ,
        //           IF(o.order_status, "订单确认成功", "下单成功")  description,  CASE  ug.view  when   0 then "资料审核未通过" when 1 then "资料审核通过"  else "资料审核被拒" END materials 
        //             from  tp_order o  '
        //    .  ' LEFT  JOIN tp_users u on u.user_id = o.user_id  '
        //    .  ' LEFT  JOIN tp_user_good_image ug on u.user_id = ug.user_id  '
        //    .  ' where  o.order_status  in (0,1)  and  o.add_time  between  "'.$timeArr['bottom'].'"  and  "'.$timeArr['top'].'" '
        //        . ' ORDER BY o.add_time  DESC   LIMIT  '.ORDER_COUNT.' '  ;

        // $data = $this->query($sql);
        // return  $data ;
    }

    /*
     *  根据用户编码查询该用户的订单信息
     *   @param    $user_id   int  :  用户编码
     *   @return   Array
     * */
    public  function  getListBy($user_id , $page , $per_page ){
        $page = $page * $per_page ;
        return      Db::table('tp_order')
                    ->alias('o')
                    ->where('user_id'  , $user_id)
                     ->where('order_status', 0)
                    ->where('pay_status', 0)
                    ->join('tp_order_goods og', 'og.order_id = o.order_id')
                    ->join('tp_goods g', 'og.goods_id = g.goods_id', 'left')
                    ->field('o.order_id,og.rec_id , og.goods_id ,g.goods_name  , concat( "'.BASE_PATH.'" ,g.original_img)  original_img  ,  o.order_sn, o.order_status, o.shipping_status,FROM_UNIXTIME( o.add_time , "%Y-%m-%d %H:%i:%s")  add_time , o.goods_price ,
                                o.shipping_price , o.total_amount, og.goods_num')
                    ->limit($page, $per_page)
                    ->order('o.add_time desc')
                    ->select();
    }

    /*
     *  根据订单编码 查询出该订单的详细信息
     *  @param    $rec_id   int   :   订单商品编码
     *  return Array
     * */
    public  function   getDetailBy($rec_id){
            return   Db::table('tp_order_goods')
                            ->alias('og')
                            ->where('rec_id' ,  $rec_id )
                            ->join('tp_order o', 'o.order_id = og.order_id', 'left')
                            ->join('tp_goods g', 'g.goods_id = og.goods_id', 'left')
                            ->join('tp_users u', 'u.user_id = o.user_id', 'left')
                            ->join('tp_region r', 'r.id = o.province','left')
                            ->join('tp_region r2', 'r2.id = o.city','left')
                            ->join('tp_region r3', 'r3.id = o.district','left')
                            ->field('og.rec_id, og.order_id, og.goods_id, og.goods_num,og.goods_price,
                                      FROM_UNIXTIME( o.add_time , "%Y-%m-%d %H:%i:%s")  add_time , o.shipping_price, order_status , 
                                      g.goods_name , u.mobile, u.nickname, r.name  province_name , r2.name  city_name , r3.name district_name , g.commission ')
                            ->find();

    }

    /*
     *  订单入库
     *  @param    $user_id    int  :  用户编码
     *  @param   $content   Array   :  post传入的数据
     *  @return   boolean
     * */
    public  function  saveData($user_id , $contents){

        $orderData = [
            'order_sn'  =>  date('YmdHis').mt_rand(1000,9999) ,
            'user_id'   =>  $user_id ,
            'consignee' => $contents['consignee'] ,
            'mobile'    => $contents['mobile'] ,
            'pay_code'  => $contents['pay_code'] ,
            'goods_price'   => $contents['goods_price'],
            'add_time'   => time(),
        ];

        if($contents['district']){
            $orderData['district'] =  $contents['district'] ;
        }
        if($contents['province']){
            $orderData['province'] =  $contents['province'] ;
        }
        if($contents['city']){
            $orderData['city'] =  $contents['city'] ;
        }



        if($contents['pay_code'] == "cod"){
            $orderData['pay_name'] = "到货付款" ;
        }elseif($contents['pay_code'] == "weixin"){
            $orderData['pay_name'] = "微信支付" ;
        }elseif ($contents['pay_code'] == "alipay"){
            $orderData['pay_name'] = "支付宝支付" ;
        }

//         var_dump($orderData) ; die ;
         return   Db::name('order')->insertGetId($orderData) ;
//           return   $this->save($orderData) ;
    }


}
