/**
 * Created by hiweb on 21.10.2016.
 */
var hw_input_repeat = {

    selector: '.hw-input-repeat',
    selector_source: '[data-source]',
    selector_wrap: '[data-wrap]',
    selector_row: 'data-row',
    selector_button_add: '[data-click="add"]',
    selector_button_remove: '[data-click="remove"]',

    init: function () {
        jQuery('body').on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_add, hw_input_repeat.click_add);
        jQuery('body').on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_remove, hw_input_repeat.click_remove);
        hw_input_repeat.make_sortable();
    },

    make_sortable: function () {
        jQuery(hw_input_repeat.selector + ' tbody').sortable();
        jQuery(hw_input_repeat.selector + ' tbody').disableSelection();
    },

    make_table_names: function (current) {
        var row = 0;
        current.find(hw_input_repeat.selector_wrap + ' > tr').not(hw_input_repeat.selector_source).each(function () {
            var tr = jQuery(this)
            tr.find('[data-col-id]').each(function () {
                jQuery(this).attr('name', current.attr('data-name') + '[' + (tr.index() - 1) + '][' + jQuery(this).attr('data-col-id') + ']')
            });
        });
    },

    click_add: function (e) {
        e.preventDefault();
        var current = jQuery(this).closest(hw_input_repeat.selector);
        var newLine = current.find(hw_input_repeat.selector_source).clone().removeAttr('data-source').attr(hw_input_repeat.selector_row, '').hide().fadeIn();
        jQuery(current).find(hw_input_repeat.selector_wrap).append(newLine);
        jQuery('[data-help="first"]').hide();
        hw_input_repeat.make_table_names(current);
    },

    click_remove: function (e) {
        e.preventDefault();
        var current = jQuery(this).closest('[' + hw_input_repeat.selector_row + ']').fadeOut(function () {
            jQuery(this).remove();
            hw_input_repeat.make_table_names(current);
        });
    }

};

jQuery(document).ready(hw_input_repeat.init);