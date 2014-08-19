
// @fileRef ci.js 
var ci={showNotice:function(message,type,ttl)
{var defaults={type:"success",ttl:5000,panelId:"notice-panel",textClass:"flash-p",textWidthOffset:30};var panelCss={position:"fixed",overflowY:"hidden",height:36,width:"100%",zIndex:9000,top:"-35px"};type=!type?defaults.type:type;np=$("#"+defaults.panelId);newObj="<div class='"+defaults.textClass+" "+type+"'>"+message+"</span>";show=function(el)
{el.stop().animate({top:0},300,function(){});el.delay(150).append("<div id='sn_flash_notice'></div>");$('#sn_flash_notice').css({width:"100%",backgroundColor:el.find("."+defaults.textClass).css("background-color"),height:35,position:"absolute",zIndex:9001,top:0,opacity:.8}).hide().fadeIn(450,function(){$(this).delay(50).fadeOut(350,function(){$(this).remove();w=el.find('.'+defaults.textClass).outerWidth();el.css({left:"50%",marginLeft:-1*w/2,width:w});})});return el;};hide=function(el,onFinish_callback)
{el.find("."+defaults.textClass).stop().animate({top:Math.abs(parseInt(panelCss.top)+1)},300,function(){el.remove();if(onFinish_callback!==undefined)
onFinish_callback(el);});return el;};createObj=function(){np="<div id='"+defaults.panelId+"'>"+newObj+"</div>";$('body').prepend(np).find("#"+defaults.panelId).css(panelCss);np=$("#"+defaults.panelId);npp=np.find("."+defaults.textClass);npp.css("width",ci.textWidth(npp)+defaults.textWidthOffset);show(np).oneTime((ttl===undefined?defaults.ttl:ttl)+"ms",function(){hide($(this));});};if(np.length>0)
{np.stopTime();hide(np,function(){createObj();});}else
createObj();},closeNotice:function(){el=$("#notice-panel");el.find(".flash-p").stop().animate({top:Math.abs(parseInt(-35)+1)},300,function(){el.remove();});},textWidth:function(object){if(object instanceof jQuery)
{var org=object;var text=org.val()||org.text();}
else
var text=object;var html=$('<span style="postion:absolute;width:auto;left:-9999px">'+(text)+'</span>');if(org!==undefined){html.css({fontFamily:org.css("font-family"),fontSize:org.css("font-size"),fontWeight:org.css("font-weight")});}
$('body').append(html);var width=html.width();html.remove();return width;},centerPosition:function(parent_object,centered_object,left_offset,top_offset){var position=parent_object.position();x=(parent_object.outerWidth()-centered_object.outerWidth())/2;y=(parent_object.outerHeight()-centered_object.outerHeight())/2;if(parent_object.css('position')!=='relative'||parent_object.css('position')!=='absolute')
parent_object.css('position','relative');if(left_offset!==undefined)
{left_offset=new String(left_offset);if(left_offset.indexOf("%")!==-1)
x=x/100*parseInt(left_offset);else
x=x+parseInt(left_offset);}
if(top_offset!==undefined)
{top_offset=new String(top_offset);if(top_offset.indexOf("%")!==-1)
y=y/100*parseInt(top_offset);else
y=y+parseInt(top_offset);}
centered_object.css({position:'absolute',zIndex:5000,top:y,left:x});},scrollTo:function(jquery_object,offset)
{offset=offset===undefined?0:offset;if($(jquery_object).length===0)
return false;$('html,body').animate({scrollTop:$(jquery_object).offset().top+offset},'slow');},inlinEditOpt:{defaults:{trigger:"dbclick",elements:".editable",init_type:"text",init_textarea_cols:35,init_textarea_rows:5,init_message_input:"Zmáčknutím tlačítka Enter údaj uložíte. Escapem akci zrušíte.",init_message_textarea:"Zmáčknutím tlačítka Shift+Enter údaj uložíte. Escapem akci zrušíte."},opt:{cached:new Array,addOptions:function(id,data){ci.inlinEditOpt.opt.cached[id]=data;},getOptions:function(id){return ci.inlinEditOpt.opt.cached[id];},changeOptions:function(id,attr,value){return ci.inlinEditOpt.opt.cached[id][attr]=value;},getInitAttr:function(setting_cache,name,attr,substitute)
{a=undefined;if(setting_cache.init[name]!==undefined)
a=setting_cache.init[name][attr];if(a===undefined)
a=ci.inlinEditOpt.defaults["init_"+(substitute===undefined?attr:substitute)];return a;}}},inlinEdit:function(element,options){t=$(element);var settings=jQuery.extend({},ci.inlinEditOpt.defaults,options);$(element).on(settings.trigger,settings.elements,function(){t=$(this);t_id="#"+t.get(0).id;if(t.find(".inlinEdit-input").length>0)
return;get_attr=ci.inlinEditOpt.opt.getInitAttr;st=ci.inlinEditOpt.opt.getOptions(element.get(0).id);type=get_attr(st,t_id,'type');ci.showNotice(get_attr(st,t_id,"message","message"+"_"+type),"info");create_input=function(element,type,arg)
{var path="http://"+window.location.host+"/images/";var img_ok='<img class="ev ok" src="'+path+'adm/med-ok.png" alt="upravit" title="upravit" style="position: relative;"/>';var img_cancel='<img class="ev cancel" src="'+path+'adm/med-cancel.png" alt="stornovat" title="stornovat"style="position: relative;"/>';text=get_attr(st,t_id,'text');if(text===undefined)
text=element.text();text=text.replace(/^\s*|\s*$/g,'');switch(type){case'textarea':var new_obj=jQuery("<textarea/>",{'class':"inlinEdit-input",text:text,cols:ci.inlinEditOpt.defaults.init_textarea_cols,rows:ci.inlinEditOpt.defaults.init_textarea_rows});break;case'input':default:break;}
var old_text=jQuery("<p/>",{style:"display: none",'class':"inlinEdit-oldText",html:text});element.empty().append(old_text).append(new_obj).append(img_ok+img_cancel).find(".inlinEdit-input").focus();if(st.afterRender!==undefined)
st.afterRender(element);};create_input($(this),type,get_attr(st,t_id,'arg'));return false;});if(typeof settings.init==="object")
$.each(settings.init,function(key,value){if(value.trigger!==undefined)
$(element).on("click",value.trigger,function(){$(key).trigger(settings.trigger);return false;});});ci.inlinEditOpt.opt.addOptions(element.get(0).id,options);$(element).on("click",'.ev.ok,.ev.cancel',function(){t=$(this);p=t.parent();if(t.hasClass("cancel"))
{old=p.find('.inlinEdit-oldText').html();p.empty().append(old);}else{st=ci.inlinEditOpt.opt.getOptions(element.get(0).id);id_name="#"+p.get(0).id;input=p.find(".inlinEdit-input");var id=st.init[id_name]['id']!==undefined?st.init[id_name]['id']:p.data('id');var value=input.val();data={'id':id,'request':st.init[id_name]['request'],'value':value}
extra=st.init[id_name]['extra'];if(extra!==undefined){if(typeof extra==="function")
extra=extra();jQuery.extend(data,extra);}
$.ajax({type:'post',url:st.url,data:data,beforeSend:function()
{var path="http://"+window.location.host+"/images/";var loading_element='<img id="l_'+id+'" src="'+path+'loading2.gif" title="loading" style="width:16px; height:11px; float:right;"/>';p.find('input, .ev').fadeOut(200);p.delay(205).append(loading_element);},success:function(data)
{input.slideUp(300,function()
{try{data=jQuery.parseJSON(data);ci.showNotice(data.response,data.status===200||data.status===undefined?"success":(data.status===100?"info":"error"));if(data.status!==200)
{old=p.find('.inlinEdit-oldText').html();p.empty().css('min-width','').append(old);}else{p.empty().append(input.val()).css('min-width','').show(200);}}catch(e){}});},error:function(data)
{old=p.find('.inlinEdit-oldText').html();p.empty().css('min-width','').append(old);ci.showNotice('Nastal obecný problém při komunikaci se serverem','error');}});}});$('body').on('keydown',"input,textarea",function(e){var code=(e.keyCode?e.keyCode:e.which);t=$(this);if((code===13&&t.is("input"))||(code===13&&e.shiftKey&&t.is("textarea"))){t.parent().find("img.ev.ok").trigger('click');return false;}else{if(code===27){ci.closeNotice();t.parent().find("img.ev.cancel").trigger('click');}}});},formValidation:{settings:{validator:{filled:function(value){return value==''?false:true;},same:function(value,ref,arg){return $('input[name='+arg+']').val()==value?true:false;},number:function(value){for(var i=0;i<value.length;++i){var new_key=value.charAt(i);if(((new_key<"0")||(new_key>"9"))&&!(new_key==""))
return false;}
return true;},email:function(value){if(value=='')
return true;var re=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return re.test(value);},checked:function(value,ref){return ref.attr('checked')===undefined?false:true;},phone:function(value){for(var i=0;i<value.length;++i)
{var new_key=value.charAt(i);if(i==0&&new_key==="+")
continue;if(((new_key<"0")||(new_key>"9"))&&!(new_key===""))
return false;}
return true;},min_chars:function(value,ref,arg){return value.length>=arg?true:false;}},defaults:{elements:'body',errorPosition:"right",errorYOffset:5,errorXOffset:10,ajax:false,wipeInput:true,customValidation:undefined,onSuccess:undefined,onBeforeCheck:undefined,onBeforeFailed:undefined,onFailed:undefined},cachedOpt:new Array,addOptions:function(form_id,data){ci.formValidation.settings.cachedOpt[form_id]=data;},getOptions:function(form_id){return ci.formValidation.settings.cachedOpt[form_id];},executed:0},run:function(options)
{var settings=jQuery.extend({},ci.formValidation.settings.defaults,options);form=$(settings.elements);if(form.length===0)
return false;if(settings.customValidation!==undefined)
jQuery.extend(ci.formValidation.settings.validator,settings.customValidation);ci.formValidation.settings.addOptions(form[0].id,settings);form.bind("submit",function(){return ci.formValidation.validate($(this));});return false;},validate:function(e)
{$('.help-inline').remove();$('.control-group.error').removeClass("error");settings=ci.formValidation.settings.getOptions(e[0].id);form_el=e.find(':input').get();if(form_el.length===0)
return false;cachedElements=ci.formValidation.prepare(form_el);er=0;if(typeof settings.onBeforeCheck==='function')
settings.onBeforeCheck();if(typeof settings.onBeforeFailed==='function'){data=new Array;cache=true;}else
cache=false;for(var inputs in cachedElements){for(var rules in cachedElements[inputs]){i=cachedElements[inputs][rules];for(var rule in i){r=i[rule];if(ci.formValidation.settings.validator[rule](r.value,r.ref,r.arg)===false)
{er++;if(cache)
data.push(r);else
ci.formValidation.setError(r.ref,r.message,settings);}}}}
if(!er&&typeof settings.onSuccess==='function')
return settings.onSuccess(e)===false?false:true;if(er>0&&cache)
return settings.onBeforeFailed(data)===true?true:false;if(er>0&&typeof settings.onFailed==='function')
return settings.onFailed()===true?true:false;if(settings.ajax&&!er)
{if(settings.onAjaxBefore!==undefined)
if(settings.onAjaxBefore(e)===false)
return false;$.ajax({url:e.attr("action"),type:"post",data:e.serialize(),beforeSend:function(){},success:function(data)
{try{data=jQuery.parseJSON(data);if(settings.onAjaxComplete!==undefined)
if(settings.onAjaxComplete(data)===false)
return false;if(data.status===200||data.status===undefined)
if(settings.onAjaxSuccess!==undefined)
settings.onAjaxSuccess(data);else
{ci.showNotice(data.response,"success");e.trigger('reset');}
else
if(settings.onAjaxError!==undefined)
settings.onAjaxError(data);else
ci.showNotice(data.response,data.status===100?"info":"error");}catch(e){}},complete:function(){$('#btn-confession').unmask();}});return false;}
return er>0?false:true;},setError:function(form_object,message,settings)
{if(settings===undefined)
{if((settings=ci.formValidation.settings.getOptions(form_object[0].id))===undefined)
settings=ci.formValidation.settings.defaults;}
if($('#'+form_object[0].id+"_errorMsg").length>=1)
return;var new_obj=jQuery("<span/>",{id:form_object[0].id+"_errorMsg",'class':"help-inline",text:message});form_object.closest(".control-group").addClass("error");form_object.after(new_obj)
$('#'+form_object[0].id+"_errorMsg").css({left:form_object.position().left+form_object.outerWidth()+settings.errorXOffset,top:form_object.position().top+settings.errorYOffset,width:ci.textWidth(new_obj)}).hide().fadeIn(200);},removeError:function(form_object)
{if((f=form_object.prev()).hasClass('help-inline'))
f.remove();},prepare:function(form_objects)
{cache=new Array;for(i=0;i<form_objects.length;i++)
{e=$(form_objects[i]);data=e.data();if(jQuery.isEmptyObject(data))
continue;var a={};jQuery.each(data,function(name,value){if(name.substring(0,10)!='validation')
return;name=name.substring(10).toLowerCase();message=(value!=null&&value!="")?value.split('::'):'';arg=message.length>1?message.splice(1,message.length):undefined;if(typeof a[e.attr("name")]=='undefined')
a[e.attr("name")]={};a[e.attr("name")][name]={value:e.val(),message:message[0],ref:e,arg:arg};});cache.push(a);}
return cache;}},modalPage:{settings:{backgroundId:"ajaxpage-background",wrapperId:"ajaxpage-wrapper",dataClass:"ajaxclass",dataOnShowCallback:"ajaxOnshowcallback",dataOnCloseCallback:"ciOnCloseCallback"},load:function(userOptions){if(!userOptions instanceof Object){userOptions={url:userOptions};}
defaultOptions={url:undefined,triggerElement:undefined,showTrigger:false,loadingEnable:true,message:undefined,maskFade:200,onSuccess:undefined,onFailed:undefined,onLoaded:undefined,onClose:undefined,pageId:undefined,post:undefined};options=$.extend({},defaultOptions,userOptions);t=$("#"+options.triggerElement);if(options.pageId!==undefined)
options.post=$.extend(options.post,{page_id:options.pageId});$.ajax({url:options.url,type:"post",data:options.post,beforeSend:function(){if((options.loadingEnable||message!==undefined)&&t!==undefined)
t.mask(options.message,options.maskFade);},success:function(data)
{if(options.onSuccess&&typeof options.onSuccess==='function'){(options.onSuccess(jQuery.parseJSON(data)));if(options.showTrigger)
try{data=jQuery.parseJSON(data);ci.modalPage.show(data.response,data.page_id===undefined?null:data.page_id,options.onLoaded,data);}catch(e){}}else{try{data=jQuery.parseJSON(data);if(data.status==500){if(options.onFailed&&typeof options.onFailed==='function'){(options.onFailed(data));}}else
ci.modalPage.show(data.response,data.page_id===undefined?null:data.page_id,options.onLoaded,data);}catch(e){}}
if(ci.modalPage.settings.onClose===undefined)
$.extend(ci.modalPage.settings,{"onClose":options.onClose});else
ci.modalPage.settings.onClose=options.onClose;},error:function(data)
{if(options.onFailed&&typeof options.onFailed==='function'){(options.onFailed(data));}},complete:function(){if((options.loadingEnable||message!==undefined)&&t!==undefined)
t.unmask();}});},getPageId:function()
{return $('#'+ci.modalPage.settings.backgroundId).data("ajaxpageid");},show:function(html_input,page_id,onLoadedCallback,data){settings=ci.modalPage.settings;$(document).keyup(function(e){if(e.which===27){ci.modalPage.hide();}});wb=$('#'+settings.backgroundId);if(wb.length>0)
{if(!page_id&&wb.data("ajaxpageid")===page_id)
{wb.stop().fadeIn(200,function(){wb.children().slideDown(200);ci.scrollTo('#'+settings.wrapperId,-50);});return;}
ci.modalPage.remove();}
var new_obj=jQuery("<div/>",{id:settings.backgroundId,html:"<div id='ajaxpage-wrapper'>"+html_input+"</div>"});new_obj.hide();$('body').prepend(new_obj);wrapper=$('#'+settings.wrapperId);wrapper_data=$(wrapper[0].firstChild).data();wb=$('#'+settings.backgroundId);if(wrapper_data[settings.dataClass]!==undefined)
wrapper.addClass(wrapper_data[settings.dataClass]);wb.stop().fadeIn(200,function(){if(page_id!==undefined)
wb.data("ajaxpageid",page_id).children().stop().slideDown(200);ci.scrollTo('#'+settings.wrapperId,-50);callback=wrapper_data[settings.dataOnShowCallback];if(callback!==undefined)
window[callback]();else
if(onLoadedCallback!==undefined)
onLoadedCallback(data);});},hide:function(){if((clb=ci.modalPage.settings.onClose)!==undefined)
if(!clb())
return false;$('#'+ci.modalPage.settings.wrapperId).stop().slideUp(200,function(){$(this).parent().hide();});},remove:function()
{$('#'+ci.modalPage.settings.wrapperId).stop().slideUp(200,function(){$(this).parent().remove();});}}};(function($){$.fn.textWidth=function()
{return ci.textWidth(this);};$.fn.inlinEdit=function(data)
{return ci.inlinEdit(this,data);};$.fn.multipleInputs=function()
{var t=this;var settings={labelWidth:15};init();function init()
{$ul=jQuery('<ul/>',{'class':'mi-holder'});$wrapper=jQuery('<div/>',{'class':'mi-itemwrapper'});t.before($ul);$ul=t.prev();t.appendTo($ul).wrap("<li class='mi-input'></li>").parent().before($wrapper);$wrapper=$ul.find("div");$ul.on('click',function(){t.focus();});t.bind("addItem",function(event,data){add_item(data);});t.bind("serialize",function(event,post_name){return serialize(post_name);});t.on("input keyup",function(){t.width(t.textWidth()+45);});t.trigger("input");$ul.append("<div class='clear'></div>");}
function add_item(label)
{nlabel=label.length>settings.labelWidth?label.substring(0,settings.labelWidth-3)+"...":label;$li=jQuery('<li/>',{'class':'mi-item',title:label,text:nlabel});tp=t.parent();token_area=tp.prev();token_area.append($li);token_area.find('li').last('li').append("<a href='#' class='mi-remove'>x</a>");token_area.find('li').last('li').find("a").on("click",function(){$(this).parent().fadeOut(200,function(){$(this).remove();});return false;});}
function serialize(post_name)
{$return=Array();$wrapper.find("li").each(function(){$return.push($(this).attr('title'));})
return{name:post_name,value:$return};}};$.fn.mask=function(c,b){$(this).each(function(){if(b!==undefined&&b>0){var d=$(this);d.data("_mask_timeout",setTimeout(function(){$.maskElement(d,c)},b))}else{$.maskElement($(this),c)}})};$.fn.unmask=function(){$(this).each(function(){$.unmaskElement($(this))})};$.fn.isMasked=function(){return this.hasClass("masked")};$.maskElement=function(d,c){if(d.data("_mask_timeout")!==undefined){clearTimeout(d.data("_mask_timeout"));d.removeData("_mask_timeout")}
if(d.isMasked()){$.unmaskElement(d);}
if(d.css("position")=="static"){d.addClass("masked-relative")}
d.addClass("masked");var e=$('<div class="loadmask"></div>');if(navigator.userAgent.toLowerCase().indexOf("msie")>-1){e.height(d.height()+parseInt(d.css("padding-top"))+parseInt(d.css("padding-bottom")));e.width(d.width()+parseInt(d.css("padding-left"))+parseInt(d.css("padding-right")))}
if(navigator.userAgent.toLowerCase().indexOf("msie 6")>-1){d.find("select").addClass("masked-hidden")}
d.append(e);if(c!==undefined){var b=$('<div class="loadmask-msg" style="display:none;"></div>');b.append("<div>"+c+"</div>");d.append(b);ci.centerPosition(d,b);}};$.unmaskElement=function(b){if(b.data("_mask_timeout")!==undefined){clearTimeout(b.data("_mask_timeout"));b.removeData("_mask_timeout")}
b.find(".loadmask-msg,.loadmask").remove();b.removeClass("masked");b.removeClass("masked-relative");b.find("select").removeClass("masked-hidden")};$.fn.showMessage=function(message,element_class,ttl,left_offset,top_offset,fixed){t=a(this);if(element_class===undefined)
element_class="error";if(ttl===undefined)
ttl=3000;if(t.find(".showmessage").length>0)
t.find(".showmessage").fadeOut(150,function(){$(this).remove()});var new_obj=jQuery("<div/>",{'class':"showmessage "+element_class,html:"<p>"+message+"</p>"});new_obj.hide();t.append(new_obj);sm=t.find('.showmessage');ci.centerPosition(t,sm,left_offset,top_offset);if(fixed)
sm.css("position","fixed");sm.fadeIn(300).delay(ttl).fadeOut(300,function(){$(this).remove();});};$.fn.everyTime=function(interval,label,fn,times,belay){return this.each(function(){jQuery.timer.add(this,interval,label,fn,times,belay);});};$.fn.oneTime=function(interval,label,fn){return this.each(function(){jQuery.timer.add(this,interval,label,fn,1);});};$.fn.stopTime=function(label,fn){return this.each(function(){jQuery.timer.remove(this,label,fn);});};})(jQuery);jQuery.extend({timer:{guid:1,global:{},regex:/^([0-9]+)\s*(.*s)?$/,powers:{ms:1,cs:10,ds:100,s:1000,das:10000,hs:100000,ks:1000000},timeParse:function(c){if(c==undefined||c==null){return null}
var a=this.regex.exec(jQuery.trim(c.toString()));if(a[2]){var b=parseInt(a[1],10);var d=this.powers[a[2]]||1;return b*d}else{return c}},add:function(e,c,d,g,h,b){var a=0;if(jQuery.isFunction(d)){if(!h){h=g}
g=d;d=c}
c=jQuery.timer.timeParse(c);if(typeof c!="number"||isNaN(c)||c<=0){return}
if(h&&h.constructor!=Number){b=!!h;h=0}
h=h||0;b=b||false;if(!e.$timers){e.$timers={}}
if(!e.$timers[d]){e.$timers[d]={}}
g.$timerID=g.$timerID||this.guid++;var f=function(){if(b&&this.inProgress){return}
this.inProgress=true;if((++a>h&&h!==0)||g.call(e,a)===false){jQuery.timer.remove(e,d,g)}
this.inProgress=false};f.$timerID=g.$timerID;if(!e.$timers[d][g.$timerID]){e.$timers[d][g.$timerID]=window.setInterval(f,c)}
if(!this.global[d]){this.global[d]=[]}
this.global[d].push(e)},remove:function(c,b,d){var e=c.$timers,a;if(e){if(!b){for(b in e){this.remove(c,b,d)}}else{if(e[b]){if(d){if(d.$timerID){window.clearInterval(e[b][d.$timerID]);delete e[b][d.$timerID]}}else{for(var d in e[b]){window.clearInterval(e[b][d]);delete e[b][d]}}
for(a in e[b]){break}
if(!a){a=null;delete e[b]}}}
for(a in e){break}
if(!a){c.$timers=null}}}}});if(jQuery.browser.msie){jQuery(window).one("unload",function(){var d=jQuery.timer.global;for(var a in d){var c=d[a],b=c.length;while(--b){jQuery.timer.remove(c[b],a)}}});};;(function(e){e.fn.extend({actual:function(t,n){if(!this[t]){throw'$.actual => The jQuery method "'+t+'" you called does not exist'}
var r={absolute:false,clone:false,includeMargin:false};var i=e.extend(r,n);var s=this.eq(0);var o,u;if(i.clone===true){o=function(){var e="position: absolute !important; top: -1000 !important; ";s=s.clone().attr("style",e).appendTo("body")};u=function(){s.remove()}}else{var a=[];var f="";var l;o=function(){if(e.fn.jquery>="1.8.0")
l=s.parents().addBack().filter(":hidden");else
l=s.parents().andSelf().filter(":hidden");f+="visibility: hidden !important; display: block !important; ";if(i.absolute===true)
f+="position: absolute !important; ";l.each(function(){var t=e(this);a.push(t.attr("style"));t.attr("style",f)})};u=function(){l.each(function(t){var n=e(this);var r=a[t];if(r===undefined){n.removeAttr("style")}else{n.attr("style",r)}})}}
o();var c=/(outer)/g.test(t)?s[t](i.includeMargin):s[t]();u();return c}})})(jQuery)!function($){"use strict";function ScrollSpy(element,options){var process=$.proxy(this.process,this),$element=$(element).is('body')?$(window):$(element),href
this.options=$.extend({},$.fn.scrollspy.defaults,options)
this.$scrollElement=$element.on('scroll.scroll-spy.data-api',process)
this.selector=(this.options.target||((href=$(element).attr('href'))&&href.replace(/.*(?=#[^\s]+$)/,''))||'')+' ul li > a'
this.$body=$('body')
this.refresh()
this.process()}
ScrollSpy.prototype={constructor:ScrollSpy,refresh:function(){var self=this,$targets
this.offsets=$([])
this.targets=$([])
$targets=this.$body.find(this.selector).map(function(){var $el=$(this),href=$el.data('target')||$el.attr('href'),$href=/^#\w/.test(href)&&$(href)
return($href&&$href.length&&[[$href.position().top+(!$.isWindow(self.$scrollElement.get(0))&&self.$scrollElement.scrollTop()),href]])||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){self.offsets.push(this[0])
self.targets.push(this[1])})},process:function(){var scrollTop=this.$scrollElement.scrollTop()+this.options.offset,scrollHeight=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,maxScroll=scrollHeight-this.$scrollElement.height(),offsets=this.offsets,targets=this.targets,activeTarget=this.activeTarget,i
if(scrollTop>=maxScroll){return activeTarget!=(i=targets.last()[0])&&this.activate(i)}
for(i=offsets.length;i--;){activeTarget!=targets[i]&&scrollTop>=offsets[i]&&(!offsets[i+1]||scrollTop<=offsets[i+1])&&this.activate(targets[i])}},activate:function(target){var active,selector
this.activeTarget=target
$(this.selector).parent('.active').removeClass('active')
selector=this.selector
+'[data-target="'+target+'"],'
+this.selector+'[href="'+target+'"]'
active=$(selector).parent('li').addClass('active')
if(active.parent('.dropdown-menu').length){active=active.closest('li.dropdown').addClass('active')}
active.trigger('activate')}}
var old=$.fn.scrollspy
$.fn.scrollspy=function(option){return this.each(function(){var $this=$(this),data=$this.data('scrollspy'),options=typeof option=='object'&&option
if(!data)
$this.data('scrollspy',(data=new ScrollSpy(this,options)))
if(typeof option=='string')
data[option]()})}
$.fn.scrollspy.Constructor=ScrollSpy
$.fn.scrollspy.defaults={offset:30}
$.fn.scrollspy.noConflict=function(){$.fn.scrollspy=old
return this}
$(window).on('load',function(){$('[data-spy="scroll"]').each(function(){var $spy=$(this)
$spy.scrollspy($spy.data())})})}(window.jQuery);

// @fileRef formValidation.js 
(function($){$.fn.validate=function(options){var settings=$.extend({showTooltip:true,tooltipPosition:'right',ajax:false,ajaxText:"Odesílám...",validateMsg:"Validuji formulář...",defaultSubmitOnSuccess:"Uloženo",customValidation:undefined,onShowError:undefined,onBeforeCheck:undefined,onSuccess:undefined,onFail:undefined},options);var ruleCache={container:{},add:function(inputName,ruleName,value,message,ref,arg)
{if(typeof this.container[inputName]==='undefined')
this.container[inputName]={};ruleName=ruleName.substring(10).toLowerCase();this.container[inputName][ruleName]={value:value,message:message,ref:ref,arg:arg};}};var validator={filled:function(value){return value==''?false:true;},same:function(value,ref,arg){return $('input[name='+arg+']').val()==value?true:false;},number:function(value){for(var i=0;i<value.length;++i){var new_key=value.charAt(i);if(((new_key<"0")||(new_key>"9"))&&!(new_key==""))
return false;}
return true;},email:function(value){if(value=='')
return true;var re=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return re.test(value);},checked:function(value,ref){return ref.attr('checked')===undefined?false:true;},phone:function(value){for(var i=0;i<value.length;++i)
{var new_key=value.charAt(i);if(i==0&&new_key==="+")
continue;if(((new_key<"0")||(new_key>"9"))&&!(new_key===""))
return false;}
return true;},min_chars:function(value,ref,arg){return value.length>=arg?true:false;},ajax:function(value,ref,arg)
{return $.ajax({url:arg[0],data:{value:value},type:"post",timeout:8000});}};if(settings.customValidation!==undefined)
validator=jQuery.extend(validator,settings.customValidation);var methods={clear:function(){ruleCache.container={};},stringFilter:function(string,value)
{if(string.substring(0,10)!='validation')
return false;string=string.substring(10).toLowerCase();var message=(value!=null&&value!="")?value.split('::'):'';arg=message.length>1?message.splice(1,message.length):undefined;return[message[0],arg];},formPrepare:function(e){var notEmpty=false;var form_objects=e.find(':input').get();for(i=0;i<form_objects.length;i++)
{var e=$(form_objects[i]);var ename=e.attr('name');var data=e.data();if(jQuery.isEmptyObject(data))
continue;jQuery.each(data,function(name,value){var result=methods.stringFilter(name,value);if(result!==false)
{notEmpty=true;ruleCache.add(ename,name,e.val(),result[0],e,result[1]);}});}
return notEmpty;},validate:function(formElement){var cache=ruleCache.container;formElement.addClass('val-disabled');methods.changeSubmitText(settings.validateMsg)
var $deferreds=[];for(var rules in cache){for(var rule in cache[rules]){$deferreds.push(methods.validateSingleElement(rule,cache[rules][rule]));}}
$finalDeferred=$.Deferred();$.when.apply($,$deferreds).then(function(){$finalDeferred.resolve();},function(e,a){$finalDeferred.reject();}).always(function(){formElement.removeClass('val-disabled');methods.changeSubmitText('');});return $finalDeferred.promise();},validateSingleElement:function(ruleName,ruleArgs){var drd=new $.Deferred();if(validator[ruleName]===undefined)
{console.error('Validation rule "'+ruleName+'" is not defined!');return drd.resolve();}else{var status=validator[ruleName](ruleArgs.value,ruleArgs.ref,ruleArgs.arg);if(status.promise)
{methods.addLoading(ruleArgs.ref);status.always(function(){methods.removeLoading(ruleArgs.ref);});return status.promise();}else{if(!status)
{methods.showError(ruleArgs.ref,ruleArgs.message);return drd.reject(ruleArgs.ref);}
return drd.resolve();}}},showError:function(element,message)
{if(settings.onShowError!==undefined)
{settings.onShowError(element,message);return;}
var p=element.closest('.form-group');p.addClass('has-error has-feedback');element.after('<span class="fa fa-warning form-control-feedback" data-toggle="tooltip" data-placement="'+settings.tooltipPosition+'" title="'+message+'"></span>');if($.fn.tooltip&&settings.showTooltip)
{p.find('.form-control-feedback').tooltip('show');}},addLoading:function(element)
{var p=element.closest('.form-group');p.addClass('has-feedback');element.after('<span class="fa fa-spinner fa-spin form-control-feedback"></span>');},removeLoading:function(element)
{element.closest('.form-group').find('.fa-spinner').remove();},removeErrors:function(formElement)
{var e=formElement.find('.has-error').removeClass('has-error has-feedback').find('span.form-control-feedback')
if($.fn.tooltip)
{e.tooltip('destroy');e.remove();}},changeSubmitText:function(text)
{methods.submitEl.val(text?text:methods.submitEl.data('prevVal'));}}
var process=function(el)
{methods.removeErrors(el);if(!methods.formPrepare(el))
{submit(el);return;}
if(typeof settings.onBeforeCheck==='function')
if(settings.onBeforeCheck(ruleCache.container)===false)
return false;methods.submitEl=el.find('input[type="submit"]');methods.submitEl.data('prevVal',methods.submitEl.val());var dfd=methods.validate(el);dfd.done(function(){submit(el);}).fail(function(){if(settings.onFail!==undefined)
settings.onFail();});console.timeEnd('init');}
var submit=function(el)
{if(settings.ajax)
{$.ajax({url:el.attr("action"),type:"post",data:el.serialize(),beforeSend:function(){methods.changeSubmitText(settings.ajaxText);el.addClass('val-disabled');},success:function(data)
{try{methods.changeSubmitText(settings.defaultSubmitOnSuccess);if(settings.onSuccess!==undefined)
{data=jQuery.parseJSON(data);if(settings.onSuccess(data)===false)
return false;}
el.trigger('reset');}
catch(e){}},error:function(e,text){methods.changeSubmitText('');if(settings.onFail!==undefined)
settings.onFail(text);},complete:function(){el.removeClass('val-disabled');}});}else{el.off('validationSubmit').data('canSubmit',true);if(settings.onSuccess!==undefined)
{if(settings.onSuccess()===false)
return false;}
el.submit();}}
this.each(function(){var t=$(this);console.log(t);t.on("validationSubmit",function(e){if(t.hasClass('val-disabled'))
return false;e.preventDefault();process(t);}).on('submit',function(e){t.trigger('validationSubmit');if(!t.data('canSubmit'))
e.preventDefault();});});return this;};}(jQuery));

// @fileRef respond.min.js 
!function(a){"use strict";a.matchMedia=a.matchMedia||function(a){var b,c=a.documentElement,d=c.firstElementChild||c.firstChild,e=a.createElement("body"),f=a.createElement("div");return f.id="mq-test-1",f.style.cssText="position:absolute;top:-100em",e.style.background="none",e.appendChild(f),function(a){return f.innerHTML='&shy;<style media="'+a+'"> #mq-test-1 { width: 42px; }</style>',c.insertBefore(e,d),b=42===f.offsetWidth,c.removeChild(e),{matches:b,media:a}}}(a.document)}(this),function(a){"use strict";function b(){v(!0)}var c={};a.respond=c,c.update=function(){};var d=[],e=function(){var b=!1;try{b=new a.XMLHttpRequest}catch(c){b=new a.ActiveXObject("Microsoft.XMLHTTP")}return function(){return b}}(),f=function(a,b){var c=e();c&&(c.open("GET",a,!0),c.onreadystatechange=function(){4!==c.readyState||200!==c.status&&304!==c.status||b(c.responseText)},4!==c.readyState&&c.send(null))},g=function(a){return a.replace(c.regex.minmaxwh,"").match(c.regex.other)};if(c.ajax=f,c.queue=d,c.unsupportedmq=g,c.regex={media:/@media[^\{]+\{([^\{\}]*\{[^\}\{]*\})+/gi,keyframes:/@(?:\-(?:o|moz|webkit)\-)?keyframes[^\{]+\{(?:[^\{\}]*\{[^\}\{]*\})+[^\}]*\}/gi,comments:/\/\*[^*]*\*+([^/][^*]*\*+)*\//gi,urls:/(url\()['"]?([^\/\)'"][^:\)'"]+)['"]?(\))/g,findStyles:/@media *([^\{]+)\{([\S\s]+?)$/,only:/(only\s+)?([a-zA-Z]+)\s?/,minw:/\(\s*min\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/,maxw:/\(\s*max\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/,minmaxwh:/\(\s*m(in|ax)\-(height|width)\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/gi,other:/\([^\)]*\)/g},c.mediaQueriesSupported=a.matchMedia&&null!==a.matchMedia("only all")&&a.matchMedia("only all").matches,!c.mediaQueriesSupported){var h,i,j,k=a.document,l=k.documentElement,m=[],n=[],o=[],p={},q=30,r=k.getElementsByTagName("head")[0]||l,s=k.getElementsByTagName("base")[0],t=r.getElementsByTagName("link"),u=function(){var a,b=k.createElement("div"),c=k.body,d=l.style.fontSize,e=c&&c.style.fontSize,f=!1;return b.style.cssText="position:absolute;font-size:1em;width:1em",c||(c=f=k.createElement("body"),c.style.background="none"),l.style.fontSize="100%",c.style.fontSize="100%",c.appendChild(b),f&&l.insertBefore(c,l.firstChild),a=b.offsetWidth,f?l.removeChild(c):c.removeChild(b),l.style.fontSize=d,e&&(c.style.fontSize=e),a=j=parseFloat(a)},v=function(b){var c="clientWidth",d=l[c],e="CSS1Compat"===k.compatMode&&d||k.body[c]||d,f={},g=t[t.length-1],p=(new Date).getTime();if(b&&h&&q>p-h)return a.clearTimeout(i),i=a.setTimeout(v,q),void 0;h=p;for(var s in m)if(m.hasOwnProperty(s)){var w=m[s],x=w.minw,y=w.maxw,z=null===x,A=null===y,B="em";x&&(x=parseFloat(x)*(x.indexOf(B)>-1?j||u():1)),y&&(y=parseFloat(y)*(y.indexOf(B)>-1?j||u():1)),w.hasquery&&(z&&A||!(z||e>=x)||!(A||y>=e))||(f[w.media]||(f[w.media]=[]),f[w.media].push(n[w.rules]))}for(var C in o)o.hasOwnProperty(C)&&o[C]&&o[C].parentNode===r&&r.removeChild(o[C]);o.length=0;for(var D in f)if(f.hasOwnProperty(D)){var E=k.createElement("style"),F=f[D].join("\n");E.type="text/css",E.media=D,r.insertBefore(E,g.nextSibling),E.styleSheet?E.styleSheet.cssText=F:E.appendChild(k.createTextNode(F)),o.push(E)}},w=function(a,b,d){var e=a.replace(c.regex.comments,"").replace(c.regex.keyframes,"").match(c.regex.media),f=e&&e.length||0;b=b.substring(0,b.lastIndexOf("/"));var h=function(a){return a.replace(c.regex.urls,"$1"+b+"$2$3")},i=!f&&d;b.length&&(b+="/"),i&&(f=1);for(var j=0;f>j;j++){var k,l,o,p;i?(k=d,n.push(h(a))):(k=e[j].match(c.regex.findStyles)&&RegExp.$1,n.push(RegExp.$2&&h(RegExp.$2))),o=k.split(","),p=o.length;for(var q=0;p>q;q++)l=o[q],g(l)||m.push({media:l.split("(")[0].match(c.regex.only)&&RegExp.$2||"all",rules:n.length-1,hasquery:l.indexOf("(")>-1,minw:l.match(c.regex.minw)&&parseFloat(RegExp.$1)+(RegExp.$2||""),maxw:l.match(c.regex.maxw)&&parseFloat(RegExp.$1)+(RegExp.$2||"")})}v()},x=function(){if(d.length){var b=d.shift();f(b.href,function(c){w(c,b.href,b.media),p[b.href]=!0,a.setTimeout(function(){x()},0)})}},y=function(){for(var b=0;b<t.length;b++){var c=t[b],e=c.href,f=c.media,g=c.rel&&"stylesheet"===c.rel.toLowerCase();e&&g&&!p[e]&&(c.styleSheet&&c.styleSheet.rawCssText?(w(c.styleSheet.rawCssText,e,f),p[e]=!0):(!/^([a-zA-Z:]*\/\/)/.test(e)&&!s||e.replace(RegExp.$1,"").split("/")[0]===a.location.host)&&("//"===e.substring(0,2)&&(e=a.location.protocol+e),d.push({href:e,media:f})))}x()};y(),c.update=y,c.getEmValue=u,a.addEventListener?a.addEventListener("resize",b,!1):a.attachEvent&&a.attachEvent("onresize",b)}}(this);
