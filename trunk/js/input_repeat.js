/**
 * Created by hiweb on 21.10.2016.
 */
var hw_input_repeat = {

	selector              : '.hw-input-repeat',
	selector_source       : '[data-source]',
	selector_wrap         : '[data-wrap]',
	selector_row          : 'data-row',
	selector_button_add   : '[data-click="add"]',
	selector_button_remove: '[data-click="remove"]',

	init: function () {
		jQuery('body').on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_add, hw_input_repeat.click_add);
		jQuery('body').on('click', hw_input_repeat.selector + ' ' + hw_input_repeat.selector_button_remove, hw_input_repeat.click_remove);
		hw_input_repeat.generateHtml();
		hw_input_repeat.make_sortable();
	},

	generateHtml: function () {

	},

	generateValue: function () {

	},

	make_sortable: function () {
		jQuery(hw_input_repeat.selector + ' tbody').sortable();
		jQuery(hw_input_repeat.selector + ' tbody').disableSelection();
	},

	click_add: function (e) {
		e.preventDefault();
		var current = jQuery(this).closest(hw_input_repeat.selector);
		var newLine = current.find(hw_input_repeat.selector_source).clone().removeAttr('data-source').attr(hw_input_repeat.selector_row, '').hide().fadeIn();
		jQuery(current).find(hw_input_repeat.selector_wrap).append(newLine);
		jQuery('[data-help="first"]').hide();
	},

	click_remove: function (e) {
		e.preventDefault();
		var current = jQuery(this).closest('[' + hw_input_repeat.selector_row + ']').fadeOut();
	}

};

jQuery(document).ready(hw_input_repeat.init);