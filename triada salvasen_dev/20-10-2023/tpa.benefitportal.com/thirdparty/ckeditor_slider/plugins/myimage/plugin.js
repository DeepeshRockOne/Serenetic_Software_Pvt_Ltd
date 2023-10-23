CKEDITOR.plugins.add( 'myimage', {
    icons: 'myimage',
    init: function( editor ) {
        //editor.addCommand( 'evpvideo', new CKEDITOR.dialogCommand( 'evpvideoDialog' ) );
        editor.ui.addButton( 'MyImage', {
            label: 'Insert Image',
            command: 'myimage',
            toolbar: 'insert'
        });
        
        //CKEDITOR.dialog.add( 'evpvideoDialog', this.path + 'dialogs/abbr.js' );
        var cmd = editor.addCommand('myimage', { exec: showMyDialog });
    }   
});
function showMyDialog(editor){
  //console.log(editor);
  //console.log(editor.config.filebrowserWindowFeatures || editor.config.fileBrowserWindowFeatures);
  editor.popup( editor.config.filebrowserImageBrowseLinkUrl, '80%','80%', editor.config.filebrowserWindowFeatures || editor.config.fileBrowserWindowFeatures );
}