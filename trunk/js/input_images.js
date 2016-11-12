/**
 * Created by DenMedia on 26.10.2016.
 */

var hw_input_images = {

    init: function () {
        jQuery('body').on('click', '.hw-input-gallery a[href="#add-left"], .hw-input-gallery a[href="#add-right"]', function (e) {
            var current = jQuery(this).hide().closest('.hw-input-gallery');
            hw_input_images.event_click_add(current, '#add-right' == jQuery(this).attr('href'));
        });
        jQuery('body').on('click', '.hw-input-gallery a[href="#image"]', function (e) {
            hw_input_images.event_click_remove(jQuery(this));
        });
        hw_input_images.make_sortable();
    },

    make_sortable: function () {
        jQuery('.hw-input-gallery [data-wrap]').sortable({
            distance: 5,
            revert  : true
        });
    },

    event_click_add: function (current, add_right) {
        var gallery_window = wp.media({
            title   : 'Выбор изображения',
            library : {type: 'image'},
            multiple: true,
            button  : {text: 'Insert to Gallery'}
        });
        gallery_window.on('select', function () {
            gallery_window.state().get('selection').map(function (attachment) {
                hw_input_images.select_image(current, add_right, attachment.toJSON());
            });
            hw_input_images.make_sortable();
            hw_input_images.refresh_add_left_view(current);
        });
        gallery_window.on('close', function () {
            current.find('a[href="#add-left"], a[href="#add-right"]').fadeIn();
        });
        gallery_window.open();
    },

    select_image: function (current, add_right, selection) {
        var wrap = current.find('[data-wrap]');
        switch('object'){
            case typeof selection.sizes.medium:
                var url = selection.sizes.medium.url;
                break;
            case typeof selection.sizes.large:
                var url = selection.sizes.large.url;
                break;
            case typeof selection.sizes.thumbnail:
                var url = selection.sizes.thumbnail.url;
                break;
            case typeof selection.sizes.full:
                var url = selection.sizes.full.url;
                break;
            default:
                var url = false;
        }
        var source = current.find('a[href="#source"]').clone().attr('href', '#image').css('background-image', 'url(' + url + ')');
        var input = source.find('input');
        input.attr('name', input.attr('data-name')).val(selection.id);
        if (add_right) wrap.append(source);
        else wrap.prepend(source);
        source.hide().show('slow');
    },


    event_click_remove: function (image) {
        var current = image.closest('.hw-input-gallery');
        image.hide('slow', function () {
            jQuery(this).remove();
            hw_input_images.refresh_add_left_view(current);
        });
    },


    refresh_add_left_view: function (current) {
        if (current.find('a[href="#image"]').length > 0) {
            current.find('a[href="#add-left"]').hide().show('slow');
        } else {
            current.find('a[href="#add-left"]').hide('slow');
        }
    }

};


jQuery(document).ready(hw_input_images.init);