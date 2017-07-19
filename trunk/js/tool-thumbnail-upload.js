/**
 * Created by denmedia on 11.03.2017.
 */

jQuery(document).ready(function ($) {

    var hw_tool_thumbnail_upload = {


        init: function () {
            $('.wp-list-table tbody tr[id]').each(function () {
                ///
                var tr = $(this);
                var upload_root = tr.find('.thumb_hw_upload_zone');

                if (tr.attr('id').match(/^post-/) != null) {
                    var post_id = tr.attr('id').replace('post-', '');
                    var type = 'post';
                } else {
                    var post_id = tr.attr('id').replace('tag-', '');
                    var type = 'taxonomy';
                }
                ///
                hw_tool_thumbnail_upload._make_events(upload_root, post_id, type);
                hw_tool_thumbnail_upload._make_upload_zone(upload_root, post_id, type);
            });

            var showDrag = false,
                timeout = -1;

            $('html, .thumb_hw_upload_zone').bind('dragenter', function () {
                $('.thumb_hw_upload_zone').addClass('global-dragover');
                showDrag = true;
            }).bind('dragover', function () {
                showDrag = true;
            }).bind('dragleave', function (e) {
                showDrag = false;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    if (!showDrag) {
                        $('.thumb_hw_upload_zone').removeClass('global-dragover');
                    }
                }, 200);
            });

        },

        open_media: function (place, post_id, type) {
            var gallery_window = wp.media({
                title: 'Выбор изображения',
                library: {type: 'image'},
                multiple: false,
                button: {text: 'Select Image'}
            });
            gallery_window.on('select', function () {
                var data = gallery_window.state().get('selection').first().toJSON();
                if (data.hasOwnProperty('id') && data.hasOwnProperty('type') && data.type == 'image' && data.hasOwnProperty('sizes') && (data.sizes.hasOwnProperty('thumbnail') || data.sizes.hasOwnProperty('medium'))) {
                    var img_src = '';
                    if (data.sizes.hasOwnProperty('thumbnail')) img_src = data.sizes.thumbnail.url;
                    else if (data.sizes.hasOwnProperty('medium')) img_src = data.sizes.medium.url;
                    place.find('[data-img]').css('background-image', 'url(' + img_src + ')');
                    place.attr('data-has-thumbnail', '1').attr('data-is-process', '1');
                    $.ajax({
                        url: ajaxurl + '?action=hw_thumbnail_post_set',
                        type: 'post',
                        dataType: 'json',
                        data: {do: 'upload', post_id: post_id, thumbnail_id: data.id, type: type},
                        success: function (data) {
                            place.attr('data-is-process', '0');
                            if (data[0]) {
                                place.attr('data-has-thumbnail', '1');
                            } else {
                                console.warn(data);
                            }
                        },
                        error: function (data) {
                            place.attr('data-is-process', '0');
                            console.warn(data);
                        }
                    });
                } else {
                    console.warn('Не найден размер изображения');
                }
            });
            gallery_window.open();
        },

        _make_events: function (place, post_id, type) {
            ///CTRL
            $(place).find('[data-ctrl-btn]').on('click', function () {
                var action = $(this).attr('data-ctrl-btn');
                switch (action) {
                    case 'upload':
                        place.trigger('click');
                        break;
                    case 'media':
                        hw_tool_thumbnail_upload.open_media(place, post_id, type);
                        break;
                    case 'remove':
                        hw_tool_thumbnail_upload._click_remove(place, post_id, type);
                        break;
                }
            });
        },

        _make_upload_zone: function (place, post_id, type) {
            if (typeof hw_Dropzone !== 'function') return;
            ///
            var upload_zone_id = $(place).attr('id'),
                showDrag = false,
                timeout = -1;
            if (jQuery("#" + upload_zone_id).length === 0) return;
            ///
            new hw_Dropzone("#" + upload_zone_id, {
                url: ajaxurl + '?action=hw_thumbnail_post_upload',
                headers: {'postid': post_id, 'posttype': type},
                maxFilesize: 20,
                filesizeBase: 1024,
                previewsContainer: false,
                type: 'post',
                dataType: 'json',
                data: {do: 'upload', post_id: post_id},
                dragenter: function () {
                    showDrag = true;
                    place.addClass('dragover');
                },
                dragover: function () {
                    showDrag = true;
                },
                dragleave: function () {
                    showDrag = false;
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        if (!showDrag) {
                            place.removeClass('dragover');
                        }
                    }, 200);
                },
                addedfile: function (data) {
                    place.removeClass('dragover');
                    place.attr('data-is-process', '1');
                    $('.global-dragover').removeClass('global-dragover');
                },
                complete: function (answer) {
                    place.attr('data-is-process', '0');
                    place.removeClass('dragover');
                    $('.global-dragover').removeClass('global-dragover');
                    var response = answer.xhr.response;
                    var data = $.parseJSON(response);
                    console.info(data);
                    if (typeof data == 'object') {
                        if (data[0] == false) {
                            alert('в ходе загрузки произошла ошибка: ' + data[1]);
                        } else {
                            place.attr('data-has-thumbnail', '1');
                            place.find('[data-img]').css('background-image', 'url(' + data[1] + ')');
                        }
                    } else {
                        alert('В ходе загрузки произошла ошибка: 1');
                        console.warn(data);
                    }
                }
            });
        },

        _click_remove: function (place, post_id, type) {
            place.removeClass('dragover');
            place.attr('data-is-process', '1');
            $.ajax({
                url: ajaxurl + '?action=hw_thumbnail_post_remove',
                type: 'post',
                dataType: 'json',
                data: {do: 'upload', post_id: post_id, type: type},
                success: function (data) {
                    place.attr('data-is-process', '0');
                    if (data[0]) {
                        place.attr('data-has-thumbnail', '0');
                    } else {
                        console.warn(data);
                    }
                },
                error: function (data) {
                    place.attr('data-is-process', '0');
                    console.warn(data);
                }
            });
        }

    };

    hw_tool_thumbnail_upload.init();

    ////
});