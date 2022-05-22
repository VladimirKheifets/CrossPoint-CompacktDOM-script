# CrossPoint, CompacktDOM script


### Version: 1.0, 2022-05-19

Author: Vladimir Kheifets <kheifets.vladimir@online.de>

Copyright &copy; 2022 Vladimir Kheifets All Rights Reserved

[CompactDOM on GitHub](https://github.com/VladimirKheifets/Java-Script-library-CompactDOM)

[Online-tutorial of the Java Script Library CompactDOM](https://www.alto-booking.com/developer/CompactDOM/)

[CrossPoint Demo](https://www.alto-booking.com/developer/crosspoint/)

## About CrossPoint

### The CrossPoint script calculate crossing point of two straight lines or line segments.

You can draw two line segments on a plane bounded by a rectangle and
calculate the coordinate of their intersection point.

### 1. Drawing the line segments on a plane.

On device without a touch screen, use the mouse cursor to indicate the starting point of
the line segment and press the left mouse button, also indicate the end point.
On touch screen devices, touch the start and end points for each line segment.

### 2. Coordinates of start and end points.

The coordinates (only positive values) of the mouse cursor or screen finger touch coordinate
are defined in the global coordinate system.
The origin of the coordinates (0,0) is located in the upper left corner of the rectangle bounding the plane.
The x-axis is directed from top to bottom, the y-axis is from left to right.
For each line segment, absolute coordinates and Cartesian coordinates are determined.
The origin (0,0) of the Cartesian coordinate system is at the center of the rectangle bounding the plane.

### 3. Transforming the line segments to a straight line.
After two line segments are drawn, radio buttons appear: "LINE SEGMENT" and "LINE".
If you check the "LINE" button, then the line segment will be transformed into a straight line passing
through two given points until it intersects with a rectangle bounding the plane.

### 4. Calculation of the coordinates of the intersection point.
To calculate the intersection point, click the "Calculate with JS"  or "Calculate with PHP" button.
If the "Calculate with PHP" button is pressed, AJAX-Request will be sent to the server
and AJAX-Response" will be received.
The request file [crossPointRequest.json](https://www.alto-booking.com/developer/crosspoint/crossPointRequest.json)
and response file [crossPointResponse.json](https://www.alto-booking.com/developer/crosspoint/crossPointResponse.json) will be saved on the server.

## index.html

```html
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="CompactDOM.min.js"></script>
<script type="text/javascript" src="index.js"></script>
</head>
<body>
  <header>
   <h1></h1>
   <div>
    <input><label></label>
    <input><label></label>
    <span id="a1"></span>
    <span id="b1"></span>
  </div>
  <div>
    <input><label></label>
    <input><label></label>
    <span id="a2"></span>
    <span id="b2"></span>
  </div>
  </header>
<main></main>
</body>
</html>
```
## index.js CompactDOM script
```js
/*

Cross Point

CompactDOM script

Version: 1.0, 2022-05-19

Author: Vladimir Kheifets (kheifets.vladimir@online.de)

Copyright ©2022 Vladimir Kheifets All Rights Reserved

Online-tutorial of the Java Script Library CompactDOM:
https://www.alto-booking.com/developer/CompactDOM/

CompactDOM on GitHub:
https://github.com/VladimirKheifets/Java-Script-library-CompactDOM

*/

__.laSelector("en");
dic = __.getDictionary(_dirLa);
//---------------------------------
  __.ready(() => {
  Env = __.env();
  __.on(Env.eor, ev => { __.reload();});
  //------------------------------------------------
  head = _("head");
  title = dic.title;
  head.create( title, {tag:"title"} );
  head.create(1,{ tag:"meta", charset:"utf-8" });
  head.create(1,
  {
    tag:"meta",
    name:"viewport",
    content:"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, user-scalable=0"
  });
  __.link("css/index.css,css/modal.css,css/button_to_up.css,css/selLanguages.css");
  ctx = [];
  lay = [];
  lines = {};
  inp = _("input");
  lab = _("label");

  //------------------------------------------------
  colors = {1:"blue", 2:"purple", 3:"red"};
  canvasWidthD = [300,500,500,600,860];
  canvasHeightD = [300,300,300,400,400];
  Env = __.env();
  canvasWidth = canvasWidthD[Env.dev];
  canvasHeight = canvasHeightD[Env.dev];
  gridCell = 10;
  X0 = canvasWidth/2 - 1;
  Y0 = canvasHeight/2 - 1;

  main = _("main");
  mainCanvasSt = "width:"+canvasWidth+"px;height:"+canvasHeight+"px"
  main.css(mainCanvasSt);
  _("header h1").content(title);

  //-- canvas -------------------------------------------------------
  main.create(3,
    {
      tag: "canvas",
      id: "lay",
      width: canvasWidth,
      height: canvasHeight,
      style:mainCanvasSt
    });
  elCanvas = _("canvas");
  //---LINE div --------------------------------------
    _("header div").each((el,ind) => {
      i = ind + 1;
      el.css("color:" + colors[i]);
      el.attr("id","dL"+i);
  });


 labContent=dic.switch.concat(dic.switch);
 _("label").each((el,ind)=>{el.content(labContent[ind])});

  //-- footer -------------------------------------------------------
  d = new Date();
  year = d.getFullYear();
  footer = "<p>&copy; </p>" + year + " Alto Booking"
  footerSt="margin-top:"+ (canvasHeight+20) + "px";
  main.create(footer, {add:"after",tag:"footer"});//, style:footerSt
  //------------------------------------------------------------------

  nav = __.create(1, {tag:"nav"});
  nav.create(
    dic.button,
    {
      tag:"button",
      id:"but"
    }
  );

  navCSS = "top:"+(_("header").position().top+10)+"px";
  nav.css(navCSS);
  navHide = () =>{if(Env.dev<3) nav.hide();};
  res = __.create(1,{tag:"p"});
  res.css("height:55px");
  res.hide(100);
  if(Env.dev < 3)
  {
    menu  = main.create(1, {add:"after",tag:"div", id:"menu"});
    fileName=(Env.tou?"menuts":"menu")+".html";
    menu.include(fileName);
    nav.hide();
    res.css(navCSS);
    menu.click(()=>{
      if(nav.ishide())
        nav.show();
      else
        nav.hide();
      if(!res.ishide())
        res.hide(100);
    });
  }

  i=1;
  while(i<3)
  {
    aI = "a" + i;
    bI = "b" + i;
    lines[aI] = _("#" + aI);
    lines[bI] = _("#" + bI);
    _("#dL"+i +" input").each((el,ind) => {
      el.attr(
      {
        "type" : "radio",
        "id" : "inp" + i,
        "name" : "inp" + i,
        "value" : ind+1
      }
      );
    });
    i++;
  }

  // Defining canvas & layots
  elCanvas.each((el,ind) => {
    ctx[ind] = el.d.getContext('2d');
    lay[ind] = el;
    el.css("z-index:" + ind);
  });

  startSetting = () => {
    pXY = {
      width: canvasWidth,
      height: canvasHeight,
      1:{line:{}},
      2:{line:{}}
    };
    activesPoint = {1:"a", 2:"a"};
    Drawing = {1:true,2:true};
    lay[2].hide();
    _("span").each((el) => {el.content("")});
    but = [];
    _("button").each( (el, ind) => {
      but[ind] = el;
      if(ind>1) el.hide();
    });
    inp.each( (el, ind) => {
      if(ind == 0 || ind == 2) el.checked(2);
      el.css("opacity:0");
    });
    lab.each( (el, ind) => {
      if(ind == 1 || ind == 3) el.css("opacity:0");
    });
    res.css("opacity:0");
  };

  showLineSelector = (iL) => {
    inpEl = {1:[0,1], 2:[2,3]};
    labEl = {1:[1],2:[3]};
    inp.each( (el, ind) => {
      if(inpEl[iL].includes(ind))
        el.css("opacity:1");
    });
    lab.each( (el, ind) => {
      if(labEl[iL].includes(ind))
        el.css("opacity:1");
    });
  }



  drawingGrid = (cansasW, cansasH, sizeCell, c, color) => {
    c.strokeStyle = "#ddd";
    for (var x = sizeCell; x < cansasW; x += sizeCell) {
      c.moveTo(x, 0);
      c.lineTo(x, cansasH);
    }

    for (var y = sizeCell; y < cansasH; y += sizeCell) {
      c.moveTo(0, y);
      c.lineTo(cansasW, y);
    }
    c.stroke();
  }

  drawingAxes = (cansasW, cansasH, c, color) => {
    // Drawing axes, Decart 0 in canvas x=399, y=198
    // Axis X
    x1X = 10;
    y1X = cansasH/2;
    x2X = cansasW - 20;
    y2X = y1X;
    drawingLine(c, x1X, y1X, x2X, y2X, color);
    drawingLine(c, x2X-10, y2X+5, x2X, y2X, color);
    drawingLine(c, x2X-10, y2X-5, x2X, y2X, color);
    drawingText(c,'X', x2X+2, y2X+7, color);

    // Axis Y
    x1Y = cansasW/2;
    y1Y = x1X+10;
    x2Y = x1Y;
    y2Y = cansasH - 10;
    drawingLine(c, x1Y, y1Y, x2Y, y2Y, color);
    drawingLine(c, x2Y+5, y1Y+10, x1Y, y1Y, color);
    drawingLine(c, x2Y-5, y1Y+10, x1Y, y1Y, color);
    drawingText(c,'Y', x1Y-6, y1Y-3, color);
  }


  drawingLine = (c, x1, y1, x2, y2, color )=> {
    c.beginPath();
    c.strokeStyle = color;
    c.moveTo(x1, y1);
    c.lineTo(x2, y2);
    c.closePath();
    c.stroke();
  };

  drawingPoint = (c, x, y, color )=> {
    c.beginPath();
    c.fillStyle = color;
    c.arc(x, y, 3, 0, 2 * Math.PI);
    c.fill();
    c.closePath();
    c.stroke();
  };

  drawingText = (c, text, x, y, color, font )=> {
    if(__.u(font)) font = '16px arial'
    c.beginPath();
    c.fillStyle = color;
    c.font = font;
    c.fillText(text, x, y);
    c.fill();
    c.closePath();
    c.stroke();
  };

  clearCanvas = (c) => {c.clearRect(0, 0, canvasWidth, canvasHeight)};

  butShowHide = (s) => {
    i = 2;
    while(i<but.length)
    {
      if(s)
        but[i].show();
      else
        but[i].hide();
      i++;
    }
  };


  ePoint = (e) => {
    id = e.target.id;
    ind = parseInt(id.substr(3))-1;
    x = e.offsetX;
    y = e.offsetY;
    Xd = x - X0;
    Yd = Y0 - y;
  };

  getlineABC = (XY) => {
     if(__.a(XY))
     {
        x1 = XY[0];
        y1 = XY[1];
        x2 = XY[2];
        y2 = XY[3];
     }
     else
     {
      x1 = XY["Xad"];
      y1 = XY["Yad"];
      x2 = XY["Xbd"];
      y2 = XY["Ybd"];
     }
     A = y2 - y1;
     B = x1 - x2;
     C = -x1*y2 + y1*x2;
     return [A,B,C];
   };

  crossPoint = (ABC1, ABC2) => {
    A1 = ABC1[0];
    B1 = ABC1[1];
    C1 = ABC1[2];
    A2 = ABC2[0];
    B2 = ABC2[1];
    C2 = ABC2[2];
    Xcd = Math.round((B1*C2 - B2*C1)/(A2*B2 - A2*B1));
    Ycd = Math.round((A1*C2 - A2*C1)/(A2*B1 - A1*B2));
    Xc = Xcd + X0;
    Yc = Y0 - Ycd;
    return [Xc, Yc, Xcd, Ycd];
  };

  crossPointK = (XY1, XY2, X0, Y0 ) => {
    x1 = XY1["Xad"];
    y1 = XY1["Yad"];
    x2 = XY1["Xbd"];
    y2 = XY1["Ybd"];

    x3 = XY2["Xad"];
    y3 = XY2["Yad"];
    x4 = XY2["Xbd"];
    y4 = XY2["Ybd"];
    //-----------------------------------
    z1 = (x3-x1)*(y2-y1)-(y3-y1)*(x2-x1);
    z2 = (x4-x1)*(y2-y1)-(y4-y1)*(x2-x1);
    z3 = (x1-x3)*(y4-y3)-(y1-y3)*(x4-x3);
    z4 = (x2-x3)*(y4-y3)-(y2-y3)*(x4-x3);
    crossPointCheck = (z3*z4>0 || z1*z2>0)?false:true;
    //-----------------------------------
    if(crossPointCheck)
    {
      k1 = (y1 - y2) / (x1 - x2);
      b1 = y2 - k1 * x2;
      k2 = (y3 - y4) / (x3 - x4);
      b2 = y4 - k2 * x4;
      Xd = Math.round((b2 - b1)/(k1 - k2));
      Yd = Math.round(k1*Xd + b1);
      X = Xd + X0;
      Y = Y0 - Yd;
      return {
        crossPointCheck: true,
        X:X,
        Y:Y,
        Xd:Xd,
        Yd:Yd
      };
    }
    else
      return {
        crossPointCheck:false
    };
  };

  elCanvas.on('click', (e) => { //mouseclick or touchstart
    navHide();
    ePoint(e);
    if(Drawing[ind])
    {
      drawingPoint(ctx[ind], x, y, colors[ind]);
      if(activesPoint[ind]==="a")
      {
        activesPoint[ind] = "b";
        pXY[ind]["Xa"] = x;
        pXY[ind]["Ya"] = y;
        pXY[ind]["Xad"] = Xd;
        pXY[ind]["Yad"] = Yd;
      }
      else
      {
        pXY[ind]["Xb"] = x;
        pXY[ind]["Yb"] = y;
        pXY[ind]["Xbd"] = Xd;
        pXY[ind]["Ybd"] = Yd;
        drawingLine(ctx[ind], pXY[ind]["Xa"], pXY[ind]["Ya"], x, y, colors[ind]);
        lay[2].show();
        Drawing[ind] = false;
        if(ind == 2)
        {
          butShowHide(1);
          showLineSelector(1);
          showLineSelector(2);
        }
      }
    }
  });

  elCanvas.on('mousemove', (e) => {
    navHide();
    ePoint(e);
    if(Drawing[ind])
    {
      ab = activesPoint[ind];
      if(ab === "b")
      {
         clearCanvas(ctx[ind]);
         drawingPoint(ctx[ind], pXY[ind]["Xa"], pXY[ind]["Ya"], colors[ind]);
         drawingLine(ctx[ind], pXY[ind]["Xa"], pXY[ind]["Ya"], x, y, colors[ind]);
         contP = dic.points[1] + ": ";
      }
      else
        contP = " "+dic.points[0]+": ";
      lines[ab+ind].content(contP + "x = "+Xd+", y = "+Yd);
    }
  });

  inp.change((e) => {
    el = e.target;
    id = _(el).attr("id");
    i = id.substr(3);
    if(activesPoint[i]==="b")
    {
      val = parseInt(_(el).val());
      if(val === 2)
      {
        ABC1 = getlineABC(pXY[i]);
        cHalbW = canvasWidth/2;
        cHalbH = canvasHeight/2;
        ABC2a = getlineABC([-cHalbW, -cHalbH, -cHalbW, cHalbH]);
        ABC2b = getlineABC([cHalbW, -cHalbH, cHalbW, cHalbH]);
        XYca = crossPoint(ABC1, ABC2a);
        XYcb = crossPoint(ABC1, ABC2b);
        drawingLine(ctx[i], XYca[0], XYca[1],  XYcb[0], XYcb[1], colors[i]);
        pXY[i]["line"] = {
          Xa:  XYca[0],
          Ya:  XYca[1],
          Xad: XYca[2],
          Yad: XYca[3],
          Xb:  XYcb[0],
          Yb:  XYcb[1],
          Xbd: XYcb[2],
          Ybd: XYcb[3]
        };
      }
      else
      {
         clearCanvas(ctx[i]);
         drawingPoint(ctx[i], pXY[i]["Xa"], pXY[i]["Ya"], colors[i]);
         drawingLine(ctx[i], pXY[i]["Xa"], pXY[i]["Ya"], pXY[i]["Xb"], pXY[i]["Yb"], colors[i]);
         drawingPoint(ctx[i], pXY[i]["Xb"], pXY[i]["Yb"], colors[i]);
         delete pXY[i].line;
      }
    }
  });

  showResult = (XYcp, code) => {
    if(__.u(code)) code="PHP";
    res.show(1);
    if(XYcp["crossPointCheck"])
    {
      drawingPoint(ctx[2], XYcp["X"], XYcp["Y"], colors[3]);
      out = "Crossing point found.<br>";
      out += "Coordinates ";
      out += "X: "+XYcp["Xd"] + ", Y: " + XYcp["Yd"];
      out += "<br>calculated with " + code;
      res.content(out);
    }
    else
    {
      out = "Crossing point not found!<br>";
      out += "Please click the \"clear\"<br>button and try again.";
      res.content(out);
    }
  };

  printInfo = (rsp) => {
    txt=dic.structure;
      var   out = "<div>";
      if(rsp)
      {
        out += txt[0];
      }
      out += txt[1];
      if(rsp)
      {
        out += txt[2];
      }
      out += "</div>";
      return out;
  };

  _("button").click((e) => {
  id = e.target.id;
  i = parseInt(id.substr(3));
    switch(i)
    {
      case 1:
      obj=
      {
        url:_dirLa+"/help.html",
        method:"get",
        func:(rsp) => {
         __.modal(rsp);
        },
        debug:1
      }
      __.send(obj);
      break;

      case 2:
      clearCanvas(ctx[0]);
      if(gridOn)
      {
        gridOn = false
        drawingAxes(canvasWidth, canvasHeight, ctx[0], "#aaa");
      }
      else
      {
        gridOn = true;
        drawingGrid(canvasWidth, canvasHeight, 10, ctx[0], "#ddd");
        drawingAxes(canvasWidth, canvasHeight, ctx[0], "#aaa");
      }
      break;

      case 3:
      XY1 = (pXY[1].line.Xa)?pXY[1].line:pXY[1];
      XY2 = (pXY[2].line.Xb)?pXY[2].line:pXY[2];
      XYcp =  crossPointK(XY1, XY2, X0, Y0 );
      showResult(XYcp,"Java Script");
      console.log(XYcp);
      break;

      case 4:
      obj=
      {
        url:"crossPoint.php",
        method:"post",
        dataType: "json",
        responseType: "json",
        data:pXY,
        func: showResult
      }
      __.send(obj);
      break;

      case 5:
      obj=
      {
        url:"crossPoint.php?pr=1",
        method:"post",
        dataType: "json",
        data:pXY,
        func: (rsp) => {
          out = "<div class = 'outPr'>";
          out += rsp;
          out += printInfo(true);
          out += "</div>";
          __.modal(out);
        }
      }
      __.send(obj);
      break;

      case 6:
      obj=
      {
        url:"crossPoint.php?pr=2",
        method:"post",
        dataType: "json",
        data:pXY,
        func: (rsp) => {
          out = "<div class = 'outPr'>";
          out += rsp;
          out += printInfo();
          out += "</div>";
          __.modal(out);
        }
      }
      __.send(obj);
      break;

      case 7:
      clearCanvas(ctx[1]);
      clearCanvas(ctx[2]);
      startSetting();
      break;
    }
    navHide();
  });

  //----------------------------------------------
  gridOn = true;
  drawingGrid(canvasWidth, canvasHeight, gridCell, ctx[0], "#ddd");
  drawingAxes(canvasWidth, canvasHeight, ctx[0], "#aaa");
  startSetting();
  __.modal();
  __.scroll();
  __.laSelector();
 });
```
## Data structure.
### Object pXY & AJAX Request

Keys of line segments and straight lines: 1,2\
Start point 'a', end point 'b':
- Xa,Ya, Xb,Yb - absolute coordinates
- Xad,Yad, Xbd,Ybd - cartesian coordinates

Key 'line' - coordinates of line or empty object\
Keys 'width', 'height' - width and height\
of the rectangle (canvas) bounding the plane\


```js
{
    "1": {
        "line": {},
        "Xa": 180,
        "Ya": 286,
        "Xad": -249,
        "Yad": -87,
        "Xb": 564,
        "Yb": 60,
        "Xbd": 135,
        "Ybd": 139
    },
    "2": {
        "line": {},
        "Xa": 566,
        "Ya": 294,
        "Xad": 137,
        "Yad": -95,
        "Xb": 330,
        "Yb": 76,
        "Xbd": -99,
        "Ybd": 123
    },
    "width": 860,
    "height": 400
}
```

### AJAX Response

Key crossPointCheck:
- true - crossing point found,
- false - crossing not found

Crossing point coordinates:
- X,Y - absolute coordinates
- Xd,Yd - cartesian coordinates

```js
{
    "crossPointCheck": true,
    "X": 410,
    "Y": 151,
    "Xd": -19,
    "Yd": 48
}
```
## File languages/selLanguages.txt

```
#de~Deutsch
#en~English
#es~Español
#fr~Français
#it~Italiano
#ru~Русский
#zh~中文
```
## File languages/en/dictionary.txt

```
#title~Calculate crossing point of two straight lines or line segments
#button~Help~On/Off grid~Calculate with JS~Calculate with PHP~AJAX Request/Response~Data structure~Reset
#switch~LINE SEGMENT~LINE
#points~start point~end point
#structure~JSON-Request

~Keys of line segments and straight lines: 1,2
Start point 'a', end point 'b':
Xa,Ya, Xb,Yb - absolute coordinates
Xad,Yad, Xbd,Ybd - cartesian coordinates
Key 'line' - coordinates of line or empty object
Keys 'width', 'height' - width and height
of the rectangle (canvas) bounding the plane~

JSON-Response

Key crossPointCheck:
true - crossing point found,
false - crossing not found
Crossing point coordinates:
X,Y - absolute coordinates
Xd,Yd - cartesian coordinates
```

## crossPoint.php

```php
<?
/*

CrossPoint

PHP script

Version: 1.0

Author: Vladimir Kheifets (kheifets.vladimir@online.de)

Copyright ©2022 Vladimir Kheifets All Rights Reserved

*/
$req = file_get_contents('php://input');
file_put_contents("crossPointRequest.json", $req);
$pXY = json_decode($req, true);
//------------------------------------------------------
function crossPointK($XY1, $XY2, $X0, $Y0){
  $x1 = $XY1["Xad"];
  $y1 = $XY1["Yad"];
  $x2 = $XY1["Xbd"];
  $y2 = $XY1["Ybd"];

  $x3 = $XY2["Xad"];
  $y3 = $XY2["Yad"];
  $x4 = $XY2["Xbd"];
  $y4 = $XY2["Ybd"];
  //----------------------------------
  $z1 = ($x3-$x1)*($y2-$y1)-($y3-$y1)*($x2-$x1);
  $z2 = ($x4-$x1)*($y2-$y1)-($y4-$y1)*($x2-$x1);
  $z3 = ($x1-$x3)*($y4-$y3)-($y1-$y3)*($x4-$x3);
  $z4 = ($x2-$x3)*($y4-$y3)-($y2-$y3)*($x4-$x3);
  $crossPointCheck = ($z3*$z4>0 || $z1*$z2>0)?false:true;
  //----------------------------------
  if($crossPointCheck)
  {
    $k1 = ($y1 - $y2) / ($x1 - $x2);
    $b1 = $y2 - $k1 * $x2;
    $k2 = ($y3 - $y4) / ($x3 - $x4);
    $b2 = $y4 - $k2 * $x4;
    $Xd = round(($b2 - $b1)/($k1 - $k2));
    $Yd = round($k1*$Xd + $b1);
    $X = $Xd + $X0;
    $Y = $Y0 - $Yd;
    return (object)
    [
      "crossPointCheck" => true,
      "X" => $X,
      "Y" => $Y,
      "Xd" =>$Xd,
      "Yd" => $Yd
    ];
  }
  else
  {
    return (object)
    [
      "crossPointCheck"=>false
    ];
  }
};
//-------------------------------------------------------
$line1 = (array) $pXY[1]["line"];
$line2 = (array) $pXY[2]["line"];
$XY1 = empty($line1)?(array) $pXY[1]:$line1;
$XY2 = empty($line2["Xa"])?(array) $pXY[2]:$line2;
$width = (int) $pXY["width"];
$height = (int) $pXY["height"];
$X0 =  $width/2 - 1;
$Y0 =  $height/2 - 1;
$XYcp = crossPointK($XY1, $XY2, $X0, $Y0);
$rsp = json_encode($XYcp);
// save Request and Response
file_put_contents("crossPointRequest.json", $req);
file_put_contents("crossPointResponse.json", $rsp);

if($_GET['pr']==1)
{
  //Print Request/Response
  echo "<h1>AJAX Request/Response</h1>";
  echo "<pre>";
  echo "<h2>JSON-Request</h2>";
  echo json_encode(json_decode($req), JSON_PRETTY_PRINT);
  echo "<h2>JSON-Response</h2>";
  echo json_encode($XYcp, JSON_PRETTY_PRINT);
}
else if($_GET['pr']==2)
{
  //Print Data structure
  echo "<pre>";
  echo "<h1>Data structure</h1>";
  echo "<h2>object pXY</h2>";
  echo json_encode(json_decode($req), JSON_PRETTY_PRINT);
}
else
{
  //- Send json Response ------------------------------------------
  header("Content-type: application/json; charset=utf-8");
  echo $rsp;
}
//---------------------------------------------------------------
?>
```

## index.css

```css
body{font-family: arial;font-size: 14px;color: #5B5B5B}

header, footer{
  font-weight: normal;
  width: 100%;
  text-align: center;
}

header h1{
  font-size: 20px;
  padding: 10px 0 10px 0;
  margin: 0;
}

footer{
  margin-top: 20px;
  text-align: center;
  font-size: 18px;
  position: relative
}

footer p{display: inline;font-size: 14px}

nav{
  position: absolute;
  width: auto;
  height: auto;
  left: 10px;
  top: 80px;
  display: table-cell;
  background-color: #FFF4F2;
  z-index: 5
}

nav button{
  width: 170px;
  display: block;
  cursor: pointer;
  margin:2px 0 2px 0;
  padding: 2px 0px 2px 0px;
  line-height: 20px
}

nav + p{
  position: absolute;
  left: 10px;
  top: 300px;
  width: 180px;
  max-height: 55px;
  background-color: white;
  border: 1px solid #85A0C9;
  border-radius: 5px;
  box-shadow:3px 3px 3px rgba(0, 0, 0, 0.5);
  margin-top: 20px;
  text-align: center;
  padding: 10px;
  line-height: 18px;
  background-color: #FFF4F2;
  z-index: 5
}

main{
  position: relative;
  margin:0 auto;
  line-height: 25px;
  display: block;
}

header div{text-align: center}

canvas{
  border:1px solid #ddd;
  position: absolute;
  left: 0px;
  top: 10px;
  background-color: transparent;
  cursor: crosshair;
}

span{display: inline-block; width:240px; text-align: left;margin-left: 40px}

.help, .outPr{
  font-family: arial;
  font-size: 14px;
  padding: 20px;
  text-align: left
}

.help{width: 800px}
.outPr{width: 720px;padding: 30px 30px 30px 50px}
#menu{position: absolute;top: 0px;cursor: none}


.outPr h1{font-size: 18px; font-weight: normal}
.outPr h2{font-size: 16px; font-weight: normal}
.outPr div{
  position: absolute;
  top: 100px;
  right: 50px;
  width: 350px !important;
  width: 200px;
  padding: 30px 30px;
  border: 1px dotted #aaa;
  border-radius: 3px;
  display: block;
  background-color: #FFF4F2;
  box-shadow:3px 3px 3px rgba(0, 0, 0, 0.5);
  line-height: 18px;
  font-size: 14px;
  word-wrap: break-word;
  white-space: pre-line;
}

@media only screen and (max-width: 800px) {
  .help{ width: calc(100vw - 45px)}
  .outPr{ width: calc(100vw - 25px);padding: 10px 10px 10px 10px }
  header h1{margin-top: 20px}
  .outPr div{ width: 150px !important;right: 10px;top: 30px;padding: 10px 10px}
}

.help p{font-size: 14px;text-align: justify;}
.help h1{font-size: 18px;font-size: normal;margin-top: 20px;}
.help h2{font-size: 16px; font-size: normal}
```