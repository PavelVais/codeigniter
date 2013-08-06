/*! jQuery UI - v1.10.3 - 2013-05-06
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.widget.js, jquery.ui.mouse.js, jquery.ui.position.js
* Copyright 2013 jQuery Foundation and other contributors Licensed MIT */

(function(e,t){function i(t,i){var a,n,r,o=t.nodeName.toLowerCase();return"area"===o?(a=t.parentNode,n=a.name,t.href&&n&&"map"===a.nodeName.toLowerCase()?(r=e("img[usemap=#"+n+"]")[0],!!r&&s(r)):!1):(/input|select|textarea|button|object/.test(o)?!t.disabled:"a"===o?t.href||i:i)&&s(t)}function s(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}var a=0,n=/^ui-id-\d+$/;e.ui=e.ui||{},e.extend(e.ui,{version:"1.10.3",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),scrollParent:function(){var t;return t=e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(document):t},zIndex:function(i){if(i!==t)return this.css("zIndex",i);if(this.length)for(var s,a,n=e(this[0]);n.length&&n[0]!==document;){if(s=n.css("position"),("absolute"===s||"relative"===s||"fixed"===s)&&(a=parseInt(n.css("zIndex"),10),!isNaN(a)&&0!==a))return a;n=n.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++a)})},removeUniqueId:function(){return this.each(function(){n.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(t){return i(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var s=e.attr(t,"tabindex"),a=isNaN(s);return(a||s>=0)&&i(t,!a)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(i,s){function a(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===s?["Left","Right"]:["Top","Bottom"],r=s.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+s]=function(i){return i===t?o["inner"+s].call(this):this.each(function(){e(this).css(r,a(this,i)+"px")})},e.fn["outer"+s]=function(t,i){return"number"!=typeof t?o["outer"+s].call(this,t):this.each(function(){e(this).css(r,a(this,t,!0,i)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.support.selectstart="onselectstart"in document.createElement("div"),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),e.extend(e.ui,{plugin:{add:function(t,i,s){var a,n=e.ui[t].prototype;for(a in s)n.plugins[a]=n.plugins[a]||[],n.plugins[a].push([i,s[a]])},call:function(e,t,i){var s,a=e.plugins[t];if(a&&e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType)for(s=0;a.length>s;s++)e.options[a[s][0]]&&a[s][1].apply(e.element,i)}},hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var s=i&&"left"===i?"scrollLeft":"scrollTop",a=!1;return t[s]>0?!0:(t[s]=1,a=t[s]>0,t[s]=0,a)}})})(jQuery);(function(e,t){var i=0,s=Array.prototype.slice,n=e.cleanData;e.cleanData=function(t){for(var i,s=0;null!=(i=t[s]);s++)try{e(i).triggerHandler("remove")}catch(a){}n(t)},e.widget=function(i,s,n){var a,r,o,h,l={},u=i.split(".")[0];i=i.split(".")[1],a=u+"-"+i,n||(n=s,s=e.Widget),e.expr[":"][a.toLowerCase()]=function(t){return!!e.data(t,a)},e[u]=e[u]||{},r=e[u][i],o=e[u][i]=function(e,i){return this._createWidget?(arguments.length&&this._createWidget(e,i),t):new o(e,i)},e.extend(o,r,{version:n.version,_proto:e.extend({},n),_childConstructors:[]}),h=new s,h.options=e.widget.extend({},h.options),e.each(n,function(i,n){return e.isFunction(n)?(l[i]=function(){var e=function(){return s.prototype[i].apply(this,arguments)},t=function(e){return s.prototype[i].apply(this,e)};return function(){var i,s=this._super,a=this._superApply;return this._super=e,this._superApply=t,i=n.apply(this,arguments),this._super=s,this._superApply=a,i}}(),t):(l[i]=n,t)}),o.prototype=e.widget.extend(h,{widgetEventPrefix:r?h.widgetEventPrefix:i},l,{constructor:o,namespace:u,widgetName:i,widgetFullName:a}),r?(e.each(r._childConstructors,function(t,i){var s=i.prototype;e.widget(s.namespace+"."+s.widgetName,o,i._proto)}),delete r._childConstructors):s._childConstructors.push(o),e.widget.bridge(i,o)},e.widget.extend=function(i){for(var n,a,r=s.call(arguments,1),o=0,h=r.length;h>o;o++)for(n in r[o])a=r[o][n],r[o].hasOwnProperty(n)&&a!==t&&(i[n]=e.isPlainObject(a)?e.isPlainObject(i[n])?e.widget.extend({},i[n],a):e.widget.extend({},a):a);return i},e.widget.bridge=function(i,n){var a=n.prototype.widgetFullName||i;e.fn[i]=function(r){var o="string"==typeof r,h=s.call(arguments,1),l=this;return r=!o&&h.length?e.widget.extend.apply(null,[r].concat(h)):r,o?this.each(function(){var s,n=e.data(this,a);return n?e.isFunction(n[r])&&"_"!==r.charAt(0)?(s=n[r].apply(n,h),s!==n&&s!==t?(l=s&&s.jquery?l.pushStack(s.get()):s,!1):t):e.error("no such method '"+r+"' for "+i+" widget instance"):e.error("cannot call methods on "+i+" prior to initialization; "+"attempted to call method '"+r+"'")}):this.each(function(){var t=e.data(this,a);t?t.option(r||{})._init():e.data(this,a,new n(r,this))}),l}},e.Widget=function(){},e.Widget._childConstructors=[],e.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(t,s){s=e(s||this.defaultElement||this)[0],this.element=e(s),this.uuid=i++,this.eventNamespace="."+this.widgetName+this.uuid,this.options=e.widget.extend({},this.options,this._getCreateOptions(),t),this.bindings=e(),this.hoverable=e(),this.focusable=e(),s!==this&&(e.data(s,this.widgetFullName,this),this._on(!0,this.element,{remove:function(e){e.target===s&&this.destroy()}}),this.document=e(s.style?s.ownerDocument:s.document||s),this.window=e(this.document[0].defaultView||this.document[0].parentWindow)),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:e.noop,_getCreateEventData:e.noop,_create:e.noop,_init:e.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetName).removeData(this.widgetFullName).removeData(e.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled "+"ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:e.noop,widget:function(){return this.element},option:function(i,s){var n,a,r,o=i;if(0===arguments.length)return e.widget.extend({},this.options);if("string"==typeof i)if(o={},n=i.split("."),i=n.shift(),n.length){for(a=o[i]=e.widget.extend({},this.options[i]),r=0;n.length-1>r;r++)a[n[r]]=a[n[r]]||{},a=a[n[r]];if(i=n.pop(),s===t)return a[i]===t?null:a[i];a[i]=s}else{if(s===t)return this.options[i]===t?null:this.options[i];o[i]=s}return this._setOptions(o),this},_setOptions:function(e){var t;for(t in e)this._setOption(t,e[t]);return this},_setOption:function(e,t){return this.options[e]=t,"disabled"===e&&(this.widget().toggleClass(this.widgetFullName+"-disabled ui-state-disabled",!!t).attr("aria-disabled",t),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_on:function(i,s,n){var a,r=this;"boolean"!=typeof i&&(n=s,s=i,i=!1),n?(s=a=e(s),this.bindings=this.bindings.add(s)):(n=s,s=this.element,a=this.widget()),e.each(n,function(n,o){function h(){return i||r.options.disabled!==!0&&!e(this).hasClass("ui-state-disabled")?("string"==typeof o?r[o]:o).apply(r,arguments):t}"string"!=typeof o&&(h.guid=o.guid=o.guid||h.guid||e.guid++);var l=n.match(/^(\w+)\s*(.*)$/),u=l[1]+r.eventNamespace,c=l[2];c?a.delegate(c,u,h):s.bind(u,h)})},_off:function(e,t){t=(t||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,e.unbind(t).undelegate(t)},_delay:function(e,t){function i(){return("string"==typeof e?s[e]:e).apply(s,arguments)}var s=this;return setTimeout(i,t||0)},_hoverable:function(t){this.hoverable=this.hoverable.add(t),this._on(t,{mouseenter:function(t){e(t.currentTarget).addClass("ui-state-hover")},mouseleave:function(t){e(t.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(t){this.focusable=this.focusable.add(t),this._on(t,{focusin:function(t){e(t.currentTarget).addClass("ui-state-focus")},focusout:function(t){e(t.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(t,i,s){var n,a,r=this.options[t];if(s=s||{},i=e.Event(i),i.type=(t===this.widgetEventPrefix?t:this.widgetEventPrefix+t).toLowerCase(),i.target=this.element[0],a=i.originalEvent)for(n in a)n in i||(i[n]=a[n]);return this.element.trigger(i,s),!(e.isFunction(r)&&r.apply(this.element[0],[i].concat(s))===!1||i.isDefaultPrevented())}},e.each({show:"fadeIn",hide:"fadeOut"},function(t,i){e.Widget.prototype["_"+t]=function(s,n,a){"string"==typeof n&&(n={effect:n});var r,o=n?n===!0||"number"==typeof n?i:n.effect||i:t;n=n||{},"number"==typeof n&&(n={duration:n}),r=!e.isEmptyObject(n),n.complete=a,n.delay&&s.delay(n.delay),r&&e.effects&&e.effects.effect[o]?s[t](n):o!==t&&s[o]?s[o](n.duration,n.easing,a):s.queue(function(i){e(this)[t](),a&&a.call(s[0]),i()})}})})(jQuery);(function(e){var t=!1;e(document).mouseup(function(){t=!1}),e.widget("ui.mouse",{version:"1.10.3",options:{cancel:"input,textarea,button,select,option",distance:1,delay:0},_mouseInit:function(){var t=this;this.element.bind("mousedown."+this.widgetName,function(e){return t._mouseDown(e)}).bind("click."+this.widgetName,function(i){return!0===e.data(i.target,t.widgetName+".preventClickEvent")?(e.removeData(i.target,t.widgetName+".preventClickEvent"),i.stopImmediatePropagation(),!1):undefined}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),this._mouseMoveDelegate&&e(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(i){if(!t){this._mouseStarted&&this._mouseUp(i),this._mouseDownEvent=i;var s=this,n=1===i.which,a="string"==typeof this.options.cancel&&i.target.nodeName?e(i.target).closest(this.options.cancel).length:!1;return n&&!a&&this._mouseCapture(i)?(this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){s.mouseDelayMet=!0},this.options.delay)),this._mouseDistanceMet(i)&&this._mouseDelayMet(i)&&(this._mouseStarted=this._mouseStart(i)!==!1,!this._mouseStarted)?(i.preventDefault(),!0):(!0===e.data(i.target,this.widgetName+".preventClickEvent")&&e.removeData(i.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(e){return s._mouseMove(e)},this._mouseUpDelegate=function(e){return s._mouseUp(e)},e(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),i.preventDefault(),t=!0,!0)):!0}},_mouseMove:function(t){return e.ui.ie&&(!document.documentMode||9>document.documentMode)&&!t.button?this._mouseUp(t):this._mouseStarted?(this._mouseDrag(t),t.preventDefault()):(this._mouseDistanceMet(t)&&this._mouseDelayMet(t)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,t)!==!1,this._mouseStarted?this._mouseDrag(t):this._mouseUp(t)),!this._mouseStarted)},_mouseUp:function(t){return e(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,t.target===this._mouseDownEvent.target&&e.data(t.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(t)),!1},_mouseDistanceMet:function(e){return Math.max(Math.abs(this._mouseDownEvent.pageX-e.pageX),Math.abs(this._mouseDownEvent.pageY-e.pageY))>=this.options.distance},_mouseDelayMet:function(){return this.mouseDelayMet},_mouseStart:function(){},_mouseDrag:function(){},_mouseStop:function(){},_mouseCapture:function(){return!0}})})(jQuery);(function(t,e){function i(t,e,i){return[parseFloat(t[0])*(p.test(t[0])?e/100:1),parseFloat(t[1])*(p.test(t[1])?i/100:1)]}function s(e,i){return parseInt(t.css(e,i),10)||0}function n(e){var i=e[0];return 9===i.nodeType?{width:e.width(),height:e.height(),offset:{top:0,left:0}}:t.isWindow(i)?{width:e.width(),height:e.height(),offset:{top:e.scrollTop(),left:e.scrollLeft()}}:i.preventDefault?{width:0,height:0,offset:{top:i.pageY,left:i.pageX}}:{width:e.outerWidth(),height:e.outerHeight(),offset:e.offset()}}t.ui=t.ui||{};var a,o=Math.max,r=Math.abs,h=Math.round,l=/left|center|right/,c=/top|center|bottom/,u=/[\+\-]\d+(\.[\d]+)?%?/,d=/^\w+/,p=/%$/,f=t.fn.position;t.position={scrollbarWidth:function(){if(a!==e)return a;var i,s,n=t("<div style='display:block;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),o=n.children()[0];return t("body").append(n),i=o.offsetWidth,n.css("overflow","scroll"),s=o.offsetWidth,i===s&&(s=n[0].clientWidth),n.remove(),a=i-s},getScrollInfo:function(e){var i=e.isWindow?"":e.element.css("overflow-x"),s=e.isWindow?"":e.element.css("overflow-y"),n="scroll"===i||"auto"===i&&e.width<e.element[0].scrollWidth,a="scroll"===s||"auto"===s&&e.height<e.element[0].scrollHeight;return{width:a?t.position.scrollbarWidth():0,height:n?t.position.scrollbarWidth():0}},getWithinInfo:function(e){var i=t(e||window),s=t.isWindow(i[0]);return{element:i,isWindow:s,offset:i.offset()||{left:0,top:0},scrollLeft:i.scrollLeft(),scrollTop:i.scrollTop(),width:s?i.width():i.outerWidth(),height:s?i.height():i.outerHeight()}}},t.fn.position=function(e){if(!e||!e.of)return f.apply(this,arguments);e=t.extend({},e);var a,p,m,g,v,b,_=t(e.of),y=t.position.getWithinInfo(e.within),w=t.position.getScrollInfo(y),x=(e.collision||"flip").split(" "),k={};return b=n(_),_[0].preventDefault&&(e.at="left top"),p=b.width,m=b.height,g=b.offset,v=t.extend({},g),t.each(["my","at"],function(){var t,i,s=(e[this]||"").split(" ");1===s.length&&(s=l.test(s[0])?s.concat(["center"]):c.test(s[0])?["center"].concat(s):["center","center"]),s[0]=l.test(s[0])?s[0]:"center",s[1]=c.test(s[1])?s[1]:"center",t=u.exec(s[0]),i=u.exec(s[1]),k[this]=[t?t[0]:0,i?i[0]:0],e[this]=[d.exec(s[0])[0],d.exec(s[1])[0]]}),1===x.length&&(x[1]=x[0]),"right"===e.at[0]?v.left+=p:"center"===e.at[0]&&(v.left+=p/2),"bottom"===e.at[1]?v.top+=m:"center"===e.at[1]&&(v.top+=m/2),a=i(k.at,p,m),v.left+=a[0],v.top+=a[1],this.each(function(){var n,l,c=t(this),u=c.outerWidth(),d=c.outerHeight(),f=s(this,"marginLeft"),b=s(this,"marginTop"),D=u+f+s(this,"marginRight")+w.width,T=d+b+s(this,"marginBottom")+w.height,C=t.extend({},v),M=i(k.my,c.outerWidth(),c.outerHeight());"right"===e.my[0]?C.left-=u:"center"===e.my[0]&&(C.left-=u/2),"bottom"===e.my[1]?C.top-=d:"center"===e.my[1]&&(C.top-=d/2),C.left+=M[0],C.top+=M[1],t.support.offsetFractions||(C.left=h(C.left),C.top=h(C.top)),n={marginLeft:f,marginTop:b},t.each(["left","top"],function(i,s){t.ui.position[x[i]]&&t.ui.position[x[i]][s](C,{targetWidth:p,targetHeight:m,elemWidth:u,elemHeight:d,collisionPosition:n,collisionWidth:D,collisionHeight:T,offset:[a[0]+M[0],a[1]+M[1]],my:e.my,at:e.at,within:y,elem:c})}),e.using&&(l=function(t){var i=g.left-C.left,s=i+p-u,n=g.top-C.top,a=n+m-d,h={target:{element:_,left:g.left,top:g.top,width:p,height:m},element:{element:c,left:C.left,top:C.top,width:u,height:d},horizontal:0>s?"left":i>0?"right":"center",vertical:0>a?"top":n>0?"bottom":"middle"};u>p&&p>r(i+s)&&(h.horizontal="center"),d>m&&m>r(n+a)&&(h.vertical="middle"),h.important=o(r(i),r(s))>o(r(n),r(a))?"horizontal":"vertical",e.using.call(this,t,h)}),c.offset(t.extend(C,{using:l}))})},t.ui.position={fit:{left:function(t,e){var i,s=e.within,n=s.isWindow?s.scrollLeft:s.offset.left,a=s.width,r=t.left-e.collisionPosition.marginLeft,h=n-r,l=r+e.collisionWidth-a-n;e.collisionWidth>a?h>0&&0>=l?(i=t.left+h+e.collisionWidth-a-n,t.left+=h-i):t.left=l>0&&0>=h?n:h>l?n+a-e.collisionWidth:n:h>0?t.left+=h:l>0?t.left-=l:t.left=o(t.left-r,t.left)},top:function(t,e){var i,s=e.within,n=s.isWindow?s.scrollTop:s.offset.top,a=e.within.height,r=t.top-e.collisionPosition.marginTop,h=n-r,l=r+e.collisionHeight-a-n;e.collisionHeight>a?h>0&&0>=l?(i=t.top+h+e.collisionHeight-a-n,t.top+=h-i):t.top=l>0&&0>=h?n:h>l?n+a-e.collisionHeight:n:h>0?t.top+=h:l>0?t.top-=l:t.top=o(t.top-r,t.top)}},flip:{left:function(t,e){var i,s,n=e.within,a=n.offset.left+n.scrollLeft,o=n.width,h=n.isWindow?n.scrollLeft:n.offset.left,l=t.left-e.collisionPosition.marginLeft,c=l-h,u=l+e.collisionWidth-o-h,d="left"===e.my[0]?-e.elemWidth:"right"===e.my[0]?e.elemWidth:0,p="left"===e.at[0]?e.targetWidth:"right"===e.at[0]?-e.targetWidth:0,f=-2*e.offset[0];0>c?(i=t.left+d+p+f+e.collisionWidth-o-a,(0>i||r(c)>i)&&(t.left+=d+p+f)):u>0&&(s=t.left-e.collisionPosition.marginLeft+d+p+f-h,(s>0||u>r(s))&&(t.left+=d+p+f))},top:function(t,e){var i,s,n=e.within,a=n.offset.top+n.scrollTop,o=n.height,h=n.isWindow?n.scrollTop:n.offset.top,l=t.top-e.collisionPosition.marginTop,c=l-h,u=l+e.collisionHeight-o-h,d="top"===e.my[1],p=d?-e.elemHeight:"bottom"===e.my[1]?e.elemHeight:0,f="top"===e.at[1]?e.targetHeight:"bottom"===e.at[1]?-e.targetHeight:0,m=-2*e.offset[1];0>c?(s=t.top+p+f+m+e.collisionHeight-o-a,t.top+p+f+m>c&&(0>s||r(c)>s)&&(t.top+=p+f+m)):u>0&&(i=t.top-e.collisionPosition.marginTop+p+f+m-h,t.top+p+f+m>u&&(i>0||u>r(i))&&(t.top+=p+f+m))}},flipfit:{left:function(){t.ui.position.flip.left.apply(this,arguments),t.ui.position.fit.left.apply(this,arguments)},top:function(){t.ui.position.flip.top.apply(this,arguments),t.ui.position.fit.top.apply(this,arguments)}}},function(){var e,i,s,n,a,o=document.getElementsByTagName("body")[0],r=document.createElement("div");e=document.createElement(o?"div":"body"),s={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},o&&t.extend(s,{position:"absolute",left:"-1000px",top:"-1000px"});for(a in s)e.style[a]=s[a];e.appendChild(r),i=o||document.documentElement,i.insertBefore(e,i.firstChild),r.style.cssText="position: absolute; left: 10.7432222px;",n=t(r).offset().left,t.support.offsetFractions=n>10&&11>n,e.innerHTML="",i.removeChild(e)}()})(jQuery);
/*! jQuery UI - v1.10.3 - 2013-05-06
* http://jqueryui.com
* Includes: jquery.ui.core.js, jquery.ui.datepicker.js
* Copyright 2013 jQuery Foundation and other contributors Licensed MIT */

(function(e,t){function i(t,i){var a,n,r,o=t.nodeName.toLowerCase();return"area"===o?(a=t.parentNode,n=a.name,t.href&&n&&"map"===a.nodeName.toLowerCase()?(r=e("img[usemap=#"+n+"]")[0],!!r&&s(r)):!1):(/input|select|textarea|button|object/.test(o)?!t.disabled:"a"===o?t.href||i:i)&&s(t)}function s(t){return e.expr.filters.visible(t)&&!e(t).parents().addBack().filter(function(){return"hidden"===e.css(this,"visibility")}).length}var a=0,n=/^ui-id-\d+$/;e.ui=e.ui||{},e.extend(e.ui,{version:"1.10.3",keyCode:{BACKSPACE:8,COMMA:188,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,LEFT:37,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SPACE:32,TAB:9,UP:38}}),e.fn.extend({focus:function(t){return function(i,s){return"number"==typeof i?this.each(function(){var t=this;setTimeout(function(){e(t).focus(),s&&s.call(t)},i)}):t.apply(this,arguments)}}(e.fn.focus),scrollParent:function(){var t;return t=e.ui.ie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(e.css(this,"position"))&&/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(e.css(this,"overflow")+e.css(this,"overflow-y")+e.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!t.length?e(document):t},zIndex:function(i){if(i!==t)return this.css("zIndex",i);if(this.length)for(var s,a,n=e(this[0]);n.length&&n[0]!==document;){if(s=n.css("position"),("absolute"===s||"relative"===s||"fixed"===s)&&(a=parseInt(n.css("zIndex"),10),!isNaN(a)&&0!==a))return a;n=n.parent()}return 0},uniqueId:function(){return this.each(function(){this.id||(this.id="ui-id-"+ ++a)})},removeUniqueId:function(){return this.each(function(){n.test(this.id)&&e(this).removeAttr("id")})}}),e.extend(e.expr[":"],{data:e.expr.createPseudo?e.expr.createPseudo(function(t){return function(i){return!!e.data(i,t)}}):function(t,i,s){return!!e.data(t,s[3])},focusable:function(t){return i(t,!isNaN(e.attr(t,"tabindex")))},tabbable:function(t){var s=e.attr(t,"tabindex"),a=isNaN(s);return(a||s>=0)&&i(t,!a)}}),e("<a>").outerWidth(1).jquery||e.each(["Width","Height"],function(i,s){function a(t,i,s,a){return e.each(n,function(){i-=parseFloat(e.css(t,"padding"+this))||0,s&&(i-=parseFloat(e.css(t,"border"+this+"Width"))||0),a&&(i-=parseFloat(e.css(t,"margin"+this))||0)}),i}var n="Width"===s?["Left","Right"]:["Top","Bottom"],r=s.toLowerCase(),o={innerWidth:e.fn.innerWidth,innerHeight:e.fn.innerHeight,outerWidth:e.fn.outerWidth,outerHeight:e.fn.outerHeight};e.fn["inner"+s]=function(i){return i===t?o["inner"+s].call(this):this.each(function(){e(this).css(r,a(this,i)+"px")})},e.fn["outer"+s]=function(t,i){return"number"!=typeof t?o["outer"+s].call(this,t):this.each(function(){e(this).css(r,a(this,t,!0,i)+"px")})}}),e.fn.addBack||(e.fn.addBack=function(e){return this.add(null==e?this.prevObject:this.prevObject.filter(e))}),e("<a>").data("a-b","a").removeData("a-b").data("a-b")&&(e.fn.removeData=function(t){return function(i){return arguments.length?t.call(this,e.camelCase(i)):t.call(this)}}(e.fn.removeData)),e.ui.ie=!!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase()),e.support.selectstart="onselectstart"in document.createElement("div"),e.fn.extend({disableSelection:function(){return this.bind((e.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(e){e.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),e.extend(e.ui,{plugin:{add:function(t,i,s){var a,n=e.ui[t].prototype;for(a in s)n.plugins[a]=n.plugins[a]||[],n.plugins[a].push([i,s[a]])},call:function(e,t,i){var s,a=e.plugins[t];if(a&&e.element[0].parentNode&&11!==e.element[0].parentNode.nodeType)for(s=0;a.length>s;s++)e.options[a[s][0]]&&a[s][1].apply(e.element,i)}},hasScroll:function(t,i){if("hidden"===e(t).css("overflow"))return!1;var s=i&&"left"===i?"scrollLeft":"scrollTop",a=!1;return t[s]>0?!0:(t[s]=1,a=t[s]>0,t[s]=0,a)}})})(jQuery);(function(t,e){function i(){this._curInst=null,this._keyEvent=!1,this._disabledInputs=[],this._datepickerShowing=!1,this._inDialog=!1,this._mainDivId="ui-datepicker-div",this._inlineClass="ui-datepicker-inline",this._appendClass="ui-datepicker-append",this._triggerClass="ui-datepicker-trigger",this._dialogClass="ui-datepicker-dialog",this._disableClass="ui-datepicker-disabled",this._unselectableClass="ui-datepicker-unselectable",this._currentClass="ui-datepicker-current-day",this._dayOverClass="ui-datepicker-days-cell-over",this.regional=[],this.regional[""]={closeText:"Done",prevText:"Prev",nextText:"Next",currentText:"Today",monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],dayNamesMin:["Su","Mo","Tu","We","Th","Fr","Sa"],weekHeader:"Wk",dateFormat:"mm/dd/yy",firstDay:0,isRTL:!1,showMonthAfterYear:!1,yearSuffix:""},this._defaults={showOn:"focus",showAnim:"fadeIn",showOptions:{},defaultDate:null,appendText:"",buttonText:"...",buttonImage:"",buttonImageOnly:!1,hideIfNoPrevNext:!1,navigationAsDateFormat:!1,gotoCurrent:!1,changeMonth:!1,changeYear:!1,yearRange:"c-10:c+10",showOtherMonths:!1,selectOtherMonths:!1,showWeek:!1,calculateWeek:this.iso8601Week,shortYearCutoff:"+10",minDate:null,maxDate:null,duration:"fast",beforeShowDay:null,beforeShow:null,onSelect:null,onChangeMonthYear:null,onClose:null,numberOfMonths:1,showCurrentAtPos:0,stepMonths:1,stepBigMonths:12,altField:"",altFormat:"",constrainInput:!0,showButtonPanel:!1,autoSize:!1,disabled:!1},t.extend(this._defaults,this.regional[""]),this.dpDiv=s(t("<div id='"+this._mainDivId+"' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"))}function s(e){var i="button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";return e.delegate(i,"mouseout",function(){t(this).removeClass("ui-state-hover"),-1!==this.className.indexOf("ui-datepicker-prev")&&t(this).removeClass("ui-datepicker-prev-hover"),-1!==this.className.indexOf("ui-datepicker-next")&&t(this).removeClass("ui-datepicker-next-hover")}).delegate(i,"mouseover",function(){t.datepicker._isDisabledDatepicker(a.inline?e.parent()[0]:a.input[0])||(t(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"),t(this).addClass("ui-state-hover"),-1!==this.className.indexOf("ui-datepicker-prev")&&t(this).addClass("ui-datepicker-prev-hover"),-1!==this.className.indexOf("ui-datepicker-next")&&t(this).addClass("ui-datepicker-next-hover"))})}function n(e,i){t.extend(e,i);for(var s in i)null==i[s]&&(e[s]=i[s]);return e}t.extend(t.ui,{datepicker:{version:"1.10.3"}});var a,r="datepicker";t.extend(i.prototype,{markerClassName:"hasDatepicker",maxRows:4,_widgetDatepicker:function(){return this.dpDiv},setDefaults:function(t){return n(this._defaults,t||{}),this},_attachDatepicker:function(e,i){var s,n,a;s=e.nodeName.toLowerCase(),n="div"===s||"span"===s,e.id||(this.uuid+=1,e.id="dp"+this.uuid),a=this._newInst(t(e),n),a.settings=t.extend({},i||{}),"input"===s?this._connectDatepicker(e,a):n&&this._inlineDatepicker(e,a)},_newInst:function(e,i){var n=e[0].id.replace(/([^A-Za-z0-9_\-])/g,"\\\\$1");return{id:n,input:e,selectedDay:0,selectedMonth:0,selectedYear:0,drawMonth:0,drawYear:0,inline:i,dpDiv:i?s(t("<div class='"+this._inlineClass+" ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")):this.dpDiv}},_connectDatepicker:function(e,i){var s=t(e);i.append=t([]),i.trigger=t([]),s.hasClass(this.markerClassName)||(this._attachments(s,i),s.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp),this._autoSize(i),t.data(e,r,i),i.settings.disabled&&this._disableDatepicker(e))},_attachments:function(e,i){var s,n,a,r=this._get(i,"appendText"),o=this._get(i,"isRTL");i.append&&i.append.remove(),r&&(i.append=t("<span class='"+this._appendClass+"'>"+r+"</span>"),e[o?"before":"after"](i.append)),e.unbind("focus",this._showDatepicker),i.trigger&&i.trigger.remove(),s=this._get(i,"showOn"),("focus"===s||"both"===s)&&e.focus(this._showDatepicker),("button"===s||"both"===s)&&(n=this._get(i,"buttonText"),a=this._get(i,"buttonImage"),i.trigger=t(this._get(i,"buttonImageOnly")?t("<img/>").addClass(this._triggerClass).attr({src:a,alt:n,title:n}):t("<button type='button'></button>").addClass(this._triggerClass).html(a?t("<img/>").attr({src:a,alt:n,title:n}):n)),e[o?"before":"after"](i.trigger),i.trigger.click(function(){return t.datepicker._datepickerShowing&&t.datepicker._lastInput===e[0]?t.datepicker._hideDatepicker():t.datepicker._datepickerShowing&&t.datepicker._lastInput!==e[0]?(t.datepicker._hideDatepicker(),t.datepicker._showDatepicker(e[0])):t.datepicker._showDatepicker(e[0]),!1}))},_autoSize:function(t){if(this._get(t,"autoSize")&&!t.inline){var e,i,s,n,a=new Date(2009,11,20),r=this._get(t,"dateFormat");r.match(/[DM]/)&&(e=function(t){for(i=0,s=0,n=0;t.length>n;n++)t[n].length>i&&(i=t[n].length,s=n);return s},a.setMonth(e(this._get(t,r.match(/MM/)?"monthNames":"monthNamesShort"))),a.setDate(e(this._get(t,r.match(/DD/)?"dayNames":"dayNamesShort"))+20-a.getDay())),t.input.attr("size",this._formatDate(t,a).length)}},_inlineDatepicker:function(e,i){var s=t(e);s.hasClass(this.markerClassName)||(s.addClass(this.markerClassName).append(i.dpDiv),t.data(e,r,i),this._setDate(i,this._getDefaultDate(i),!0),this._updateDatepicker(i),this._updateAlternate(i),i.settings.disabled&&this._disableDatepicker(e),i.dpDiv.css("display","block"))},_dialogDatepicker:function(e,i,s,a,o){var h,l,c,u,d,p=this._dialogInst;return p||(this.uuid+=1,h="dp"+this.uuid,this._dialogInput=t("<input type='text' id='"+h+"' style='position: absolute; top: -100px; width: 0px;'/>"),this._dialogInput.keydown(this._doKeyDown),t("body").append(this._dialogInput),p=this._dialogInst=this._newInst(this._dialogInput,!1),p.settings={},t.data(this._dialogInput[0],r,p)),n(p.settings,a||{}),i=i&&i.constructor===Date?this._formatDate(p,i):i,this._dialogInput.val(i),this._pos=o?o.length?o:[o.pageX,o.pageY]:null,this._pos||(l=document.documentElement.clientWidth,c=document.documentElement.clientHeight,u=document.documentElement.scrollLeft||document.body.scrollLeft,d=document.documentElement.scrollTop||document.body.scrollTop,this._pos=[l/2-100+u,c/2-150+d]),this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px"),p.settings.onSelect=s,this._inDialog=!0,this.dpDiv.addClass(this._dialogClass),this._showDatepicker(this._dialogInput[0]),t.blockUI&&t.blockUI(this.dpDiv),t.data(this._dialogInput[0],r,p),this},_destroyDatepicker:function(e){var i,s=t(e),n=t.data(e,r);s.hasClass(this.markerClassName)&&(i=e.nodeName.toLowerCase(),t.removeData(e,r),"input"===i?(n.append.remove(),n.trigger.remove(),s.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)):("div"===i||"span"===i)&&s.removeClass(this.markerClassName).empty())},_enableDatepicker:function(e){var i,s,n=t(e),a=t.data(e,r);n.hasClass(this.markerClassName)&&(i=e.nodeName.toLowerCase(),"input"===i?(e.disabled=!1,a.trigger.filter("button").each(function(){this.disabled=!1}).end().filter("img").css({opacity:"1.0",cursor:""})):("div"===i||"span"===i)&&(s=n.children("."+this._inlineClass),s.children().removeClass("ui-state-disabled"),s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",!1)),this._disabledInputs=t.map(this._disabledInputs,function(t){return t===e?null:t}))},_disableDatepicker:function(e){var i,s,n=t(e),a=t.data(e,r);n.hasClass(this.markerClassName)&&(i=e.nodeName.toLowerCase(),"input"===i?(e.disabled=!0,a.trigger.filter("button").each(function(){this.disabled=!0}).end().filter("img").css({opacity:"0.5",cursor:"default"})):("div"===i||"span"===i)&&(s=n.children("."+this._inlineClass),s.children().addClass("ui-state-disabled"),s.find("select.ui-datepicker-month, select.ui-datepicker-year").prop("disabled",!0)),this._disabledInputs=t.map(this._disabledInputs,function(t){return t===e?null:t}),this._disabledInputs[this._disabledInputs.length]=e)},_isDisabledDatepicker:function(t){if(!t)return!1;for(var e=0;this._disabledInputs.length>e;e++)if(this._disabledInputs[e]===t)return!0;return!1},_getInst:function(e){try{return t.data(e,r)}catch(i){throw"Missing instance data for this datepicker"}},_optionDatepicker:function(i,s,a){var r,o,h,l,c=this._getInst(i);return 2===arguments.length&&"string"==typeof s?"defaults"===s?t.extend({},t.datepicker._defaults):c?"all"===s?t.extend({},c.settings):this._get(c,s):null:(r=s||{},"string"==typeof s&&(r={},r[s]=a),c&&(this._curInst===c&&this._hideDatepicker(),o=this._getDateDatepicker(i,!0),h=this._getMinMaxDate(c,"min"),l=this._getMinMaxDate(c,"max"),n(c.settings,r),null!==h&&r.dateFormat!==e&&r.minDate===e&&(c.settings.minDate=this._formatDate(c,h)),null!==l&&r.dateFormat!==e&&r.maxDate===e&&(c.settings.maxDate=this._formatDate(c,l)),"disabled"in r&&(r.disabled?this._disableDatepicker(i):this._enableDatepicker(i)),this._attachments(t(i),c),this._autoSize(c),this._setDate(c,o),this._updateAlternate(c),this._updateDatepicker(c)),e)},_changeDatepicker:function(t,e,i){this._optionDatepicker(t,e,i)},_refreshDatepicker:function(t){var e=this._getInst(t);e&&this._updateDatepicker(e)},_setDateDatepicker:function(t,e){var i=this._getInst(t);i&&(this._setDate(i,e),this._updateDatepicker(i),this._updateAlternate(i))},_getDateDatepicker:function(t,e){var i=this._getInst(t);return i&&!i.inline&&this._setDateFromField(i,e),i?this._getDate(i):null},_doKeyDown:function(e){var i,s,n,a=t.datepicker._getInst(e.target),r=!0,o=a.dpDiv.is(".ui-datepicker-rtl");if(a._keyEvent=!0,t.datepicker._datepickerShowing)switch(e.keyCode){case 9:t.datepicker._hideDatepicker(),r=!1;break;case 13:return n=t("td."+t.datepicker._dayOverClass+":not(."+t.datepicker._currentClass+")",a.dpDiv),n[0]&&t.datepicker._selectDay(e.target,a.selectedMonth,a.selectedYear,n[0]),i=t.datepicker._get(a,"onSelect"),i?(s=t.datepicker._formatDate(a),i.apply(a.input?a.input[0]:null,[s,a])):t.datepicker._hideDatepicker(),!1;case 27:t.datepicker._hideDatepicker();break;case 33:t.datepicker._adjustDate(e.target,e.ctrlKey?-t.datepicker._get(a,"stepBigMonths"):-t.datepicker._get(a,"stepMonths"),"M");break;case 34:t.datepicker._adjustDate(e.target,e.ctrlKey?+t.datepicker._get(a,"stepBigMonths"):+t.datepicker._get(a,"stepMonths"),"M");break;case 35:(e.ctrlKey||e.metaKey)&&t.datepicker._clearDate(e.target),r=e.ctrlKey||e.metaKey;break;case 36:(e.ctrlKey||e.metaKey)&&t.datepicker._gotoToday(e.target),r=e.ctrlKey||e.metaKey;break;case 37:(e.ctrlKey||e.metaKey)&&t.datepicker._adjustDate(e.target,o?1:-1,"D"),r=e.ctrlKey||e.metaKey,e.originalEvent.altKey&&t.datepicker._adjustDate(e.target,e.ctrlKey?-t.datepicker._get(a,"stepBigMonths"):-t.datepicker._get(a,"stepMonths"),"M");break;case 38:(e.ctrlKey||e.metaKey)&&t.datepicker._adjustDate(e.target,-7,"D"),r=e.ctrlKey||e.metaKey;break;case 39:(e.ctrlKey||e.metaKey)&&t.datepicker._adjustDate(e.target,o?-1:1,"D"),r=e.ctrlKey||e.metaKey,e.originalEvent.altKey&&t.datepicker._adjustDate(e.target,e.ctrlKey?+t.datepicker._get(a,"stepBigMonths"):+t.datepicker._get(a,"stepMonths"),"M");break;case 40:(e.ctrlKey||e.metaKey)&&t.datepicker._adjustDate(e.target,7,"D"),r=e.ctrlKey||e.metaKey;break;default:r=!1}else 36===e.keyCode&&e.ctrlKey?t.datepicker._showDatepicker(this):r=!1;r&&(e.preventDefault(),e.stopPropagation())},_doKeyPress:function(i){var s,n,a=t.datepicker._getInst(i.target);return t.datepicker._get(a,"constrainInput")?(s=t.datepicker._possibleChars(t.datepicker._get(a,"dateFormat")),n=String.fromCharCode(null==i.charCode?i.keyCode:i.charCode),i.ctrlKey||i.metaKey||" ">n||!s||s.indexOf(n)>-1):e},_doKeyUp:function(e){var i,s=t.datepicker._getInst(e.target);if(s.input.val()!==s.lastVal)try{i=t.datepicker.parseDate(t.datepicker._get(s,"dateFormat"),s.input?s.input.val():null,t.datepicker._getFormatConfig(s)),i&&(t.datepicker._setDateFromField(s),t.datepicker._updateAlternate(s),t.datepicker._updateDatepicker(s))}catch(n){}return!0},_showDatepicker:function(e){if(e=e.target||e,"input"!==e.nodeName.toLowerCase()&&(e=t("input",e.parentNode)[0]),!t.datepicker._isDisabledDatepicker(e)&&t.datepicker._lastInput!==e){var i,s,a,r,o,h,l;i=t.datepicker._getInst(e),t.datepicker._curInst&&t.datepicker._curInst!==i&&(t.datepicker._curInst.dpDiv.stop(!0,!0),i&&t.datepicker._datepickerShowing&&t.datepicker._hideDatepicker(t.datepicker._curInst.input[0])),s=t.datepicker._get(i,"beforeShow"),a=s?s.apply(e,[e,i]):{},a!==!1&&(n(i.settings,a),i.lastVal=null,t.datepicker._lastInput=e,t.datepicker._setDateFromField(i),t.datepicker._inDialog&&(e.value=""),t.datepicker._pos||(t.datepicker._pos=t.datepicker._findPos(e),t.datepicker._pos[1]+=e.offsetHeight),r=!1,t(e).parents().each(function(){return r|="fixed"===t(this).css("position"),!r}),o={left:t.datepicker._pos[0],top:t.datepicker._pos[1]},t.datepicker._pos=null,i.dpDiv.empty(),i.dpDiv.css({position:"absolute",display:"block",top:"-1000px"}),t.datepicker._updateDatepicker(i),o=t.datepicker._checkOffset(i,o,r),i.dpDiv.css({position:t.datepicker._inDialog&&t.blockUI?"static":r?"fixed":"absolute",display:"none",left:o.left+"px",top:o.top+"px"}),i.inline||(h=t.datepicker._get(i,"showAnim"),l=t.datepicker._get(i,"duration"),i.dpDiv.zIndex(8000),t.datepicker._datepickerShowing=!0,t.effects&&t.effects.effect[h]?i.dpDiv.show(h,t.datepicker._get(i,"showOptions"),l):i.dpDiv[h||"show"](h?l:null),t.datepicker._shouldFocusInput(i)&&i.input.focus(),t.datepicker._curInst=i))}},_updateDatepicker:function(e){this.maxRows=4,a=e,e.dpDiv.empty().append(this._generateHTML(e)),this._attachHandlers(e),e.dpDiv.find("."+this._dayOverClass+" a").mouseover();var i,s=this._getNumberOfMonths(e),n=s[1],r=17;e.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""),n>1&&e.dpDiv.addClass("ui-datepicker-multi-"+n).css("width",r*n+"em"),e.dpDiv[(1!==s[0]||1!==s[1]?"add":"remove")+"Class"]("ui-datepicker-multi"),e.dpDiv[(this._get(e,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl"),e===t.datepicker._curInst&&t.datepicker._datepickerShowing&&t.datepicker._shouldFocusInput(e)&&e.input.focus(),e.yearshtml&&(i=e.yearshtml,setTimeout(function(){i===e.yearshtml&&e.yearshtml&&e.dpDiv.find("select.ui-datepicker-year:first").replaceWith(e.yearshtml),i=e.yearshtml=null},0))},_shouldFocusInput:function(t){return t.input&&t.input.is(":visible")&&!t.input.is(":disabled")&&!t.input.is(":focus")},_checkOffset:function(e,i,s){var n=e.dpDiv.outerWidth(),a=e.dpDiv.outerHeight(),r=e.input?e.input.outerWidth():0,o=e.input?e.input.outerHeight():0,h=document.documentElement.clientWidth+(s?0:t(document).scrollLeft()),l=document.documentElement.clientHeight+(s?0:t(document).scrollTop());return i.left-=this._get(e,"isRTL")?n-r:0,i.left-=s&&i.left===e.input.offset().left?t(document).scrollLeft():0,i.top-=s&&i.top===e.input.offset().top+o?t(document).scrollTop():0,i.left-=Math.min(i.left,i.left+n>h&&h>n?Math.abs(i.left+n-h):0),i.top-=Math.min(i.top,i.top+a>l&&l>a?Math.abs(a+o):0),i},_findPos:function(e){for(var i,s=this._getInst(e),n=this._get(s,"isRTL");e&&("hidden"===e.type||1!==e.nodeType||t.expr.filters.hidden(e));)e=e[n?"previousSibling":"nextSibling"];return i=t(e).offset(),[i.left,i.top]},_hideDatepicker:function(e){var i,s,n,a,o=this._curInst;!o||e&&o!==t.data(e,r)||this._datepickerShowing&&(i=this._get(o,"showAnim"),s=this._get(o,"duration"),n=function(){t.datepicker._tidyDialog(o)},t.effects&&(t.effects.effect[i]||t.effects[i])?o.dpDiv.hide(i,t.datepicker._get(o,"showOptions"),s,n):o.dpDiv["slideDown"===i?"slideUp":"fadeIn"===i?"fadeOut":"hide"](i?s:null,n),i||n(),this._datepickerShowing=!1,a=this._get(o,"onClose"),a&&a.apply(o.input?o.input[0]:null,[o.input?o.input.val():"",o]),this._lastInput=null,this._inDialog&&(this._dialogInput.css({position:"absolute",left:"0",top:"-100px"}),t.blockUI&&(t.unblockUI(),t("body").append(this.dpDiv))),this._inDialog=!1)},_tidyDialog:function(t){t.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")},_checkExternalClick:function(e){if(t.datepicker._curInst){var i=t(e.target),s=t.datepicker._getInst(i[0]);(i[0].id!==t.datepicker._mainDivId&&0===i.parents("#"+t.datepicker._mainDivId).length&&!i.hasClass(t.datepicker.markerClassName)&&!i.closest("."+t.datepicker._triggerClass).length&&t.datepicker._datepickerShowing&&(!t.datepicker._inDialog||!t.blockUI)||i.hasClass(t.datepicker.markerClassName)&&t.datepicker._curInst!==s)&&t.datepicker._hideDatepicker()}},_adjustDate:function(e,i,s){var n=t(e),a=this._getInst(n[0]);this._isDisabledDatepicker(n[0])||(this._adjustInstDate(a,i+("M"===s?this._get(a,"showCurrentAtPos"):0),s),this._updateDatepicker(a))},_gotoToday:function(e){var i,s=t(e),n=this._getInst(s[0]);this._get(n,"gotoCurrent")&&n.currentDay?(n.selectedDay=n.currentDay,n.drawMonth=n.selectedMonth=n.currentMonth,n.drawYear=n.selectedYear=n.currentYear):(i=new Date,n.selectedDay=i.getDate(),n.drawMonth=n.selectedMonth=i.getMonth(),n.drawYear=n.selectedYear=i.getFullYear()),this._notifyChange(n),this._adjustDate(s)},_selectMonthYear:function(e,i,s){var n=t(e),a=this._getInst(n[0]);a["selected"+("M"===s?"Month":"Year")]=a["draw"+("M"===s?"Month":"Year")]=parseInt(i.options[i.selectedIndex].value,10),this._notifyChange(a),this._adjustDate(n)},_selectDay:function(e,i,s,n){var a,r=t(e);t(n).hasClass(this._unselectableClass)||this._isDisabledDatepicker(r[0])||(a=this._getInst(r[0]),a.selectedDay=a.currentDay=t("a",n).html(),a.selectedMonth=a.currentMonth=i,a.selectedYear=a.currentYear=s,this._selectDate(e,this._formatDate(a,a.currentDay,a.currentMonth,a.currentYear)))},_clearDate:function(e){var i=t(e);this._selectDate(i,"")},_selectDate:function(e,i){var s,n=t(e),a=this._getInst(n[0]);i=null!=i?i:this._formatDate(a),a.input&&a.input.val(i),this._updateAlternate(a),s=this._get(a,"onSelect"),s?s.apply(a.input?a.input[0]:null,[i,a]):a.input&&a.input.trigger("change"),a.inline?this._updateDatepicker(a):(this._hideDatepicker(),this._lastInput=a.input[0],"object"!=typeof a.input[0]&&a.input.focus(),this._lastInput=null)},_updateAlternate:function(e){var i,s,n,a=this._get(e,"altField");a&&(i=this._get(e,"altFormat")||this._get(e,"dateFormat"),s=this._getDate(e),n=this.formatDate(i,s,this._getFormatConfig(e)),t(a).each(function(){t(this).val(n)}))},noWeekends:function(t){var e=t.getDay();return[e>0&&6>e,""]},iso8601Week:function(t){var e,i=new Date(t.getTime());return i.setDate(i.getDate()+4-(i.getDay()||7)),e=i.getTime(),i.setMonth(0),i.setDate(1),Math.floor(Math.round((e-i)/864e5)/7)+1},parseDate:function(i,s,n){if(null==i||null==s)throw"Invalid arguments";if(s="object"==typeof s?""+s:s+"",""===s)return null;var a,r,o,h,l=0,c=(n?n.shortYearCutoff:null)||this._defaults.shortYearCutoff,u="string"!=typeof c?c:(new Date).getFullYear()%100+parseInt(c,10),d=(n?n.dayNamesShort:null)||this._defaults.dayNamesShort,p=(n?n.dayNames:null)||this._defaults.dayNames,f=(n?n.monthNamesShort:null)||this._defaults.monthNamesShort,m=(n?n.monthNames:null)||this._defaults.monthNames,g=-1,v=-1,_=-1,b=-1,y=!1,x=function(t){var e=i.length>a+1&&i.charAt(a+1)===t;return e&&a++,e},k=function(t){var e=x(t),i="@"===t?14:"!"===t?20:"y"===t&&e?4:"o"===t?3:2,n=RegExp("^\\d{1,"+i+"}"),a=s.substring(l).match(n);if(!a)throw"Missing number at position "+l;return l+=a[0].length,parseInt(a[0],10)},w=function(i,n,a){var r=-1,o=t.map(x(i)?a:n,function(t,e){return[[e,t]]}).sort(function(t,e){return-(t[1].length-e[1].length)});if(t.each(o,function(t,i){var n=i[1];return s.substr(l,n.length).toLowerCase()===n.toLowerCase()?(r=i[0],l+=n.length,!1):e}),-1!==r)return r+1;throw"Unknown name at position "+l},D=function(){if(s.charAt(l)!==i.charAt(a))throw"Unexpected literal at position "+l;l++};for(a=0;i.length>a;a++)if(y)"'"!==i.charAt(a)||x("'")?D():y=!1;else switch(i.charAt(a)){case"d":_=k("d");break;case"D":w("D",d,p);break;case"o":b=k("o");break;case"m":v=k("m");break;case"M":v=w("M",f,m);break;case"y":g=k("y");break;case"@":h=new Date(k("@")),g=h.getFullYear(),v=h.getMonth()+1,_=h.getDate();break;case"!":h=new Date((k("!")-this._ticksTo1970)/1e4),g=h.getFullYear(),v=h.getMonth()+1,_=h.getDate();break;case"'":x("'")?D():y=!0;break;default:D()}if(s.length>l&&(o=s.substr(l),!/^\s+/.test(o)))throw"Extra/unparsed characters found in date: "+o;if(-1===g?g=(new Date).getFullYear():100>g&&(g+=(new Date).getFullYear()-(new Date).getFullYear()%100+(u>=g?0:-100)),b>-1)for(v=1,_=b;;){if(r=this._getDaysInMonth(g,v-1),r>=_)break;v++,_-=r}if(h=this._daylightSavingAdjust(new Date(g,v-1,_)),h.getFullYear()!==g||h.getMonth()+1!==v||h.getDate()!==_)throw"Invalid date";return h},ATOM:"yy-mm-dd",COOKIE:"D, dd M yy",ISO_8601:"yy-mm-dd",RFC_822:"D, d M y",RFC_850:"DD, dd-M-y",RFC_1036:"D, d M y",RFC_1123:"D, d M yy",RFC_2822:"D, d M yy",RSS:"D, d M y",TICKS:"!",TIMESTAMP:"@",W3C:"yy-mm-dd",_ticksTo1970:1e7*60*60*24*(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925)),formatDate:function(t,e,i){if(!e)return"";var s,n=(i?i.dayNamesShort:null)||this._defaults.dayNamesShort,a=(i?i.dayNames:null)||this._defaults.dayNames,r=(i?i.monthNamesShort:null)||this._defaults.monthNamesShort,o=(i?i.monthNames:null)||this._defaults.monthNames,h=function(e){var i=t.length>s+1&&t.charAt(s+1)===e;return i&&s++,i},l=function(t,e,i){var s=""+e;if(h(t))for(;i>s.length;)s="0"+s;return s},c=function(t,e,i,s){return h(t)?s[e]:i[e]},u="",d=!1;if(e)for(s=0;t.length>s;s++)if(d)"'"!==t.charAt(s)||h("'")?u+=t.charAt(s):d=!1;else switch(t.charAt(s)){case"d":u+=l("d",e.getDate(),2);break;case"D":u+=c("D",e.getDay(),n,a);break;case"o":u+=l("o",Math.round((new Date(e.getFullYear(),e.getMonth(),e.getDate()).getTime()-new Date(e.getFullYear(),0,0).getTime())/864e5),3);break;case"m":u+=l("m",e.getMonth()+1,2);break;case"M":u+=c("M",e.getMonth(),r,o);break;case"y":u+=h("y")?e.getFullYear():(10>e.getYear()%100?"0":"")+e.getYear()%100;break;case"@":u+=e.getTime();break;case"!":u+=1e4*e.getTime()+this._ticksTo1970;break;case"'":h("'")?u+="'":d=!0;break;default:u+=t.charAt(s)}return u},_possibleChars:function(t){var e,i="",s=!1,n=function(i){var s=t.length>e+1&&t.charAt(e+1)===i;return s&&e++,s};for(e=0;t.length>e;e++)if(s)"'"!==t.charAt(e)||n("'")?i+=t.charAt(e):s=!1;else switch(t.charAt(e)){case"d":case"m":case"y":case"@":i+="0123456789";break;case"D":case"M":return null;case"'":n("'")?i+="'":s=!0;break;default:i+=t.charAt(e)}return i},_get:function(t,i){return t.settings[i]!==e?t.settings[i]:this._defaults[i]},_setDateFromField:function(t,e){if(t.input.val()!==t.lastVal){var i=this._get(t,"dateFormat"),s=t.lastVal=t.input?t.input.val():null,n=this._getDefaultDate(t),a=n,r=this._getFormatConfig(t);try{a=this.parseDate(i,s,r)||n}catch(o){s=e?"":s}t.selectedDay=a.getDate(),t.drawMonth=t.selectedMonth=a.getMonth(),t.drawYear=t.selectedYear=a.getFullYear(),t.currentDay=s?a.getDate():0,t.currentMonth=s?a.getMonth():0,t.currentYear=s?a.getFullYear():0,this._adjustInstDate(t)}},_getDefaultDate:function(t){return this._restrictMinMax(t,this._determineDate(t,this._get(t,"defaultDate"),new Date))},_determineDate:function(e,i,s){var n=function(t){var e=new Date;return e.setDate(e.getDate()+t),e},a=function(i){try{return t.datepicker.parseDate(t.datepicker._get(e,"dateFormat"),i,t.datepicker._getFormatConfig(e))}catch(s){}for(var n=(i.toLowerCase().match(/^c/)?t.datepicker._getDate(e):null)||new Date,a=n.getFullYear(),r=n.getMonth(),o=n.getDate(),h=/([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,l=h.exec(i);l;){switch(l[2]||"d"){case"d":case"D":o+=parseInt(l[1],10);break;case"w":case"W":o+=7*parseInt(l[1],10);break;case"m":case"M":r+=parseInt(l[1],10),o=Math.min(o,t.datepicker._getDaysInMonth(a,r));break;case"y":case"Y":a+=parseInt(l[1],10),o=Math.min(o,t.datepicker._getDaysInMonth(a,r))}l=h.exec(i)}return new Date(a,r,o)},r=null==i||""===i?s:"string"==typeof i?a(i):"number"==typeof i?isNaN(i)?s:n(i):new Date(i.getTime());return r=r&&"Invalid Date"==""+r?s:r,r&&(r.setHours(0),r.setMinutes(0),r.setSeconds(0),r.setMilliseconds(0)),this._daylightSavingAdjust(r)},_daylightSavingAdjust:function(t){return t?(t.setHours(t.getHours()>12?t.getHours()+2:0),t):null},_setDate:function(t,e,i){var s=!e,n=t.selectedMonth,a=t.selectedYear,r=this._restrictMinMax(t,this._determineDate(t,e,new Date));t.selectedDay=t.currentDay=r.getDate(),t.drawMonth=t.selectedMonth=t.currentMonth=r.getMonth(),t.drawYear=t.selectedYear=t.currentYear=r.getFullYear(),n===t.selectedMonth&&a===t.selectedYear||i||this._notifyChange(t),this._adjustInstDate(t),t.input&&t.input.val(s?"":this._formatDate(t))},_getDate:function(t){var e=!t.currentYear||t.input&&""===t.input.val()?null:this._daylightSavingAdjust(new Date(t.currentYear,t.currentMonth,t.currentDay));return e},_attachHandlers:function(e){var i=this._get(e,"stepMonths"),s="#"+e.id.replace(/\\\\/g,"\\");e.dpDiv.find("[data-handler]").map(function(){var e={prev:function(){t.datepicker._adjustDate(s,-i,"M")},next:function(){t.datepicker._adjustDate(s,+i,"M")},hide:function(){t.datepicker._hideDatepicker()},today:function(){t.datepicker._gotoToday(s)},selectDay:function(){return t.datepicker._selectDay(s,+this.getAttribute("data-month"),+this.getAttribute("data-year"),this),!1},selectMonth:function(){return t.datepicker._selectMonthYear(s,this,"M"),!1},selectYear:function(){return t.datepicker._selectMonthYear(s,this,"Y"),!1}};t(this).bind(this.getAttribute("data-event"),e[this.getAttribute("data-handler")])})},_generateHTML:function(t){var e,i,s,n,a,r,o,h,l,c,u,d,p,f,m,g,v,_,b,y,x,k,w,D,T,C,M,S,N,I,P,A,z,H,E,F,O,W,j,R=new Date,L=this._daylightSavingAdjust(new Date(R.getFullYear(),R.getMonth(),R.getDate())),Y=this._get(t,"isRTL"),B=this._get(t,"showButtonPanel"),J=this._get(t,"hideIfNoPrevNext"),K=this._get(t,"navigationAsDateFormat"),Q=this._getNumberOfMonths(t),V=this._get(t,"showCurrentAtPos"),U=this._get(t,"stepMonths"),q=1!==Q[0]||1!==Q[1],X=this._daylightSavingAdjust(t.currentDay?new Date(t.currentYear,t.currentMonth,t.currentDay):new Date(9999,9,9)),G=this._getMinMaxDate(t,"min"),$=this._getMinMaxDate(t,"max"),Z=t.drawMonth-V,te=t.drawYear;if(0>Z&&(Z+=12,te--),$)for(e=this._daylightSavingAdjust(new Date($.getFullYear(),$.getMonth()-Q[0]*Q[1]+1,$.getDate())),e=G&&G>e?G:e;this._daylightSavingAdjust(new Date(te,Z,1))>e;)Z--,0>Z&&(Z=11,te--);for(t.drawMonth=Z,t.drawYear=te,i=this._get(t,"prevText"),i=K?this.formatDate(i,this._daylightSavingAdjust(new Date(te,Z-U,1)),this._getFormatConfig(t)):i,s=this._canAdjustMonth(t,-1,te,Z)?"<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click' title='"+i+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"e":"w")+"'>"+i+"</span></a>":J?"":"<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='"+i+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"e":"w")+"'>"+i+"</span></a>",n=this._get(t,"nextText"),n=K?this.formatDate(n,this._daylightSavingAdjust(new Date(te,Z+U,1)),this._getFormatConfig(t)):n,a=this._canAdjustMonth(t,1,te,Z)?"<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click' title='"+n+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"w":"e")+"'>"+n+"</span></a>":J?"":"<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='"+n+"'><span class='ui-icon ui-icon-circle-triangle-"+(Y?"w":"e")+"'>"+n+"</span></a>",r=this._get(t,"currentText"),o=this._get(t,"gotoCurrent")&&t.currentDay?X:L,r=K?this.formatDate(r,o,this._getFormatConfig(t)):r,h=t.inline?"":"<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>"+this._get(t,"closeText")+"</button>",l=B?"<div class='ui-datepicker-buttonpane ui-widget-content'>"+(Y?h:"")+(this._isInRange(t,o)?"<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'>"+r+"</button>":"")+(Y?"":h)+"</div>":"",c=parseInt(this._get(t,"firstDay"),10),c=isNaN(c)?0:c,u=this._get(t,"showWeek"),d=this._get(t,"dayNames"),p=this._get(t,"dayNamesMin"),f=this._get(t,"monthNames"),m=this._get(t,"monthNamesShort"),g=this._get(t,"beforeShowDay"),v=this._get(t,"showOtherMonths"),_=this._get(t,"selectOtherMonths"),b=this._getDefaultDate(t),y="",k=0;Q[0]>k;k++){for(w="",this.maxRows=4,D=0;Q[1]>D;D++){if(T=this._daylightSavingAdjust(new Date(te,Z,t.selectedDay)),C=" ui-corner-all",M="",q){if(M+="<div class='ui-datepicker-group",Q[1]>1)switch(D){case 0:M+=" ui-datepicker-group-first",C=" ui-corner-"+(Y?"right":"left");break;case Q[1]-1:M+=" ui-datepicker-group-last",C=" ui-corner-"+(Y?"left":"right");break;default:M+=" ui-datepicker-group-middle",C=""}M+="'>"}for(M+="<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix"+C+"'>"+(/all|left/.test(C)&&0===k?Y?a:s:"")+(/all|right/.test(C)&&0===k?Y?s:a:"")+this._generateMonthYearHeader(t,Z,te,G,$,k>0||D>0,f,m)+"</div><table class='ui-datepicker-calendar'><thead>"+"<tr>",S=u?"<th class='ui-datepicker-week-col'>"+this._get(t,"weekHeader")+"</th>":"",x=0;7>x;x++)N=(x+c)%7,S+="<th"+((x+c+6)%7>=5?" class='ui-datepicker-week-end'":"")+">"+"<span title='"+d[N]+"'>"+p[N]+"</span></th>";for(M+=S+"</tr></thead><tbody>",I=this._getDaysInMonth(te,Z),te===t.selectedYear&&Z===t.selectedMonth&&(t.selectedDay=Math.min(t.selectedDay,I)),P=(this._getFirstDayOfMonth(te,Z)-c+7)%7,A=Math.ceil((P+I)/7),z=q?this.maxRows>A?this.maxRows:A:A,this.maxRows=z,H=this._daylightSavingAdjust(new Date(te,Z,1-P)),E=0;z>E;E++){for(M+="<tr>",F=u?"<td class='ui-datepicker-week-col'>"+this._get(t,"calculateWeek")(H)+"</td>":"",x=0;7>x;x++)O=g?g.apply(t.input?t.input[0]:null,[H]):[!0,""],W=H.getMonth()!==Z,j=W&&!_||!O[0]||G&&G>H||$&&H>$,F+="<td class='"+((x+c+6)%7>=5?" ui-datepicker-week-end":"")+(W?" ui-datepicker-other-month":"")+(H.getTime()===T.getTime()&&Z===t.selectedMonth&&t._keyEvent||b.getTime()===H.getTime()&&b.getTime()===T.getTime()?" "+this._dayOverClass:"")+(j?" "+this._unselectableClass+" ui-state-disabled":"")+(W&&!v?"":" "+O[1]+(H.getTime()===X.getTime()?" "+this._currentClass:"")+(H.getTime()===L.getTime()?" ui-datepicker-today":""))+"'"+(W&&!v||!O[2]?"":" title='"+O[2].replace(/'/g,"&#39;")+"'")+(j?"":" data-handler='selectDay' data-event='click' data-month='"+H.getMonth()+"' data-year='"+H.getFullYear()+"'")+">"+(W&&!v?"&#xa0;":j?"<span class='ui-state-default'>"+H.getDate()+"</span>":"<a class='ui-state-default"+(H.getTime()===L.getTime()?" ui-state-highlight":"")+(H.getTime()===X.getTime()?" ui-state-active":"")+(W?" ui-priority-secondary":"")+"' href='#'>"+H.getDate()+"</a>")+"</td>",H.setDate(H.getDate()+1),H=this._daylightSavingAdjust(H);M+=F+"</tr>"}Z++,Z>11&&(Z=0,te++),M+="</tbody></table>"+(q?"</div>"+(Q[0]>0&&D===Q[1]-1?"<div class='ui-datepicker-row-break'></div>":""):""),w+=M}y+=w}return y+=l,t._keyEvent=!1,y},_generateMonthYearHeader:function(t,e,i,s,n,a,r,o){var h,l,c,u,d,p,f,m,g=this._get(t,"changeMonth"),v=this._get(t,"changeYear"),_=this._get(t,"showMonthAfterYear"),b="<div class='ui-datepicker-title'>",y="";if(a||!g)y+="<span class='ui-datepicker-month'>"+r[e]+"</span>";else{for(h=s&&s.getFullYear()===i,l=n&&n.getFullYear()===i,y+="<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>",c=0;12>c;c++)(!h||c>=s.getMonth())&&(!l||n.getMonth()>=c)&&(y+="<option value='"+c+"'"+(c===e?" selected='selected'":"")+">"+o[c]+"</option>");y+="</select>"}if(_||(b+=y+(!a&&g&&v?"":"&#xa0;")),!t.yearshtml)if(t.yearshtml="",a||!v)b+="<span class='ui-datepicker-year'>"+i+"</span>";else{for(u=this._get(t,"yearRange").split(":"),d=(new Date).getFullYear(),p=function(t){var e=t.match(/c[+\-].*/)?i+parseInt(t.substring(1),10):t.match(/[+\-].*/)?d+parseInt(t,10):parseInt(t,10);
return isNaN(e)?d:e},f=p(u[0]),m=Math.max(f,p(u[1]||"")),f=s?Math.max(f,s.getFullYear()):f,m=n?Math.min(m,n.getFullYear()):m,t.yearshtml+="<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>";m>=f;f++)t.yearshtml+="<option value='"+f+"'"+(f===i?" selected='selected'":"")+">"+f+"</option>";t.yearshtml+="</select>",b+=t.yearshtml,t.yearshtml=null}return b+=this._get(t,"yearSuffix"),_&&(b+=(!a&&g&&v?"":"&#xa0;")+y),b+="</div>"},_adjustInstDate:function(t,e,i){var s=t.drawYear+("Y"===i?e:0),n=t.drawMonth+("M"===i?e:0),a=Math.min(t.selectedDay,this._getDaysInMonth(s,n))+("D"===i?e:0),r=this._restrictMinMax(t,this._daylightSavingAdjust(new Date(s,n,a)));t.selectedDay=r.getDate(),t.drawMonth=t.selectedMonth=r.getMonth(),t.drawYear=t.selectedYear=r.getFullYear(),("M"===i||"Y"===i)&&this._notifyChange(t)},_restrictMinMax:function(t,e){var i=this._getMinMaxDate(t,"min"),s=this._getMinMaxDate(t,"max"),n=i&&i>e?i:e;return s&&n>s?s:n},_notifyChange:function(t){var e=this._get(t,"onChangeMonthYear");e&&e.apply(t.input?t.input[0]:null,[t.selectedYear,t.selectedMonth+1,t])},_getNumberOfMonths:function(t){var e=this._get(t,"numberOfMonths");return null==e?[1,1]:"number"==typeof e?[1,e]:e},_getMinMaxDate:function(t,e){return this._determineDate(t,this._get(t,e+"Date"),null)},_getDaysInMonth:function(t,e){return 32-this._daylightSavingAdjust(new Date(t,e,32)).getDate()},_getFirstDayOfMonth:function(t,e){return new Date(t,e,1).getDay()},_canAdjustMonth:function(t,e,i,s){var n=this._getNumberOfMonths(t),a=this._daylightSavingAdjust(new Date(i,s+(0>e?e:n[0]*n[1]),1));return 0>e&&a.setDate(this._getDaysInMonth(a.getFullYear(),a.getMonth())),this._isInRange(t,a)},_isInRange:function(t,e){var i,s,n=this._getMinMaxDate(t,"min"),a=this._getMinMaxDate(t,"max"),r=null,o=null,h=this._get(t,"yearRange");return h&&(i=h.split(":"),s=(new Date).getFullYear(),r=parseInt(i[0],10),o=parseInt(i[1],10),i[0].match(/[+\-].*/)&&(r+=s),i[1].match(/[+\-].*/)&&(o+=s)),(!n||e.getTime()>=n.getTime())&&(!a||e.getTime()<=a.getTime())&&(!r||e.getFullYear()>=r)&&(!o||o>=e.getFullYear())},_getFormatConfig:function(t){var e=this._get(t,"shortYearCutoff");return e="string"!=typeof e?e:(new Date).getFullYear()%100+parseInt(e,10),{shortYearCutoff:e,dayNamesShort:this._get(t,"dayNamesShort"),dayNames:this._get(t,"dayNames"),monthNamesShort:this._get(t,"monthNamesShort"),monthNames:this._get(t,"monthNames")}},_formatDate:function(t,e,i,s){e||(t.currentDay=t.selectedDay,t.currentMonth=t.selectedMonth,t.currentYear=t.selectedYear);var n=e?"object"==typeof e?e:this._daylightSavingAdjust(new Date(s,i,e)):this._daylightSavingAdjust(new Date(t.currentYear,t.currentMonth,t.currentDay));return this.formatDate(this._get(t,"dateFormat"),n,this._getFormatConfig(t))}}),t.fn.datepicker=function(e){if(!this.length)return this;t.datepicker.initialized||(t(document).mousedown(t.datepicker._checkExternalClick),t.datepicker.initialized=!0),0===t("#"+t.datepicker._mainDivId).length&&t("body").append(t.datepicker.dpDiv);var i=Array.prototype.slice.call(arguments,1);return"string"!=typeof e||"isDisabled"!==e&&"getDate"!==e&&"widget"!==e?"option"===e&&2===arguments.length&&"string"==typeof arguments[1]?t.datepicker["_"+e+"Datepicker"].apply(t.datepicker,[this[0]].concat(i)):this.each(function(){"string"==typeof e?t.datepicker["_"+e+"Datepicker"].apply(t.datepicker,[this].concat(i)):t.datepicker._attachDatepicker(this,e)}):t.datepicker["_"+e+"Datepicker"].apply(t.datepicker,[this[0]].concat(i))},t.datepicker=new i,t.datepicker.initialized=!1,t.datepicker.uuid=(new Date).getTime(),t.datepicker.version="1.10.3"})(jQuery);
/*
 * jQuery UI Timepicker 0.3.1
 *
 * Copyright 2010-2011, Francois Gelinas
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://fgelinas.com/code/timepicker
 *
 * Depends:
 *	jquery.ui.core.js
 *  jquery.ui.position.js (only if position settngs are used)
 *
 * Change version 0.1.0 - moved the t-rex up here
 *
                                                  ____
       ___                                      .-~. /_"-._
      `-._~-.                                  / /_ "~o\  :Y
          \  \                                / : \~x.  ` ')
           ]  Y                              /  |  Y< ~-.__j
          /   !                        _.--~T : l  l<  /.-~
         /   /                 ____.--~ .   ` l /~\ \<|Y
        /   /             .-~~"        /| .    ',-~\ \L|
       /   /             /     .^   \ Y~Y \.^>/l_   "--'
      /   Y           .-"(  .  l__  j_j l_/ /~_.-~    .
     Y    l          /    \  )    ~~~." / `/"~ / \.__/l_
     |     \     _.-"      ~-{__     l  :  l._Z~-.___.--~
     |      ~---~           /   ~~"---\_  ' __[>
     l  .                _.^   ___     _>-y~
      \  \     .      .-~   .-~   ~>--"  /
       \  ~---"            /     ./  _.-'
        "-.,_____.,_  _.--~\     _.-~
                    ~~     (   _}       -Row
                           `. ~(
                             )  \
                            /,`--'~\--'~\
                  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                             ->T-Rex<-
*/

(function ($) {

    $.extend($.ui, { timepicker: { version: "0.3.2"} });

    var PROP_NAME = 'timepicker',
        tpuuid = new Date().getTime();

    /* Time picker manager.
    Use the singleton instance of this class, $.timepicker, to interact with the time picker.
    Settings for (groups of) time pickers are maintained in an instance object,
    allowing multiple different settings on the same page. */

    function Timepicker() {
        this.debug = true; // Change this to true to start debugging
        this._curInst = null; // The current instance in use
        this._disabledInputs = []; // List of time picker inputs that have been disabled
        this._timepickerShowing = false; // True if the popup picker is showing , false if not
        this._inDialog = false; // True if showing within a "dialog", false if not
        this._dialogClass = 'ui-timepicker-dialog'; // The name of the dialog marker class
        this._mainDivId = 'ui-timepicker-div'; // The ID of the main timepicker division
        this._inlineClass = 'ui-timepicker-inline'; // The name of the inline marker class
        this._currentClass = 'ui-timepicker-current'; // The name of the current hour / minutes marker class
        this._dayOverClass = 'ui-timepicker-days-cell-over'; // The name of the day hover marker class

        this.regional = []; // Available regional settings, indexed by language code
        this.regional[''] = { // Default regional settings
            hourText: 'Hour',           // Display text for hours section
            minuteText: 'Minute',       // Display text for minutes link
            amPmText: ['AM', 'PM'],     // Display text for AM PM
            closeButtonText: 'Done',        // Text for the confirmation button (ok button)
            nowButtonText: 'Now',           // Text for the now button
            deselectButtonText: 'Deselect'  // Text for the deselect button
        };
        this._defaults = { // Global defaults for all the time picker instances
            showOn: 'focus',    // 'focus' for popup on focus,
                                // 'button' for trigger button, or 'both' for either (not yet implemented)
            button: null,                   // 'button' element that will trigger the timepicker
            showAnim: 'fadeIn',             // Name of jQuery animation for popup
            showOptions: {},                // Options for enhanced animations
            appendText: '',                 // Display text following the input box, e.g. showing the format

            beforeShow: null,               // Define a callback function executed before the timepicker is shown
            onSelect: null,                 // Define a callback function when a hour / minutes is selected
            onClose: null,                  // Define a callback function when the timepicker is closed

            timeSeparator: ':',             // The character to use to separate hours and minutes.
            periodSeparator: ' ',           // The character to use to separate the time from the time period.
            showPeriod: false,              // Define whether or not to show AM/PM with selected time
            showPeriodLabels: true,         // Show the AM/PM labels on the left of the time picker
            showLeadingZero: true,          // Define whether or not to show a leading zero for hours < 10. [true/false]
            showMinutesLeadingZero: true,   // Define whether or not to show a leading zero for minutes < 10.
            altField: '',                   // Selector for an alternate field to store selected time into
            defaultTime: 'now',             // Used as default time when input field is empty or for inline timePicker
                                            // (set to 'now' for the current time, '' for no highlighted time)
            myPosition: 'left top',         // Position of the dialog relative to the input.
                                            // see the position utility for more info : http://jqueryui.com/demos/position/
            atPosition: 'left bottom',      // Position of the input element to match
                                            // Note : if the position utility is not loaded, the timepicker will attach left top to left bottom
            //NEW: 2011-02-03
            onHourShow: null,			    // callback for enabling / disabling on selectable hours  ex : function(hour) { return true; }
            onMinuteShow: null,             // callback for enabling / disabling on time selection  ex : function(hour,minute) { return true; }

            hours: {
                starts: 0,                  // first displayed hour
                ends: 23                    // last displayed hour
            },
            minutes: {
                starts: 0,                  // first displayed minute
                ends: 55,                   // last displayed minute
                interval: 5                 // interval of displayed minutes
            },
            rows: 4,                        // number of rows for the input tables, minimum 2, makes more sense if you use multiple of 2
            // 2011-08-05 0.2.4
            showHours: true,                // display the hours section of the dialog
            showMinutes: true,              // display the minute section of the dialog
            optionalMinutes: false,         // optionally parse inputs of whole hours with minutes omitted
						
            // buttons
            showCloseButton: false,         // shows an OK button to confirm the edit
            showNowButton: false,           // Shows the 'now' button
            showDeselectButton: false       // Shows the deselect time button

        };
        $.extend(this._defaults, this.regional['']);

        this.tpDiv = $('<div id="' + this._mainDivId + '" class="ui-timepicker ui-widget ui-helper-clearfix ui-corner-all " style="display: none"></div>');
    }

    $.extend(Timepicker.prototype, {
        /* Class name added to elements to indicate already configured with a time picker. */
        markerClassName: 'hasTimepicker',

        /* Debug logging (if enabled). */
        log: function () {
            if (this.debug)
                console.log.apply('', arguments);
        },

        _widgetTimepicker: function () {
            return this.tpDiv;
        },

        /* Override the default settings for all instances of the time picker.
        @param  settings  object - the new settings to use as defaults (anonymous object)
        @return the manager object */
        setDefaults: function (settings) {
            extendRemove(this._defaults, settings || {});
            return this;
        },

        /* Attach the time picker to a jQuery selection.
        @param  target    element - the target input field or division or span
        @param  settings  object - the new settings to use for this time picker instance (anonymous) */
        _attachTimepicker: function (target, settings) {
            // check for settings on the control itself - in namespace 'time:'
            var inlineSettings = null;
            for (var attrName in this._defaults) {
                var attrValue = target.getAttribute('time:' + attrName);
                if (attrValue) {
                    inlineSettings = inlineSettings || {};
                    try {
                        inlineSettings[attrName] = eval(attrValue);
                    } catch (err) {
                        inlineSettings[attrName] = attrValue;
                    }
                }
            }
            var nodeName = target.nodeName.toLowerCase();
            var inline = (nodeName == 'div' || nodeName == 'span');

            if (!target.id) {
                this.uuid += 1;
                target.id = 'tp' + this.uuid;
            }
            var inst = this._newInst($(target), inline);
            inst.settings = $.extend({}, settings || {}, inlineSettings || {});
            if (nodeName == 'input') {
                this._connectTimepicker(target, inst);
                // init inst.hours and inst.minutes from the input value
                this._setTimeFromField(inst);
            } else if (inline) {
                this._inlineTimepicker(target, inst);
            }


        },

        /* Create a new instance object. */
        _newInst: function (target, inline) {
            var id = target[0].id.replace(/([^A-Za-z0-9_-])/g, '\\\\$1'); // escape jQuery meta chars
            return {
                id: id, input: target, // associated target
                inline: inline, // is timepicker inline or not :
                tpDiv: (!inline ? this.tpDiv : // presentation div
                    $('<div class="' + this._inlineClass + ' ui-timepicker ui-widget  ui-helper-clearfix"></div>'))
            };
        },

        /* Attach the time picker to an input field. */
        _connectTimepicker: function (target, inst) {
            var input = $(target);
            inst.append = $([]);
            inst.trigger = $([]);
            if (input.hasClass(this.markerClassName)) { return; }
            this._attachments(input, inst);
            input.addClass(this.markerClassName).
                keydown(this._doKeyDown).
                keyup(this._doKeyUp).
                bind("setData.timepicker", function (event, key, value) {
                    inst.settings[key] = value;
                }).
                bind("getData.timepicker", function (event, key) {
                    return this._get(inst, key);
                });
            $.data(target, PROP_NAME, inst);
        },

        /* Handle keystrokes. */
        _doKeyDown: function (event) {
            var inst = $.timepicker._getInst(event.target);
            var handled = true;
            inst._keyEvent = true;
            if ($.timepicker._timepickerShowing) {
                switch (event.keyCode) {
                    case 9: $.timepicker._hideTimepicker();
                        handled = false;
                        break; // hide on tab out
                    case 13:
                        $.timepicker._updateSelectedValue(inst);
                        $.timepicker._hideTimepicker();
                            
						return false; // don't submit the form
						break; // select the value on enter
                    case 27: $.timepicker._hideTimepicker();
                        break; // hide on escape
                    default: handled = false;
                }
            }
            else if (event.keyCode == 36 && event.ctrlKey) { // display the time picker on ctrl+home
                $.timepicker._showTimepicker(this);
            }
            else {
                handled = false;
            }
            if (handled) {
                event.preventDefault();
                event.stopPropagation();
            }
        },

        /* Update selected time on keyUp */
        /* Added verion 0.0.5 */
        _doKeyUp: function (event) {
            var inst = $.timepicker._getInst(event.target);
            $.timepicker._setTimeFromField(inst);
            $.timepicker._updateTimepicker(inst);
        },

        /* Make attachments based on settings. */
        _attachments: function (input, inst) {
            var appendText = this._get(inst, 'appendText');
            var isRTL = this._get(inst, 'isRTL');
            if (inst.append) { inst.append.remove(); }
            if (appendText) {
                inst.append = $('<span class="' + this._appendClass + '">' + appendText + '</span>');
                input[isRTL ? 'before' : 'after'](inst.append);
            }
            input.unbind('focus.timepicker', this._showTimepicker);
            input.unbind('click.timepicker', this._adjustZIndex);

            if (inst.trigger) { inst.trigger.remove(); }

            var showOn = this._get(inst, 'showOn');
            if (showOn == 'focus' || showOn == 'both') { // pop-up time picker when in the marked field
                input.bind("focus.timepicker", this._showTimepicker);
                input.bind("click.timepicker", this._adjustZIndex);
            }
            if (showOn == 'button' || showOn == 'both') { // pop-up time picker when 'button' element is clicked
                var button = this._get(inst, 'button');
                $(button).bind("click.timepicker", function () {
                    if ($.timepicker._timepickerShowing && $.timepicker._lastInput == input[0]) {
                        $.timepicker._hideTimepicker();
                    } else if (!inst.input.is(':disabled')) {
                        $.timepicker._showTimepicker(input[0]);
                    }
                    return false;
                });

            }
        },


        /* Attach an inline time picker to a div. */
        _inlineTimepicker: function(target, inst) {
            var divSpan = $(target);
            if (divSpan.hasClass(this.markerClassName))
                return;
            divSpan.addClass(this.markerClassName).append(inst.tpDiv).
                bind("setData.timepicker", function(event, key, value){
                    inst.settings[key] = value;
                }).bind("getData.timepicker", function(event, key){
                    return this._get(inst, key);
                });
            $.data(target, PROP_NAME, inst);

            this._setTimeFromField(inst);
            this._updateTimepicker(inst);
            inst.tpDiv.show();
        },

        _adjustZIndex: function(input) {
            input = input.target || input;
            var inst = $.timepicker._getInst(input);
            inst.tpDiv.css('zIndex', 8000);
        },

        /* Pop-up the time picker for a given input field.
        @param  input  element - the input field attached to the time picker or
        event - if triggered by focus */
        _showTimepicker: function (input) {
            input = input.target || input;
            if (input.nodeName.toLowerCase() != 'input') { input = $('input', input.parentNode)[0]; } // find from button/image trigger

            if ($.timepicker._isDisabledTimepicker(input) || $.timepicker._lastInput == input) { return; } // already here

            // fix v 0.0.8 - close current timepicker before showing another one
            $.timepicker._hideTimepicker();

            var inst = $.timepicker._getInst(input);
            if ($.timepicker._curInst && $.timepicker._curInst != inst) {
                $.timepicker._curInst.tpDiv.stop(true, true);
            }
            var beforeShow = $.timepicker._get(inst, 'beforeShow');
            extendRemove(inst.settings, (beforeShow ? beforeShow.apply(input, [input, inst]) : {}));
            inst.lastVal = null;
            $.timepicker._lastInput = input;

            $.timepicker._setTimeFromField(inst);

            // calculate default position
            if ($.timepicker._inDialog) { input.value = ''; } // hide cursor
            if (!$.timepicker._pos) { // position below input
                $.timepicker._pos = $.timepicker._findPos(input);
                $.timepicker._pos[1] += input.offsetHeight; // add the height
            }
            var isFixed = false;
            $(input).parents().each(function () {
                isFixed |= $(this).css('position') == 'fixed';
                return !isFixed;
            });
            if (isFixed && $.browser.opera) { // correction for Opera when fixed and scrolled
                $.timepicker._pos[0] -= document.documentElement.scrollLeft;
                $.timepicker._pos[1] -= document.documentElement.scrollTop;
            }

            var offset = { left: $.timepicker._pos[0], top: $.timepicker._pos[1] };

            $.timepicker._pos = null;
            // determine sizing offscreen
            inst.tpDiv.css({ position: 'absolute', display: 'block', top: '-1000px' });
            $.timepicker._updateTimepicker(inst);


            // position with the ui position utility, if loaded
            if ( ( ! inst.inline )  && ( typeof $.ui.position == 'object' ) ) {
                inst.tpDiv.position({
                    of: inst.input,
                    my: $.timepicker._get( inst, 'myPosition' ),
                    at: $.timepicker._get( inst, 'atPosition' ),
                    // offset: $( "#offset" ).val(),
                    // using: using,
                    collision: 'flip'
                });
                var offset = inst.tpDiv.offset();
                $.timepicker._pos = [offset.top, offset.left];
            }
		  

            // reset clicked state
            inst._hoursClicked = false;
            inst._minutesClicked = false;

            // fix width for dynamic number of time pickers
            // and adjust position before showing
            offset = $.timepicker._checkOffset(inst, offset, isFixed);
            inst.tpDiv.css({ position: ($.timepicker._inDialog && $.blockUI ?
			    'static' : (isFixed ? 'fixed' : 'absolute')), display: 'none',
                left: offset.left + 'px', top: offset.top + 'px'
            });
            if ( ! inst.inline ) {
                var showAnim = $.timepicker._get(inst, 'showAnim');
                var duration = $.timepicker._get(inst, 'duration');

                var postProcess = function () {
                    $.timepicker._timepickerShowing = true;
                    var borders = $.timepicker._getBorders(inst.tpDiv);
                    inst.tpDiv.find('iframe.ui-timepicker-cover'). // IE6- only
					css({ left: -borders[0], top: -borders[1],
					    width: inst.tpDiv.outerWidth(), height: inst.tpDiv.outerHeight()
					});
                };

                // Fixed the zIndex problem for real (I hope) - FG - v 0.2.9
                $.timepicker._adjustZIndex(input);
                //inst.tpDiv.css('zIndex', $.timepicker._getZIndex(input) +1);

                if ($.effects && $.effects[showAnim]) {
                    inst.tpDiv.show(showAnim, $.timepicker._get(inst, 'showOptions'), duration, postProcess);
                }
                else {
                    inst.tpDiv.show((showAnim ? duration : null), postProcess);
                }
                if (!showAnim || !duration) { postProcess(); }
                if (inst.input.is(':visible') && !inst.input.is(':disabled')) { inst.input.focus(); }
                $.timepicker._curInst = inst;
            }
        },

        // This is a copy of the zIndex function of UI core 1.8.??
        // Copied in the timepicker to stay backward compatible.
        _getZIndex: function (target) {
            var elem = $( target ), position, value;
            while ( elem.length && elem[ 0 ] !== document ) {
                position = elem.css( "position" );
                if ( position === "absolute" || position === "relative" || position === "fixed" ) {
                    value = parseInt( elem.css( "zIndex" ), 10 );
                    if ( !isNaN( value ) && value !== 0 ) {
                        return value;
                    }
                }
                elem = elem.parent();
            }
        },

        /* Refresh the time picker
           @param   target  element - The target input field or inline container element. */
        _refreshTimepicker: function(target) {
            var inst = this._getInst(target);
            if (inst) {
                this._updateTimepicker(inst);
            }
        },


        /* Generate the time picker content. */
        _updateTimepicker: function (inst) {
            inst.tpDiv.empty().append(this._generateHTML(inst));
            this._rebindDialogEvents(inst);

        },

        _rebindDialogEvents: function (inst) {
            var borders = $.timepicker._getBorders(inst.tpDiv),
                self = this;
            inst.tpDiv
			.find('iframe.ui-timepicker-cover') // IE6- only
				.css({ left: -borders[0], top: -borders[1],
				    width: inst.tpDiv.outerWidth(), height: inst.tpDiv.outerHeight()
				})
			.end()
            // after the picker html is appended bind the click & double click events (faster in IE this way
            // then letting the browser interpret the inline events)
            // the binding for the minute cells also exists in _updateMinuteDisplay
            .find('.ui-timepicker-minute-cell')
                .unbind()
                .bind("click", { fromDoubleClick:false }, $.proxy($.timepicker.selectMinutes, this))
                .bind("dblclick", { fromDoubleClick:true }, $.proxy($.timepicker.selectMinutes, this))
            .end()
            .find('.ui-timepicker-hour-cell')
                .unbind()
                .bind("click", { fromDoubleClick:false }, $.proxy($.timepicker.selectHours, this))
                .bind("dblclick", { fromDoubleClick:true }, $.proxy($.timepicker.selectHours, this))
            .end()
			.find('.ui-timepicker td a')
                .unbind()
				.bind('mouseout', function () {
				    $(this).removeClass('ui-state-hover');
				    if (this.className.indexOf('ui-timepicker-prev') != -1) $(this).removeClass('ui-timepicker-prev-hover');
				    if (this.className.indexOf('ui-timepicker-next') != -1) $(this).removeClass('ui-timepicker-next-hover');
				})
				.bind('mouseover', function () {
				    if ( ! self._isDisabledTimepicker(inst.inline ? inst.tpDiv.parent()[0] : inst.input[0])) {
				        $(this).parents('.ui-timepicker-calendar').find('a').removeClass('ui-state-hover');
				        $(this).addClass('ui-state-hover');
				        if (this.className.indexOf('ui-timepicker-prev') != -1) $(this).addClass('ui-timepicker-prev-hover');
				        if (this.className.indexOf('ui-timepicker-next') != -1) $(this).addClass('ui-timepicker-next-hover');
				    }
				})
			.end()
			.find('.' + this._dayOverClass + ' a')
				.trigger('mouseover')
			.end()
            .find('.ui-timepicker-now').bind("click", function(e) {
                    $.timepicker.selectNow(e);
            }).end()
            .find('.ui-timepicker-deselect').bind("click",function(e) {
                    $.timepicker.deselectTime(e);
            }).end()
            .find('.ui-timepicker-close').bind("click",function(e) {
                    $.timepicker._hideTimepicker();
            }).end();
        },

        /* Generate the HTML for the current state of the time picker. */
        _generateHTML: function (inst) {

            var h, m, row, col, html, hoursHtml, minutesHtml = '',
                showPeriod = (this._get(inst, 'showPeriod') == true),
                showPeriodLabels = (this._get(inst, 'showPeriodLabels') == true),
                showLeadingZero = (this._get(inst, 'showLeadingZero') == true),
                showHours = (this._get(inst, 'showHours') == true),
                showMinutes = (this._get(inst, 'showMinutes') == true),
                amPmText = this._get(inst, 'amPmText'),
                rows = this._get(inst, 'rows'),
                amRows = 0,
                pmRows = 0,
                amItems = 0,
                pmItems = 0,
                amFirstRow = 0,
                pmFirstRow = 0,
                hours = Array(),
                hours_options = this._get(inst, 'hours'),
                hoursPerRow = null,
                hourCounter = 0,
                hourLabel = this._get(inst, 'hourText'),
                showCloseButton = this._get(inst, 'showCloseButton'),
                closeButtonText = this._get(inst, 'closeButtonText'),
                showNowButton = this._get(inst, 'showNowButton'),
                nowButtonText = this._get(inst, 'nowButtonText'),
                showDeselectButton = this._get(inst, 'showDeselectButton'),
                deselectButtonText = this._get(inst, 'deselectButtonText'),
                showButtonPanel = showCloseButton || showNowButton || showDeselectButton;
            


            // prepare all hours and minutes, makes it easier to distribute by rows
            for (h = hours_options.starts; h <= hours_options.ends; h++) {
                hours.push (h);
            }
            hoursPerRow = Math.ceil(hours.length / rows); // always round up

            if (showPeriodLabels) {
                for (hourCounter = 0; hourCounter < hours.length; hourCounter++) {
                    if (hours[hourCounter] < 12) {
                        amItems++;
                    }
                    else {
                        pmItems++;
                    }
                }
                hourCounter = 0; 

                amRows = Math.floor(amItems / hours.length * rows);
                pmRows = Math.floor(pmItems / hours.length * rows);

                // assign the extra row to the period that is more densely populated
                if (rows != amRows + pmRows) {
                    // Make sure: AM Has Items and either PM Does Not, AM has no rows yet, or AM is more dense
                    if (amItems && (!pmItems || !amRows || (pmRows && amItems / amRows >= pmItems / pmRows))) {
                        amRows++;
                    } else {
                        pmRows++;
                    }
                }
                amFirstRow = Math.min(amRows, 1);
                pmFirstRow = amRows + 1;

                if (amRows == 0) {
                    hoursPerRow = Math.ceil(pmItems / pmRows);
                } else if (pmRows == 0) {
                    hoursPerRow = Math.ceil(amItems / amRows);
                } else {
                    hoursPerRow = Math.ceil(Math.max(amItems / amRows, pmItems / pmRows));
                }
            }


            html = '<table class="ui-timepicker-table ui-widget-content ui-corner-all"><tr>';

            if (showHours) {

                html += '<td class="ui-timepicker-hours">' +
                        '<div class="ui-timepicker-title ui-widget-header ui-helper-clearfix ui-corner-all">' +
                        hourLabel +
                        '</div>' +
                        '<table class="ui-timepicker">';

                for (row = 1; row <= rows; row++) {
                    html += '<tr>';
                    // AM
                    if (row == amFirstRow && showPeriodLabels) {
                        html += '<th rowspan="' + amRows.toString() + '" class="periods" scope="row">' + amPmText[0] + '</th>';
                    }
                    // PM
                    if (row == pmFirstRow && showPeriodLabels) {
                        html += '<th rowspan="' + pmRows.toString() + '" class="periods" scope="row">' + amPmText[1] + '</th>';
                    }
                    for (col = 1; col <= hoursPerRow; col++) {
                        if (showPeriodLabels && row < pmFirstRow && hours[hourCounter] >= 12) {
                            html += this._generateHTMLHourCell(inst, undefined, showPeriod, showLeadingZero);
                        } else {
                            html += this._generateHTMLHourCell(inst, hours[hourCounter], showPeriod, showLeadingZero);
                            hourCounter++;
                        }
                    }
                    html += '</tr>';
                }
                html += '</table>' + // Close the hours cells table
                        '</td>'; // Close the Hour td
            }

            if (showMinutes) {
                html += '<td class="ui-timepicker-minutes">';
                html += this._generateHTMLMinutes(inst);
                html += '</td>';
            }
            
            html += '</tr>';


            if (showButtonPanel) {
                var buttonPanel = '<tr><td colspan="3"><div class="ui-timepicker-buttonpane ui-widget-content">';
                if (showNowButton) {
                    buttonPanel += '<button type="button" class="ui-timepicker-now ui-state-default ui-corner-all" '
                                   + ' data-timepicker-instance-id="#' + inst.id.replace(/\\\\/g,"\\") + '" >'
                                   + nowButtonText + '</button>';
                }
                if (showDeselectButton) {
                    buttonPanel += '<button type="button" class="ui-timepicker-deselect ui-state-default ui-corner-all" '
                                   + ' data-timepicker-instance-id="#' + inst.id.replace(/\\\\/g,"\\") + '" >'
                                   + deselectButtonText + '</button>';
                }
                if (showCloseButton) {
                    buttonPanel += '<button type="button" class="ui-timepicker-close ui-state-default ui-corner-all" '
                                   + ' data-timepicker-instance-id="#' + inst.id.replace(/\\\\/g,"\\") + '" >'
                                   + closeButtonText + '</button>';
                }

                html += buttonPanel + '</div></td></tr>';
            }
            html += '</table>';

            return html;
        },

        /* Special function that update the minutes selection in currently visible timepicker
         * called on hour selection when onMinuteShow is defined  */
        _updateMinuteDisplay: function (inst) {
            var newHtml = this._generateHTMLMinutes(inst);
            inst.tpDiv.find('td.ui-timepicker-minutes').html(newHtml);
            this._rebindDialogEvents(inst);
                // after the picker html is appended bind the click & double click events (faster in IE this way
                // then letting the browser interpret the inline events)
                // yes I know, duplicate code, sorry
/*                .find('.ui-timepicker-minute-cell')
                    .bind("click", { fromDoubleClick:false }, $.proxy($.timepicker.selectMinutes, this))
                    .bind("dblclick", { fromDoubleClick:true }, $.proxy($.timepicker.selectMinutes, this));
*/

        },

        /*
         * Generate the minutes table
         * This is separated from the _generateHTML function because is can be called separately (when hours changes)
         */
        _generateHTMLMinutes: function (inst) {

            var m, row, html = '',
                rows = this._get(inst, 'rows'),
                minutes = Array(),
                minutes_options = this._get(inst, 'minutes'),
                minutesPerRow = null,
                minuteCounter = 0,
                showMinutesLeadingZero = (this._get(inst, 'showMinutesLeadingZero') == true),
                onMinuteShow = this._get(inst, 'onMinuteShow'),
                minuteLabel = this._get(inst, 'minuteText');

            if ( ! minutes_options.starts) {
                minutes_options.starts = 0;
            }
            if ( ! minutes_options.ends) {
                minutes_options.ends = 59;
            }
            for (m = minutes_options.starts; m <= minutes_options.ends; m += minutes_options.interval) {
                minutes.push(m);
            }
            minutesPerRow = Math.round(minutes.length / rows + 0.49); // always round up

            /*
             * The minutes table
             */
            // if currently selected minute is not enabled, we have a problem and need to select a new minute.
            if (onMinuteShow &&
                (onMinuteShow.apply((inst.input ? inst.input[0] : null), [inst.hours , inst.minutes]) == false) ) {
                // loop minutes and select first available
                for (minuteCounter = 0; minuteCounter < minutes.length; minuteCounter += 1) {
                    m = minutes[minuteCounter];
                    if (onMinuteShow.apply((inst.input ? inst.input[0] : null), [inst.hours, m])) {
                        inst.minutes = m;
                        break;
                    }
                }
            }



            html += '<div class="ui-timepicker-title ui-widget-header ui-helper-clearfix ui-corner-all">' +
                    minuteLabel +
                    '</div>' +
                    '<table class="ui-timepicker">';
            
            minuteCounter = 0;
            for (row = 1; row <= rows; row++) {
                html += '<tr>';
                while (minuteCounter < row * minutesPerRow) {
                    var m = minutes[minuteCounter];
                    var displayText = '';
                    if (m !== undefined ) {
                        displayText = (m < 10) && showMinutesLeadingZero ? "0" + m.toString() : m.toString();
                    }
                    html += this._generateHTMLMinuteCell(inst, m, displayText);
                    minuteCounter++;
                }
                html += '</tr>';
            }

            html += '</table>';

            return html;
        },

        /* Generate the content of a "Hour" cell */
        _generateHTMLHourCell: function (inst, hour, showPeriod, showLeadingZero) {

            var displayHour = hour;
            if ((hour > 12) && showPeriod) {
                displayHour = hour - 12;
            }
            if ((displayHour == 0) && showPeriod) {
                displayHour = 12;
            }
            if ((displayHour < 10) && showLeadingZero) {
                displayHour = '0' + displayHour;
            }

            var html = "";
            var enabled = true;
            var onHourShow = this._get(inst, 'onHourShow');		//custom callback

            if (hour == undefined) {
                html = '<td><span class="ui-state-default ui-state-disabled">&nbsp;</span></td>';
                return html;
            }

            if (onHourShow) {
            	enabled = onHourShow.apply((inst.input ? inst.input[0] : null), [hour]);
            }

            if (enabled) {
                html = '<td class="ui-timepicker-hour-cell" data-timepicker-instance-id="#' + inst.id.replace(/\\\\/g,"\\") + '" data-hour="' + hour.toString() + '">' +
                   '<a class="ui-state-default ' +
                   (hour == inst.hours ? 'ui-state-active' : '') +
                   '">' +
                   displayHour.toString() +
                   '</a></td>';
            }
            else {
            	html =
            		'<td>' +
		                '<span class="ui-state-default ui-state-disabled ' +
		                (hour == inst.hours ? ' ui-state-active ' : ' ') +
		                '">' +
		                displayHour.toString() +
		                '</span>' +
		            '</td>';
            }
            return html;
        },

        /* Generate the content of a "Hour" cell */
        _generateHTMLMinuteCell: function (inst, minute, displayText) {
        	 var html = "";
             var enabled = true;
             var onMinuteShow = this._get(inst, 'onMinuteShow');		//custom callback
             if (onMinuteShow) {
            	 //NEW: 2011-02-03  we should give the hour as a parameter as well!
             	enabled = onMinuteShow.apply((inst.input ? inst.input[0] : null), [inst.hours,minute]);		//trigger callback
             }

             if (minute == undefined) {
                 html = '<td><span class="ui-state-default ui-state-disabled">&nbsp;</span></td>';
                 return html;
             }

             if (enabled) {
	             html = '<td class="ui-timepicker-minute-cell" data-timepicker-instance-id="#' + inst.id.replace(/\\\\/g,"\\") + '" data-minute="' + minute.toString() + '" >' +
	                   '<a class="ui-state-default ' +
	                   (minute == inst.minutes ? 'ui-state-active' : '') +
	                   '" >' +
	                   displayText +
	                   '</a></td>';
             }
             else {

            	html = '<td>' +
	                 '<span class="ui-state-default ui-state-disabled" >' +
	                 	displayText +
	                 '</span>' +
                 '</td>';
             }
             return html;
        },


        /* Detach a timepicker from its control.
           @param  target    element - the target input field or division or span */
        _destroyTimepicker: function(target) {
            var $target = $(target);
            var inst = $.data(target, PROP_NAME);
            if (!$target.hasClass(this.markerClassName)) {
                return;
            }
            var nodeName = target.nodeName.toLowerCase();
            $.removeData(target, PROP_NAME);
            if (nodeName == 'input') {
                inst.append.remove();
                inst.trigger.remove();
                $target.removeClass(this.markerClassName)
                    .unbind('focus.timepicker', this._showTimepicker)
                    .unbind('click.timepicker', this._adjustZIndex);
            } else if (nodeName == 'div' || nodeName == 'span')
                $target.removeClass(this.markerClassName).empty();
        },

        /* Enable the date picker to a jQuery selection.
           @param  target    element - the target input field or division or span */
        _enableTimepicker: function(target) {
            var $target = $(target),
                target_id = $target.attr('id'),
                inst = $.data(target, PROP_NAME);
            
            if (!$target.hasClass(this.markerClassName)) {
                return;
            }
            var nodeName = target.nodeName.toLowerCase();
            if (nodeName == 'input') {
                target.disabled = false;
                var button = this._get(inst, 'button');
                $(button).removeClass('ui-state-disabled').disabled = false;
                inst.trigger.filter('button').
                    each(function() { this.disabled = false; }).end();
            }
            else if (nodeName == 'div' || nodeName == 'span') {
                var inline = $target.children('.' + this._inlineClass);
                inline.children().removeClass('ui-state-disabled');
                inline.find('button').each(
                    function() { this.disabled = false }
                )
            }
            this._disabledInputs = $.map(this._disabledInputs,
                function(value) { return (value == target_id ? null : value); }); // delete entry
        },

        /* Disable the time picker to a jQuery selection.
           @param  target    element - the target input field or division or span */
        _disableTimepicker: function(target) {
            var $target = $(target);
            var inst = $.data(target, PROP_NAME);
            if (!$target.hasClass(this.markerClassName)) {
                return;
            }
            var nodeName = target.nodeName.toLowerCase();
            if (nodeName == 'input') {
                var button = this._get(inst, 'button');

                $(button).addClass('ui-state-disabled').disabled = true;
                target.disabled = true;

                inst.trigger.filter('button').
                    each(function() { this.disabled = true; }).end();

            }
            else if (nodeName == 'div' || nodeName == 'span') {
                var inline = $target.children('.' + this._inlineClass);
                inline.children().addClass('ui-state-disabled');
                inline.find('button').each(
                    function() { this.disabled = true }
                )

            }
            this._disabledInputs = $.map(this._disabledInputs,
                function(value) { return (value == target ? null : value); }); // delete entry
            this._disabledInputs[this._disabledInputs.length] = $target.attr('id');
        },

        /* Is the first field in a jQuery collection disabled as a timepicker?
        @param  target_id element - the target input field or division or span
        @return boolean - true if disabled, false if enabled */
        _isDisabledTimepicker: function (target_id) {
            if ( ! target_id) { return false; }
            for (var i = 0; i < this._disabledInputs.length; i++) {
                if (this._disabledInputs[i] == target_id) { return true; }
            }
            return false;
        },

        /* Check positioning to remain on screen. */
        _checkOffset: function (inst, offset, isFixed) {
            var tpWidth = inst.tpDiv.outerWidth();
            var tpHeight = inst.tpDiv.outerHeight();
            var inputWidth = inst.input ? inst.input.outerWidth() : 0;
            var inputHeight = inst.input ? inst.input.outerHeight() : 0;
            var viewWidth = document.documentElement.clientWidth + $(document).scrollLeft();
            var viewHeight = document.documentElement.clientHeight + $(document).scrollTop();

            offset.left -= (this._get(inst, 'isRTL') ? (tpWidth - inputWidth) : 0);
            offset.left -= (isFixed && offset.left == inst.input.offset().left) ? $(document).scrollLeft() : 0;
            offset.top -= (isFixed && offset.top == (inst.input.offset().top + inputHeight)) ? $(document).scrollTop() : 0;

            // now check if datepicker is showing outside window viewport - move to a better place if so.
            offset.left -= Math.min(offset.left, (offset.left + tpWidth > viewWidth && viewWidth > tpWidth) ?
			Math.abs(offset.left + tpWidth - viewWidth) : 0);
            offset.top -= Math.min(offset.top, (offset.top + tpHeight > viewHeight && viewHeight > tpHeight) ?
			Math.abs(tpHeight + inputHeight) : 0);

            return offset;
        },

        /* Find an object's position on the screen. */
        _findPos: function (obj) {
            var inst = this._getInst(obj);
            var isRTL = this._get(inst, 'isRTL');
            while (obj && (obj.type == 'hidden' || obj.nodeType != 1)) {
                obj = obj[isRTL ? 'previousSibling' : 'nextSibling'];
            }
            var position = $(obj).offset();
            return [position.left, position.top];
        },

        /* Retrieve the size of left and top borders for an element.
        @param  elem  (jQuery object) the element of interest
        @return  (number[2]) the left and top borders */
        _getBorders: function (elem) {
            var convert = function (value) {
                return { thin: 1, medium: 2, thick: 3}[value] || value;
            };
            return [parseFloat(convert(elem.css('border-left-width'))),
			parseFloat(convert(elem.css('border-top-width')))];
        },


        /* Close time picker if clicked elsewhere. */
        _checkExternalClick: function (event) {
            if (!$.timepicker._curInst) { return; }
            var $target = $(event.target);
            if ($target[0].id != $.timepicker._mainDivId &&
				$target.parents('#' + $.timepicker._mainDivId).length == 0 &&
				!$target.hasClass($.timepicker.markerClassName) &&
				!$target.hasClass($.timepicker._triggerClass) &&
				$.timepicker._timepickerShowing && !($.timepicker._inDialog && $.blockUI))
                $.timepicker._hideTimepicker();
        },

        /* Hide the time picker from view.
        @param  input  element - the input field attached to the time picker */
        _hideTimepicker: function (input) {
            var inst = this._curInst;
            if (!inst || (input && inst != $.data(input, PROP_NAME))) { return; }
            if (this._timepickerShowing) {
                var showAnim = this._get(inst, 'showAnim');
                var duration = this._get(inst, 'duration');
                var postProcess = function () {
                    $.timepicker._tidyDialog(inst);
                    this._curInst = null;
                };
                if ($.effects && $.effects[showAnim]) {
                    inst.tpDiv.hide(showAnim, $.timepicker._get(inst, 'showOptions'), duration, postProcess);
                }
                else {
                    inst.tpDiv[(showAnim == 'slideDown' ? 'slideUp' :
					    (showAnim == 'fadeIn' ? 'fadeOut' : 'hide'))]((showAnim ? duration : null), postProcess);
                }
                if (!showAnim) { postProcess(); }

                this._timepickerShowing = false;

                this._lastInput = null;
                if (this._inDialog) {
                    this._dialogInput.css({ position: 'absolute', left: '0', top: '-100px' });
                    if ($.blockUI) {
                        $.unblockUI();
                        $('body').append(this.tpDiv);
                    }
                }
                this._inDialog = false;

                var onClose = this._get(inst, 'onClose');
                 if (onClose) {
                     onClose.apply(
                         (inst.input ? inst.input[0] : null),
 					    [(inst.input ? inst.input.val() : ''), inst]);  // trigger custom callback
                 }

            }
        },



        /* Tidy up after a dialog display. */
        _tidyDialog: function (inst) {
            inst.tpDiv.removeClass(this._dialogClass).unbind('.ui-timepicker');
        },

        /* Retrieve the instance data for the target control.
        @param  target  element - the target input field or division or span
        @return  object - the associated instance data
        @throws  error if a jQuery problem getting data */
        _getInst: function (target) {
            try {
                return $.data(target, PROP_NAME);
            }
            catch (err) {
                throw 'Missing instance data for this timepicker';
            }
        },

        /* Get a setting value, defaulting if necessary. */
        _get: function (inst, name) {
            return inst.settings[name] !== undefined ?
			inst.settings[name] : this._defaults[name];
        },

        /* Parse existing time and initialise time picker. */
        _setTimeFromField: function (inst) {
            if (inst.input.val() == inst.lastVal) { return; }
            var defaultTime = this._get(inst, 'defaultTime');

            var timeToParse = defaultTime == 'now' ? this._getCurrentTimeRounded(inst) : defaultTime;
            if ((inst.inline == false) && (inst.input.val() != '')) { timeToParse = inst.input.val() }

            if (timeToParse instanceof Date) {
                inst.hours = timeToParse.getHours();
                inst.minutes = timeToParse.getMinutes();
            } else {
                var timeVal = inst.lastVal = timeToParse;
                if (timeToParse == '') {
                    inst.hours = -1;
                    inst.minutes = -1;
                } else {
                    var time = this.parseTime(inst, timeVal);
                    inst.hours = time.hours;
                    inst.minutes = time.minutes;
                }
            }


            $.timepicker._updateTimepicker(inst);
        },

        /* Update or retrieve the settings for an existing time picker.
           @param  target  element - the target input field or division or span
           @param  name    object - the new settings to update or
                           string - the name of the setting to change or retrieve,
                           when retrieving also 'all' for all instance settings or
                           'defaults' for all global defaults
           @param  value   any - the new value for the setting
                       (omit if above is an object or to retrieve a value) */
        _optionTimepicker: function(target, name, value) {
            var inst = this._getInst(target);
            if (arguments.length == 2 && typeof name == 'string') {
                return (name == 'defaults' ? $.extend({}, $.timepicker._defaults) :
                    (inst ? (name == 'all' ? $.extend({}, inst.settings) :
                    this._get(inst, name)) : null));
            }
            var settings = name || {};
            if (typeof name == 'string') {
                settings = {};
                settings[name] = value;
            }
            if (inst) {
                if (this._curInst == inst) {
                    this._hideTimepicker();
                }
                extendRemove(inst.settings, settings);
                this._updateTimepicker(inst);
            }
        },


        /* Set the time for a jQuery selection.
	    @param  target  element - the target input field or division or span
	    @param  time    String - the new time */
	    _setTimeTimepicker: function(target, time) {
		    var inst = this._getInst(target);
		    if (inst) {
			    this._setTime(inst, time);
    			this._updateTimepicker(inst);
	    		this._updateAlternate(inst, time);
		    }
	    },

        /* Set the time directly. */
        _setTime: function(inst, time, noChange) {
            var origHours = inst.hours;
            var origMinutes = inst.minutes;
            if (time instanceof Date) {
                inst.hours = time.getHours();
                inst.minutes = time.getMinutes();
            } else {
                var time = this.parseTime(inst, time);
                inst.hours = time.hours;
                inst.minutes = time.minutes;
            }

            if ((origHours != inst.hours || origMinutes != inst.minutes) && !noChange) {
                inst.input.trigger('change');
            }
            this._updateTimepicker(inst);
            this._updateSelectedValue(inst);
        },

        /* Return the current time, ready to be parsed, rounded to the closest 5 minute */
        _getCurrentTimeRounded: function (inst) {
            var currentTime = new Date(),
                currentMinutes = currentTime.getMinutes(),
                // round to closest 5
                adjustedMinutes = Math.round( currentMinutes / 5 ) * 5;
            currentTime.setMinutes(adjustedMinutes);
            return currentTime;
        },

        /*
        * Parse a time string into hours and minutes
        */
        parseTime: function (inst, timeVal) {
            var retVal = new Object();
            retVal.hours = -1;
            retVal.minutes = -1;
            
            if(!timeVal)
                return '';

            var timeSeparator = this._get(inst, 'timeSeparator'),
                amPmText = this._get(inst, 'amPmText'),
                showHours = this._get(inst, 'showHours'),
                showMinutes = this._get(inst, 'showMinutes'),
                optionalMinutes = this._get(inst, 'optionalMinutes'),
                showPeriod = (this._get(inst, 'showPeriod') == true),
                p = timeVal.indexOf(timeSeparator);

            // check if time separator found
            if (p != -1) {
                retVal.hours = parseInt(timeVal.substr(0, p), 10);
                retVal.minutes = parseInt(timeVal.substr(p + 1), 10);
            }
            // check for hours only
            else if ( (showHours) && ( !showMinutes || optionalMinutes ) ) {
                retVal.hours = parseInt(timeVal, 10);
            }
            // check for minutes only
            else if ( ( ! showHours) && (showMinutes) ) {
                retVal.minutes = parseInt(timeVal, 10);
            }

            if (showHours) {
                var timeValUpper = timeVal.toUpperCase();
                if ((retVal.hours < 12) && (showPeriod) && (timeValUpper.indexOf(amPmText[1].toUpperCase()) != -1)) {
                    retVal.hours += 12;
                }
                // fix for 12 AM
                if ((retVal.hours == 12) && (showPeriod) && (timeValUpper.indexOf(amPmText[0].toUpperCase()) != -1)) {
                    retVal.hours = 0;
                }
            }
            
            return retVal;
        },

        selectNow: function(event) {
            var id = $(event.target).attr("data-timepicker-instance-id"),
                $target = $(id),
                inst = this._getInst($target[0]);
            //if (!inst || (input && inst != $.data(input, PROP_NAME))) { return; }
            var currentTime = new Date();
            inst.hours = currentTime.getHours();
            inst.minutes = currentTime.getMinutes();
            this._updateSelectedValue(inst);
            this._updateTimepicker(inst);
            this._hideTimepicker();
        },

        deselectTime: function(event) {
            var id = $(event.target).attr("data-timepicker-instance-id"),
                $target = $(id),
                inst = this._getInst($target[0]);
            inst.hours = -1;
            inst.minutes = -1;
            this._updateSelectedValue(inst);
            this._hideTimepicker();
        },


        selectHours: function (event) {
            var $td = $(event.currentTarget),
                id = $td.attr("data-timepicker-instance-id"),
                newHours = parseInt($td.attr("data-hour")),
                fromDoubleClick = event.data.fromDoubleClick,
                $target = $(id),
                inst = this._getInst($target[0]),
                showMinutes = (this._get(inst, 'showMinutes') == true);

            // don't select if disabled
            if ( $.timepicker._isDisabledTimepicker($target.attr('id')) ) { return false }

            $td.parents('.ui-timepicker-hours:first').find('a').removeClass('ui-state-active');
            $td.children('a').addClass('ui-state-active');
            inst.hours = newHours;

            // added for onMinuteShow callback
            var onMinuteShow = this._get(inst, 'onMinuteShow');
            if (onMinuteShow) {
                // this will trigger a callback on selected hour to make sure selected minute is allowed. 
                this._updateMinuteDisplay(inst);
            }

            this._updateSelectedValue(inst);

            inst._hoursClicked = true;
            if ((inst._minutesClicked) || (fromDoubleClick) || (showMinutes == false)) {
                $.timepicker._hideTimepicker();
            }
            // return false because if used inline, prevent the url to change to a hashtag
            return false;
        },

        selectMinutes: function (event) {
            var $td = $(event.currentTarget),
                id = $td.attr("data-timepicker-instance-id"),
                newMinutes = parseInt($td.attr("data-minute")),
                fromDoubleClick = event.data.fromDoubleClick,
                $target = $(id),
                inst = this._getInst($target[0]),
                showHours = (this._get(inst, 'showHours') == true);

            // don't select if disabled
            if ( $.timepicker._isDisabledTimepicker($target.attr('id')) ) { return false }

            $td.parents('.ui-timepicker-minutes:first').find('a').removeClass('ui-state-active');
            $td.children('a').addClass('ui-state-active');

            inst.minutes = newMinutes;
            this._updateSelectedValue(inst);

            inst._minutesClicked = true;
            if ((inst._hoursClicked) || (fromDoubleClick) || (showHours == false)) {
                $.timepicker._hideTimepicker();
                // return false because if used inline, prevent the url to change to a hashtag
                return false;
            }

            // return false because if used inline, prevent the url to change to a hashtag
            return false;
        },

        _updateSelectedValue: function (inst) {
            var newTime = this._getParsedTime(inst);
            if (inst.input) {
                inst.input.val(newTime);
                inst.input.trigger('change');
            }
            var onSelect = this._get(inst, 'onSelect');
            if (onSelect) { onSelect.apply((inst.input ? inst.input[0] : null), [newTime, inst]); } // trigger custom callback
            this._updateAlternate(inst, newTime);
            return newTime;
        },
        
        /* this function process selected time and return it parsed according to instance options */
        _getParsedTime: function(inst) {

            if (inst.hours == -1 && inst.minutes == -1) {
                return '';
            }

            // default to 0 AM if hours is not valid
            if ((inst.hours < inst.hours.starts) || (inst.hours > inst.hours.ends )) { inst.hours = 0; }
            // default to 0 minutes if minute is not valid
            if ((inst.minutes < inst.minutes.starts) || (inst.minutes > inst.minutes.ends)) { inst.minutes = 0; }

            var period = "",
                showPeriod = (this._get(inst, 'showPeriod') == true),
                showLeadingZero = (this._get(inst, 'showLeadingZero') == true),
                showHours = (this._get(inst, 'showHours') == true),
                showMinutes = (this._get(inst, 'showMinutes') == true),
                optionalMinutes = (this._get(inst, 'optionalMinutes') == true),
                amPmText = this._get(inst, 'amPmText'),
                selectedHours = inst.hours ? inst.hours : 0,
                selectedMinutes = inst.minutes ? inst.minutes : 0,
                displayHours = selectedHours ? selectedHours : 0,
                parsedTime = '';

            // fix some display problem when hours or minutes are not selected yet
            if (displayHours == -1) { displayHours = 0 }
            if (selectedMinutes == -1) { selectedMinutes = 0 }

            if (showPeriod) { 
                if (inst.hours == 0) {
                    displayHours = 12;
                }
                if (inst.hours < 12) {
                    period = amPmText[0];
                }
                else {
                    period = amPmText[1];
                    if (displayHours > 12) {
                        displayHours -= 12;
                    }
                }
            }

            var h = displayHours.toString();
            if (showLeadingZero && (displayHours < 10)) { h = '0' + h; }

            var m = selectedMinutes.toString();
            if (selectedMinutes < 10) { m = '0' + m; }

            if (showHours) {
                parsedTime += h;
            }
            if (showHours && showMinutes && (!optionalMinutes || m != 0)) {
                parsedTime += this._get(inst, 'timeSeparator');
            }
            if (showMinutes && (!optionalMinutes || m != 0)) {
                parsedTime += m;
            }
            if (showHours) {
                if (period.length > 0) { parsedTime += this._get(inst, 'periodSeparator') + period; }
            }
            
            return parsedTime;
        },
        
        /* Update any alternate field to synchronise with the main field. */
        _updateAlternate: function(inst, newTime) {
            var altField = this._get(inst, 'altField');
            if (altField) { // update alternate field too
                $(altField).each(function(i,e) {
                    $(e).val(newTime);
                });
            }
        },

        _getTimeAsDateTimepicker: function(input) {
            var inst = this._getInst(input);
            if (inst.hours == -1 && inst.minutes == -1) {
                return '';
            }

            // default to 0 AM if hours is not valid
            if ((inst.hours < inst.hours.starts) || (inst.hours > inst.hours.ends )) { inst.hours = 0; }
            // default to 0 minutes if minute is not valid
            if ((inst.minutes < inst.minutes.starts) || (inst.minutes > inst.minutes.ends)) { inst.minutes = 0; }

            return new Date(0, 0, 0, inst.hours, inst.minutes, 0);
        },
        /* This might look unused but it's called by the $.fn.timepicker function with param getTime */
        /* added v 0.2.3 - gitHub issue #5 - Thanks edanuff */
        _getTimeTimepicker : function(input) {
            var inst = this._getInst(input);
            return this._getParsedTime(inst);
        },
        _getHourTimepicker: function(input) {
            var inst = this._getInst(input);
            if ( inst == undefined) { return -1; }
            return inst.hours;
        },
        _getMinuteTimepicker: function(input) {
            var inst= this._getInst(input);
            if ( inst == undefined) { return -1; }
            return inst.minutes;
        }

    });



    /* Invoke the timepicker functionality.
    @param  options  string - a command, optionally followed by additional parameters or
    Object - settings for attaching new timepicker functionality
    @return  jQuery object */
    $.fn.timepicker = function (options) {

        /* Initialise the time picker. */
        if (!$.timepicker.initialized) {
            $(document).mousedown($.timepicker._checkExternalClick).
			find('body').append($.timepicker.tpDiv);
            $.timepicker.initialized = true;
        }



        var otherArgs = Array.prototype.slice.call(arguments, 1);
        if (typeof options == 'string' && (options == 'getTime' || options == 'getTimeAsDate' || options == 'getHour' || options == 'getMinute' ))
            return $.timepicker['_' + options + 'Timepicker'].
			    apply($.timepicker, [this[0]].concat(otherArgs));
        if (options == 'option' && arguments.length == 2 && typeof arguments[1] == 'string')
            return $.timepicker['_' + options + 'Timepicker'].
                apply($.timepicker, [this[0]].concat(otherArgs));
        return this.each(function () {
            typeof options == 'string' ?
			$.timepicker['_' + options + 'Timepicker'].
				apply($.timepicker, [this].concat(otherArgs)) :
			$.timepicker._attachTimepicker(this, options);
        });
    };

    /* jQuery extend now ignores nulls! */
    function extendRemove(target, props) {
        $.extend(target, props);
        for (var name in props)
            if (props[name] == null || props[name] == undefined)
                target[name] = props[name];
        return target;
    };

    $.timepicker = new Timepicker(); // singleton instance
    $.timepicker.initialized = false;
    $.timepicker.uuid = new Date().getTime();
    $.timepicker.version = "0.3.2";

    // Workaround for #4055
    // Add another global to avoid noConflict issues with inline event handlers
    window['TP_jQuery_' + tpuuid] = $;

})(jQuery);
