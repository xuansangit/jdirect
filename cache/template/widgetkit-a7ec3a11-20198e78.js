window["WIDGETKIT_URL"]="/joomladirect_2013/media/widgetkit";function wk_ajax_render_url(widgetid){return"/joomladirect_2013/index.php?option=com_widgetkit&amp;format=raw&amp;id="+widgetid}
(function(g,e){var a={};e.$widgetkit={lazyloaders:{},load:function(b){a[b]||(a[b]=g.getScript(b));return a[b]},lazyload:function(a){g("[data-widgetkit]",a||document).each(function(){var a=g(this),b=a.data("widgetkit"),d=a.data("options")||{};!a.data("wk-loaded")&&$widgetkit.lazyloaders[b]&&($widgetkit.lazyloaders[b](a,d),a.data("wk-loaded",true))})}};g(function(){$widgetkit.lazyload()});for(var b=document.createElement("div"),b=b.style,d=false,c="-webkit- -moz- -o- -ms- -khtml-".split(" "),f="Webkit Moz O ms Khtml".split(" "),i="",h=0;h<f.length;h++)if(b[f[h]+"Transition"]===""){d=f[h]+"Transition";i=c[h];break}$widgetkit.prefix=i;$widgetkit.support={transition:d,css3d:d&&"WebKitCSSMatrix"in window&&"m11"in new WebKitCSSMatrix&&!navigator.userAgent.match(/Chrome/i),canvas:function(){var a=document.createElement("canvas");return!(!a.getContext||!a.getContext("2d"))}()};$widgetkit.css3=function(a){a=a||{};a.transition&&(a[i+"transition"]=a.transition);a.transform&&(a[i+"transform"]=a.transform);a["transform-origin"]&&(a[i+"transform-origin"]=a["transform-origin"]);return a};b=null})(jQuery,window);(function(g){g.browser.msie&&parseInt(g.browser.version)<9&&(g(document).ready(function(){g("body").addClass("wk-ie wk-ie"+parseInt(g.browser.version))}),g.each("abbr,article,aside,audio,canvas,details,figcaption,figure,footer,header,hgroup,mark,meter,nav,output,progress,section,summary,time,video".split(","),function(){document.createElement(this)}))})(jQuery);(function(g,e){e.$widgetkit.trans={__data:{},addDic:function(a){g.extend(this.__data,a)},add:function(a,b){this.__data[a]=b},get:function(a){if(!this.__data[a])return a;var b=arguments.length==1?[]:Array.prototype.slice.call(arguments,1);return this.printf(String(this.__data[a]),b)},printf:function(a,b){if(!b)return a;var d="",c=a.split("%s");if(c.length==1)return a;for(var f=0;f<b.length;f++)c[f].lastIndexOf("%")==c[f].length-1&&f!=b.length-1&&(c[f]+="s"+c.splice(f+1,1)[0]),d+=c[f]+b[f];return d+
c[c.length-1]}}})(jQuery,window);(function(g){g.easing.jswing=g.easing.swing;g.extend(g.easing,{def:"easeOutQuad",swing:function(e,a,b,d,c){return g.easing[g.easing.def](e,a,b,d,c)},easeInQuad:function(e,a,b,d,c){return d*(a/=c)*a+b},easeOutQuad:function(e,a,b,d,c){return-d*(a/=c)*(a-2)+b},easeInOutQuad:function(e,a,b,d,c){return(a/=c/2)<1?d/2*a*a+b:-d/2*(--a*(a-2)-1)+b},easeInCubic:function(e,a,b,d,c){return d*(a/=c)*a*a+b},easeOutCubic:function(e,a,b,d,c){return d*((a=a/c-1)*a*a+1)+b},easeInOutCubic:function(e,a,b,d,c){return(a/=c/2)<1?d/2*a*a*a+b:d/2*((a-=2)*a*a+2)+b},easeInQuart:function(e,a,b,d,c){return d*(a/=c)*a*a*a+b},easeOutQuart:function(e,a,b,d,c){return-d*((a=a/c-1)*a*a*a-1)+b},easeInOutQuart:function(e,a,b,d,c){return(a/=c/2)<1?d/2*a*a*a*a+b:-d/2*((a-=2)*a*a*a-2)+b},easeInQuint:function(e,a,b,d,c){return d*(a/=c)*a*a*a*a+b},easeOutQuint:function(e,a,b,d,c){return d*((a=a/c-1)*a*a*a*a+1)+b},easeInOutQuint:function(e,a,b,d,c){return(a/=c/2)<1?d/2*a*a*a*a*a+b:d/2*((a-=2)*a*a*a*a+2)+b},easeInSine:function(e,a,b,d,c){return-d*Math.cos(a/c*(Math.PI/2))+d+b},easeOutSine:function(e,a,b,d,c){return d*Math.sin(a/c*(Math.PI/2))+b},easeInOutSine:function(e,a,b,d,c){return-d/2*(Math.cos(Math.PI*a/c)-1)+b},easeInExpo:function(e,a,b,d,c){return a==0?b:d*Math.pow(2,10*(a/c-1))+b},easeOutExpo:function(e,a,b,d,c){return a==c?b+d:d*(-Math.pow(2,-10*a/c)+1)+b},easeInOutExpo:function(e,a,b,d,c){return a==0?b:a==c?b+d:(a/=c/2)<1?d/2*Math.pow(2,10*(a-1))+b:d/2*(-Math.pow(2,-10*--a)+2)+b},easeInCirc:function(e,a,b,d,c){return-d*(Math.sqrt(1-(a/=c)*a)-1)+b},easeOutCirc:function(e,a,b,d,c){return d*Math.sqrt(1-(a=a/c-1)*a)+b},easeInOutCirc:function(e,a,b,d,c){return(a/=c/2)<1?-d/2*(Math.sqrt(1-a*a)-1)+b:d/2*(Math.sqrt(1-(a-=2)*a)+1)+b},easeInElastic:function(e,a,b,d,c){var e=1.70158,f=0,g=d;if(a==0)return b;if((a/=c)==1)return b+d;f||(f=c*0.3);g<Math.abs(d)?(g=d,e=f/4):e=f/(2*Math.PI)*Math.asin(d/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*c-e)*2*Math.PI/f))+b},easeOutElastic:function(e,a,b,d,c){var e=1.70158,f=0,g=d;if(a==0)return b;if((a/=c)==1)return b+d;f||(f=c*0.3);g<Math.abs(d)?(g=d,e=f/4):e=f/(2*Math.PI)*Math.asin(d/g);return g*Math.pow(2,-10*a)*Math.sin((a*c-e)*2*Math.PI/f)+d+b},easeInOutElastic:function(e,a,b,d,c){var e=1.70158,f=0,g=d;if(a==0)return b;if((a/=c/2)==2)return b+d;f||(f=c*0.3*1.5);g<Math.abs(d)?(g=d,e=f/4):e=f/(2*Math.PI)*Math.asin(d/g);return a<1?-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*c-e)*2*Math.PI/f)+b:g*Math.pow(2,-10*(a-=1))*Math.sin((a*c-e)*2*Math.PI/f)*0.5+d+b},easeInBack:function(e,a,b,d,c,f){f==void 0&&(f=1.70158);return d*(a/=c)*a*((f+1)*a-f)+b},easeOutBack:function(e,a,b,d,c,f){f==void 0&&(f=1.70158);return d*((a=a/c-1)*a*((f+1)*a+f)+1)+b},easeInOutBack:function(e,a,b,d,c,f){f==void 0&&(f=1.70158);return(a/=c/2)<1?d/2*a*a*(((f*=1.525)+1)*a-f)+b:d/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+b},easeInBounce:function(e,a,b,d,c){return d-g.easing.easeOutBounce(e,c-a,0,d,c)+b},easeOutBounce:function(e,a,b,d,c){return(a/=c)<1/2.75?d*7.5625*a*a+b:a<2/2.75?d*(7.5625*(a-=1.5/2.75)*a+0.75)+
b:a<2.5/2.75?d*(7.5625*(a-=2.25/2.75)*a+0.9375)+b:d*(7.5625*(a-=2.625/2.75)*a+0.984375)+b},easeInOutBounce:function(e,a,b,d,c){return a<c/2?g.easing.easeInBounce(e,a*2,0,d,c)*0.5+b:g.easing.easeOutBounce(e,a*2-c,0,d,c)*0.5+d*0.5+b}})})(jQuery);(function(g){function e(a){var d=a||window.event,c=[].slice.call(arguments,1),f=0,e=0,h=0,a=g.event.fix(d);a.type="mousewheel";a.wheelDelta&&(f=a.wheelDelta/120);a.detail&&(f=-a.detail/3);h=f;d.axis!==void 0&&d.axis===d.HORIZONTAL_AXIS&&(h=0,e=-1*f);d.wheelDeltaY!==void 0&&(h=d.wheelDeltaY/120);d.wheelDeltaX!==void 0&&(e=-1*d.wheelDeltaX/120);c.unshift(a,f,e,h);return g.event.handle.apply(this,c)}var a=["DOMMouseScroll","mousewheel"];g.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var b=a.length;b;)this.addEventListener(a[--b],e,false);else this.onmousewheel=e},teardown:function(){if(this.removeEventListener)for(var b=a.length;b;)this.removeEventListener(a[--b],e,false);else this.onmousewheel=null}};g.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery);(function(g){g.support.ajaxupload=function(){function e(){var a=new XMLHttpRequest;return!(!a||!("upload"in a&&"onprogress"in a.upload))}return function(){var a=document.createElement("INPUT");a.type="file";return"files"in a}()&&e()&&!!window.FormData}();g.support.ajaxupload&&g.event.props.push("dataTransfer");g.fn.uploadOnDrag=function(e){return!g.support.ajaxupload?this:this.each(function(){var a=g(this),b=g.extend({action:"",single:false,method:"POST",params:{},loadstart:function(){},load:function(){},loadend:function(){},progress:function(){},complete:function(){},allcomplete:function(){},readystatechange:function(){}},e);a.on("drop",function(a){function c(a,b){for(var d=new FormData,c=new XMLHttpRequest,f=0,e;e=a[f];f++)d.append("files[]",e);for(var h in b.params)d.append(h,b.params[h]);c.upload.addEventListener("progress",function(a){b.progress(a.loaded/a.total*100,a)},false);c.addEventListener("loadstart",function(a){b.loadstart(a)},false);c.addEventListener("load",function(a){b.load(a)},false);c.addEventListener("loadend",function(a){b.loadend(a)},false);c.addEventListener("error",function(a){b.error(a)},false);c.addEventListener("abort",function(a){b.abort(a)},false);c.open(b.method,b.action,true);c.onreadystatechange=function(){b.readystatechange(c);if(c.readyState==4){var a=c.responseText;if(b.type=="json")try{a=g.parseJSON(a)}catch(d){a=false}b.complete(a,c)}};c.send(d)}a.stopPropagation();a.preventDefault();var f=a.dataTransfer.files;if(b.single){var e=a.dataTransfer.files.length,h=0,j=b.complete;b.complete=function(a,d){h+=1;j(a,d);h<e?c([f[h]],b):b.allcomplete()};c([f[0]],b)}else c(f,b)}).on("dragover",function(a){a.stopPropagation();a.preventDefault()})})};g.fn.ajaxform=function(e){return!g.support.ajaxupload?this:this.each(function(){var a=g(this),b=g.extend({action:a.attr("action"),method:a.attr("method"),loadstart:function(){},load:function(){},loadend:function(){},progress:function(){},complete:function(){},readystatechange:function(){}},e);a.on("submit",function(a){a.preventDefault();var a=new FormData(this),c=new XMLHttpRequest;a.append("formdata","1");c.upload.addEventListener("progress",function(a){b.progress(a.loaded/a.total*100,a)},false);c.addEventListener("loadstart",function(a){b.loadstart(a)},false);c.addEventListener("load",function(a){b.load(a)},false);c.addEventListener("loadend",function(a){b.loadend(a)},false);c.addEventListener("error",function(a){b.error(a)},false);c.addEventListener("abort",function(a){b.abort(a)},false);c.open(b.method,b.action,true);c.onreadystatechange=function(){b.readystatechange(c);if(c.readyState==4){var a=c.responseText;if(b.type=="json")try{a=g.parseJSON(a)}catch(d){a=false}b.complete(a,c)}};c.send(a)})})}})(jQuery);(function(b,e,f){function d(d){g.innerHTML='&shy;<style media="'+d+'"> #mq-test-1 { width: 42px; }</style>';h.insertBefore(i,c);a=g.offsetWidth==42;h.removeChild(i);return a}function j(a){var b=d(a.media);if(a._listeners&&a.matches!=b){a.matches=b;for(var b=0,c=a._listeners.length;b<c;b++)a._listeners[b](a)}}if(!e.matchMedia||b.userAgent.match(/(iPhone|iPod|iPad)/i)){var a,h=f.documentElement,c=h.firstElementChild||h.firstChild,i=f.createElement("body"),g=f.createElement("div");g.id="mq-test-1";g.style.cssText="position:absolute;top:-100em";i.style.background="none";i.appendChild(g);e.matchMedia=function(a){var b,c=[];b={matches:d(a),media:a,_listeners:c,addListener:function(a){typeof a==="function"&&c.push(a)},removeListener:function(a){for(var b=0,d=c.length;b<d;b++)c[b]===a&&delete c[b]}};e.addEventListener&&e.addEventListener("resize",function(){j(b)},false);f.addEventListener&&f.addEventListener("orientationchange",function(){j(b)},false);return b}}})(navigator,window,document);(function(b,e,f){if(!b.onMediaQuery){var d={},j=e.matchMedia&&e.matchMedia("only all").matches;b(f).ready(function(){for(var a in d)b(d[a]).trigger("init"),d[a].matches&&b(d[a]).trigger("valid")});b(e).bind("load",function(){for(var a in d)d[a].matches&&b(d[a]).trigger("valid")});b.onMediaQuery=function(a,f){var c=a&&d[a];if(!c)c=d[a]=e.matchMedia(a),c.supported=j,c.addListener(function(){b(c).trigger(c.matches?"valid":"invalid")});b(c).bind(f);return c}}})(jQuery,window,document);(function(d){var a=function(){};a.prototype=d.extend(a.prototype,{name:"accordion",options:{index:0,duration:500,easing:"easeOutQuart",animated:"slide",event:"click",collapseall:true,matchheight:true,toggler:".toggler",content:".content"},initialize:function(a,b){var b=d.extend({},this.options,b),c=a.find(b.toggler),g=function(a){var f=c.eq(a).hasClass("active")?d([]):c.eq(a),e=c.eq(a).hasClass("active")?c.eq(a):d([]);f.hasClass("active")&&(e=f,f=d([]));b.collapseall&&(e=c.filter(".active"));switch(b.animated){case"slide":f.next().stop().show().animate({height:f.next().data("height")},{easing:b.easing,duration:b.duration});e.next().stop().animate({height:0},{easing:b.easing,duration:b.duration,complete:function(){e.next().hide()}});break;default:f.next().show().css("height",f.next().data("height")),e.next().hide().css("height",0)}f.addClass("active");e.removeClass("active")},j=function(){var i=0;b.matchheight&&a.find(b.content).css("min-height","").css("height","").each(function(){i=Math.max(i,d(this).height())}).css("min-height",i);c.each(function(){var b=d(this),a=b.next();a.data("height",a.css("height","").show().height());b.hasClass("active")?a.show():a.hide().css("height",0)})};c.each(function(a){var c=d(this).bind(b.event,function(){g(a)}),e=c.next().css("overflow","hidden").addClass("content-wrapper");a==b.index||b.index=="all"?(c.addClass("active"),e.show()):e.hide().css("height",0)});j();d(window).bind("resize",function(){j()})}});d.fn[a.prototype.name]=function(){var h=arguments,b=h[0]?h[0]:null;return this.each(function(){var c=d(this);if(a.prototype[b]&&c.data(a.prototype.name)&&b!="initialize")c.data(a.prototype.name)[b].apply(c.data(a.prototype.name),Array.prototype.slice.call(h,1));else if(!b||d.isPlainObject(b)){var g=new a;a.prototype.initialize&&g.initialize.apply(g,d.merge([c],h));c.data(a.prototype.name,g)}else d.error("Method "+b+" does not exist on jQuery."+a.name)})};window.$widgetkit&&($widgetkit.lazyloaders.accordion=function(a,b){d(a).accordion(b)})})(jQuery);(function(){$widgetkit.lazyloaders["gallery-slider"]=function(b,a){var f=b.find(".slides:first"),d=f.children(),e=a.total_width=="auto"?b.width():a.total_width>b.width()?b.width():a.total_width,h=e/d.length-a.spacing,g=a.width,c=a.height;if(a.total_width=="auto"||a.total_width>=e)c=a.width/(e/2),g=a.width/c,c=a.height/c,d.css("background-size",g+"px "+c+"px");d.css({width:h,"margin-right":a.spacing});f.width(d.eq(0).width()*d.length*2);b.css({width:e,height:c});$widgetkit.load(WIDGETKIT_URL+"/widgets/gallery/js/slider.js").done(function(){b.galleryslider(a)})}})(jQuery);$widgetkit.load('/joomladirect_2013/media/widgetkit/widgets/lightbox/js/lightbox.js').done(function(){jQuery(function($){$('a[data-lightbox]').lightbox({"titlePosition":"float","transitionIn":"fade","transitionOut":"fade","overlayShow":1,"overlayColor":"#777","overlayOpacity":0.7});});});(function(){$widgetkit.lazyloaders.googlemaps=function(a,b){$widgetkit.load(WIDGETKIT_URL+"/widgets/map/js/map.js").done(function(){a.googlemaps(b)})}})(jQuery);$widgetkit.trans.addDic({"FROM_ADDRESS":"From address: ","GET_DIRECTIONS":"Get directions","FILL_IN_ADDRESS":"Please fill in your address.","ADDRESS_NOT_FOUND":"Sorry, address not found!","LOCATION_NOT_FOUND":", not found!"});if(!window['mejs']){$widgetkit.load('/joomladirect_2013/media/widgetkit/widgets/mediaplayer/mediaelement/mediaelement-and-player.js').done(function(){jQuery(function($){mejs.MediaElementDefaults.pluginPath='/joomladirect_2013/media/widgetkit/widgets/mediaplayer/mediaelement/';$('video,audio').each(function(){var ele=$(this);if(!ele.parent().hasClass('mejs-mediaelement')){ele.data('mediaelement',new mejs.MediaElementPlayer(this,{"pluginPath":"\/joomladirect_2013\/media\/widgetkit\/widgets\/mediaplayer\/mediaelement\/"}));var w=ele.data('mediaelement').width,h=ele.data('mediaelement').height;$.onMediaQuery('(max-width: 767px)',{valid:function(){ele.data('mediaelement').setPlayerSize('100%',ele.is('video')?'100%':h);},invalid:function(){var parent_width=ele.parent().width();if(w>parent_width){ele.css({width:'',height:''}).data('mediaelement').setPlayerSize('100%','100%');}else{ele.css({width:'',height:''}).data('mediaelement').setPlayerSize(w,h);}}});if($(window).width()<=767){ele.data('mediaelement').setPlayerSize('100%',ele.is('video')?'100%':h);}}});});});}else{jQuery(function($){mejs.MediaElementDefaults.pluginPath='/joomladirect_2013/media/widgetkit/widgets/mediaplayer/mediaelement/';$('video,audio').each(function(){var ele=$(this);if(!ele.parent().hasClass('mejs-mediaelement')){ele.data('mediaelement',new mejs.MediaElementPlayer(this,{"pluginPath":"\/joomladirect_2013\/media\/widgetkit\/widgets\/mediaplayer\/mediaelement\/"}));var w=ele.data('mediaelement').width,h=ele.data('mediaelement').height;$.onMediaQuery('(max-width: 767px)',{valid:function(){ele.data('mediaelement').setPlayerSize('100%',ele.is('video')?'100%':h);},invalid:function(){var parent_width=ele.parent().width();if(w>parent_width){ele.css({width:'',height:''}).data('mediaelement').setPlayerSize('100%','100%');}else{ele.css({width:'',height:''}).data('mediaelement').setPlayerSize(w,h);}}});if($(window).width()<=767){ele.data('mediaelement').setPlayerSize('100%',ele.is('video')?'100%':h);}}});});;}
(function(e){$widgetkit.lazyloaders.slideset=function(a,f){a.css("visibility","hidden");var h=a.find(".sets:first"),c=h.css({width:""}).width(),d=a.find("ul.set").show(),g=0;f.width=="auto"&&a.width();var i=f.height=="auto"?d.eq(0).children().eq(0).outerHeight(true):f.height;d.each(function(){var a=e(this).show(),b=0;e(this).children().each(function(){var a=e(this);a.css("left",b);b+=a.width()});g=Math.max(g,b);a.css("width",b).data("width",b).hide()});d.eq(0).show();h.css({height:i});g>c&&(c=g/c,d.css($widgetkit.css3({transform:"scale("+1/c+")"})),h.css("height",i/c));d.css({height:i});$widgetkit.load(WIDGETKIT_URL+"/widgets/slideset/js/slideset.js").done(function(){a.slideset(f).css("visibility","visible");a.find("img[data-src]").each(function(){var a=e(this),b=a.data("src");setTimeout(function(){a.attr("src",b)},1)})})}})(jQuery);(function(f){$widgetkit.lazyloaders.slideshow=function(a,c){$widgetkit.support.canvas&&a.find("img[data-src]").each(function(){var a=f(this),b=document.createElement("canvas"),c=b.getContext("2d");b.width=a.attr("width");b.height=a.attr("height");c.drawImage(this,0,0);a.attr("src",b.toDataURL("image/png"))});a.css("visibility","hidden");var b=c.width,d=c.height,e=a.find("ul.slides:first"),g=e.children();g.css("width","");g.css("height","");e.css("height","");a.css("width","");b!="auto"&&a.width()<b&&(d=b="auto");a.css({width:b=="auto"?a.width():b});e.width();b=d;b=="auto"&&(b=g.eq(0).show().height());g.css({height:b});e.css("height",b);$widgetkit.load(WIDGETKIT_URL+"/widgets/slideshow/js/slideshow.js").done(function(){a.find("img[data-src]").each(function(){var a=f(this),b=a.data("src");setTimeout(function(){a.attr("src",b)},1)});a.slideshow(c).css("visibility","visible")})};$widgetkit.lazyloaders.showcase=function(a,c){var b=a.find(".wk-slideshow").css("visibility","hidden"),d=a.find(".wk-slideset").css("visibility","hidden"),e=d.find("ul.set > li");$widgetkit.lazyloaders.slideshow(b,c);$widgetkit.lazyloaders.slideset(d,f.extend({},c,{width:"auto",height:"auto",autoplay:false,duration:c.slideset_effect_duration,index:parseInt(c.index/c.items_per_set)}));f(window).bind("resize",function(){var b=function(){a.css("width","");c.width=="auto"||c.width>a.width()?a.width(a.width()):a.width(c.width)};b();return b}());f.when($widgetkit.load(WIDGETKIT_URL+"/widgets/slideset/js/slideset.js"),$widgetkit.load(WIDGETKIT_URL+"/widgets/slideshow/js/slideshow.js")).done(function(){b.css("visibility","visible");d.css("visibility","visible");var a=b.data("slideshow"),c=d.data("slideset");e.eq(a.index).addClass("active");b.bind("slideshow-show",function(a,b,d){if(!e.removeClass("active").eq(d).addClass("active").parent().is(":visible"))switch(d){case 0:c.show(0);break;case e.length-1:c.show(c.sets.length-1);break;default:c[d>b?"next":"previous"]()}});e.each(function(b){f(this).bind("click",function(){a.stop();a.show(b)})})})}})(jQuery);$widgetkit.load('/joomladirect_2013/media/widgetkit/widgets/spotlight/js/spotlight.js').done(function(){jQuery(function($){$('[data-spotlight]').spotlight({"duration":300});});});jQuery(function(b){var f=function(b){var a=new Date(Date.parse(b.replace(/(\d+)-(\d+)-(\d+)T(.+)([-\+]\d+):(\d+)/g,"$1/$2/$3 $4 UTC$5$6"))),a=parseInt(((arguments.length>1?arguments[1]:new Date).getTime()-a)/1E3);return a<60?$widgetkit.trans.get("LESS_THAN_A_MINUTE_AGO"):a<120?$widgetkit.trans.get("ABOUT_A_MINUTE_AGO"):a<2700?$widgetkit.trans.get("X_MINUTES_AGO",parseInt(a/60).toString()):a<5400?$widgetkit.trans.get("ABOUT_AN_HOUR_AGO"):a<86400?$widgetkit.trans.get("X_HOURS_AGO",parseInt(a/3600).toString()):a<172800?$widgetkit.trans.get("ONE_DAY_AGO"):$widgetkit.trans.get("X_DAYS_AGO",parseInt(a/86400).toString())};b(".wk-twitter time").each(function(){b(this).html(f(b(this).attr("datetime")))});var d=b(".wk-twitter-bubbles");if(d.length){var e=function(){d.each(function(){var c=0;b(this).find("p.content").each(function(){var a=b(this).height();a>c&&(c=a)}).css("min-height",c)})};e();b(window).bind("load",e)}});$widgetkit.trans.addDic({"LESS_THAN_A_MINUTE_AGO":"less than a minute ago","ABOUT_A_MINUTE_AGO":"about a minute ago","X_MINUTES_AGO":"%s minutes ago","ABOUT_AN_HOUR_AGO":"about an hour ago","X_HOURS_AGO":"about %s hours ago","ONE_DAY_AGO":"1 day ago","X_DAYS_AGO":"%s days ago"});