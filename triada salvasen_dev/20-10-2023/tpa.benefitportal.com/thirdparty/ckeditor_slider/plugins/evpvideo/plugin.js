CKEDITOR.plugins.add( 'evpvideo', {
    icons: 'evpvideo',
    init: function( editor ) {
        editor.addCommand( 'evpvideo', new CKEDITOR.dialogCommand( 'evpvideoDialog' ) );
        editor.ui.addButton( 'Evpvideo', {
            label: 'Insert Video',
            command: 'evpvideo',
            toolbar: 'insert'
        });
        
        CKEDITOR.dialog.add( 'evpvideoDialog', this.path + 'dialogs/abbr.js' );
        
    }   
});
