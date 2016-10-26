/**
 * Created by hiweb on 21.10.2016.
 */
var hw_input_repeat = {

    selector: '.hw-input-repeat',
    selector_source: '.row.source',
    selector_wrap: 'tbody.wrap',
    selector_row: '.row',
    selector_button_add: '[data-click="add"]',
    selector_button_remove: '[data-click="remove"]',

    init: function () {
        jQuery('body').on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_add, hw_input_repeat.click_add).on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_remove, hw_input_repeat.click_remove);
        hw_input_repeat.make_sortable();
        hw_input_repeat.make_table_names(jQuery(hw_input_repeat.selector));
    },

    make_sortable: function () {
        jQuery(hw_input_repeat.selector + ' tbody').sortable({
            update: function () {
                hw_input_repeat.make_table_names(jQuery(this).closest(hw_input_repeat.selector));
            },
            distance: 5,
            handle: '.drag-handle',
            helper: function (e, ui) {
                ui.find('th, td').each(function () {
                    jQuery(this).width(jQuery(this).width());
                });
                return ui;
            },
            revert: true,
            start: function (e, elements) {
                elements.placeholder.height(elements.helper.height());
            }
        });
        jQuery(hw_input_repeat.selector + ' tbody').disableSelection();
    },

    make_table_names: function (current) {
        var row = 0;
        current.each(function () {
            var subcurrent = jQuery(this);
            subcurrent.find(hw_input_repeat.selector_wrap + ' > tr').not(hw_input_repeat.selector_source).each(function () {
                var tr = jQuery(this)
                tr.find('[data-col-id]').each(function () {
                    jQuery(this).attr('name', subcurrent.attr('id') + '[' + tr.index() + '][' + jQuery(this).attr('data-col-id') + ']')
                });
            });
        });

    },

    click_add: function (e) {
        e.preventDefault();
        var current = jQuery(this).closest(hw_input_repeat.selector);
        var newLine = current.find(hw_input_repeat.selector_source).clone().removeClass('source').hide().fadeIn();
        jQuery(current).find(hw_input_repeat.selector_wrap).append(newLine);
        jQuery(current).find('.message').hide();
        hw_input_repeat.make_table_names(current);
    },

    click_remove: function (e) {
        e.preventDefault();
        var current = jQuery(this).closest(hw_input_repeat.selector);
        jQuery(this).closest(hw_input_repeat.selector_row).fadeOut(function () {
            jQuery(this).remove();
            hw_input_repeat.make_table_names(current);
            var rows = current.find(hw_input_repeat.selector_wrap + ' > tr.row').not(hw_input_repeat.selector_source).length;
            console.info(rows);
            if (rows == 0) {
                current.find('.message').fadeIn();
            }
        });
    }

};

jQuery(document).ready(hw_input_repeat.init);