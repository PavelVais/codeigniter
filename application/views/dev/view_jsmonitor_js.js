/*!
 * jQuery Nearest plugin v1.3.0
 *
 * Finds elements closest to a single point based on screen location and pixel dimensions
 * http://gilmoreorless.github.io/jquery-nearest/
 * Open source under the MIT licence: http://gilmoreorless.mit-license.org/2011/
 *
 * Requires jQuery 1.4 or above
 * Also supports Ben Alman's "each2" plugin for faster looping (if available)
 */
!function(t, e) {
	function n(e, n, h) {
		e || (e = "div");
		var a, i, o, s = t(n.container), c = s.offset() || {left: 0, top: 0}, f = [s.width() || 0, s.height() || 0], u = {x: [c.left, c.left + f[0]], y: [c.top, c.top + f[1]], w: [0, f[0]], h: [0, f[1]]};
		for (a in u)
			u.hasOwnProperty(a) && (o = r.exec(n[a]), o && (i = u[a], n[a] = (i[1] - i[0]) * o[1] / 100 + i[0]));
		n.sameX === !1 && n.checkHoriz === !1 && (n.sameX = !n.checkHoriz), n.sameY === !1 && n.checkVert === !1 && (n.sameY = !n.checkVert);
		var l = s.find(e), d = [], p = !!n.furthest, m = !n.sameX, y = !n.sameY, v = !!n.onlyX, x = !!n.onlyY, g = p ? 0 : 1 / 0, k = parseFloat(n.x) || 0, w = parseFloat(n.y) || 0, X = parseFloat(k + n.w) || k, Y = parseFloat(w + n.h) || w, F = parseFloat(n.tolerance) || 0, S = !!t.fn.each2, H = Math.min, M = Math.max;
		!n.includeSelf && h && (l = l.not(h)), 0 > F && (F = 0), l[S ? "each2" : "each"](function(e, n) {
			var r, h, a, i, o = S ? n : t(this), s = o.offset(), c = s.left, f = s.top, u = o.outerWidth(), l = o.outerHeight(), j = c + u, z = f + l, O = M(c, k), P = H(j, X), V = M(f, w), W = H(z, Y), b = P >= O, q = W >= V;
			(m && y || !m && !y && b && q || m && q || y && b || m && v || y && x) && (r = b ? 0 : O - P, h = q ? 0 : V - W, a = v || x ? v ? r : h : b || q ? M(r, h) : Math.sqrt(r * r + h * h), i = p ? a >= g - F : g + F >= a, i && (g = p ? M(g, a) : H(g, a), d.push({node: this, dist: a})))
		});
		var j, z, O, P, V = d.length, W = [];
		if (V)
			for (p?(j = g - F, z = g):(j = g, z = g + F), O = 0; V > O; O++)
				P = d[O], P.dist >= j && P.dist <= z && W.push(P.node);
		return W
	}
	var r = /^([\d.]+)%$/;
	t.each(["nearest", "furthest", "touching"], function(r, h) {
		var a = {x: 0, y: 0, w: 0, h: 0, tolerance: 1, container: document, furthest: "furthest" == h, includeSelf: !1, sameX: "touching" === h, sameY: "touching" === h, onlyX: !1, onlyY: !1};
		t[h] = function(r, h, i) {
			if (!r || r.x === e || r.y === e)
				return t([]);
			var o = t.extend({}, a, r, i || {});
			return t(n(h, o))
		}, t.fn[h] = function(e, r) {
			if (!this.length)
				return this.pushStack([]);
			var h;
			if (e && t.isPlainObject(e))
				return h = t.extend({}, a, e, r || {}), this.pushStack(n(this, h));
			var i = this.offset(), o = {x: i.left, y: i.top, w: this.outerWidth(), h: this.outerHeight()};
			return h = t.extend({}, a, o, r || {}), this.pushStack(n(e, h, this))
		}
	})
}(jQuery);


$(document).ready(function() {
	var vars = {
		MOVE: 0,
		CLICK: 1,
		TYPING: 2,
		SCROLL: 3,
		FOCUS: 4,
		getMethod: function(id)
		{
			switch (id)
			{
				case 0:
				default:
					return 'MOVE';
				case 1:
					return 'CLICK';
				case 2:
					return 'TYPING';
				case 3:
					return 'SCROLL';
				case 4:
					return 'FOCUS';
			}
		},
		getSelectedText: function()
		{

			var userSelection;
			if (window.getSelection) {
				selection = window.getSelection();
			} else if (document.selection) {
				selection = document.selection.createRange();
			}
			var parent = selection.anchorNode;
			while (parent != null && parent.localName != "P") {
				parent = parent.parentNode;
			}
			if (parent == null) {
				return "";
			} else {
				return parent.innerText || parent.textContent;
			}
		}
	};
	var mouse = {
		setX: function(x)
		{
			//this.previousX = this.lastX;
			this.lastX = x;

		},
		setY: function(y)
		{
			//this.previousY = this.lastY;
			this.lastY = y;
		},
		isMoved: function()
		{
			var tol = 1;
			if ((Math.abs(this.previousX - this.lastX) > tol || Math.abs(this.previousY - this.lastY) > tol))
			{
				this.previousX = this.lastX;
				this.previousY = this.lastY;
				return true;
			}
			return false;

		},
		getElementPerPosition: function(top, left) {
			return $("body").find("*").filter(function() {
				return $(this).offset().top == top && $(this).offset().left == left
			})
		},
		moveCursor: function(x, y, speed, onFinish)
		{
			var a = {left: x, top: y};
			if (!x)
				a = {top: y};
			if (!y)
				a = {left: y};
			$('#jscursor').stop().animate(a, speed, function() {
				if (onFinish !== undefined)
					onFinish();
			});
		},
		lastX: 0,
		lastY: 0,
		previousX: 0,
		previousY: 0
	};

	var monitor = {
		data: [],
		lastTime: Date.now(),
		check: function() {
			if (mouse.isMoved())
			{
				console.log("mys se hejbla");
				this.put(mouse.lastX, mouse.lastY, 0);
			}
		},
		put: function(x, y, type) {
			var t = Date.now();
			t = t - this.lastTime;
			this.data.push([x, y, type, t]);
		},
		init: function()
		{
			console.log("Monitor init");
			this.lastTime = Date.now();
			if (!Date.now) {
				Date.now = function() {
					return new Date().getTime();
				};
			}

			$(document).on('mousemove', function(e) {
				mouse.setX(e.pageX);
				mouse.setY(e.pageY);
				//$closestToPoint.css({backgroundColor: 'rgba(100,150,200,.15)'});
			}).mouseover().on('click', function(e) {
				var t = $(e.target);
				monitor.put(e.pageX, e.pageY, vars.CLICK);
			}).on('mouseup', function(e) {
				//var t = $(e.target);
				//console.log("on mouseup");
				//monitor.put(e.pageX, e.pageY, vars.CLICK);
			});
			
			
			$(window).on('scroll', function(e) {
				$(document).trigger('mouseover');
				var lastScroll = $(window).scrollTop();
				monitor.put(lastScroll, mouse.lastY, vars.SCROLL);
			});
		},
		desctruct: function()
		{
			$(document).off('mousemove').off('click');
			$(window).off('scroll');
			this.lastTime = Date.now();
		},
		save: function() {
			var t = [];
			$.each(this.data, function(idx2, val2) {
				var str = "[" + val2 + "]";
				t.push(str);
			});
			t = t.join(", ");
			t = "[ " + t + " ]";
			//t = this.data + "";
			$('#jsoutput').val(t);
		}



	}


	var loop = {
		tick: 500,
		t: undefined,
		start: function()
		{
			loop.t = setInterval(function() {
				monitor.check();
			}, this.tick);
		},
		stop: function()
		{
			clearInterval(loop.t);
		}
	}



	$('#jsstart').click(function() {
		monitor.init();
		loop.start();
		return false;
	});
	$('#jsplay').click(function() {
		server.origData = eval($('#jsoutput').val());
		server.init();
		server.status = 1;
		server.play();
		return false;
	});

	$('#jsstop').click(function() {
		console.log("STOP process");
		loop.stop();
		monitor.desctruct();
		monitor.save();
		return false;
	});





	server = {
		lastTime: 0,
		t: undefined,
		//origData: [ [409,292,0,507], [412,306,0,1007], [412,306,1,1324], [726,313,0,2012], [791,307,1,2524], [792,307,0,2536], [867,352,0,3010], [881,346,0,3502], [882,346,1,3737], [881,358,0,4003], [878,363,1,4495], [878,363,0,4510], [878,388,0,5005], [876,424,0,5505], [876,424,1,5860], [620,417,0,6498], [608,428,0,6999], [608,428,1,7238], [581,396,0,7998], [581,386,1,8381], [581,362,0,8498], [490,337,0,9040], [419,340,0,9500], [419,340,1,9659], [460,274,0,9995], [493,172,0,10506], [391,169,0,11006] ],
		origData: [[343, 266, 0, 1012], [380, 305, 0, 1511], [381, 305, 1, 1804], [493, 324, 0, 2011], [866, 317, 0, 2509], [866, 317, 1, 2660], [1, , 3, 3088], [3, , 3, 3107], [6, , 3, 3123], [9, , 3, 3139], [14, , 3, 3155], [19, , 3, 3172], [24, , 3, 3190], [29, , 3, 3206], [35, , 3, 3220], [41, , 3, 3240], [49, , 3, 3254], [58, , 3, 3270], [67, , 3, 3288], [77, , 3, 3295], [86, , 3, 3310], [98, , 3, 3325], [113, , 3, 3351], [132, , 3, 3359], [159, , 3, 3374], [187, , 3, 3390], [217, , 3, 3408], [247, , 3, 3423], [274, , 3, 3440], [300, , 3, 3457], [322, , 3, 3467], [359, , 3, 3497], [371, , 3, 3514], [378, , 3, 3528], [380, , 3, 3545], [381, , 3, 4007], [383, , 3, 4029], [386, , 3, 4042], [389, , 3, 4062], [394, , 3, 4076], [398, , 3, 4094], [403, , 3, 4105], [409, , 3, 4122], [414, , 3, 4139], [420, , 3, 4156], [425, , 3, 4172], [431, , 3, 4189], [436, , 3, 4206], [441, , 3, 4223], [446, , 3, 4240], [451, , 3, 4247], [455, , 3, 4261], [459, , 3, 4280], [463, , 3, 4294], [466, , 3, 4313], [469, , 3, 4323], [472, , 3, 4349], [476, , 3, 4356], [481, , 3, 4372], [486, , 3, 4389], [491, , 3, 4412], [497, , 3, 4419], [503, , 3, 4436], [514, , 3, 4464], [519, , 3, 4481], [525, , 3, 4498], [866, 803, 0, 4514], [530, , 3, 4514], [535, , 3, 4531], [540, , 3, 4541], [544, , 3, 4563], [548, , 3, 4576], [552, , 3, 4590], [556, , 3, 4605], [559, , 3, 4623], [562, , 3, 4638], [565, , 3, 4654], [568, , 3, 4700], [569, , 3, 4720], [570, , 3, 4742], [514, 1001, 0, 5052], [498, 1008, 0, 5511], [498, 1008, 1, 5842], [571, , 3, 6097], [578, , 3, 6134], [602, , 3, 6165], [627, , 3, 6183], [657, , 3, 6202], [694, , 3, 6217], [735, , 3, 6229], [774, , 3, 6243], [815, , 3, 6260], [852, , 3, 6276], [889, , 3, 6295], [923, , 3, 6309], [952, , 3, 6326], [979, , 3, 6346], [1001, , 3, 6354], [1035, , 3, 6384], [1043, , 3, 6401], [1045, , 3, 6413], [512, 1464, 0, 6508], [476, 1431, 0, 7008], [476, 1431, 1, 7569], [482, 1432, 0, 9000], [484, 1378, 0, 9497], [468, 1322, 0, 9997], [468, 1322, 1, 10176], [471, 1322, 0, 11011], [1044, , 3, 11060], [1042, , 3, 11080], [1037, , 3, 11104], [1029, , 3, 11112], [1017, , 3, 11131], [1004, , 3, 11142], [986, , 3, 11166], [961, , 3, 11179], [936, , 3, 11196], [908, , 3, 11212], [880, , 3, 11229], [856, , 3, 11237], [831, , 3, 11250], [809, , 3, 11270], [787, , 3, 11284], [766, , 3, 11297], [745, , 3, 11314], [722, , 3, 11329], [700, , 3, 11346], [678, , 3, 11362], [658, , 3, 11379], [640, , 3, 11396], [622, , 3, 11412], [608, , 3, 11422], [584, , 3, 11464], [576, , 3, 11491], [571, , 3, 11509], [570, , 3, 11527], [569, , 3, 11635], [553, , 3, 11700], [538, , 3, 11718], [471, , 3, 11744], [424, , 3, 11764], [377, , 3, 11783], [332, , 3, 11801], [244, , 3, 11846], [163, , 3, 11871], [97, , 3, 11897], [71, , 3, 11913], [46, , 3, 11925], [26, , 3, 11937], [12, , 3, 11944], [3, , 3, 11962], [0, , 3, 11977], [366, 223, 0, 12515]],
		data: [],
		currentData: undefined,
		status: 0,
		maxTime: 0,
		init: function() {
			this.data = [];
			this.data = jQuery.extend(this.data, this.origData);
			this.currentData = this.getOne();
			this.maxTime = this.data[this.data.length - 1][3];
			$('.jsclicked').removeClass('jsclicked');

		},
		play: function() {
			this.lastTime = server.currentData[3];
			this.status = 1;
			this.progress.start(this.maxTime);
			this.nextStep();
		},
		stop: function()
		{
			this.status = 0;
			clearTimeout(this.t);
			this.progress.reset();
		},
		nextStep: function()
		{

			if (this.currentData === undefined)
			{
				this.stop();
			} else {

				this.stepProcess(this.currentData);
			}

		},
		stepProcess: function(d)
		{
			this.t = setTimeout(function() {
				var timeDiff = d[3] - server.lastTime;
				var fn = window['methods'][vars.getMethod(d[2])];
				if (typeof fn === 'function') {
					fn(d, timeDiff);
				}
				//console.log("cekam: ", d[3], server.lastTime);
				server.lastTime = server.currentData[3];
				server.currentData = server.getOne();
				if (server.status === 1)
					server.nextStep();


			}, (server.currentData[3] - server.lastTime));
		},
		getOne: function()
		{
			return this.data.shift();
		},
		progress: {
			start: function(maxTime)
			{
				$('#jsmonitor-seeker').stop().animate({
					width: '100%'
				}, maxTime);
			},
			stop: function()
			{
				$('#jsmonitor-seeker').stop();
			}
			,
			reset: function()
			{
				$('#jsmonitor-seeker').stop().animate({
					width: '0%'
				}, 300);
			}
		}

	}

	$('#serverinit').click(function() {
		server.init();
	});
	$('#serverplay').click(function() {
		server.status = 1;
		server.play();
	});

	$('#serverstep').click(function() {
		server.status = 0;
		server.play();
	});

	methods = {
		MOVE: function(d, t)
		{
			//console.log("MOVE", d, t);
			mouse.moveCursor(d[0], d[1], t);
		},
		CLICK: function(d, t)
		{
			mouse.moveCursor(d[0], d[1], t);
			$('#jscursor').hide();
			console.log("onfinish event");
			var e = new jQuery.Event("click");
			e.pageX = d[0];
			e.pageY = d[1];
			var el = jQuery(document.elementFromPoint(d[0] - window.pageXOffset, d[1] - window.pageYOffset)).trigger(e);
			console.log("CLICK", d, t, el);
			if (!el.length)
			{
				el = $.nearest({x: d[0], y: d[1]}, 'div,h2,a,p,h1,li,h3,img,section');
				console.log("prvni klik se nezvladl", el);
				el.last().trigger('click');
			}



			$('#jscursor').addClass('clicking').show();
			setTimeout(function() {
				$('#jscursor').removeClass('clicking');
			}, 290);
		},
		SCROLL: function(d, t)
		{
			//console.log("SCROLL",d);
			mouse.moveCursor(null, d[1], t);
			$('html, body').stop().animate({
				scrollTop: d[0]
			}, t);
		},
		TYPING: function(d, t)
		{
			//console.log("SCROLL",d);
			mouse.moveCursor( d[0], d[1], t);
			$('html, body').stop().animate({
				scrollTop: d[0]
			}, t);
		},
	}

	$('body').on('click', function(e) {

		var t = $(e.target);
		if (t.is('button'))
			return false;
		console.log(e.pageX, e.pageY);
		//console.log("nekdo KLIKNUL!", t, jQuery(document.elementFromPoint(e.pageX, e.pageY)));
		t = $.nearest({x: e.pageX, y: e.pageY}, 'div,h2,a,p,h1,li,h3,img,section');

		var $target = t.children();

		while ($target.length) {
			$target = $target.children();
		}
		t = t.last();
		t.addClass('jsclicked').removeClass('jsclicked2');
		setTimeout(function() {
			t.addClass('jsclicked2');
		}, 800);

	});

	server.init();
});