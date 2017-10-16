/**
 * Created by DenMedia on 25.10.2016.
 */

var hw_input_file = {

    init: function () {
        jQuery('body').on('click', '.hw-input-file[data-has-file="0"]', function (e) {
            var current = jQuery(e.currentTarget).closest('.hw-input-file');
            hw_input_file.event_click_select(current);
        });
        jQuery('body').on('click', '.hw-input-file[data-has-file="1"]', function (e) {
            e.preventDefault();
            var current = jQuery(e.currentTarget).closest('.hw-input-file');
            hw_input_file.deselect_file(current);
        });
        jQuery('body').on('change', '.hw-input-file input[name]', function () {
            console.info('!!!'); //todo детек изменения поля для сброса
        });
    },

    event_click_select: function (current) {
        var gallery_window = wp.media({
            title: 'Выбор файла PDF',
            //library: {type: 'file'},
            multiple: false,
            button: {text: 'Select file'}
        });
        gallery_window.on('select', function () {
            hw_input_file.select_file(current, gallery_window.state().get('selection').first().toJSON());
        });
        gallery_window.open();
    },

    select_file: function (current, selection) {
        console.info(current);
        var input = current.find('input');
        var media_id = selection.id;

        //var file_preview = current.find('.thumbnail');
        input.val(media_id);
        //file_preview.css('background-file', 'url(' + url + ')');
        current.attr('data-has-file', '1');
    },

    deselect_file: function (current) {
        var input = current.find('input').val('');
        var file_preview = current.find('.thumbnail');
        file_preview.css('background-file', 'none');
        current.attr('data-has-file', '0');
    }

};

jQuery(document).ready(hw_input_file.init);