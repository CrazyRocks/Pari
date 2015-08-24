<div class="ads-contuiter">


    <div id="header" class="move-aciton">
        <div class="set-width-tool">
            <div class="logo">
                <p><img src="/images/logo.png" /> | 登录-注册</p>
                <p class="websit-name">亚洲现金制在线博彩服务娱乐公司</p>
            </div>
        </div>
    </div>

    <div id="arcle">
        <div class="h-login-banner banner"></div>
        <div class="login-contruter">
            <div class="table">
                <div class="td">
                    <div class="input-box scale-action" >
                        <div class="ads-row t_menu">
                            <div class="ads-c-4 li"><a class="atice" href="javascript:;" >登   录</a></div>
                            <div class="ads-c-8 li"><a href="javascript:;" >快速注册</a></div>
                        </div>
                        <div class="ads-row i-box">
                            <div class="ads-row e-i">
                                <div class="inpput-group">
                                    <label>用户名：</label>
                                    <input class="form-control" id="login_name" type="text" />
                                </div>
                                <div class="inpput-group">
                                    <label>密   码：</label>
                                    <input class="form-control" id="login_password" type="password" />
                                </div>

                                <div class="inpput-group">
                                    <label>&nbsp;</label>
                                    <input type="checkbox" /> 自动登录 | <a href="javascript:;" >忘记密码</a>？
                                </div>

                                <div class="inpput-group">
                                    <label>&nbsp;</label>
                                    <div class="btn-control">
                                        <a id="ac-login" class="btn" href="javascript:;">登录</a>
                                    </div>
                                </div>

                            </div>


                            <div class="ads-row e-i">

                                <div class="inpput-group">
                                    <label>用户名：</label>
                                    <input class="form-control" id="reg_name" type="text" />
                                </div>
                                <div class="inpput-group">
                                    <label>邮  箱：</label>
                                    <input class="form-control" id="reg_email" type="text" />
                                </div>

                                <div class="inpput-group">
                                    <label>密   码：</label>
                                    <input class="form-control" id="reg_password" type="password" />
                                </div>

                                <div class="inpput-group">
                                    <label>&nbsp;</label>
                                    <div class="btn-control">
                                        <a class="btn" id="reg_action" href="javascript:;">快速注册</a>
                                    </div>
                                </div>


                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="footer" class="fixed move-aciton" >
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


    document.onreadystatechange = subSomething;//当页面加载状态改变的时候执行这个方法.
    function subSomething()
    {

        if(document.readyState == 'complete') //当页面加载状态
        {
            showLoadingImg();
        }


    }

    function show(index){
        var other = index== 0 ? 1 : 0;
        $('.i-box .e-i').eq(other).stop().hide();
        $('.i-box .e-i').eq(index).stop().fadeIn();
    }

    function showLoadingImg(){
        var smartmub = new Date(),b;
        smartmub = smartmub.getDate();
        if( localStorage.getItem(smartmub) != null){
            b =localStorage.getItem(smartmub);
        }else{
            b = Math.random();
            b = parseInt(b*100);
            b=b%6+1;
        }

        var banner=new Image();
        banner.src = '/case/login_banner/static_banner_0'+b+'.jpg';
        localStorage.clear();
        localStorage.setItem(smartmub,b);
        banner.onload = function(){
            $('.banner').css({backgroundImage:'url("'+banner.src+'")'});
        };


    }

    function isEmail(str){
        var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
        return reg.test(str);
    }


    c.ready(function(){


        $('.t_menu .li').each(function(k){
            $(this).click(function(){
                if($(this).find('a').hasClass('atice')) return false;
                $(this).find('a').addClass('atice');
                $(this).siblings('.li').find('a').removeClass('atice');
                show(k);
            });
        });



        $('#ac-login').click(function(){

            var name = $.trim($('#login_name').val())
                    ,password = $.trim($('#login_password').val()),data={};

            if(name == ''){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'用户名不能为空！',function(){});
                return false;
            }

            if(password == ''){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'密码不能为空！',function(){});
                return false;
            }

            if(password.length <6){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'密码不正确！',function(){});
                return false;
            }

            data['name'] = name;
            data['password'] = password;

            c.API.ajax.request(
                    '//sso.'+ c.config['domain'] +'/user/signin' // URL
                    , 'post' // request type
                    , data // data
                    ,'json' // datatype
                    ,{
                        showIcon : true
                        ,theme:0
                        ,elem :$('#main')
                    }
                    , function(data){
                        console.log(data);
                        //TODO  跳转或者其他操作
                        //alert(data);
                        $.confrim.show({
                            sureText : '知道了',
                            showCannel :false
                        },JSON.stringify(data),function(){});

                    });





        });


        //注册 name email password

        $('#reg_action').click(function(){
            var name = $.trim($('#reg_name').val())
                    ,email = $.trim($('#reg_email').val())
                    ,password = $.trim($('#reg_password').val()),data={};

            if(name == ''){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'用户名不能为空！',function(){});
                return false;
            }

            if(email == ''){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'邮箱地址不能为空！',function(){});
                return false;
            }

            if(password == ''){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'密码不能为空！',function(){});
                return false;
            }

            if(!isEmail(email)){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'邮箱格式有误,请检查一下！',function(){});
                return false;
            }

            if(password.length <6){
                $.confrim.show({
                    sureText : '知道了',
                    showCannel :false
                },'密码最少6位！',function(){});
                return false;
            }


            data['name'] = name;
            data['email'] = email;
            data['password'] = password;

            c.API.ajax.request(
                    '//sso.'+ c.config['domain'] +'/user/signup' // URL
                    , 'post' // request type
                    , data // data
                    ,'json' // datatype
                    ,{
                        showIcon : true
                        ,theme:0
                        ,elem :$('#main')
                    }
                    , function(data){

                        $.confrim.show({
                            sureText : '知道了',
                            showCannel :false
                        },JSON.stringify(data),function(){});
                    });





        });


    })

</script>


