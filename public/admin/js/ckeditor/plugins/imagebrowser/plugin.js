CKEDITOR.plugins.add( 'imagebrowser', {
    init: function( editor ) {
        editor.config.filebrowserBrowseUrl = '/admin/js/ckeditor/plugins/imagebrowser/imagebrowser.php?imgroot=/admin/uploads';
    }
});
