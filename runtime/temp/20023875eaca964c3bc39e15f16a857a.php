<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:36:"./template/mobile/new2/user/reg.html";i:1512988700;s:41:"./template/mobile/new2/public/header.html";i:1512896668;s:45:"./template/mobile/new2/public/header_nav.html";i:1506391060;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>注册--<?php echo $tpshop_config['shop_info_store_title']; ?></title>
    <link rel="stylesheet" href="__STATIC__/css/style.css">
    <link rel="stylesheet" type="text/css" href="__STATIC__/css/iconfont.css"/>
    <script src="__STATIC__/js/jquery-3.1.1.min.js" type="text/javascript" charset="utf-8"></script>
    <!--<script src="__STATIC__/js/zepto-1.2.0-min.js" type="text/javascript" charset="utf-8"></script>-->
    <script src="__STATIC__/js/style.js" type="text/javascript" charset="utf-8"></script>
    <script src="__STATIC__/js/mobile-util.js" type="text/javascript" charset="utf-8"></script>
    <script src="__PUBLIC__/js/global.js"></script>
    <script src="__STATIC__/js/layer.js"  type="text/javascript" ></script>
    <script src="__STATIC__/js/swipeSlide.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="__STATIC__/js//md5.js"></script>

</head>
<body class="g4">

<div class="classreturn loginsignup ">
    <div class="content">
        <div class="ds-in-bl return">
            <a href="javascript:history.back(-1)"><img src="__STATIC__/images/return.png" alt="返回"></a>
        </div>
        <div class="ds-in-bl search center">
            <span>注册</span>
        </div>
        <div class="ds-in-bl menu">
            <a href="javascript:void(0);"><img src="__STATIC__/images/class1.png" alt="菜单"></a>
        </div>
    </div>
</div>
<div class="flool tpnavf">
    <div class="footer">
        <ul>
            <li>
                <a class="yello" href="<?php echo U('Index/index'); ?>">
                    <div class="icon">
                        <i class="icon-shouye iconfont"></i>
                        <p>首页</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('Goods/categoryList'); ?>">
                    <div class="icon">
                        <i class="icon-fenlei iconfont"></i>
                        <p>分类</p>
                    </div>
                </a>
            </li>
            <li>
                <!--<a href="shopcar.html">-->
                <a href="<?php echo U('Cart/index'); ?>">
                    <div class="icon">
                        <i class="icon-gouwuche iconfont"></i>
                        <p>购物车</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="<?php echo U('User/index'); ?>">
                    <div class="icon">
                        <i class="icon-wode iconfont"></i>
                        <p>我的</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>
<style>
    #verify_code_img{
        padding: .55467rem .21333rem;
        width: 4.6rem;
        height: 2.9rem;
        color: white;
        border-radius: .128rem;
    }
</style>
<!--注册表单-s-->
<div class="loginsingup-input singupphone">
    <form action="" method="post" id="regFrom" >
        <div class="content30">
            <div class="lsu boo wicheck">
                <input type="text" name="nickname" id="nickname" value="" placeholder="请输入真实姓名"  class="c-form-txt-normal" onBlur="checkNickname(this.value);">
                <span id="nickname_notice"></span>
            </div>
            <div class="lsu boo wicheck">
                <input type="text" name="mobile" id="mobile" value="" placeholder="请输入手机号"  class="c-form-txt-normal" onBlur="checkMobilePhone(this.value);">
                <span id="mobile_phone_notice"></span>
            </div>
            <input type="hidden" name="md5pwd" id="md5pwd" value="" >
            <div class="lsu boo wicheck">
                <input type="password" name="password" id="password" value="" placeholder="请设置6-20位登录密码" class="c-form-txt-normal" onBlur="check_password(this.value);">
                <span id="password_notice"></span>
            </div>
            <input type="hidden" name="md5pwd2" id="md5pwd2" value="" >
            <div class="lsu boo wicheck">
                <input type="password" id="password2" name="password2" value="" placeholder="确认密码" onBlur="check_confirm_password(this.value);">
                <span id="confirm_password_notice"></span>
            </div>

            <?php if($regis_sms_enable == 1): ?>
                <div class="lsu boo zc_se">
                    <input type="text" id="mobile_code" value="" name="mobile_code" placeholder="请输入短信验证码" >
                    <a rel="mobile" onClick="sendcode(this)">获取短信验证码</a>
                </div>
            <?php endif; ?>


            <div class="lsu boo wicheck">
                <input type="text" id="id_card" name="id_card" value="" placeholder="请输入身份证号码" onBlur="check_id_card(this.value);">
                <span id="confirm_id_card"></span>
            </div>

            <?php if($a != null): ?>
                <div class="lsu boo wicheck">
                    <input type="text" name="invite" id="invite1" readOnly value="<?php echo $a; ?>"  placeholder="请输入推荐人手机号"  class="c-form-txt-normal" onBlur="checkRecomMobilePhone(this.value);">
                    <span id="invite_phone_notice"></span>
                </div>
                <?php else: ?>
                <div class="lsu boo wicheck">
                    <input type="text" name="invite" id="invite2" value="" placeholder="请输入推荐人手机号"  class="c-form-txt-normal" onBlur="checkRecomMobilePhone(this.value);">
                    <span id="invite_phone"></span>
                </div>
            <?php endif; ?>
            <div class="lsu submit">
                <input type="button" name=""   id="mobile_register" onclick="checkSubmit()" value="注册"/>
            </div>
            <div class="signup-find">
            </div>
        </div>
    </form>
</div>
<!--注册表单-s-->
<script type="text/javascript">
    //提交表单
    function checkSubmit()
    {
        $.ajax({
            type:'POST',
            url:"/index.php?m=Mobile&c=User&a=reg",
            dataType:'JSON',
            data:$('#regFrom').serialize(),
            success:function(data){
                console.log(data);
                if(data.status == 1){
                    if(data.type == 1){
                        location.href='https://fir.im/AichesongNC';
                    }else if(data.type == 2){
                        location.href='http://mobile.baidu.com/item?docid=22781568&source=pc';
                    }
                }else{
                    showErrorMsg(data.msg);
                }
            }
        })
    }
    var flag = false;

    //用户名验证
    function  checkNickname(nickname) {
        if(nickname ==''){
            showErrorMsg('用户名不能为空');
            flag = false;
        }else if(checkname(nickname)){
            flag = true;
        }else{
            showErrorMsg('* 用户名格式不正确');
            flag = false;
        }
    }
    //手机验证
    function checkMobilePhone(mobile){
        if(mobile == ''){
            showErrorMsg('手机不能空');
            flag = false;
        }else if(checkMobile(mobile)){ //判断手机格式
            $.ajax({
                type : "GET",
                url:"/index.php?m=Home&c=Api&a=issetMobile",//+tab,
//			url:"<?php echo U('Mobile/User/comment',array('status'=>$_GET['status']),''); ?>/is_ajax/1/p/"+page,//+tab,
                data :{mobile:mobile},// 你的formid 搜索表单 序列化提交
                success: function(data)
                {
                    if(data == '0')
                    {
                        flag = true;
                    }else{
                        showErrorMsg('* 手机号已存在');
                        flag = false;
                    }
                }
            });
        }else{
            showErrorMsg('* 手机号码格式不正确');
            flag = false;
        }
    }



    function checkRecomMobilePhone(mobile){
        if(mobile == ''){
            showErrorMsg('推荐人不能空');
            flag = false;
        }else if(checkMobile(mobile)){ //判断手机格式
            $.ajax({
                type : "GET",
                url:"/index.php?m=Home&c=Api&a=issetMobile",//+tab,
//			url:"<?php echo U('Mobile/User/comment',array('status'=>$_GET['status']),''); ?>/is_ajax/1/p/"+page,//+tab,
                data :{mobile:mobile},// 你的formid 搜索表单 序列化提交
                success: function(data)
                {
                    if(data == '0')
                    {
                        showErrorMsg('* 推荐人手机号不存在');
                        flag = false;
                    }else{
                        flag = true;
                    }
                }
            });
        }else{
            showErrorMsg('* 手机号码格式不正确');
            flag = false;
        }
    }

    //身份证
    function   check_id_card(id_card){
        if(id_card == ''){
            showErrorMsg('身份证不能空');
        }else if(checkCar(id_card)){
            $.ajax({
                type : "GET",
                url:"/index.php?m=Home&c=Api&a=issetCard",//+tab,
                data :{id_card:id_card},// 你的formid 搜索表单 序列化提交
                success: function(data)
                {
                    if(data == '0')
                    {
                        flag = true;
                    }else{
                        showErrorMsg('身份证号已存在');
                        flag = false;
                    }
                }
            });

        }else{
            showErrorMsg('身份证格式不正确');
        }
    }

    /**
     * 用户名格式判断
     * @param idcar
     * @returns {boolean}
     */
    function checkname(name) {
        var  reg =/^[\u4e00-\u9fa5]+$/;
        if(reg.test(name)){
            return true;
        }else{
            return false;
        };
    }

    /**
     * 身份证格式判断
     * @param idcar
     * @returns {boolean}
     */

    function checkCar(idcar){
        var val =  $.trim(idcar);
        if(val.length == 15){
            var reg=/^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}[0-9Xx]$/;
            if(reg.test(val)){
                return true;
            }else{
                return false;
            }
        }else if(val.length == 18){
            var reg=/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/;
            if(reg.test(val)){
                return true;
            }else{
                return false;
            }
        }
    }

    //密码
    function check_password(password) {
        var password = $.trim(password);
        if(password == ''){
            showErrorMsg("*登录密码不能包含空格");
            flag = false;
        }else if (password.length < 6) {
            showErrorMsg('*登录密码不能少于 6 个字符。');
            flag = false;
        }else{
            var tp='TPSHOP';
            $.trim($('#md5pwd').val(hex_md5(tp+password)));
            flag = true;
        }
    }


    //验证确认密码
    function check_confirm_password(confirm_password) {
        var password1 = $.trim($('#password').val());
        var password2 = $.trim(confirm_password);
        if (password1 == '') {
            showErrorMsg("*确认密码不能包含空格");
            flag = false;
        }
        if (password2.length < 6) {
            showErrorMsg('*登录密码不能少于 6 个字符。');
            flag = false;
        }
        if (password2 != password1) {
            showErrorMsg('*两次密码不一致');
            flag = false;
        }else{
            var tp='TPSHOP';
            $.trim($('#md5pwd2').val(hex_md5(tp+password2)));
            flag = true;
        }
    }


    function countdown(obj) {
        var s = 60;
        //改变按钮状态
        obj.disabled = true;
        callback();
        //循环定时器
        var T = window.setInterval(callback,1000);
        function callback()
        {
            if(s <= 0){
                //移除定时器
                window.clearInterval(T);
                obj.disabled=false;
                obj.innerHTML='获取短信验证码';
            }else{
                if(s<=10){
                    obj.innerHTML = '0'+ --s + '秒后再获取';
                }else{
                    obj.innerHTML = --s+ '秒后再获取';
                }
            }
        }
    }

    //发送短信验证码
    function sendcode(obj){
        if(flag){
            $.ajax({
                url:'/index.php?m=Home&c=Api&a=send_code&t='+Math.random() ,
                type:'post',
                dataType:'json',
                data:{type:$(obj).attr('rel'),send:$.trim($('#mobile').val()), scene:1},
                success:function(res){
                    if(res.status==1){
                        //成功
                        countdown(obj)
                        showErrorMsg(res.msg);
                    }else{
                        //失败
                        showErrorMsg(res.msg);
                    }
                }
            })
        }else{
            showErrorMsg('请输入手机号！');
        }
    }


    /**
     * 提示弹窗
     * */
    function showErrorMsg(msg){
        layer.open({content:msg,time:2});
    }
    // 普通 图形验证码
    //    function verify(){
    //        $('#verify_code_img').attr('src','/index.php?m=Home&c=User&a=verify&type=user_reg&r='+Math.random());
    //    }
</script>
</body>
</html>