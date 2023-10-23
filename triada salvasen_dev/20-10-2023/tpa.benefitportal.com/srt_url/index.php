<?php
include (__DIR__)."/includes/connect.php";
if(isset($_GET['code'])){
  $selSql="SELECT type,dest_url FROM short_url WHERE short_code=:code ORDER BY id DESC LIMIT 1";
  $selRow=$pdo->selectOne($selSql,array(":code"=>$_GET['code']));
  if($selRow){
    if($selRow['type']=='Redirect' && $selRow['dest_url']!=""){
      redirect($selRow['dest_url']);
    }
  }
}
?>

<html>
	<head>
  	<title></title>
    <style type="text/css">
			body{display: table;height: 100%;margin: 0;width: 100%;}
    	.content-block{display: table-cell;margin: 0 auto;max-width: 650px;position: relative;text-align: center;vertical-align: middle;}
			h1{color: #303030;display: inline-block;font-size:70px;line-height:70px;margin: 0;text-shadow: 1px 0 3px #666666;}
			p{color: #333333;font-size: 24px; font-family:Verdana; margin-top:10px;}
    </style>
  </head>
  <body>
  	<div class="content-block">
    <div class="text-center"><svg version="1.1" id="svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="146.096 116.166 203.99 203" enable-background="new 146.096 116.166 203.99 203" xml:space="preserve"><circle fill="#f05c5c" cx="247.812" cy="217.333" r="101.003"/>
<g><polygon fill="#FFFFFF" points="254.065,230.051 240.712,230.051 237.532,158.835 257.244,158.835  "/>
	<circle fill="#FFFFFF" cx="247.812" cy="260.572" r="15.26"/>
</g>
</svg>
</div>
  	<h1>Sorry!</h1>
		<p>The URL you have requested is not found</p>
    </div>
     
  </body>
</html>