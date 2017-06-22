/**
 * Created by denmedia on 14.06.2017.
 */

var hw_tinyMCE_init = function(id){
    var settings = {
        theme: "modern",
        skin: "lightgray",
        language: "ru",
        formats: {
            alignleft: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign: "left"}}, {selector: "img,table,dl.wp-caption", classes: "alignleft"}],
            aligncenter: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign: "center"}}, {selector: "img,table,dl.wp-caption", classes: "aligncenter"}],
            alignright: [{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign: "right"}}, {selector: "img,table,dl.wp-caption", classes: "alignright"}],
            strikethrough: {inline: "del"}
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        browser_spellcheck: true,
        fix_list_elements: true,
        entities: "38,amp,60,lt,62,gt",
        entity_encoding: "raw",
        keep_styles: false,
        cache_suffix: "wp-mce-4603-20170530",
        resize: "vertical",
        menubar: false,
        branding: false,
        preview_styles: "font-family font-size font-weight font-style text-decoration text-transform",
        end_container_on_empty_block: true,
        wpeditimage_html5_captions: true,
        wp_lang_attr: "ru-RU",
        wp_shortcut_labels: {
            "Heading 1": "access1",
            "Heading 2": "access2",
            "Heading 3": "access3",
            "Heading 4": "access4",
            "Heading 5": "access5",
            "Heading 6": "access6",
            "Paragraph": "access7",
            "Blockquote": "accessQ",
            "Underline": "metaU",
            "Strikethrough": "accessD",
            "Bold": "metaB",
            "Italic": "metaI",
            "Code": "accessX",
            "Align center": "accessC",
            "Align right": "accessR",
            "Align left": "accessL",
            "Justify": "accessJ",
            "Cut": "metaX",
            "Copy": "metaC",
            "Paste": "metaV",
            "Select all": "metaA",
            "Undo": "metaZ",
            "Redo": "metaY",
            "Bullet list": "accessU",
            "Numbered list": "accessO",
            "Insert\/edit image": "accessM",
            "Remove link": "accessS",
            "Toolbar Toggle": "accessZ",
            "Insert Read More tag": "accessT",
            "Insert Page Break tag": "accessP",
            "Distraction-free writing mode": "accessW",
            "Keyboard Shortcuts": "accessH"
        },
        content_css: "http://photobank.hiweb.site/wp-includes/css/dashicons.min.css?ver=4.8,http://photobank.hiweb.site/wp-includes/js/tinymce/skins/wordpress/wp-content.css?ver=4.8",
        plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
        selector: "#" + id,
        wpautop: true,
        indent: false,
        toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv",
        toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
        toolbar3: "",
        toolbar4: "",
        tabfocus_elements: ":prev,:next",
        body_class: id + " locale-ru-ru"
    };

    tinyMCE.init(settings);
};

///
jQuery('body').on('drag_stop', '.tmce-active', function (item) {
    var id = jQuery(this).find('textarea').attr('id');
    var editor = tinymce.get(id);
    editor.save();
    editor.remove();
    hw_tinyMCE_init(id);

});
///
jQuery('body').on('init_3', '.wp-editor-wrap', function () {

    var id = jQuery(this).find('textarea').attr('id');
    hw_tinyMCE_init(id);

});