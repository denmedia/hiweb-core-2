/**
 * Created by denmedia on 13.07.2017.
 */

jQuery(document).ready(function ($) {
    $('body').on('init_3', '.hw-input-terms-select', function () {
        $(this).chosen({
            allow_single_deselect: true
        });
    });
    $('.hw-input-terms-select').trigger('init_3');
});
