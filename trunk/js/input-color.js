var hw_input_color = {

    init: function(){
        jQuery('body').on('init', '[data-type-color]', function () {
            hw_input_color._make();
        });
        hw_input_color._make();
    },

    _make: function(){
        jQuery('[data-type-color]').removeAttr('data-type-color').colorPicker({
            opacity: true
        });
    }

};


jQuery(document).ready(hw_input_color.init);


