// Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved.
// For licensing, see LICENSE.md

CKEDITOR.plugins.add( 'imageuploader', {
    init: function( editor ) {
        editor.config.filebrowserBrowseUrl = site_url  + 'assets/js/ckfinder/ckfinder.html',
        editor.config.filebrowserUploadUrl = site_url  + 'assets/js/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        editor.config.filebrowserWindowWidth =  '1000',
        editor.config.filebrowserWindowHeight = '700'
    }
});
