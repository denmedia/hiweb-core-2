/////////////////////////////////////////////////////////////////////////////////////
//            B A S E   J a v a S c r i p t   F u n c t i o n s   v.3.0            //
/////////////////////////////////////////////////////////////////////////////////////


var hiweb = {

    ready: false,

    width: 960,
    height: 800,

    mouse_x: 0,
    mouse_y: 0,

    ini: function () {
        ////////////
        jQuery(document).ready(function () {
            hiweb.ready = true;
            //Загрузка HTML без картинок
            hiweb.width = jQuery(window).width();
            hiweb.height = jQuery(window).height();
            //Координаты мышки
            jQuery(document).mousemove(function (e) {
                hiweb.mouse_x = e.pageX;
                hiweb.mouse_y = e.pageY;
            });
        });
        ////////////
        jQuery.fn.extend({
            slideRightShow: function (in_time) {
                return this.each(function () {
                    jQuery(this).show('slide', {direction: 'right'}, in_time);
                });
            },
            slideLeftHide: function (in_time) {
                return this.each(function () {
                    jQuery(this).hide('slide', {direction: 'left'}, in_time);
                });
            },
            slideRightHide: function (in_time) {
                return this.each(function () {
                    jQuery(this).hide('slide', {direction: 'right'}, in_time);
                });
            },
            slideLeftShow: function (in_time) {
                return this.each(function () {
                    jQuery(this).show('slide', {direction: 'left'}, in_time);
                });
            }
        });
        ////////////
        jQuery(window).resize(function () {
            hiweb.width = jQuery(window).width();
            hiweb.height = jQuery(window).height();
        });
    },

    play_sound: function (in_soundFile) {
        if (typeof(in_soundFile) == 'undefined') {
            in_soundFile = 'play_sound.mp3';
        }
        if (jQuery('.object_js_beep').length == 0) {
            jQuery("<audio class='object_js_beep'></audio>").attr({
                'src': 'base/_snd/' + in_soundFile,
                'volume': 1
            }).appendTo("body");
        }
        jQuery('.object_js_beep')[0].play();
    },

    alert: function (in_text, appendToBody, in_level) {
        return_str = '';
        if (!hiweb.isset(appendToBody)) {
            appendToBody = true;
        }
        if (!hiweb.isset(in_level)) {
            in_level = 3;
        }
        ///
        if (typeof(in_text) == 'object') {
            if (appendToBody) {
                if (jQuery('.object_js_alert').length == 0) {
                    jQuery('body').prepend('<div class="object_js_alert"></div>');
                }
                jQuery('.object_js_alert').html(hiweb.dump(in_text, '', in_level, true));
            }
            else {
                hiweb.dump(in_text, '', 3, false);
            }
        }
        else {
            if (appendToBody) {
                if (jQuery('.object_js_alert').length == 0) {
                    jQuery('body').prepend('<div class="object_js_alert"></div>');
                }
                jQuery('.object_js_alert').html("[<strong>" + in_text + "</strong>]");
            }
            else {
                noty({text: "[<strong>" + in_text + "</strong>]", dismissQueue: true});
            }

        }
        ////
        if (jQuery.treeview) {
            jQuery(".filetree").treeview({
                animated: "fast",
                collapsed: true,
                persist: "location"
            });
        }
    },

    dump: function (in_arr, in_title, in_level, return_html) {
        if (!hiweb.isset(in_title)) {
            in_title = '>>>';
        }
        if (!hiweb.isset(in_level)) {
            in_level = 4;
        }
        if (!hiweb.isset(return_html)) {
            return_html = false;
        }
        return_str = '';
        //return_str = "<h2>typeOf ["+typeof(in_arr)+"]</h2>";
        return_str += '<ul class="filetree" style="font-size: 10px; color: #333">';

        if (typeof in_arr != 'object') {
            return_str += ("<li style=\'color: #ddd\'><span><strong>" + in_arr + "</strong> : <i>error</i></span></li>");
        }
        else {

            for (list_n in in_arr) {
                if (hiweb.in_array(list_n, ['selectionDirection', 'selectionEnd', 'selectionStart'])) {
                    return_str += ("<li style=\'color: #ddd\'><span><strong>" + list_n + "</strong> : <i>error</i></span></li>");
                }
                else if (typeof(in_arr[list_n]) == 'function') {
                    return_str += ("<li style=\'color: #77f\'><span><strong>" + list_n + "</strong> : <i>function()</i></span>{...}</li>");
                }
                else if (typeof(in_arr[list_n]) == 'object') {
                    if (in_level < 2) {
                        return_str += ("<li style=\'color: #7a7\'><span class=\"folder\">" + list_n + " : <strong>...</strong> [<i>" + typeof(in_arr[list_n]) + "</i>]</span></li>");
                    }
                    else {
                        return_str += '<li style=\'color: #7a7\'><span class="folder">' + list_n + ' : </span>' + hiweb.dump(in_arr[list_n], in_title, in_level - 1, true) + '</li>';
                    }
                }
                else if (typeof(in_arr[list_n]) == 'undefined') {
                    return_str += ('<li style=\'color: #a77\'><span class="file">' + list_n + " : [<i>" + typeof(in_arr[list_n]) + "</i>]</span></li>");
                }
                else if (typeof(in_arr[list_n]) == 'string') {
                    //if(in_arr[list_n].length > 100) { in_arr[list_n] = in_arr[list_n].substring(0, 100); }
                    return_str += ("<li>" + list_n + " => <strong style=\'color: #000\'>" + in_arr[list_n].replace(/(<([^>]+)>)/ig, "") + "</strong> [<i>" + typeof(in_arr[list_n]) + "[" + in_arr[list_n].length + "]</i>]</li>");
                    //return_str +=("<li>" + list_n + " => <strong style=\'color: #000\'>...</strong> [<i>"+typeof(in_arr[list_n])+"</i>]</li>");
                }
                else if (typeof(in_arr[list_n]) == 'number') {
                    return_str += ("<li>" + list_n + " => <strong style=\'color: #000\'>" + in_arr[list_n] + "</strong> [<i>" + typeof(in_arr[list_n]) + "</i>]</li>");
                }
                else if (typeof(in_arr[list_n]) == 'boolean') {
                    return_str += ("<li>" + list_n + " => <strong style=\'color: #000\'>" + (in_arr[list_n] ? 'true' : 'false') + "</strong> [<i>" + typeof(in_arr[list_n]) + "</i>]</li>");
                }
                else {
                    return_str += ("<li>" + list_n + " => <strong style=\'color: #000\'>...</strong> [<i>" + typeof(in_arr[list_n]) + "</i>]</li>");
                }
            }

        }
        return_str += '</ul>';
        ////
        if (return_html) {
            return return_str;
        }
        else {
            noty({text: return_str, dismissQueue: true});
        }
    },

    //Конвертировать путь в Latin
    sanitize_path: function (inPathStr) {
        if (typeof inPathStr != 'string') {
            return 'file/_upload';
        }
        var len = inPathStr.length;
        returnStr = '';
        var strtr = {
            'а': 'a',
            'б': 'b',
            'в': 'v',
            'г': 'g',
            'д': 'd',
            'е': 'e',
            'ё': 'e',
            'ж': 'zh',
            'з': 'z',
            'и': 'i',
            'й': 'y',
            'к': 'k',
            'л': 'l',
            'м': 'm',
            'н': 'n',
            'о': 'o',
            'п': 'p',
            'р': 'r',
            'с': 's',
            'т': 't',
            'у': 'u',
            'ф': 'f',
            'х': 'h',
            'ц': 'c',
            'ч': 'ch',
            'ш': 'sh',
            'щ': 'sh',
            'ъ': '',
            'ы': 'yi',
            'ь': '',
            'э': 'e',
            'ю': 'yu',
            'я': 'ya',
            '0': '0',
            '1': '1',
            '2': '2',
            '3': '3',
            '4': '4',
            '5': '5',
            '6': '6',
            '7': '7',
            '8': '8',
            '9': '9',
            '-': '-',
            ' ': '_',
            '_': '_',
            ':': '_',
            ';': '_',
            'q': 'q',
            'w': 'w',
            'e': 'e',
            'r': 'r',
            't': 't',
            'y': 'y',
            'u': 'u',
            'i': 'i',
            'o': 'o',
            'p': 'p',
            'a': 'a',
            's': 's',
            'd': 'd',
            'f': 'f',
            'g': 'g',
            'h': 'h',
            'j': 'j',
            'k': 'k',
            'l': 'l',
            'z': 'z',
            'x': 'x',
            'c': 'c',
            'v': 'v',
            'b': 'b',
            'n': 'n',
            'm': 'm'
        };
        for (n = 0; n < len; n++) {
            symb = inPathStr.substr(n, 1).toLowerCase();
            if (symb == '/') {
                returnStr += '/';
            }
            else if (typeof strtr[symb] == 'string') {
                returnStr += strtr[symb];
            }
        }
        return returnStr;
    },

    on_load: function () {
        this.on_load = true;
    },

    isset: function (in_var) {
        return (typeof(in_var) != 'undefined' ? true : false );
    },

    ord: function (in_str) {
        return in_str.charCodeAt(0);
    },
    chr: function (in_ascii) {
        return String.fromCharCode(in_ascii);
    },

    count: function (in_arr_or_object) {
        list_n = 0;
        for (var list_key in in_arr_or_object) {
            list_n++
        }
        return list_n;
    },

    scrollTop: function (inPixel) {
        if (hiweb.isset(inPixel)) {
            if (hiweb.in_array(hiweb.client.browser, ['chrome', 'opera'])) {
                jQuery('body').scrollTop(inPixel);
            }
            else {
                jQuery('html').scrollTop(inPixel);
            }
        }
        else {
            return jQuery(document).scrollTop();
        }
    },

    rand: function (length, use_latin, use_register, use_numbers) {
        return_str = '';
        symb_arr = new Array;
        symb_num = 0;
        symb_only_alphavite = new Array();
        ////
        if (!hiweb.isset(length)) {
            length = 20;
        }
        if (!hiweb.isset(use_latin)) {
            use_latin = true;
        }
        if (!hiweb.isset(use_register)) {
            use_register = true;
        }
        if (!hiweb.isset(use_numbers)) {
            use_numbers = true;
        }
        ////Создает таблицу разрешенных символов
        if (use_latin) {
            for (list_n = hiweb.ord('a'); list_n <= hiweb.ord('z'); list_n++) {
                symb_only_alphavite[symb_num] = hiweb.chr(list_n);
                symb_arr[symb_num] = hiweb.chr(list_n);
                symb_num++;
            }
        }
        if (use_latin && use_register) {
            for (list_n = hiweb.ord('A'); list_n <= hiweb.ord('Z'); list_n++) {
                symb_arr[symb_num] = hiweb.chr(list_n);
                symb_num++;
            }
        }
        if (use_numbers) {
            for (list_n = hiweb.ord('0'); list_n <= hiweb.ord('9'); list_n++) {
                symb_arr[symb_num] = hiweb.chr(list_n);
                symb_num++;
            }
        }
        ////Выборка из разрешенных символов случайные
        for (list_n = 0; list_n < length; list_n++) {
            if (use_latin && list_n == 0) {
                return_str += symb_arr[jQuery.randomBetween(0, symb_only_alphavite.length - 1)];
            }
            else {
                return_str += symb_arr[jQuery.randomBetween(0, symb_arr.length - 1)];
            }
        }
        /////
        return return_str;
    },

    explode: function (in_split_str, in_arr_str) {
        if (!hiweb.isset(in_arr_str)) {
            in_arr_str = '';
        }
        return in_arr_str.split(in_split_str);
    },

    implode: function (delimeterStr, inArray) {
        return ( ( inArray instanceof Array ) ? inArray.join(delimeterStr) : inArray );
    },

    in_array: function (needle, haystack, argStrict) {
        var key = '', strict = !!argStrict;
        if (strict) {
            for (key in haystack) {
                if (haystack[key] === needle) {
                    return true;
                }
            }
        } else {
            for (key in haystack) {
                if (haystack[key] == needle) {
                    return true;
                }
            }
        }
        return false;
    },

    array_search: function (needle, haystack) {
        for (var i in haystack) {
            if (haystack[i] == needle) return i;
        }
        return false;
    },

    array_key_exists: function (inArray, key) {
        // http://kevin.vanzonneveld.net
        // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // +   improved by: Felix Geisendoerfer (http://www.debuggable.com/felix)
        // *     example 1: array_key_exists({'kevin': 'van Zonneveld'}, 'kevin');
        // *     returns 1: true
        // input sanitation
        if (!inArray || (inArray.constructor !== Array && inArray.constructor !== Object)) {
            return false;
        }
        return key in inArray;
    },

    strpos: function (haystack, needle, offset) {
        var i = (haystack + '').indexOf(needle, (offset || 0));
        return i === -1 ? false : i;
    },

    strRpos: function (haystack, needle, offset) {
        if (offset) offset = offset;
        else offset = 0;
        return haystack.lastindexOf(needle, offset);
    },

    strstr: function (haystack, needle, bool) {
        var pos = 0;
        haystack += "";
        pos = haystack.indexOf(needle);
        if (pos == -1) {
            return false;
        } else {
            if (bool) {
                return haystack.substr(0, pos);
            } else {
                return haystack.slice(pos);
            }
        }
    },

    trim: function (str) {
        return str.replace(/(^ *)|( *jQuery)/, "");
    },
    rtrim: function (str) {
        return str.replace(/( *jQuery)/, "");
    },
    ltrim: function (str) {
        return str.replace(/(^ *)/, "");
    },

    parse_str: function (str) {
        var arr = new Array();
        if (str.indexOf('&') != -1) {
            var GET = str.split('&');
            for (i = 0; i < GET.length; i++) {
                var cur = GET[i].split('=');
                arr[cur[0]] = cur[1];
            }
        }
        return arr;
    },

    str_replace: function (search, replace, subject) {
        return subject.split(search).join(replace);
    },

    strtr: function (str, from, to) {
        var fr = '',
            i = 0,
            j = 0,
            lenStr = 0,
            lenFrom = 0,
            tmpStrictForIn = false,
            fromTypeStr = '',
            toTypeStr = '',
            istr = '';
        var tmpFrom = [];
        var tmpTo = [];
        var ret = '';
        var match = false;

        // Received replace_pairs?
        // Convert to normal from->to chars
        if (typeof from === 'object') {
            //tmpStrictForIn = this.ini_set('phpjs.strictForIn', false); // Not thread-safe; temporarily set to true
            //from = this.krsort(from);
            //this.ini_set('phpjs.strictForIn', tmpStrictForIn);

            for (fr in from) {
                if (from.hasOwnProperty(fr)) {
                    tmpFrom.push(fr);
                    tmpTo.push(from[fr]);
                }
            }

            from = tmpFrom;
            to = tmpTo;
        }

        // Walk through subject and replace chars when needed
        lenStr = str.length;
        lenFrom = from.length;
        fromTypeStr = typeof from === 'string';
        toTypeStr = typeof to === 'string';

        for (i = 0; i < lenStr; i++) {
            match = false;
            if (fromTypeStr) {
                istr = str.charAt(i);
                for (j = 0; j < lenFrom; j++) {
                    if (istr == from.charAt(j)) {
                        match = true;
                        break;
                    }
                }
            } else {
                for (j = 0; j < lenFrom; j++) {
                    if (str.substr(i, from[j].length) == from[j]) {
                        match = true;
                        // Fast forward
                        i = (i + from[j].length) - 1;
                        break;
                    }
                }
            }
            if (match) {
                ret += toTypeStr ? to.charAt(j) : to[j];
            } else {
                ret += str.charAt(i);
            }
        }

        return ret;
    },

    isNumber: function (inMix) {
        inMix = hiweb.str_replace(' ', '', inMix);
        return !isNaN(inMix - 0) && inMix != null && inMix != "";
    },

    get_arrDeParam: function (query) {
        var query_string = {};
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            pair[0] = decodeURIComponent(pair[0]);
            pair[1] = decodeURIComponent(pair[1]);
            // If first entry with this name
            if (typeof query_string[pair[0]] === "undefined") {
                query_string[pair[0]] = pair[1];
                // If second entry with this name
            } else if (typeof query_string[pair[0]] === "string") {
                var arr = [query_string[pair[0]], pair[1]];
                query_string[pair[0]] = arr;
                // If third or later entry with this name
            } else {
                query_string[pair[0]].push(pair[1]);
            }
        }
        return query_string;
    },

    sort_htmlLi: function (selectorStr, sortDescending, selectorSortElementStr) {
        var mylist = jQuery(selectorStr);
        var listitems = mylist.find('li').get();
        listitems.sort(function (a, b) {
            textA = '';
            textB = '';
            ////
            if (typeof(selectorSortElementStr) == 'undefined' || selectorSortElementStr == '') {
                textA = jQuery(a).text().toUpperCase();
                textB = jQuery(b).text().toUpperCase();
            }
            else {
                textA = jQuery(a).find(selectorSortElementStr).text().toUpperCase();
                textB = jQuery(b).find(selectorSortElementStr).text().toUpperCase();
            }
            ////

            if (hiweb.isNumber(textA) && hiweb.isNumber(textB)) {
                textA = hiweb.str_replace(' ', '', textA);
                textB = hiweb.str_replace(' ', '', textB);
                return (parseFloat(textA) < parseFloat(textB)) ? 1 : -1;
            }
            else {
                return textA.localeCompare(textB);
            }
        });
        if (sortDescending) {
            listitems.reverse();
        }
        jQuery.each(listitems, function (idx, itm) {
            mylist.append(itm);
        });
    }

};

//////////////////////////////////////////////////////////////////////////////////////
///jQuery.URLEncode, jQuery.URLDecode
jQuery.extend({
    URLEncode: function (c) {
        var o = '';
        var x = 0;
        c = c.toString();
        var r = /(^[a-zA-Z0-9_.]*)/;
        while (x < c.length) {
            var m = r.exec(c.substr(x));
            if (m != null && m.length > 1 && m[1] != '') {
                o += m[1];
                x += m[1].length;
            } else {
                if (c[x] == ' ') o += '+'; else {
                    var d = c.charCodeAt(x);
                    var h = d.toString(16);
                    o += '%' + (h.length < 2 ? '0' : '') + h.toUpperCase();
                }
                x++;
            }
        }
        return o;
    },
    URLDecode: function (s) {
        var o = s;
        var binVal, t;
        var r = /(%[^%]{2})/;
        while ((m = r.exec(o)) != null && m.length > 1 && m[1] != '') {
            b = parseInt(m[1].substr(1), 16);
            t = String.fromCharCode(b);
            o = o.replace(m[1], t);
        }
        return o;
    }
});

////jQuery.random()
jQuery.extend({
    random: function (X) {
        return Math.floor(X * (Math.random() % 1));
    },
    randomBetween: function (MinV, MaxV) {
        return MinV + jQuery.random(MaxV - MinV + 1);
    }
});

////jQuery.disableSelection()
(function (jQuery) {
    jQuery.fn.disableSelection = function () {
        return this.each(function () {
            jQuery(this).attr('unselectable', 'on')
                .css({'-moz-user-select': 'none', '-o-user-select': 'none', '-khtml-user-select': 'none', '-webkit-user-select': 'none', '-ms-user-select': 'none', 'user-select': 'none'})
                .each(function () {
                    jQuery(this).attr('unselectable', 'on').bind('selectstart', function () {
                        return false;
                    });
                });
        });
    };
})(jQuery);

////jQuery.moveElementTo(selector)
(function (jQuery) {
    jQuery.fn.moveElementTo = function (selector) {
        element = this.detach();
        jQuery(selector).append(element);
        return element;
        /*return this.each(function(){
         var cl = jQuery(this).clone();
         jQuery(cl).appendTo(selector);
         jQuery(this).remove();
         });*/
    };
})(jQuery);