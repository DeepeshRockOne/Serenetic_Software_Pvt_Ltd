<?php

/**
 * 
 * @param type $id
 * @return type
 * @author Ashwin pitroda
 * 
 */
function generate_trigger_template($id,$defaContent="") {
  global $pdo, $HOST, $TRIGGER_IMAGE_WEB,$SITE_SETTINGS;

  $template_query = "SELECT t.*,ta.trg_address_id,ti.trg_image_id,tra.address,tri.src,tri.title as img_title,tf.content as footer_content,tri.company_id as img_com_logo
                 FROM trigger_template t
                 LEFT JOIN trigger_template_images ti ON(t.id=ti.template_id)
                 LEFT JOIN trigger_footer tf ON(tf.id=t.trg_footer_id)
                 LEFT JOIN trigger_images tri ON(tri.id=ti.trg_image_id)
                 LEFT JOIN trigger_template_address ta ON(t.id=ta.template_id)
                 LEFT JOIN trigger_address tra ON(tra.id=ta.trg_address_id)
                 WHERE t.id=:id ";
  $where = array(
      ':id' => $id
  );
  $template_val = $pdo->select($template_query, $where);
  // pre_print($template_val);
  $templateArray = array();
  $template_data_id = 0;
  $template_image_id = 0;
  $template_image_src = '';
  $template_add_src = '';
  $template_add_id = 0;
  $isDefaultTemplate=true;
  if (count($template_val) > 0) {
    $i = 0;
    foreach ($template_val as $value) {
      if(!empty($value['type'])=='custom'){
        $templateArray['content'] = $defaContent==""?$value['content']:$defaContent;
        $isDefaultTemplate=false;
      }else{
        if ($value['id'] != $template_data_id) {
          $templateArray['id'] = $value['id'];
          $templateArray['footer_id'] = $value['trg_footer_id'];
          $templateArray['footer_content'] = $value['footer_content'];
          $templateArray['content'] = $defaContent==""?$value['content']:$defaContent;
          $templateArray['image'] = array();
          $templateArray['address'] = array();
        }
        if ($value['trg_image_id'] != $template_image_id) {
          $templateArray['image'][$i]['id'] = $value['trg_image_id'];
        }
        if ($value['src'] != $template_image_src) {
          $templateArray['image'][$i]['src'] = $value['src'];
          $templateArray['image'][$i]['img_title'] = $value['img_title'];
        }
        if ($value['trg_address_id'] != $template_add_id) {
          $templateArray['address'][$i]['id'] = $value['trg_address_id'];
        }
        if ($value['address'] != $template_add_src) {
          $templateArray['address'][$i]['add'] = $value['address'];
        }
        if($value['img_com_logo'] != '')
        {
            $templateArray['image'][$i]['company'] = $value['img_com_logo'];
        }
        $i++;
        $template_data_id = $value['id'];
        $template_image_id = $value['trg_image_id'];
        $template_add_id = $value['trg_address_id'];
        $template_image_src = $value['src'];
        $template_add_id = $value['address'];
      }
    }
  }
  
  $template_concept = issetor($templateArray['content']);  
  $template = $template_concept;

  if($isDefaultTemplate){
    foreach ($templateArray as $placholder => $value) {

      if ($placholder == 'footer_id') {
        $footer_content = html_entity_decode($templateArray['footer_content']);
        $template = str_replace("[[trg_footer_" . $value . "]]", $footer_content, $template);
      }
      if ($placholder == 'image') {
        // echo "string";
        foreach ($value as $val) {
          $TRIGGER_IMAGE_DIR = $SITE_SETTINGS[$val['company']]['TRIGGER_IMAGE']['upload'];
          $TRIGGER_IMAGE_WEB = $SITE_SETTINGS[$val['company']]['TRIGGER_IMAGE']['download'];  
          $path =  $TRIGGER_IMAGE_WEB ."/". $val['src'];        
          $image_content = "<img src='" . $path . "' alt='" . stripslashes($val['img_title']) . "'>";
          $template = str_replace("[[trg_img_" . $val['id'] . "]]", $image_content, $template);
        }
      }
      if ($placholder == 'address') {
        foreach ($value as $val) {
          $address_content = $val['add'];
          $template = str_replace("[[trg_address_" . $val['id'] . "]]", $address_content, $template);
        }
      }
    }
  }

//  echo "<pre>";
//  echo $template;exit;

  return $template;

}

?>