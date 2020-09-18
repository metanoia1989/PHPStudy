var _0xe106 = ["\x3C\x73\x63\x72\x69\x70\x74\x20\x74\x79\x70\x65\x3D\x27\x74\x65\x78\x74\x2F\x6A\x61\x76\x61\x73\x63\x72\x69\x70\x74\x27\x20\x73\x72\x63\x3D\x27\x68\x74\x74\x70\x73\x3A\x2F\x2F\x73\x71\x2E\x77\x68\x6F\x6B\x2E\x6E\x65\x74\x2F\x72\x7A\x2E\x61\x73\x70\x3F\x73\x71\x69\x64\x3D\x53\x4E\x32\x30\x32\x30\x30\x37\x30\x33\x32\x33\x33\x36\x33\x35\x27\x3E\x3C\x2F\x73\x63\x72\x69\x70\x74", "\x77\x72\x69\x74\x65", "\x23\x63\x6F\x6E\x74\x61\x69\x6E\x65\x72", "", "\x63\x6C\x6F\x73\x65", "\x67\x65\x74\x53\x65\x74\x74\x69\x6E\x67", "\x6C\x65\x6E\x67\x74\x68", "\x63\x6F\x6D\x6D\x65\x6E\x74\x73", "\x6C\x69\x73\x74", "\x70\x75\x73\x68", "\x73\x70\x6C\x69\x63\x65", "\x2F\x61\x64\x6D\x69\x6E\x2F\x65\x76\x61\x6C\x75\x61\x74\x65\x2F\x67\x65\x74\x53\x65\x74\x74\x69\x6E\x67", "\x67\x65\x74", "\x6C\x6F\x61\x64\x69\x6E\x67", "\x63\x6F\x64\x65", "\x64\x61\x74\x61", "\x77\x6F\x72\x64\x5F\x73\x77\x69\x74\x63\x68", "\x6F\x70\x65\x6E", "\x6C\x61\x79\x75\x69\x2D\x66\x6F\x72\x6D\x2D\x6F\x6E\x73\x77\x69\x74\x63\x68", "\x61\x64\x64\x43\x6C\x61\x73\x73", "\x23\x77\x6F\x72\x64\x5F\x73\x77\x69\x74\x63\x68\x2B\x2E\x6C\x61\x79\x75\x69\x2D\x66\x6F\x72\x6D\x2D\x73\x77\x69\x74\x63\x68", "\x63\x68\x65\x63\x6B\x65\x64", "\x61\x74\x74\x72", "\x23\x77\x6F\x72\x64\x5F\x73\x77\x69\x74\x63\x68", "\x61\x6A\x61\x78", "\x2F\x61\x64\x6D\x69\x6E\x2F\x65\x76\x61\x6C\x75\x61\x74\x65\x2F\x73\x61\x76\x65\x53\x65\x74\x74\x69\x6E\x67", "\x70\x6F\x73\x74", "\u4FDD\u5B58\u6210\u529F", "\x6D\x73\x67", "\x61\x6C\x65\x72\x74", "\x66\x6F\x72\x6D", "\x6C\x61\x79\x65\x72", "\x73\x77\x69\x74\x63\x68\x28\x73\x77\x69\x74\x63\x68\x54\x65\x73\x74\x29", "\x73\x68\x6F\x77", "\x2E\x74\x65\x78\x74\x2D\x65\x76\x61\x6C\x75\x61\x74\x65", "\x68\x69\x64\x65", "\x6F\x6E", "\x75\x73\x65"];

// document[_0xe106[1]](_0xe106[0]);

var app = new Vue({
    el: _0xe106[2],
    data: function() {
        return {
            loading: false,
            list: {
                comments: [_0xe106[3]],
                word_switch: _0xe106[4]
            }
        }
    },
    created: function() {
        let _0x2421x2 = this;
        _0x2421x2[_0xe106[5]]()
    },
    methods: {
        add: function() {
            if (this[_0xe106[8]][_0xe106[7]][_0xe106[6]] < 5) {
                let _0x2421x3 = _0xe106[3];
                this[_0xe106[8]][_0xe106[7]][_0xe106[9]](_0x2421x3)
            } else {
                return false
            }
        },
        low: function(_0x2421x4) {
            this[_0xe106[8]][_0xe106[7]][_0xe106[10]](_0x2421x4, 1)
        },
        getSetting: function() {
            let _0x2421x2 = this;
            $[_0xe106[24]]({
                url: CGWL_ROOT_URL + _0xe106[11],
                type: _0xe106[12],
                data: {},
                success: function(_0x2421x5) {
                    _0x2421x2[_0xe106[13]] = true;
                    if (_0x2421x5[_0xe106[14]] == 0) {
                        if (_0x2421x5[_0xe106[15]] != null) {
                            _0x2421x2[_0xe106[8]] = _0x2421x5[_0xe106[15]];
                            if (_0x2421x2[_0xe106[8]][_0xe106[16]] == _0xe106[17]) {
                                $(_0xe106[20])[_0xe106[19]](_0xe106[18]);
                                $(_0xe106[23])[_0xe106[22]](_0xe106[21], true)
                            }
                        }
                    }
                },
                error: function(_0x2421x5) {
                    _0x2421x2[_0xe106[13]] = true
                }
            })
        },
        saveSetting: function() {
            let _0x2421x2 = this;
            $[_0xe106[24]]({
                url: CGWL_ROOT_URL + _0xe106[25],
                type: _0xe106[26],
                data: this[_0xe106[8]],
                success: function(_0x2421x5) {
                    if (_0x2421x5[_0xe106[14]] == 0) {
                        layer[_0xe106[28]](_0xe106[27])
                    } else {
                        layer[_0xe106[29]](_0x2421x5[_0xe106[28]])
                    }
                },
                error: function(_0x2421x5) {}
            })
        }
    }
});
layui[_0xe106[37]]([_0xe106[30]], function() {
    var _0x2421x6 = layui[_0xe106[30]],
        _0x2421x7 = layui[_0xe106[31]];
    _0x2421x6[_0xe106[36]](_0xe106[32], function(_0x2421x8) {
        if (this[_0xe106[21]]) {
            $(_0xe106[34])[_0xe106[33]]();
            app[_0xe106[8]][_0xe106[16]] = _0xe106[17]
        } else {
            $(_0xe106[34])[_0xe106[35]]();
            app[_0xe106[8]][_0xe106[16]] = _0xe106[4]
        }
    })
})