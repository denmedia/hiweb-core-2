/**
 * Created by denmedia on 08.06.2017.
 */


var hw_input_post = {

    init: function () {
        jQuery('body').on('init', '.hw-input-post', hw_input_post._event_init);
        jQuery('.hw-input-post').each(hw_input_post._event_init);
    },

    _event_init: function () {
        jQuery(this).find('> .selectator_element').remove();
        if(typeof jQuery(this).selectator === 'function') {
            jQuery(this).find('> select').fadeOut().selectator({
                labels: { search: 'Поиск...' }
            });
        }
    },

    _make_selectrator: function () {

    }

};

jQuery(document).ready(hw_input_post.init);