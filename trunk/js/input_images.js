/**
 * Created by DenMedia on 26.10.2016.
 */

var hw_input_images = {

    init: function () {
        jQuery('body').on('click', '.hw-input-images [data-ctrl-sub="left"], .hw-input-images [data-ctrl-sub="right"]', function (e) {
            e.preventDefault();
            hw_input_images.event_click_add(hw_input_images.get_root(this), 'right' === jQuery(this).attr('data-ctrl-sub'));
        });
        jQuery('body').on('click', '.hw-input-images [data-click-remove]', function (e) {
            e.preventDefault();
            hw_input_images.event_click_remove(jQuery(this).closest('[data-image-id]'));
        });
        jQuery('body').on('click', '.hw-input-images [data-ctrl] [data-click-reverse]', function (e) {
            e.preventDefault();
            hw_input_images.event_click_reverse(this);
        });
        jQuery('body').on('click', '.hw-input-images [data-ctrl] [data-click-clear]', function (e) {
            e.preventDefault();
            hw_input_images.event_click_clear(this);
        });
        jQuery('body').on('click', '.hw-input-images [data-ctrl] [data-click-add]', function (e) {
            e.preventDefault();
            hw_input_images.event_click_add(hw_input_images.get_root(this), true);
        });
        hw_input_images.make_sortable();
    },

    get_root: function (e) {
        return jQuery(e).closest('.hw-input-images');
    },

    get_source: function (e) {
        var root = hw_input_images.get_root(e);
        return root.find('[data-source]');
    },

    get_list_root: function (e) {
        var root = hw_input_images.get_root(e);
        return root.find('.items');
    },

    get_list_images: function (e) {
        return hw_input_images.get_list_root(e).find('> [data-image-id]');
    },

    make_sortable: function () {
        jQuery('.hw-input-images .attachments .items').sortable({
            distance: 5,
            helper: 'clone',
            revert: true
        });
    },

    event_click_add: function (root, add_right) {
        var gallery_window = wp.media({
            title: 'Выбор изображения',
            library: {type: 'image'},
            multiple: true,
            button: {text: 'Выбрать файлы'}
        });
        gallery_window.on('select', function () {
            gallery_window.state().get('selection').map(function (attachment) {
                hw_input_images.select_image(root, add_right, attachment.toJSON());
            });
            hw_input_images.make_sortable();
            //hw_input_images.refresh_view(current);
        });
        gallery_window.open();
    },

    select_image: function (root, add_right, selection) {
        switch ('object') {
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
        var source = hw_input_images.get_source(root).clone();
        source.find('img').attr('src', url);
        source.removeAttr('data-source').attr('data-image-id', selection);
        var input = source.find('input');
        input.val(selection.id).attr('name', input.attr('data-name'));
        source.attr('id', root.attr('data-id'));
        if (add_right) {
            hw_input_images.get_list_root(root).append(source);
        } else {
            hw_input_images.get_list_root(root).prepend(source);
        }
    },


    event_click_remove: function (image) {
        jQuery(image).hide('slow', function () {
            jQuery(this).remove();
            hw_input_images.refresh_view(hw_input_images.get_root(this));
        });
    },

    event_click_reverse: function (e) {
        var list = hw_input_images.get_list_root(e);
        list.children().each(function (i, li) {
            list.prepend(li)
        })
    },

    event_click_clear: function (e) {
        if (confirm('Remove all images?')) {
            hw_input_images.get_list_images(e).each(function () {
                hw_input_images.event_click_remove(this);
            });
        }
    },


    refresh_view: function (root) {
        //
    }

};


jQuery(document).ready(hw_input_images.init);