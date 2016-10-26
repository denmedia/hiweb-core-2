/**
 * Created by DenMedia on 25.10.2016.
 */

var hw_input_image = {

    init: function () {
        jQuery('body').on('click', '.hw-input-image [data-click="select"]', function (e) {
            var current = jQuery(e.currentTarget).closest('.hw-input-image');
            hw_input_image.event_click_select(current);
        });
        jQuery('body').on('click', '.hw-input-image [data-click="deselect"]', function (e) {
            e.preventDefault();
            var current = jQuery(e.currentTarget).closest('.hw-input-image');
            hw_input_image.deselect_image(current);
        });
    },

    event_click_select: function (current) {
        var gallery_window = wp.media({
            title: 'Выбор изображения',
            library: {type: 'image'},
            multiple: false,
            button: {text: 'Insert'}
        });
        gallery_window.on('select', function () {
            hw_input_image.select_image(current, gallery_window.state().get('selection').first().toJSON());
        });
        gallery_window.open();
    },

    select_image: function (current, selection) {
        var input = current.find('input');
        var image_preview = current.find('.image-select');
        var thumbnail_url = selection.sizes.thumbnail.url;
        var media_id = selection.id;
        input.val(media_id);
        image_preview.css('background-image', 'url(' + thumbnail_url + ')').attr('data-click','deselect');
        current.attr('data-has-image', '1');
    },

    deselect_image: function (current) {
        var input = current.find('input').val('');
        var image_preview = current.find('.image-select');
        image_preview.css('background-image', 'none').attr('data-click','select');
        current.attr('data-has-image', '0');
    }

};

jQuery(document).ready(hw_input_image.init);