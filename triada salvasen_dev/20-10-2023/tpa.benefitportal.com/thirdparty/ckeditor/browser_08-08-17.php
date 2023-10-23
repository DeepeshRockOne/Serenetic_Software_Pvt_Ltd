<?php include (dirname(__DIR__)).'/../agentra/includes/connect.php';
//header("Content-Type: text/html; charset=utf-8\n");  
//header("Cache-Control: no-cache, must-revalidate\n");  
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//ini_set("display_error", 1);
//error_reporting(E_ALL);
//exit("here");
$REAL_IP_ADDRESS = get_real_ipaddress();
if(!isset($_SESSION['admin']['id'])){
  //redirect("http://cyberxllc.com/momentis/admin/");
  exit("Please Login To Continue...");
}
//delete image
if(isset($_GET['del_id'])){
  $selSql="select * from content_images where admin_id=:admin_id AND id=:id";
  $selParams=array(
      ":id"=>makeSafe($_GET['del_id']),
      ":admin_id"=>$_SESSION['admin']['id']
      );
  $imgs=$pdo->selectOne($selSql,$selParams);
  if (count($imgs) > 0) {
    $delSql = "delete from content_images where admin_id=:admin_id AND id=:id";
    $delParams = array(
        ":id" => makeSafe($_GET['del_id']),
        ":admin_id" => $_SESSION['admin']['id']
    );
    $pdo->delete($delSql, $delParams);
    unlink($imgs['full_path']);
    $msg = "Image Deleted Successfully";
  }
}

// uploading new image
if (isset($_POST['s_submit'])) {
  $image = $_FILES['s_file'];
  if ($image['name'] == "") {
    $error = 'Select File';
  }
  if ($image['name'] != "") {
    if (!in_array($image['type'], array("image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png"))) {
      $error = 'Only .jpg, .png File';
    }
  }
  if ($image['name']) {
    
    $filename = time() . strtolower(preg_replace('/[^A-Za-z0-9\.]/', '', $image['name']));
    $destination = $_SERVER['DOCUMENT_ROOT'] . '/uploads/ckeditor/' . $filename;
    move_uploaded_file($image['tmp_name'], $destination);
    //setNotifySuccess("Profile Photo Uploaded Successfully");
    //redirect("dashboard.php");
    $insParams=array(
        'admin_id'=>$_SESSION['admin']['id'],
        'file_name'=>  makeSafe($filename),
        'full_path'=> makeSafe($destination),
        'location'=> makeSafe('Head Coach Training'),
        'ip_address'=>!empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
        'created_at'=>'msqlfunc_NOW()'
    );
    $pdo->insert("content_images",$insParams);
    $msg="Image Uploaded Successfully";
  }
} 
// e-z params  
$dim = 150;         /* image displays proportionally within this square dimension ) */
$cols = 5;          /* thumbnails per row */
$thumIndicator = '_th'; /* e.g., *image123_th.jpg*) -> if not using thumbNails then use empty string */
?>  
<!DOCTYPE html>  
<html>  
  <head>  
    <title>browse file</title>  
    <meta charset="utf-8">  

    <style>  
      .brwrap{ border: 1px solid #ddd; padding-bottom:15px; }
      h1{ font: normal 22px arial,'lucida sans unicode','lucida grande','Trebuchet MS',verdana,helvetica,helve,sans-serif; color: #666; padding: 0 0 10px; margin: 10px 20px 5px; border-bottom: 1px solid #ddd; }
      table {width:100%;}  
      td {text-align:center; padding:5px;}  
      img {border:5px solid #f7f7f7; padding:0; verticle-align: middle; box-shadow:  0 0 5px rgba(0,0,0,0.5);}  
      img:hover { border-color:#BFFFBF; cursor:pointer; }  
      .submit { background: #fff; border-radius: 5px; border:1px solid #0269a5; color:#0269a5; font-size: 12px; line-height: 17px; margin: 10px 0; padding: 6px 15px; text-transform: uppercase; transition: all 0.5s ease 0s; cursor:pointer; }
      .submit:hover { background: #0269a5; color:#fff; }
      .select_file { background: #ffffff; border-radius: 5px; border:1px solid #ddd; color:#bbb; padding:4.5px 5px; }

      .img_base { position: relative; display: inline-block; *display:inline; zoom:1;}
      .img_base .delte_icon_div { display: block; }
      .delte_icon_div a { color: #ec0000; text-decoration:none; font-size:14px; font-weight:bold;}
      p { font: normal 14px arial,'lucida sans unicode','lucida grande','Trebuchet MS',verdana,helvetica,helve,sans-serif; color: #666; margin:10px 20px; }
      .success { color:#00b930; }
      .error { color:#ec0000; }
    </style>  

  </head>  


  <body>  
    <div class="brwrap">
      <h1>Upload New Image :
      <form enctype="multipart/form-data" method="post">
          <input type="file" class="select_file" name="s_file" id="s_file">
          <input type="submit" class="submit" name="s_submit" value="submit">
        </form></h1>
      <?php if($msg!=""){?>
      <p class="success"><?=$msg?></p>
      <?php }?>
      <?php if($error!=""){?>
      <p class="error"><?=$error?></p>
      <?php }?>
      <h1>Select Image</h1>
      <?php
        $HOST = "http://" . $_SERVER['HTTP_HOST'].'/cyberxgroup';
        $dir = '../../uploads/ckeditor';
        $jsdir = $HOST . '/uploads/ckeditor';
        //$files = scandir($dir);

        $images = array();
        $selFileSql="select * from content_images where admin_id=:admin_id ORDER BY id DESC";
        $params=array(":admin_id"=>$_SESSION['admin']['id']);
        $fileRow=$pdo->select($selFileSql,$params);
        
        if(count($fileRow)>0){
        echo"<table> ";        
        
        foreach ($fileRow as $file) {
          // filter for thumbNail image files (use an empty string for $thumIndicator if not using thumbnails )
          if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $file['file_name']))
            continue;

          if ($file) {
            $thumbSrc = $dir . '/' . $file['file_name'];
            $fileBaseName = str_replace('_th.', '.', $file['file_name']);

            $image_info = getimagesize($thumbSrc);
            $_w = $image_info[0];
            $_h = $image_info[1];

            if ($_w > $_h) {       // $a is the longer side and $b is the shorter side
              $a = $_w;
              $b = $_h;
            } else {
              $a = $_h;
              $b = $_w;
            }

            $pct = $b / $a;     // the shorter sides relationship to the longer side

            if ($a > $dim)
              $a = $dim;      // limit the longer side to the dimension specified

            $b = (int) ($a * $pct);  // calculate the shorter side

            $width = $_w > $_h ? $a : $b;
            $height = $_w > $_h ? $b : $a;

            // produce an image tag
            $str = sprintf('<div class="img_base"><img src="%s" width="%d" height="%d" title="%s" alt=""><div class="delte_icon_div"><a href="javascript:void(0);" onclick="return del_image('.$file['id'].')" class="delete">Delete</a></div></div>', $thumbSrc, $width, $height, $fileBaseName
            );

            // save image tags in an array
            $images[] = str_replace("'", "\\'", $str); // an unescaped apostrophe would break js  
          }
        }

        $numRows = floor(count($images) / $cols);

        if (count($images) % $cols != 0)
          $numRows++;

        // produce the correct number of table rows with empty cells
        for ($i = 0; $i < $numRows; $i++)
          echo "\t<tr>" . implode('', array_fill(0, $cols, '<td></td>')) . "</tr>\n\n";
        
        echo" </table>"; 
        }
        else{
          echo"<p>No Images Uploaded</p>";
        }
        ?>  
    </div>
    <script>
    function del_image(id){
      if(confirm("Are you sure you want to delete this image?")){
        window.location="browser.php?del_id="+id;
      }
      return false;
    }  
    // make a js array from the php array
      images = [
<?php
foreach ($images as $v)
  echo sprintf("\t'%s',\n", $v);
?>];

      tbl = document.getElementsByTagName('table')[0];

      td = tbl.getElementsByTagName('td');

    // fill the empty table cells with the img tags
      for (var i = 0; i < images.length; i++)
        td[i].innerHTML = images[i];


    // event handler to place clicked image into CKeditor
      tbl.onclick =
              function(e) {
                //console.log(e);
                var tgt = e.target || event.srcElement,
                        url;
                
                if (tgt.nodeName != 'IMG')
                  return;

                url = '<?php echo $jsdir; ?>' + '/' + tgt.title;
                //console.log(url);
                this.onclick = null;
                var instance="";
                for (var key in window.opener.CKEDITOR.instances) {
                  instance=key;
                  //console.log(window.opener.CKEDITOR.instances[key]);
                }
                var editor = window.opener.CKEDITOR.instances[instance];
                var myimage= editor.document.createElement( 'img',{attributes:{src:url, width:'80%'}} );
                   
                editor.insertElement( myimage);
                // $_GET['CKEditorFuncNum'] was supplied by CKeditor
                //window.opener.CKEDITOR.tools.callFunction(<?php echo $_GET['CKEditorFuncNum']; ?>, url);
                //console.log(window.opener.CKEDITOR);
                //window.opener.CKEDITOR.instances['content'].insertHtml("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");               
                window.close();
              }
    </script>  
    
  </body>  
</html>  
