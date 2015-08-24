<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width,user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta content="telephone=no,email=no" name="format-detection" />

    <title>{{ get_title(false)|e }}</title>

    <!-- 动态添加头部js文件 -->
    {{ assets.outputCss() }}
    <link rel="stylesheet" href="/css/main.css"/>

    <!--[if lte IE 9]>
    <script src="/js/css3-mediaqueries.js"></script>
    <![endif]-->
    <!-- 动态添加头部js文件 -->
    {{ assets.outputJs() }}
    <script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>

</head>
<body>
<!-- ***************************** 头部结束 ***************************** -->
<!-- ***************************** 内容开始 ***************************** -->
{{ content() }}
<!-- ***************************** 内容结束 ***************************** -->
<!-- Small modal confrim -->
<div id="comfrie-ui-main" class="ui-dialog-main" tabindex="-1" style="display: none;">
    <div class="dialog-box">
        <div class="dialog-show">
            <!--   <div class="dialog-waring btn-close"><a href="javascript:;" >×</a></div>-->
            <div class="show-Icon-tip" style="display: none;">
                <!--<img src="images/confrim-tip-icon.png" />-->
            </div>
            <div class="dialog-content"></div>
            <div class="dialog-controler"></div>
        </div>
    </div>
</div>

<!-- demo temple 3种写法 -->
<script class="tmpl-mode"  type="text/html">

    <tr>
        <td>${result.code}</td>
        <td>
            {!--if result.data!=null--}
            {!--each(i,item) result.data--}
            {!--= item.name--}
            {!--if item.id==1--}
            true
            {!--else--}
            false
            {!--/if--}
            {!--/each--}
            {!--else--}
            无数据
            {!--/if--}

        </td>
    </tr>

</script>


<!-- ***************************** 尾部开始 ***************************** -->
<!-- 尾部动态增加css -->
{{ assets.outputCss() }}
<!-- 尾部动态增加js -->
{{ assets.outputJs() }}
<!-- ***************************** 尾部结束 ***************************** -->

</body>
</html>
