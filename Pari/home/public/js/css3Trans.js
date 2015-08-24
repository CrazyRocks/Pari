
!function ($) {

    $.support.trans = (function () {

        var transitionEnd = (function () {

            var el = document.createElement('div')
                ,transName =[]
                ,actictTrans=[]
                , transEndEventNames = {
                    'WebkitTransform' : 'webkitTransitionEnd'
                    ,  'MozTransform'    : 'transitionend'
                    ,  'OTransform'      : 'oTransitionEnd otransitionend'
                    ,  'transform'       : 'transitionend'
                }
                , animateName={
                      'WebkitAnimation' : 'webkitAnimationEnd'
                    ,  'MozAnimation'    : 'animationend'
                    ,  'OAnimation'      : 'oAnimationEnd oanimationend'
                    ,  'animation'       : 'animationend'
                }
                , name

            for (name in transEndEventNames){
                if (el.style[name] !== undefined) {
                    actictTrans.push(name);
                    transName.push(transEndEventNames[name]);
                    break;
                }
            }
            for (name in animateName){
                if (el.style[name] !== undefined) {
                    actictTrans.push(name);
                    transName.push(animateName[name]);
                    break;
                }
            }

            return {
                ed :transName,
                trs : actictTrans
            }

        })();

        return transitionEnd && {
            end: transitionEnd.ed,
            trans : transitionEnd.trs
        }

    })();



    var Transitions = function(elenment , option ,callback){
        this.version = '1.0.01';
        this.obj = elenment;
        this.potion = option;
        this.callback = null;
        this.step =[];
        this.tatol = 0;
        this.className;
        this.endCallback = callback;

    };

    Transitions.prototype ={

        init : function(){

            var me = this;

            var className = this.potion.inClassName;
            className = className.split(' ');

            if(className.length > 1){


                me.className = (className.length > 1)? className : this.potion.inClassName;
                me.tatol = me.className.length;

                for(var k=0;k< me.className.length ;k++){
                    //注册callback
                    (function(k){

                        me.step[k] = function(){

                            me.potion.inClassName = me.className[k+1]; //下个动画添加class 实现
                            me.show();

                            me.end = (k == me.tatol - 2)? me.endCallback : me.step[k+1]; //是否是最后一个动画 callback

                            me.callback = me.end; // 绑定callback 待触发

                        };

                    })(k);
                }

                this.potion.inClassName = me.className[0];
                me.callback =(className.length > 1)?  me.step[0] : me.endCallback; // 绑定去触发第一个callback
                me.show();

            }else{
                me.callback = me.endCallback;
                this.show();
            }

        },

        addEffect:function(){
            if(this.potion.inClassName != null) $(this.obj).addClass(this.potion.inClassName);
        },

        returnDefault :function(){
            if(this.potion.inClassName != null) $(this.obj).removeClass(this.potion.inClassName);
        },


        show : function(){

            var _th = this,onece =0;
            $(_th.obj).show();

            if ($.support.trans) {
                _th.obj[0].offsetWidth //TODO force reflow 计算渲染区域 （重点）
            }

            _th.addEffect();

            if($.support.trans){

                for(var k=0;k< $.support.trans.end.length;k++){

                    (function(k){

                        $(_th.obj).one($.support.trans.end[k] , function(){

                            if(!/animation/g.test($.support.trans.end[k]) || _th.potion.isDeleClass)  _th.returnDefault();
                            if(onece==0){
                                (typeof _th.callback =='function')? _th.callback() : null;
                                onece++;
                            }


                        });

                    })(k);
                }


            }else{
                (typeof _th.callback =='function')? _th.callback() : null;
            }
        }

    }

    $.fn.CSS3Trans = function(option,callback){

        var def = {
            inClassName : null,
            isDeleClass : true // 是否删除加进来的calss
        };

        var $this = $(this);
        var datas = new Transitions($this , $.extend(def,option),callback);
        datas.init();

    };



}(window.jQuery);

