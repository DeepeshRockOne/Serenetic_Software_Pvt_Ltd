$.extend({
getValues: function(url) {
    var result = null;
    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        async: false,
        success: function(data) {
            result = data;
        }
    });
   return result;
},
getSize: function(url) {
    var result = null;
    $.ajax({
        url: url,
        type: 'get',
        dataType: 'json',
        async: false,
        success: function(data) {
            result = data;
        }
    });
   return result;
}
});

var results = $.getValues('ajax_get_evp_video.php');
var ImgWidth;
var ImgHeight;
/*var itemsArr;
    $.ajax({
        url:'ajax_get_evp_video.php',
        success:function(res){
            itemsArr=res;
        }
    });*/
    //console.log(results);
CKEDITOR.dialog.add( 'evpvideoDialog', function( editor ) {
    
    return {
        title: 'Video Properties',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'select',
                        id: 'select_video',
                        label: 'Select Training Video',
                        items: results,
                        validate: CKEDITOR.dialog.validate.notEmpty( "Select Any Video." ),
                        onChange: function( api ) {
                            // this = CKEDITOR.ui.dialog.select
                            var sdata=$.getSize('ajax_get_evp_video_size.php?id='+ this.getValue());
                             
                            ImgWidth=sdata.width;
                            ImgHeight=sdata.height;
                        }
                    }
                ]
            }
            
        ],
        onOk: function() {
            //console.log(ImgWidth);
            //console.log(ImgHeight);
            
            var dialog = this;
            var HOST = window.location.protocol + "//" + window.location.hostname+'/impactspending' ;
            //var div_id='evp-imp-wrap';
            var video_id = dialog.getValueOf( 'tab-basic', 'select_video' );
            //console.log(div_id);
            var myvideo= editor.document.createElement( 'img',{attributes:{id:video_id,src:HOST+'/images/video.jpg',class:'evp_img', width:ImgWidth, height:ImgHeight}} );
                   
            editor.insertElement( myvideo);
            /*var mydiv= editor.document.createElement( 'div',{attributes:{id:div_id,class:'evp-video-wrap'}} );
                   
            editor.insertElement( mydiv);
            //editor.insertHtml( aaa);
            var mysrc="http://192.168.1.30/impactspending/evp/framework.php?div_id=evp-imp&id="+video_id+"&v=1420786144&profile=default";
            //var myscript=editor.document.createElement( 'script',{attributes:{type:'text/javascript',src:mysrc}} );
            
            //editor.insertElement( myscript);
             
            var div = CKEDITOR.dom.element.createFromHtml( '<div id="'+div_id+' class="evp-video-wrap"></div>' );
            //editor.insertElement( div);    
            var script = CKEDITOR.dom.element.createFromHtml( '<script type="text/javascript" src="'+mysrc+'"></script>' );
            editor.insertElement( script);
            var script = CKEDITOR.dom.element.createFromHtml( '<script type="text/javascript"> _evpInit(\''+video_id+'[evp-imp]\'); </script>' );
            editor.insertElement( script);*/
        
        //editor.insertHtml('<div id="'+div_id+' class="evp-video-wrap"></div>');
        }
    };
});