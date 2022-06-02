/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/*
 Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config) {
    config.language             = 'tr';
    config.uiColor              = "#ACACAC";
    config.removePlugins        = 'scayt';
    config.toolbar              = 'Full';
    config.skin                 = 'moono';
    config.htmlEncodeOutput     = false;
    config.entities             = false;
    config.entities_latin       = false;
    config.allowedContent       = true;
    config.filebrowserBrowseUrl = '/assets/ckeditor/Filemanager/index.html';
    config.contentsCss          = [CKEDITOR.basePath + 'contents.css'];
    config.bodyClass            = 'ckeditorpadding';
    config.fillEmptyBlocks      = false;

    config.toolbar_Full =
        [
            ['Source', '-', 'Save', 'NewPage', 'Preview', '-', 'Templates'],
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Print'],
            ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat'],
            '/',
            ['Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript'],
            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
            ['Link', 'Unlink', 'Anchor'],
            ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
            '/',
            //    ['Styles','Format','Font','FontSize'],
            ['Format', 'Font', 'FontSize'],
            ['TextColor', 'BGColor'],
            ['Maximize', 'ShowBlocks']
        ];

    config.toolbar_Basic =
        [
            ['Cut', 'Copy', 'Paste', '-', 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink'],
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Outdent', 'Indent'],
            '/',
            ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
            '/',
            ['Format', 'Font', 'FontSize'],
            ['TextColor', 'BGColor'],
            ['Maximize', 'ShowBlocks', '-', 'Source']
        ];

};

$.each(CKEDITOR.dtd.$removeEmpty, function(i, value) {
    CKEDITOR.dtd.$removeEmpty[i] = 0;
});