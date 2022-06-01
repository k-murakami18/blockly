var BookCover = (function () {
  var BookCover = function () {
    var self = this;
    self.__strokeWeight = 1;
    self.__stroke = "none";
    self.__strokeOpacity = 1;
    self.__fill   = "#000000";
    self.__fillOpacity = 1;
    self.__fontFamily = "MS-Mincho";
    /* char * __fontFamily = "Times New Roman"; */
    self.__fontSize = 12;

    self.__gDepth = 0;
    self.__gStack = [ ];
    self.__width = 297;
    self.__height = 210;
    self.__landscape = 1;

    self.__leftMargin   = 15;
    self.__rightMargin  = 30;
    self.__topMargin    = 15;
    self.__bottomMargin = 15;

    self.__currentLeftMargin   = self.__leftMargin;
    self.__currentRightMargin  = self.__rightMargin;
    self.__currentTopMargin    = self.__topMargin;
    self.__currentBottomMargin = self.__bottomMargin;

//    self.__currentGroup;
//    self.__root;

    self.__firstVertex = true;
    self.__shapePath = "";

    /* turtle graphics */
    self.__turtlePen = self.CLEAN;  /* up: 0, down: 1, dirty: 2 */
    self.__turtleX = 148.5;
    self.__turtleY = 105;
    self.__turtleHeading = 0;
    self.__turtleStack = [ ];

    /* cards */
    self.__cards     = [ { "x": 0, "y": 0, "width": self.__width, "height": self.__height } ];
    self.__cardSpecs = { };
    self.__clipMargin = 1;

    self.__regexAstralSymbols = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
  };

  BookCover.prototype = {
    /* ---------------------------------------------------------------------- *
     * begin common part
     * ---------------------------------------------------------------------- */

    bound1: function(d) {
        if (d < 0) d = 0.0;
        if (d > 1) d = 1.0;
        return d;
    },

    bound255: function(i) {
        if (i < 0) i = 0;
        if (i > 255) i = 255;
        return i;
    },

    // Xorshift による擬似乱数生成法
    // http://d.hatena.ne.jp/nakaXmura001/20110521/1305997364
    __xors: {
      x: 123456789,
      y: 362436069,
      z: 521288629,
      w: 88675123
    },

    xorsSeed:  function(s) {
      var self = this;
      self.__xors.w = s;
    },

    xorsRand: function() {
      var self = this; 
      var t = self.__xors.x ^ (self.__xors.x << 11);
      self.__xors.x = self.__xors.y;
      self.__xors.y = self.__xors.z;
      self.__xors.z = self.__xors.w;
      return self.__xors.w = (self.__xors.w ^ (self.__xors.w >>> 19)) ^ (t ^ (t >>> 8));
    },

    randomSeed: function(value) {
        this.xorsSeed(value);
    },

    randomizeByTime: function() {
        this.randomSeed(Date.now());
    },

    __intMin: (1 << 31),

    random: function() {
        return this.xorsRand() - this.__intMin ;
    },

    __randMax: - 2 * (1 << 31) - 1,

    randomInRange: function(min, max) {
        var r = this.random();
        return (max - min) * r / this.__randMax  + min;
    },

    radians: function(deg) {
        return deg / 180 * Math.PI;
    },

    strokeWeight: function(arg) {
        this.__strokeWeight = arg;
    },

    mySprintfX: function(w, num) {
       var str = num.toString(16);
       var len = str.length;
       if (len < w) {
          str = "0".repeat(w - len) + str;
       }
       return str;
    },

    stroke: function(arg) {
        if (arg == null || arg == "none") {
            this.noStroke(); return;
        }
        if (arg > 0xffffff) arg = 0xffffff;
        else if (arg < 0) arg = 0;
        this.__stroke = "#" + this.mySprintfX(6, arg);
    },

    noStroke: function() {
        this.__stroke = "none";
    },

    strokeOpacity: function(o) {
        this.__strokeOpacity = this.bound1(o);
    },

    fill: function(arg) {
        if (arg == null || arg == "none") {
            this.noFill(); return;
        }
        if (arg > 0xffffff) arg = 0xffffff;
        else if (arg < 0) arg = 0;
        this.__fill = "#" + this.mySprintfX(6, arg);
    },

    noFill: function() {
        this.__fill = "none";
    },


    fillOpacity: function(o) {
        this.__fillOpacity = this.bound1(o);
    },

    textFont: function(font, size) {
        this.__fontFamily = font;
        this.__fontSize = size;
    },


    rgb255: function(r, g, b) {
        r = this.bound255(r); g = this.bound255(g); b = this.bound255(b);
        return (Math.floor(r) * 0x10000) + (Math.floor(g) * 0x100) + Math.floor(b);
    },

    rgb100: function(r, g, b) {
        return this.rgb255(r * 2.55, g * 2.55, b * 2.55);
    },

    rgb1: function(r, g, b) {
        return this.rgb255(r * 255, g * 255, b * 255);
    },

    hsb1: function(h, s, v) {
        var r = 0, g = 0, b = 0;

        h *= 360;
        while (h >= 360) h -= 360;
        while (h < 0)   h += 360;

        s = this.bound1(s);
        if (s == 0) {
            r = g = b = v;
        } else {
            var H, h1;
            var f, p, q, t;

            v = this.bound1(v);

            H  = Math.floor(h);
            h1 = Math.floor(H / 60);

            f = h / 60 - h1;
            p = v * (1 - s);
            q = v * (1 - f * s);
            t = v * (1 - (1 - f) * s);

            switch (h1) {
            case 0: r = v; g = t; b = p; break;
            case 1: r = q; g = v; b = p; break;
            case 2: r = p; g = v; b = t; break;
            case 3: r = p; g = q; b = v; break;
            case 4: r = t; g = p; b = v; break;
            case 5: r = v; g = p; b = q; break;
            }
        }
        return this.rgb1(r, g, b);
    },

    hsb100: function(h, s, v) {
        return this.hsb1(h / 100, s / 100, v / 100);
    },

    hsb360: function(h, s, v) {
        return this.hsb1(h / 360, s / 100, v / 100);
    },

    hsb255: function(h, s, v) {
        return this.hsb1(h / 255, s / 255, v / 255);
    },

    hsl1: function(h, s, l) {
        if (l == 0) {
            /* max = min = 0 */
            return this.hsb1(h, 0, 0);
        } else if (l <= 0.5) {
            var diff = s * l;    // 
            var max  = l + diff;
            var min  = l - diff;
            return this.hsb1(h, 1 - min / max, max);
        } else {
            var diff = s * (1 - l);
            var max  = l + diff;
            var min  = l - diff;
            return this.hsb1(h, 1 - min / max, max);
        }
    },

    hsb1tohsl1: function(h, s, b) {
        var max = b / 100;
        var min = max * (1 - s / 100);
        var diff = (max - min) / 2;
        var luminance = max - diff;
        var saturation = luminance <= 0.5 ? diff / luminance : diff / (luminance - 1);
        return ([h, saturation, luminance]);
    },

    hsl100: function(h, s, l) {
        return this.hsl1(h / 100, s / 100, l / 100);
    },

    hsl360: function(h, s, l) {
        return this.hsl1(h / 360, s / 100, l / 100);
    },

    hsl255: function(h, s, l) {
        return this.hsb1(h / 255, s / 255, l / 255);
    },

    bw255: function(v) {
        return this.rgb255(v, v, v);
    },

    bw100: function(v) {
        return this.bw255(v * 2.55);
    },

    bw1: function(v) {
        return this.bw255(v * 255);
    },

    cos360: function(deg) {
        return Math.cos(this.radians(deg));
    },

    sin360: function(deg) {
        return Math.sin(this.radians(deg));
    },

    rgb2hsb360: function(rgb) {
        var b = rgb % 0x100;
        var g = rgb / 0x100 % 0x100;
        var r = rgb / 0x10000 % 0x100;
        var max, min, sub, angle;

        if (r >= g) { // ? r ? g ?
            if (g >= b) { // r g b
                max = r; min = b; sub = g - b; angle = 0;
            } else if (b >= r) { // b r g
                max = b; min = g; sub = r - g; angle = 240;
            } else { // r b g
                max = r; min = g; sub = g - b; angle = 0;
            }
        } else { // ? g ? r ?
            if (r >= b) { // g r b
                max = g; min = b; sub = b - r; angle = 120;
            } else if (b >= g) { // b g r
                max = b; min = r; sub = r - g; angle = 240;
            } else { // g b r
                max = g; min = r; sub = b - r; angle = 120;
            }
        }
 
        ret = [0, 0, 0];
        ret[0] = 60 * (sub / (max - min)) + angle;
        if (ret[0] < 0) ret[0] += 360;
        ret[1] = 100.0 * (max - min) / max;
        ret[2] = max / 255.0 * 100;
        return ret;
    },

    rgb2hsl360: function(rgb) {
        var b = rgb % 0x100;
        var g = rgb / 0x100 % 0x100;
        var r = rgb / 0x10000 % 0x100;
        var max, min, sub, angle;

        if (r >= g) { // ? r ? g ?
            if (g >= b) { // r g b
                max = r; min = b; sub = g - b; angle = 0;
            } else if (b >= r) { // b r g
                max = b; min = g; sub = r - g; angle = 240;
            } else { // r b g
                max = r; min = g; sub = g - b; angle = 0;
            }
        } else { // ? g ? r ?
            if (r >= b) { // g r b
                max = g; min = b; sub = b - r; angle = 120;
            } else if (b >= g) { // b g r
                max = b; min = r; sub = r - g; angle = 240;
            } else { // g b r
                max = g; min = r; sub = b - r; angle = 120;
            }
        }
 
        ret = [0, 0, 0];
        ret[0] = 60 * (sub / (max - min)) + angle;
        if (ret[0] < 0) ret[0] += 360;
        ret[1] = max == min ? 0 : 100.0 * (max - min) / (255 - Math.abs(max + min - 255));
        ret[2] = (max + min) / 255.0 * 50;
        return ret;
    },

    rotateH360: function(color, a) {
        var hsb = this.rgb2hsb360(color);
        hsb[0] += a;
        return this.hsb360(hsb[0], hsb[1], hsb[2]);
    },

    rotateH: function(color) {
        return rotateH360(color, PHYLLOTAXIS360);
    },

    addS100: function(color, a) {
        var hsb = this.rgb2hsb360(color);
        hsb[1] += a;
        return this.hsb360(hsb[0], hsb[1], hsb[2]);
    },

    addS100L: function(color, a) {
        var hsl = this.rgb2hsl360(color);
        hsl[1] += a;
        return this.hsl360(hsl[0], hsl[1], hsl[2]);
    },

    scaleS: function(color, a) {
        var hsb =  this.rgb2hsb360(color);
        hsb[1] *= a;
        return this.hsb360(hsb[0], hsb[1], hsb[2]);
    },

    scaleSL: function(color, a) {
        var hsl =  this.rgb2hsl360(color);
        hsl[1] *= a;
        return this.hsl360(hsl[0], hsl[1], hsl[2]);
    },

    addB100: function(color, a) {
        var hsb = this.rgb2hsb360(color);
        hsb[2] += a;
        return this.hsb360(hsb[0], hsb[1], hsb[2]);
    },

    addL100: function(color, a) {
        var hsl = this.rgb2hsl360(color);
        hsl[2] += a;
        return this.hsl360(hsl[0], hsl[1], hsl[2]);
    },

    scaleB: function(color, a) {
        var hsb = this.rgb2hsb360(color);
        hsb[2] *= a;
        return this.hsb360(hsb[0], hsb[1], hsb[2]);
    },

    scaleL: function(color, a) {
        var hsl = this.rgb2hsl360(color);
        hsl[2] *= a;
        return this.hsl360(hsl[0], hsl[1], hsl[2]);
    },

    /* ---------------------------------------------------------------------- *
     * end common part
     * ---------------------------------------------------------------------- */

    setPageSize: function(w, h) {
        this.__width = w; this.__height = h;
    },


    centerX: function() {
        return this.__width / 2;
    },

    centerY: function() {
        return this.__height / 2;
    },

    pageWidth: function() {
        return this.__width;
    },

    pageHeight: function() {
        return this.__height;
    },

    a4Landscape: function() {
        this.__width = 297;  this.__height = 210;
        this.__landscape = 1;
        this.__currentLeftMargin   = this.__leftMargin;
        this.__currentRightMargin  = this.__rightMargin;
        this.__currentTopMargin    = this.__topMargin;
        this.__currentBottomMargin = this.__bottomMargin;
    },

    a4Portrait: function() {
        this.__width = 210;  this.__height = 297;
        this.__landscape = 0;
        this.__currentTopMargin    = this.__rightMargin;
        this.__currentBottomMargin = this.__leftMargin;
        this.__currentLeftMargin   = this.__topMargin;
        this.__currentRightMargin  = this.__bottomMargin;
    },

    getWidth: function() {
        return this.__width;
    },

    getHeight: function() {
        return this.__height;
    },

    init: function () {
        var self = this;
    	self.__strokeWeight = 1;
    	self.__stroke = "none";
    	self.__strokeOpacity = 1;
    	self.__fill   = "#000000";
    	self.__fillOpacity = 1;
    	self.__fontFamily = "MS-Mincho";
    	/* char * __fontFamily = "Times New Roman"; */
    	self.__fontSize = 12;

    	self.__firstVertex = true;
    	self.__shapePath = "";

    	/* turtle graphics */
    	self.__turtlePen = self.CLEAN;  /* up: 0, down: 1, dirty: 2 */
    	self.__turtleX = 148.5;
    	self.__turtleY = 105;
    	self.__turtleHeading = 0;
    	self.__turtleStack = [ ];
        self.__gDepth = 0;
        self.__gStack = [ ];

    	self.__width = 297;
    	self.__height = 210;
    	self.__landscape = 1;

    	self.__leftMargin   = 15;
    	self.__rightMargin  = 30;
    	self.__topMargin    = 15;
    	self.__bottomMargin = 15;

    	self.__currentLeftMargin   = self.__leftMargin;
    	self.__currentRightMargin  = self.__rightMargin;
    	self.__currentTopMargin    = self.__topMargin;
    	self.__currentBottomMargin = self.__bottomMargin;
    	self.__firstVertex = true;
    	self.__shapePath = "";

    	/* turtle graphics */
    	self.__turtlePen = self.CLEAN;  /* up: 0, down: 1, dirty: 2 */
    	self.__turtleX = 148.5;
    	self.__turtleY = 105;
    	self.__turtleHeading = 0;
    	self.__turtleStack = [ ];

        self.__clipMargin = 1;
    },


    start: function(draw) {
    //    printf("<?xml version=\"1.0\" encoding=\"%s\"?>\n", enc);
    //    printf("<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\"\n");
    //    printf("  \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n");
    //    printf("<svg version=\"1.1\" width=\"%.2fmm\" height=\"%.2fmm\"\n", width, height);
    //    printf("  viewBox=\"0 0 %.2f %.2f\"\n", width, height);
        draw.attr('preserveAspectRatio', 'xMidYMid');
    //    printf("  preserveAspectRatio=\"xMidYMid\"\n");
    //    printf("  fill-rule=\"evenodd\"\n");
    //    printf("  xmlns=\"http://www.w3.org/2000/svg\"\n");
    //    printf("  xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n");

    	this.__strokeWeight = 1;
    	this.__stroke = "none";
    	this.__strokeOpacity = 1;
    	this.__fill   = "#000000";
    	this.__fillOpacity = 1;
    	this.__fontFamily = "MS-Mincho";
    	/* char * __fontFamily = "Times New Roman"; */
    	this.__fontSize = 12;

        this.randomizeByTime();
        this.__gDepth = 0;
        this.__currentGroup = draw;
        this.__root         = draw;

    	this.__firstVertex = true;
    	this.__shapePath = "";

    	/* turtle graphics */
    	this.__turtlePen = this.CLEAN;  /* up: 0, down: 1, dirty: 2 */
    	this.__turtleX = 148.5;
    	this.__turtleY = 105;
    	this.__turtleHeading = 0;
    	this.__turtleStack = [ ];
    },

    resetMatrix: function() {
        while(this.__gDepth > 0) {
            this.__gDepth--;
            this.__currentGroup = this.__currentGroup.parent();
    //        printf("</g>\n");
        }
        this.__gStack = [];
    },

    translate: function(x, y) {
    //    printf("<g transform=\"translate(%f,%f)\">\n", x, y);
        var group = this.__currentGroup.group();
        group.translate(x, y);
        this.__currentGroup = group;
        this.__gDepth++;
    },

    rotate360: function(theta) {
    //    printf("<g transform=\"rotate(%f)\">\n", theta);
        var group = this.__currentGroup.group();
        group.rotate(theta);
        this.__currentGroup = group;
        this.__gDepth++;
    },

    rotate: function(deg) {
        this.rotate360(deg * 180 / Math.PI);
    },

    scale: function(sx, sy) {
    //    printf("<g transform=\"scale(%f, %f)\">\n", sx, sy);
        var group = this.__currentGroup.group();
        group.scale(sx, sy);
        this.__currentGroup = group;
        this.__gDepth++;
    },

    pushMatrix: function() {
        this.__gStack.push(this.__gDepth);
    },

    popMatrix: function() {
        var tmp = this.__gStack.pop();
        while(this.__gDepth > tmp) {
            this.__gDepth--;
    //        printf("</g>\n");
            this.__currentGroup = this.__currentGroup.parent();
        }
    },

    line: function(x1, y1, x2, y2) {
    //    printf("<line x1=\"%f\" y1=\"%f\" x2=\"%f\" y2=\"%f\"", x1, y1, x2, y2);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"",
    //           __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        this.__currentGroup.line(x1, y1, x2, y2).attr({
         'stroke':         this.__stroke
       , 'stroke-width':   this.__strokeWeight
       , 'stroke-opacity': this.__strokeOpacity
       , 'stroke-linecap': "round"
       });
    },

    rect: function(x, y, w, h) {
    //    printf("<rect width=\"%f\" height=\"%f\"", w, h);
    //    printf("  x=\"%f\" y=\"%f\"", x, y);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        this.__currentGroup.rect(w, h).move(x, y).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    ellipse: function(x, y, w, h) {
    //    printf("<ellipse rx=\"%f\" ry=\"%f\"", w / 2, h / 2);
    //    printf("  cx=\"%f\" cy=\"%f\"", x, y);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\" />\n", __fill, __fillOpacity);
        this.__currentGroup.ellipse(w, h).move(x, y).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    triangle: function(x1, y1, x2, y2, x3, y3) {
    //    printf("<polygon points=");
    //    printf("\"%f,%f  %f,%f, %f,%f\"", x1, y1, x2, y2, x3, y3);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        this.__currentGroup.polygon([[x1,y1],[x2,y2],[x3,y3]]).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    quad: function(x1, y1, x2, y2, x3, y3, x4, y4) {
    //    printf("<polygon points=");
    //    printf("\"%f,%f  %f,%f, %f,%f, %f,%f\"", x1, y1, x2, y2, x3, y3, x4, y4);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        this.__currentGroup.polygon([[x1,y1],[x2,y2],[x3,y3],[x4,y4]]).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    quadrilateral: function(x1, y1, x2, y2, x3, y3, x4, y4) {
    //    printf("<polygon points=");
    //    printf("\"%f,%f  %f,%f, %f,%f, %f,%f\"", x1, y1, x2, y2, x3, y3, x4, y4);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        this.quad(x1, y1, x2, y2, x3, y3, x4, y4);
    },

    myVsprintf: function() {
        var format = arguments[0];
        var index = 1, i = 0;
        var ret = "";
        while (1) {
          	var j = format.indexOf("%", i);
          	if (j < 0) {
                ret += format.substring(i);
                break;
            }
          	ret += format.substring(i, j);
          	if (j + 1 == format.length) { // 最後の文字が %
          	    ret += "%"; break;
          	}
            switch (format.charAt(j + 1)) {
            case '%': ret += '%'; i = j + 2; break;
            case 's': ret += arguments[index++]; i = j + 2; break;
            case 'd': ret += arguments[index++]; i = j + 2; break;
            case 'f': ret += arguments[index++]; i = j + 2; break;
            case 'X': ret += arguments[index++].toString(16).toUpperCase();
                      i = j + 2; break;
            case 'x': ret += arguments[index++].toString(16).toLowerCase();
    	          i = j + 2; break;
            default:  ret += arguments[index++]; i = j + 2; break;
            // TODO: %03d, %.3f など
            }
        }
        return ret;
    },

    arc: function(x, y, w, h, start, stop) {
        var x0, y0, x1, y1;
        var large;

        if (stop < start) {
            var tmp = stop;
            stop = start;
            start = tmp;
        }
        x0 = x + w  / 2 + w / 2 * Math.cos(start), 
           y0 = y + h / 2 + h / 2 * Math.sin(start),
           x1 = x + w / 2 + w / 2 * Math.cos(stop), 
           y1 = y + h / 2 + h / 2 * Math.sin(stop);
        large = stop - start > Math.PI ? 1 : 0;
    //    printf("<path d=");
    //    printf("\"M%f,%f A%f,%f", x0, y0, w / 2, h / 2);
    //    printf(" 0 %d,%d %f,%f\"\n", large, 1, x1, y1);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
        var pathStr = this.myVsprintf("M%f,%f A%f,%f  0 %d,%d %f,%f",
                                 x0, y0, w / 2, h / 2, large, 1, x1, y1);
        this.__currentGroup.path(pathStr).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    arc360: function(x, y, w, h, start, stop) {
        this.arc(x, y, w, h, this.radians(start), this.radians(stop));
    },

    bezier: function(ax0, ay0, cx0, cy0,
                cx1, cy1, ax1, ay1) {
    //    printf("<path d=");
    //    printf("\"M%f,%f", ax0, ay0);
    //    printf("  C%f,%f %f,%f %f,%f\"\n", cx0, cy0, cx1, cy1, ax1, ay1);
    ////    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"none\"");
    //    printf("  stroke-linecap=\"round\" />\n");
        var pathStr = this.myVsprintf("M%f,%f C%f,%f %f,%f %f,%f"
                               , ax0, ay0, cx0, cy0, cx1, cy1, ax1, ay1);
        this.__currentGroup.path(pathStr).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    },

    // function vtext(str, x, y, list) {
    //    printf("<text x=\"%f\" y=\"%f\" \n", x, y);
    //    printf("  font-family=\"%s\" font-size=\"%f\" \n", __fontFamily, __fontSize);
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\" \n", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\" >\n", __fill, __fillOpacity);
    //    vsprintf(str, list);
    //    printf("\n");
    //    printf("</text>\n");
    // }

    text: function() {
        var f = arguments[0];
        var x = arguments[1];
        var y = arguments[2];
        var as = [];
        var len = arguments.length
        as.push("" + f);  /* String に強制 */
        for (var k = 3; k < len; k++) {
            as.push(arguments[k]);
        }
        var str = this.myVsprintf.apply(null, as);
        this.__currentGroup.text(str).attr({
            "x": x, "y": y
          , "font-family":    this.__fontFamily
          , "font-size":      this.__fontSize
          , 'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
        });
    },


    beginShape: function() {
    //    printf("<path d=\"");
        this.__shapePath = "";
        this.__firstVertex = true;

    },

    vertex: function(x, y) {
        if (this.__firstVertex) {
            this.__shapePath += "M";
            this.__firstVertex = false;
        } else {
            this.__shapePath += "L";
        }
        this.__shapePath += this.myVsprintf("%f,%f ", x, y);
    },

    bezierVertex: function(cx0, cy0, cx1, cy1, x1, y1) {
        this.__shapePath += this.myVsprintf("C %f,%f %f,%f %f,%f ", cx0, cy0, cx1, cy1, x1, y1);
    },

    endShape: function(close) {
        if (close) {
            this.__shapePath += "Z";
        }
        this.__currentGroup.path(this.__shapePath).attr({
            'stroke':         this.__stroke
          , 'stroke-width':   this.__strokeWeight
          , 'stroke-opacity': this.__strokeOpacity
          , 'fill':           this.__fill
          , 'fill-opacity':   this.__fillOpacity
          , 'stroke-linecap': "round"
        });
    //    printf("  stroke=\"%s\" stroke-width=\"%f\" stroke-opacity=\"%f\"", __stroke, __strokeWeight, __strokeOpacity);
    //    printf("  fill=\"%s\" fill-opacity=\"%f\"", __fill, __fillOpacity);
    //    printf("  stroke-linecap=\"round\" />\n");
    },

    image: function(url, x, y, w, h) {
    //    printf("<image x=\"%f\" y=\"%f\" width=\"%f\" height=\"%f\"", x, y, w, h);
    //    printf(" xlink:href=\"%s\" />\n", url);
        this.__currentGroup.image(url, w, h).move(x, y);
    },

    use: function(url, x, y, w, h) {
    //    printf("<g style=\"stroke:%s;stroke-width:%f;stroke-opacity:%f", __stroke, __strokeWeight, __strokeOpacity);
    //    printf(";fill:%s;fill-opacity:%f\">\n", __fill, __fillOpacity);
    //    printf("<use x=\"%f\" y=\"%f\" width=\"%f\" height=\"%f\"", x, y, w, h);
    //    printf(" xlink:href=\"%s\" />\n", url);
    //    printf("</g>\n");
        this.__currentGroup.use(url).move(x, y).size(w, h);
    },

    upperBar: function(height) {
        this.rect(0, 28.5 - height, this.__width, height);
        /*
            printf("<g style=\"stroke:none;fill:%s;fill-opacity:%f\">\n", __fill, __fillOpacity);
            printf("    <path d=\"M 0,23.5 L 297,23.5 297,28.5 0,28.5 0,23.5 Z\"/>\n");
            printf("</g>\n");
        */
    },

    lowerBar: function(height) {
        this.rect(0, 181.5, this.__width, height);
        /*
            printf("<g style=\"stroke:none;fill:%s;fill-opacity:%f\">\n", __fill, __fillOpacity);
            printf("    <path d=\"M 0,181.5 L 297,181.5 297,186.5 0,186.5 0,181.5 Z\"/>\n");
            printf("</g>\n");
        */
    },

    guideBars: function(height) {
        this.upperBar(height);
        this.lowerBar(height);
    },

    rulers: function() {
        var tmpW = this.__strokeWeight;
        var tmpC = this.__stroke;
        this.strokeWeight(0.2);
        this.stroke(this.bw1(0));
        this.line(0, 28.5, 20, 28.5);
        this.line(277, 28.5, 297, 28.5);
        this.line(0, 181.5, 20, 181.5);
        this.line(277, 181.5, 297, 181.5);
        this.__stroke = tmpC;
        this.strokeWeight(tmpW);
    },

    pageFrame: function()  {
        var tmpW = this.__strokeWeight;
        var tmpO = this.__strokeOpacity;
        var tmpC = this.__stroke;
        var tmpF = this.__fill;
        this.strokeWeight(0.2);
        this.strokeOpacity(1);
        this.stroke(this.bw1(0));
        this.noFill();
        this.rect(0, 0, this.__width, this.__height);
        this.__strokeOpacity = tmpO;
        this.__stroke = tmpC;
        this.__fill   = tmpF;
        this.strokeWeight(tmpW);
    },

    trimMark: function() {
        var sx = 1, sy = 1;
        var tmpW = this.__strokeWeight;
        var tmpO = this.__strokeOpacity;
        var tmpQ = this.__fillOpacity;
        var tmpC = this.__stroke;
        var tmpF = this.__fill;

        this.strokeWeight(0.5);
        this.stroke(this.bw1(0));
        this.strokeOpacity(1);
        if (this.__landscape) {
            this.line(10 * sx, 10 * sy, 30 * sx, 10 * sy);
            this.line(10 * sx, 10 * sy, 10 * sx, 30 * sy);

            this.line(10 * sx, 200 * sy, 30 * sx, 200 * sy);
            this.line(10 * sx, 200 * sy, 10 * sx, 180 * sy);

            this.line(272 * sx, 200 * sy, 252 * sx, 200 * sy);
            this.line(272 * sx, 200 * sy, 272 * sx, 180 * sy);
        } else {
            this.line(10 * sx, 10 * sy, 30 * sx, 10 * sy);
            this.line(10 * sx, 10 * sy, 10 * sx, 30 * sy);

            this.line(10 * sx, 272 * sy, 10 * sx, 252 * sy);
            this.line(10 * sx, 272 * sy, 30 * sx, 272 * sy);

            this.line(200 * sx, 10 * sy, 200 * sx, 30 * sy);
            this.line(200 * sx, 10 * sy, 180, 10 * sy);
        }

        this.noStroke();

        this.fill(this.bw1(0));
        this.fillOpacity(1);
        if (this.__landscape) {
            this.ellipse(28 * sx, 182 * sy, 4 * sx, 4 * sy);
        } else {
            this.ellipse(28 * sx, 28 * sy, 4 * sx, 4 * sy);
        }
        this.__fillOpacity = tmpQ;
        this.__fill = tmpF;
 
        this.__strokeOpacity = tmpO;
        this.__stroke = tmpC;
        this.strokeWeight(tmpW);
    },

/*
 *  symbols (deprecated)
 */  

    genericSmilieSymbol: function(callback) {
        var ret = this.__currentGroup.symbol().viewbox(0, 0, 5000, 5000);
    //    printf("<symbol viewBox=\"0 0 5000 5000\" preserveAspectRatio=\"xMidYMid\" id=\"%s\">\n", id);
        symbol.path("M 2500,0 L 2762,13 3014,49 3257,109 3488,191 3708,294 3914,417 4105,559 4281,719 4441,895 4583,1086 4706,1292 4809,1512 4891,1743 4951,1986 4987,2238 5000,2500 4987,2762 4951,3014 4891,3257 4809,3488 4706,3708 4583,3914 4441,4105 4281,4281 4105,4441 3914,4583 3708,4706 3488,4809 3257,4891 3014,4951 2762,4987 2500,5000 2238,4987 1986,4951 1743,4891 1512,4809 1292,4706 1086,4583 895,4441 719,4281 559,4105 417,3914 294,3708 191,3488 109,3257 49,3014 13,2762 0,2500 13,2238 49,1986 109,1743 191,1512 294,1292 417,1086 559,895 719,719 895,559 1086,417 1292,294 1512,191 1743,109 1986,49 2238,13 2500,0 Z")
    //    printf("<path d=\"M 2500,0 L 2762,13 3014,49 3257,109 3488,191 3708,294 3914,417 4105,559 4281,719 4441,895 4583,1086 4706,1292 4809,1512 4891,1743 4951,1986 4987,2238 5000,2500 4987,2762 4951,3014 4891,3257 4809,3488 4706,3708 4583,3914 4441,4105 4281,4281 4105,4441 3914,4583 3708,4706 3488,4809 3257,4891 3014,4951 2762,4987 2500,5000 2238,4987 1986,4951 1743,4891 1512,4809 1292,4706 1086,4583 895,4441 719,4281 559,4105 417,3914 294,3708 191,3488 109,3257 49,3014 13,2762 0,2500 13,2238 49,1986 109,1743 191,1512 294,1292 417,1086 559,895 719,719 895,559 1086,417 1292,294 1512,191 1743,109 1986,49 2238,13 2500,0 Z\"/>\n");
        var g1 = symbol.group().attr({ 'stroke': "none", 'fill': "black" });
    //    printf("<g style=\"stroke:none;fill:black\">\n");
        g1.path("M 1690,1469 L 1718,1470 1745,1474 1771,1481 1796,1490 1820,1501 1842,1514 1863,1529 1882,1547 1899,1565 1914,1586 1927,1608 1938,1632 1947,1657 1954,1683 1958,1710 1959,1738 1958,1766 1954,1793 1947,1819 1938,1844 1927,1868 1914,1890 1899,1911 1882,1930 1863,1947 1842,1962 1820,1975 1796,1986 1771,1995 1745,2002 1718,2006 1690,2007 1662,2006 1635,2002 1609,1995 1584,1986 1560,1975 1538,1962 1517,1947 1499,1930 1481,1911 1466,1890 1453,1868 1442,1844 1433,1819 1426,1793 1422,1766 1421,1738 1422,1710 1426,1683 1433,1657 1442,1632 1453,1608 1466,1586 1481,1565 1499,1547 1517,1529 1538,1514 1560,1501 1584,1490 1609,1481 1635,1474 1662,1470 1690,1469 Z");
    //    printf("<path d=\"M 1690,1469 L 1718,1470 1745,1474 1771,1481 1796,1490 1820,1501 1842,1514 1863,1529 1882,1547 1899,1565 1914,1586 1927,1608 1938,1632 1947,1657 1954,1683 1958,1710 1959,1738 1958,1766 1954,1793 1947,1819 1938,1844 1927,1868 1914,1890 1899,1911 1882,1930 1863,1947 1842,1962 1820,1975 1796,1986 1771,1995 1745,2002 1718,2006 1690,2007 1662,2006 1635,2002 1609,1995 1584,1986 1560,1975 1538,1962 1517,1947 1499,1930 1481,1911 1466,1890 1453,1868 1442,1844 1433,1819 1426,1793 1422,1766 1421,1738 1422,1710 1426,1683 1433,1657 1442,1632 1453,1608 1466,1586 1481,1565 1499,1547 1517,1529 1538,1514 1560,1501 1584,1490 1609,1481 1635,1474 1662,1470 1690,1469 Z\"/>\n");
    //    printf("</g>\n");
        var g2 = symbol.group().attr({ 'stroke': "none", 'fill': "black" });
    //    printf("<g style=\"stroke:none;fill:black\">\n");
        g2.path("M 3308,1469 L 3336,1470 3363,1474 3389,1481 3414,1490 3438,1501 3460,1514 3481,1529 3500,1547 3517,1565 3532,1586 3545,1608 3556,1632 3565,1657 3572,1683 3576,1710 3577,1738 3576,1766 3572,1793 3565,1819 3556,1844 3545,1868 3532,1890 3517,1911 3500,1930 3481,1947 3460,1962 3438,1975 3414,1986 3389,1995 3363,2002 3336,2006 3308,2007 3280,2006 3253,2002 3227,1995 3202,1986 3178,1975 3156,1962 3135,1947 3117,1930 3099,1911 3084,1890 3071,1868 3060,1844 3051,1819 3044,1793 3040,1766 3039,1738 3040,1710 3044,1683 3051,1657 3060,1632 3071,1608 3084,1586 3099,1565 3117,1547 3135,1529 3156,1514 3178,1501 3202,1490 3227,1481 3253,1474 3280,1470 3308,1469 Z");
    //    printf("<path d=\"M 3308,1469 L 3336,1470 3363,1474 3389,1481 3414,1490 3438,1501 3460,1514 3481,1529 3500,1547 3517,1565 3532,1586 3545,1608 3556,1632 3565,1657 3572,1683 3576,1710 3577,1738 3576,1766 3572,1793 3565,1819 3556,1844 3545,1868 3532,1890 3517,1911 3500,1930 3481,1947 3460,1962 3438,1975 3414,1986 3389,1995 3363,2002 3336,2006 3308,2007 3280,2006 3253,2002 3227,1995 3202,1986 3178,1975 3156,1962 3135,1947 3117,1930 3099,1911 3084,1890 3071,1868 3060,1844 3051,1819 3044,1793 3040,1766 3039,1738 3040,1710 3044,1683 3051,1657 3060,1632 3071,1608 3084,1586 3099,1565 3117,1547 3135,1529 3156,1514 3178,1501 3202,1490 3227,1481 3253,1474 3280,1470 3308,1469 Z\"/>\n");
    //    printf("</g>\n");
        var g3 = symbol.group().attr({ 'stroke': "black", 'stroke-width': 200, 'fill': "none" });
    //    printf("<g style=\"stroke:black;stroke-width:200;fill:none\">\n");
        callback(g3);
    //    printf("%s\n", str);
    //    printf("</g>\n");
    //    printf("</symbol>\n");
        return ret;
    },

    smilieSymbol1: function() {
        return genericSmilieSymbol(function (g) {
           g.path("M 1127,3590 L 1293,3672 1462,3743 1632,3803 1804,3852 1977,3890 2151,3918 2325,3934 2500,3940 2675,3934 2849,3918 3023,3890 3196,3852 3368,3803 3538,3743 3707,3672 3873,3590");
        });
    },

    smilieSymbol2: function() {
        return genericSmilieSymbol(function (g) {
            g.line(1127, 4000, 3873, 4000);
        });
    },

    smilieSymbol3: function() {
        return genericSmilieSymbol(function (g) {
            g.path("M 1127,4056 L 1293,3974 1462,3903 1632,3843 1804,3794 1977,3756 2151,3728 2325,3712 2500,3707 2675,3712 2849,3728 3023,3756 3196,3794 3368,3843 3538,3903 3707,3974 3873,4056");
        });
    },

    flowerSymbol: function(id) {
        var ret = this.__currentGroup.symbol().viewbox(0, 0, 5000, 5000);
    //    printf("<symbol viewBox=\"0 0 5000 5000\" preserveAspectRatio=\"xMidYMid\" id=\"%s\">\n", id);
        ret.path("M 4505,2553 L 4505,2516 4463,2516 4428,2516 4385,2478 4428,2478 4463,2478 4505,2478 4505,2441 4795,2215 4965,1915 5001,1577 4838,1239 4633,976 4300,826 3925,826 3557,938 3515,938 3515,976 3473,976 3473,1013 3473,976 3473,938 3473,938 3473,901 3388,600 3267,375 3062,187 2815,37 2525,0 2192,37 1945,187 1733,375 1612,600 1570,901 1570,938 1570,938 1570,976 1570,1013 1527,976 1485,976 1450,938 1450,938 1075,826 700,826 367,976 162,1239 0,1577 42,1915 205,2215 495,2441 495,2478 537,2478 580,2478 622,2478 580,2516 537,2516 495,2516 495,2553 205,2779 42,3085 0,3386 162,3724 367,3987 700,4137 1075,4174 1450,4062 1450,4024 1485,4024 1527,4024 1570,3987 1570,4024 1570,4062 1570,4062 1570,4099 1612,4400 1733,4625 1945,4813 2192,4963 2525,5001 2815,4963 3062,4813 3267,4625 3388,4400 3473,4099 3473,4062 3473,4062 3473,4024 3473,3987 3473,4024 3515,4024 3515,4024 3557,4062 3925,4174 4300,4137 4633,3987 4838,3724 5001,3386 4965,3085 4795,2779 4505,2553 4505,2553 Z M 2525,1577 L 2192,1614 1902,1765 1655,1952 1527,2215 1485,2516 1527,2779 1655,3048 1902,3235 2192,3386 2525,3423 2815,3386 3098,3235 3352,3048 3473,2779 3557,2516 3473,2215 3352,1952 3098,1765 2815,1614 2525,1577 2525,1577 Z");
    //    printf("<path d=\"M 4505,2553 L 4505,2516 4463,2516 4428,2516 4385,2478 4428,2478 4463,2478 4505,2478 4505,2441 4795,2215 4965,1915 5001,1577 4838,1239 4633,976 4300,826 3925,826 3557,938 3515,938 3515,976 3473,976 3473,1013 3473,976 3473,938 3473,938 3473,901 3388,600 3267,375 3062,187 2815,37 2525,0 2192,37 1945,187 1733,375 1612,600 1570,901 1570,938 1570,938 1570,976 1570,1013 1527,976 1485,976 1450,938 1450,938 1075,826 700,826 367,976 162,1239 0,1577 42,1915 205,2215 495,2441 495,2478 537,2478 580,2478 622,2478 580,2516 537,2516 495,2516 495,2553 205,2779 42,3085 0,3386 162,3724 367,3987 700,4137 1075,4174 1450,4062 1450,4024 1485,4024 1527,4024 1570,3987 1570,4024 1570,4062 1570,4062 1570,4099 1612,4400 1733,4625 1945,4813 2192,4963 2525,5001 2815,4963 3062,4813 3267,4625 3388,4400 3473,4099 3473,4062 3473,4062 3473,4024 3473,3987 3473,4024 3515,4024 3515,4024 3557,4062 3925,4174 4300,4137 4633,3987 4838,3724 5001,3386 4965,3085 4795,2779 4505,2553 4505,2553 Z M 2525,1577 L 2192,1614 1902,1765 1655,1952 1527,2215 1485,2516 1527,2779 1655,3048 1902,3235 2192,3386 2525,3423 2815,3386 3098,3235 3352,3048 3473,2779 3557,2516 3473,2215 3352,1952 3098,1765 2815,1614 2525,1577 2525,1577 Z\"/>\n");
    //    printf("</symbol>\n");
        return ret;
    },

    heartSymbol: function(id) {
        var ret = this.__currentGroup.symbol().viewbox(0, 0, 5000, 5000);
    //    printf("<symbol viewBox=\"0 0 5000 5000\" preserveAspectRatio=\"xMidYMid\" id=\"%s\">\n", id);
        ret.path("M 2498,5000 L 74,1594 16,1397 11,1373 8,1349 5,1325 3,1300 1,1251 0,1201 2,1154 7,1106 14,1058 23,1011 34,965 48,919 63,874 81,829 101,785 122,741 146,699 171,657 198,616 227,575 257,536 289,498 323,461 358,425 395,390 433,357 473,324 513,293 555,263 598,234 642,207 688,181 734,157 782,135 830,114 879,95 929,77 980,61 1032,46 1084,34 1137,23 1190,15 1244,8 1298,4 1353,1 1408,0 1464,2 1519,6 1575,12 1630,20 1687,31 1743,44 1799,59 1854,77 1911,98 1967,121 2022,147 2075,175 2131,206 2187,240 2240,276 2292,315 2345,359 2397,406 2499,502 2706,315 2730,294 2756,275 2782,256 2809,239 2865,206 2922,175 2951,161 2984,147 3054,122 3086,110 3113,98 3124,93 3133,87 3137,85 3140,82 3142,80 3144,77 3199,59 3254,44 3310,31 3367,20 3422,12 3478,6 3534,2 3590,0 3644,1 3699,4 3753,8 3807,15 3860,23 3913,34 3965,46 4017,61 4068,77 4118,94 4167,114 4215,135 4262,157 4309,181 4354,207 4399,234 4442,263 4484,293 4524,324 4564,357 4602,390 4638,425 4674,461 4708,498 4740,536 4770,575 4799,616 4826,657 4851,698 4875,741 4896,784 4916,829 4934,873 4950,918 4963,964 4974,1011 4983,1058 4990,1106 4995,1153 4997,1201 4997,1250 4994,1300 4989,1349 4981,1397 4976,1422 4970,1447 4955,1496 4923,1594 2499,5001 2498,5000 Z");
    //    printf("<path d=\"M 2498,5000 L 74,1594 16,1397 11,1373 8,1349 5,1325 3,1300 1,1251 0,1201 2,1154 7,1106 14,1058 23,1011 34,965 48,919 63,874 81,829 101,785 122,741 146,699 171,657 198,616 227,575 257,536 289,498 323,461 358,425 395,390 433,357 473,324 513,293 555,263 598,234 642,207 688,181 734,157 782,135 830,114 879,95 929,77 980,61 1032,46 1084,34 1137,23 1190,15 1244,8 1298,4 1353,1 1408,0 1464,2 1519,6 1575,12 1630,20 1687,31 1743,44 1799,59 1854,77 1911,98 1967,121 2022,147 2075,175 2131,206 2187,240 2240,276 2292,315 2345,359 2397,406 2499,502 2706,315 2730,294 2756,275 2782,256 2809,239 2865,206 2922,175 2951,161 2984,147 3054,122 3086,110 3113,98 3124,93 3133,87 3137,85 3140,82 3142,80 3144,77 3199,59 3254,44 3310,31 3367,20 3422,12 3478,6 3534,2 3590,0 3644,1 3699,4 3753,8 3807,15 3860,23 3913,34 3965,46 4017,61 4068,77 4118,94 4167,114 4215,135 4262,157 4309,181 4354,207 4399,234 4442,263 4484,293 4524,324 4564,357 4602,390 4638,425 4674,461 4708,498 4740,536 4770,575 4799,616 4826,657 4851,698 4875,741 4896,784 4916,829 4934,873 4950,918 4963,964 4974,1011 4983,1058 4990,1106 4995,1153 4997,1201 4997,1250 4994,1300 4989,1349 4981,1397 4976,1422 4970,1447 4955,1496 4923,1594 2499,5001 2498,5000 Z\"/>\n");
    //    printf("</symbol>\n");
        return ret;
    },

/*
 *  turtle grphics functions
 */

    pushTurtle: function() {
        this.__turtleStack.push(this.__turtleX);
        this.__turtleStack.push(this.__turtleY);
        this.__turtleStack.push(this.__turtleHeading);
    },

    popTurtle: function() {
        this.__turtleHeading = this.__turtleStack.pop();
        this.__turtleY       = this.__turtleStack.pop();
        this.__turtleX       = this.__turtleStack.pop();
    },

    UP:  0,
    CLEAN: 1,
    DIRTY: 2,

    penUp: function() {
        if (this.__turtlePen == this.DIRTY) {
            /*    endShape(0);  */
        }
        this.__turtlePen = this.UP;
    },

    finish: function() {
        this.penUp();
        this.resetMatrix();
        var draw = this.__currentGroup; 
//        draw.size(this.__width, this.__height);
        draw.size('100%', '100%');
        draw.viewbox(0, 0, this.__width, this.__height);
    },

    penDown: function() {
        if (this.__turtlePen == this.UP) {
            this.__turtlePen = this.CLEAN;
        }
    },

    forward: function(len) {
        var dx = len * this.cos360(this.__turtleHeading);
        var dy = len * this.sin360(this.__turtleHeading);
        if (this.__turtlePen == this.CLEAN) {
            this.__turtlePen = this.DIRTY;
            /*
                    beginShape();
                    vertex(this.__turtleX, __turtleY);
            */
        }
        if (this.__turtlePen == this.DIRTY) {
            this.line(this.__turtleX, this.__turtleY, this.__turtleX + dx, this.__turtleY + dy);
            /*        vertex(__turtleX + dx, __turtleY + dy); */
        }
        this.__turtleX += dx;
        this.__turtleY += dy;
    },

    backward: function(len) {
        var dx = - len * this.cos360(this.__turtleHeading);
        var dy = - len * this.sin360(this.__turtleHeading);
        this.__turtleX += dx;
        this.__turtleY += dy;
    },

    turn: function(angle) {
        var tmp;
        this.__turtleHeading += angle;

        tmp = Math.floor(this.__turtleHeading);
        this.__turtleHeading -= tmp;
        if (this.__turtleHeading < 0) {
            this.__turtleHeading += 1;
            tmp--;
        }
        tmp %= 360;
        if (tmp < 0) {
            tmp += 360;
        }
        this.__turtleHeading += tmp;
    },

    direction: function(dir) {
        var tmp;
        this.__turtleHeading = dir;

        tmp = Math.floor(this.__turtleHeading);
        this.__turtleHeading -= tmp;
        if (this.__turtleHeading < 0) {
            this.__turtleHeading += 1;
            tmp--;
        }
        tmp %= 360;
        if (tmp < 0) {
            tmp += 360;
        }
        this.__turtleHeading += tmp;
    },

    go: function(x, y) {
        if (this.__turtleX != x || this.__turtleY != y) {
            if(this.__turtlePen == this.DIRTY) {
                this.penUp();
                this.__turtleX = x; this.__turtleY = y;
                this.penDown();
            } else {
                this.__turtleX = x; this.__turtleY = y;
            }
        }
    },

    center: function() {
        this.go(148.5, 105);
    },

    getX: function() {
        return this.__turtleX;
    },

    getY: function() {
        return this.__turtleY;
    },

    getAngle: function() {
        return this.__turtleHeading;
    },

    say: function() {
        this.pushMatrix();
        this.translate(this.__turtleX, this.__turtleY);
        this.rotate360(this.__turtleHeading);
        var args = [];
        args.push("" + arguments[0]);  /* String に強制 */
        args.push(0);
        args.push(0);
        for (i = 1; i < arguments.length; i++) {
            args.push(arguments(i));
        }
        this.text.apply(this, args);
        this.popMatrix();
    },

    makeCards: function(sx, sy, dx, dy, w, h, cols, rows) {
       	var ret = new Array(cols * rows);
       	var n = 0;
       	var x = sx;
       	for (var i = 0; i < cols; i++) {
       	    var y = sy;
       	    for (var j = 0; j < rows; j++, n++) {
       	        ret[n] = { 'x': x, 'y': y, 'width': w, 'height': h };
       	        y += dy;
       	    }
       	    x += dx;
       	}
       	return ret;	
    },

    buildCardSpecs: function() {
        this.__cardSpecs["エーワン F8A4-5"] = {
            'width':  210,
            'height': 297,
            'cards':  this.makeCards(8, 10.5, 97, 69, 97, 69, 2, 4)
        };
        this.__cardSpecs["エーワン F10A4-2"] = {
            'width':  210,
            'height': 297,
            'cards':  this.makeCards(18.6, 21.2, 86.4, 50.8, 86.4, 50.8, 2, 5)
        };
        this.__cardSpecs["エーワン F10A4-1"] = {
            'width':  210,
            'height': 297,
            'cards':  this.makeCards(14, 11, 91, 55, 91, 55, 2, 5)
        };
    },

    cardFrame: function(card) {
        var tmpW = this.__strokeWeight;
        var tmpO = this.__strokeOpacity;
        var tmpC = this.__stroke;
        var tmpF = this.__fill;
        this.strokeWeight(0.2);
        this.strokeOpacity(1);
        this.stroke(this.bw1(0));
        this.noFill();
        this.rect(this.__clipMargin, this.__clipMargin, 
                  card['width'] - 2 * this.__clipMargin, card['height'] - 2 * this.__clipMargin);
        this.__strokeOpacity = tmpO;
        this.__stroke = tmpC;
        this.__fill   = tmpF;
        this.strokeWeight(tmpW);
    },

    clipWithCard: function(card) {
        var rect = this.__currentGroup
                       .rect(card['width'] - 2 * this.__clipMargin, card['height'] - 2 * this.__clipMargin)
                       .move(this.__clipMargin, this.__clipMargin);
        this.__currentGroup.clipWith(rect);
    },

    numCards: function() {
        if (this.__cards) return cards.length;
        return 1;   // カードなし
    },

    // https://mathiasbynens.be/notes/javascript-unicode
    countSymbols: function(string) {
	return string
		// Replace every surrogate pair with a BMP symbol.
		.replace(this.__regexAstralSymbols, '_')
		// _and *then* get the length.
		.length;
    },

    // https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/String/charAt 
    fixedCharAt: function(str, idx) {
	  var ret = '';
	  str += '';
	  var end = str.length;

	  var surrogatePairs = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
	  while ((surrogatePairs.exec(str)) != null) {
	    var li = surrogatePairs.lastIndex;
	    if (li - 2 < idx) {
	      idx++;
	    } else {
	      break;
	    }
	  }

	  if (idx >= end || idx < 0) {
	    return '';
	  }

	  ret += str.charAt(idx);

	  if (/[\uD800-\uDBFF]/.test(ret) && /[\uDC00-\uDFFF]/.test(str.charAt(idx+1))) {
	    ret += str.charAt(idx+1); // Go one further, since one of the "characters" is part of a surrogate pair
	  }
	  return ret;
    },

  };

  var ret = new BookCover();
//  ret.randomizeByTime(); 
  ret.buildCardSpecs();
  return ret; 
})();

// https://developer.mozilla.org/ja/docs/Web/JavaScript/Reference/Global_Objects/String/fromCodePoint
/*! http://mths.be/fromcodepoint v0.1.0 by @mathias */
if (!String.fromCodePoint) {
  (function() {
    var defineProperty = (function() {
      // IE 8 only supports `Object.defineProperty` on DOM elements
      try {
        var object = {};
        var $defineProperty = Object.defineProperty;
        var result = $defineProperty(object, object, object) && $defineProperty;
      } catch(error) {}
      return result;
    }());
    var stringFromCharCode = String.fromCharCode;
    var floor = Math.floor;
    var fromCodePoint = function() {
      var MAX_SIZE = 0x4000;
      var codeUnits = [];
      var highSurrogate;
      var lowSurrogate;
      var index = -1;
      var length = arguments.length;
      if (!length) {
        return '';
      }
      var result = '';
      while (++index < length) {
        var codePoint = Number(arguments[index]);
        if (
          !isFinite(codePoint) ||       // `NaN`, `+Infinity`, or `-Infinity`
          codePoint < 0 ||              // not a valid Unicode code point
          codePoint > 0x10FFFF ||       // not a valid Unicode code point
          floor(codePoint) != codePoint // not an integer
        ) {
          throw RangeError('Invalid code point: ' + codePoint);
        }
        if (codePoint <= 0xFFFF) { // BMP code point
          codeUnits.push(codePoint);
        } else { // Astral code point; split in surrogate halves
          // http://mathiasbynens.be/notes/javascript-encoding#surrogate-formulae
          codePoint -= 0x10000;
          highSurrogate = (codePoint >> 10) + 0xD800;
          lowSurrogate = (codePoint % 0x400) + 0xDC00;
          codeUnits.push(highSurrogate, lowSurrogate);
        }
        if (index + 1 == length || codeUnits.length > MAX_SIZE) {
          result += stringFromCharCode.apply(null, codeUnits);
          codeUnits.length = 0;
        }
      }
      return result;
    };
    if (defineProperty) {
      defineProperty(String, 'fromCodePoint', {
        'value': fromCodePoint,
        'configurable': true,
        'writable': true
      });
    } else {
      String.fromCodePoint = fromCodePoint;
    }
  }());
}
