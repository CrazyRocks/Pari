!function(){

    window.c = window.c ==undefined ? {} :c;

    //todo 接口初始化
    !function(){
        var config = function(){
            var jses = document.getElementsByTagName("script");
            var js = jses[jses.length -1];
            var src = js.getAttribute('src');
            this.config = {};
            this.config['token'] = this.get(src,'token');
            this['version'] = this.get(src,'v');
            this.config['domain'] = document.domain.replace('www.','');
            this.config['host'] = 'http://'+document.domain+'/';

        };
        config.prototype.get=function(longstring,name ){
            eval('var reg=/'+name + '\=[^#|^&]+/g;');
            var res = longstring.match(reg);
            if(res !=null){
                res = res[0];
                res = res.split('=');
                res = res[1];
            }else{ res = null}
            return res;
        };

        c = new config();
        c.ready = function(fn){
            $(function(){
                fn();
            })
        };

    }();

    //todo 事件禁止
    (function(){
        c.event = {

            stop : false,
            listener : function(e){
                e.preventDefault();

            },
            stopScroll : function(bool){
                this.stop = bool || true;
                (this.stop)? this.preventDefault() : this.delePrevent();
            },
            defaultScroll : function(bool){
                this.stop = bool || false;
                (this.stop)? this.preventDefault() : this.delePrevent();
            },

            preventDefault : function(){
                window.addEventListener = (window.addEventListener)? window.addEventListener : window.attachEvent;
                $('body').css({paddingRight: '17px',overflow: 'hidden'});
                window.addEventListener('touchmove',this.listener,false);
                window.addEventListener('DOMMouseScroll',this.listener,false);
                window.addEventListener('mousewheel',this.listener,false);
            },
            delePrevent : function(){
                 window.removeEventListener = (window.removeEventListener)? window.removeEventListener : window.attachEvent;
                $('body').css({paddingRight: '0',overflow: 'auto'});
                window.removeEventListener('touchmove',this.listener,false);
                window.removeEventListener('DOMMouseScroll',this.listener,false);
                window.removeEventListener('mousewheel',this.listener,false);
            }


        };
    })();


    $(function(){
        (function(){
            $('.ui-dialog-main').css({display:'none'});
        })();
    });


    var confrim = {

        show : function(option, TipsContent, fn ,cfn){

            var options = {}, msg = '', callbackFn = null, ops ;
            var agr = arguments;

            (function () {

                if (agr.length < 3) {

                    if (agr.length == 2) {

                        if (typeof agr[0] == 'string' || typeof agr[0] == 'number') {
                            options = {};
                            msg = agr[0];
                            callbackFn = agr[1];
                        }else if(typeof agr[0] == 'object'){
                            options = option;
                            msg = (typeof agr[1] == 'string')? TipsContent : '';
                            callbackFn = (typeof agr[1] == 'string')? null : (typeof agr[1] != 'function')? null : TipsContent;
                        }else{
                            options = option;
                            msg = TipsContent;
                            callbackFn = null;
                        }



                    } else if (agr.length == 1) {

                        if (typeof agr[0] == 'string' || typeof agr[0] == 'number') {

                            msg = agr[0];
                            options = {};
                            callbackFn = null;

                        } else {
                            throw new Error('confrim is not a string!!');
                        }
                    }
                }else{
                    options = option;
                    msg = TipsContent;
                    callbackFn = fn;
                    cfn = cfn;
                }

                if (typeof options != 'object')  throw new Error('option is not a object!!');
                if (typeof msg != 'string' && typeof msg != 'number')  throw new Error('text is not a string!!');


                callbackFn = (typeof callbackFn != 'function') ? null : callbackFn;
                cfn = (typeof cfn != 'function') ? null : cfn;

                var def = {
                    stute : false,
                    html : false,
                    targert :null,
                    ImgSrc : null,
                    showSure: true,
                    showCannel: false,
                    sureText : null,
                    cnnelText : null
                }

                ops = $.extend(def, options);

                creatDOM();

            })();


            function creatDOM(){

                if(ops.html) $('#comfrie-ui-main').addClass('edit-html-ceBox');

                if (ops.showSure && $('.ui-dialog-main .dialog-controler a.dialog-btn-true').length == 0) {
                    $('.ui-dialog-main .dialog-controler').append('<a class="dialog-btn-cannel dialog-btn-true" href="javascript:;" >确定</a>');
                }

                if (ops.showCannel && $('.ui-dialog-main .dialog-controler a.dialog-btn-can').length == 0) {
                    $('.ui-dialog-main .dialog-controler').append('<a class="dialog-btn-cannel dialog-btn-can" href="javascript:;" >取消</a>');
                }

                // $('.ui-dialog-main').css({display: 'table'});

                if(ops.ImgSrc != null){
                    $('.ui-dialog-main .show-Icon-tip').html('<img src="'+ops.ImgSrc+'" />').show();
                }


                $('.ui-dialog-main').show();
                $('.ui-dialog-main').addClass('on-showing');

                $('.ui-dialog-main .dialog-content').html(msg);


                ops.sureText = ops.sureText == null ? '确定' : ops.sureText;
                ops.cnnelText = ops.cnnelText == null ? '取消' : ops.cnnelText;
                if(ops.showSure) $('a.dialog-btn-true').text(ops.sureText);
                if(ops.showSure) $('a.dialog-btn-can').text(ops.cnnelText);


                addEvents('a.dialog-btn-true');
                addEvents('a.dialog-btn-can');
                addEvents('.btn-close a');

                $('.ui-dialog-main').addClass('on-showing');

            };

            function hidden(boole,event){

                //event.stopPropagation();

                $('.ui-dialog-main').removeClass('on-showing');

                $('.ui-dialog-main').hide();

                $('.ui-dialog-main .dialog-controler').html('');
                $('.ui-dialog-main .show-Icon-tip').html('').hide();
                if (typeof callbackFn == 'function' && boole) callbackFn();
                if (typeof cfn == 'function' && !boole) cfn();

                //释放内存
                options = {};
                msg = '';
                callbackFn = null;
                cfn = null;
                c.event.defaultScroll();


            };

            function addEvents(evement){

               // $(evement).off('click');
                $('.ui-dialog-main').one('click', evement, function (e) {
                    ($(this).hasClass('dialog-btn-true'))? hidden(true,e):hidden(false,e);
                    c.event.stopScroll();

                });
            }

        }

    };

    $.confrim = confrim;



    //todo ajax load
    (function(){
        var iconLoading=false;

        $.fn.loading=function(){

            var $th = $(this);
            if(!$th.find('.loadInDom-main').length){
                var h1st = '<div id="caseBlanche"><div class="rond"><div class="point"></div></div>';
                var h2st = '<div class="load-rond"><p>Loading</p></div></div>';
                var $tds=$('<div class="load-td"></div>');
                var $k = $('<div class="loadInDom-main" ></div>');

                var $circleImg2=$('<div class="lodingImg-content"><img src="/images/load-content.gif" /></div>');

               // $tds.html(h1st + h2st).appendTo($k);
                $circleImg2.appendTo($th);
                $k.show();
            }

        };

        var ajaxhttp  = {
            loading :{

                id : "loading-main",

                elem : null,

                show : function(){

                    if(!iconLoading){
                        var self = this;
                        if(!$('#'+self.id).length) self.addDom();
                        $('#'+self.id).fadeIn();
                        iconLoading = false;
                    }
                },
                hiden : function(){
                    iconLoading = false;
                    var self = this;
                    $('#'+self.id).stop().fadeOut();
                },

                addDom : function(){
                    var o =this;
                    var $load =  $('<div id="'+o.id+'" ></div>');
                    var $td=$('<div class="load-td"></div>');
                    var $circle=$('<div class="circle"></div>');
                    var $circle1=$('<div class="circle1"></div>');
                    var $circleImg=$('<div class="lodingImg"><img src="/images/loding.gif" /></div>');
                   // $circle.appendTo($td);
                    //$circle1.appendTo($td);
                    $circleImg.appendTo($td);
                    $td.appendTo($load);
                    $load.appendTo($('body')).css({display:'none'});
                }
            },

            showInDom : function(loddNode){

                if(loddNode ==null){
                    throw new Error('dom is undefined!!');
                }else{
                    loddNode.loading();
                }
            },

            hideInDom : function(loddNode){
                loddNode.find('.lodingImg-content').detach();
            },

            request: function (url, type, data ,typedata,loading) {
                loading = typeof loading =='object' ?
                    $.extend({showIcon : false,theme:0,elem :null},loading):
                {showIcon : false,theme:0,elem :null};

                return this.call(url,type,data,typedata,loading,null);
            },
            request : function (url, type, data,typedata,option, callback) {
                var o =this;
                var result,loddNode,jsonpCallback;

                option = typeof option =='object' ?
                    $.extend({showIcon : false,theme:0,elem :null},option):
                {showIcon : false,theme:0,elem :null};

                o.elem = loddNode =  option.elem;
                option.showIcon =iconLoading ? false : option.showIcon;
                typedata = typedata || 'json';

                data['token'] = c.config.token;

                jsonpCallback = c.config.token;

                $.ajax({
                    type: type,
                    url: url,
                    data: data || {},
                   // dataType: typedata,
                    dataType: "jsonp",
                    jsonp: jsonpCallback,
                    async: (callback == null) ? false : true,
                    beforeSend: function(){
                        (option.theme != 0)?  o.showInDom(loddNode) : (option.showIcon? o.loading.show() : null);
                        if(option.theme ==0) c.event.stopScroll();
                    },
                    success: function (date) {
                        if(option.theme ==0) c.event.defaultScroll();
                        if(option.theme!=0){
                            o.hideInDom(loddNode);
                        }else{
                            if (option.showIcon) o.loading.hiden();
                        }

                        var response = date;
                        if (response != null) {
                            (callback == null) ? result = response : callback(response);
                            return;
                        }
                        (callback == null) ? result = null : callback(null);
                    },
                    error: function(e){
                        $.confrim.show({
                            showCannel: true,
                            showCannel:false,
                            sureText : '知道了'
                        },"请求服务器有误!"+JSON.stringify(e),function(){});
                    }
                });
                if (callback == null) return result;
            }
        };

        c.API ={};

        c.API['ajax'] =ajaxhttp;
    })();

    // todo templet
    !function () {
        var a = {}, b = function ($obj, option, complate) {
            var _this = this, d = 'tmpl-mode',
                $tmps = !$obj.find('.' + d).length ? null : $obj.find('.' + d),
                f = 'render-mode',
                $t = !$obj.find('.' + f).length ? null : $obj.find('.' + f),
                tagname = $obj.parent().get(0).tagName,
                isTable =(tagname == 'TABLE'|| tagname == 'TBODY' || tagname == 'TR')? true : false;
            _this.option = $.extend({poolFor: null}, option);
            _this.dom = $obj;
            _this.complate = complate;
            $tmps = ($tmps == null) ? (_this.option.tmpl || null) : $tmps;

            if ($tmps == null) {
                console.error('undefined the tmpl html!');
                return false;
            }
            if ($t == null) {
                if(isTable){
                    $t = _this.dom.addClass(f);
                }else{
                    $tmps.after('<div class="' + f + '"></div>');
                    $t = $obj.find('.' + f);
                }

            }
            ;
            _this.m = $tmps;
            _this.t = $t;
            _this.g();
            if (_this.option.poolFor != null && typeof _this.option.poolFor == 'function') {
                _this.p = c.option.poolFor;
                _this.p();
            }
            ;
            $tmps.detach();
        };
        b.prototype = {g: function () {
            this.m.tmpl(this.option.data).appendTo(this.t);
        }, updateData: function (ndata) {
            this.option.data = $.extend(this.option.data, ndata);
        }, rAgain: function (data) {
            this.option.data = data || {};
            this.g();
        }};
        $.fn.render = function (option, complate) {
            if (arguments.length < 2 && typeof option == 'function') {
                console.error('data is undefined !');
                return false;
            }
            new b($(this), option, complate);
        }
    }();





}();