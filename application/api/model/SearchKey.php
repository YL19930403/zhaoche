<?php



namespace app\api\model{

    class SearchKey{
        //搜索条件
        public $searchKey = null ;

        //排序字段
        public $sort = null  ;

        //排序方式 asc,desc
        public $order = null;

        //页数 默认0
        public $page = 0;

        //每页笔数 默认50
        public $perPage = 50;

        //开始页数
        public $startNum = 0 ;

        //计算开始页数
        public function setStartNum($page,$perPage){
            $this->page = $page;
            $this->perPage = $perPage;
            $this->startNum = $page * $perPage;
        }

        //时间范围
        public  $time = null;

        //区域
        public $region = null;


        //状态
        public $status = null;

        //编码
        public $id= null;

        //经度
        public $longitude = null;

        //纬度
        public $latitude = null;


    }
}
