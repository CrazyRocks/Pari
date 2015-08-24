<div class="ads-contuiter">


    <div id="header" class="border-bottom inner-main-header">
        <div class="set-width-tool">
            <div class="logo">
                <p><img src="/images/logo.png" /> | 新用户注册</p>
                <p class="websit-name">亚洲现金制在线博彩服务娱乐公司</p>
            </div>
        </div>

    </div>



    <div id="main" class="ac-main">

        <div class="set-width-tool">

            <div class="ads-row tool-title-main">
                <a class="cur" href="javascript:;" >新用户注册</a>
                |
                <a href="javascript:;" >返回登录</a>
            </div>

            <div class="ads-row findedPassword">

                <div class="ads-row i-item-box">

                    <div class="ads-row fixBox">
                        <label>用户名：</label>
                        <div class="inpput-group">
                            <input type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>手机号码：</label>
                        <div class="inpput-group">
                            <input type="text" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>邮箱：</label>
                        <div class="inpput-group">
                            <input type="text" class="form-control" />
                        </div>
                    </div>


                    <div class="ads-row fixBox">
                        <label>验证码：</label>
                        <div class="inpput-group">
                            <input type="text" class="form-control" />
                            <span  class="input-addon" ><a href="javascript:;" >获取验证码</a></span>
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>登录密码：</label>
                        <div class="inpput-group">
                            <input type="password" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>确认密码：</label>
                        <div class="inpput-group">
                            <input type="password" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>&nbsp;</label>
                        <div class="inpput-group">
                            <a class="btn" href="javascript:postdata();">确认</a>
                        </div>
                    </div>

                </div>

                <div class="ads-row i-item-box" style="display: none;">
                    <div class="ads-row fixBox">
                        <label>邮箱：</label>
                        <div class="inpput-group">
                            <span class="text">j****@126.com</span>
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>验证码：</label>
                        <div class="inpput-group">
                            <input type="text" class="form-control" />
                            <span  class="input-addon" ><a href="javascript:;" >获取验证码</a></span>
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>新密码：</label>
                        <div class="inpput-group">
                            <input type="password" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>确认新密码：</label>
                        <div class="inpput-group">
                            <input type="password" class="form-control" />
                        </div>
                    </div>
                    <div class="ads-row fixBox">
                        <label>&nbsp;</label>
                        <div class="inpput-group">
                            <a class="btn" href="javascript:;">确认</a>
                        </div>
                    </div>
                </div>

            </div>




        </div>

    </div>

    <div id="footer" class="" >
        <div class="set-width-tool">
            <img class="logo"  src="/images/parner-logo.jpg" />
            <div class="info">
                <h5>Winner Mayfair limited集团旗下</h5>
                <p>正式取得菲律宾政府卡格杨政府Cagayan leisure and Resort Corporation ( www.firstcagayan.com ) 在线博彩牌照授权。</p>
                <p>关于我们 | 集团简介 | 咨询帮助</p>
            </div>

        </div>
    </div>
</div>



<script type="text/javascript" >

    function postdata(){

        c.API.ajax.request(
                'test.php?id=3'
                , 'GET'
                , {}
                ,'json'
                ,{
                    showIcon : true
                    ,theme:0
                    ,elem :$('#main')
                }
                , function(data){

                    //alert(data);

                });
        c.API.ajax.request(
                'test.php'
                , 'GET'
                , {}
                ,'json'
                ,{
                    showIcon : true
                    ,theme:1
                    ,elem :$('#footer')
                }
                , function(data){

                    alert(data);

                });



    }



    $(function(){




    })

</script>