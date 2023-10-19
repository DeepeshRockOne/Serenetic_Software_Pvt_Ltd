<?php

class Validation {

  private $errors;

  public function __construct() {
    $this->errors = array();
  }

  public function string($options, $messages) {
    if (trim($options['value']) == "") {
      $this->errors[$options['field']] = $messages['required'];
    }
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function string2($options, $messages) {
    if (trim($options['value']) == "") {
      $this->errors[$options['field']] = $messages['required'];
    }
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function email($options, $messages) {
    $options['pattern'] = '/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i';  
    $this->regex($options, $messages);

    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function validDate($options, $messages) {
    $options['pattern'] = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/';
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function creditCard($options, $messages) {
    $options['pattern'] = '/^(?:4[0-9]{12}(?:[0-9]{3})?|
                              5[1-5][0-9]{14}|
                              6011[0-9]{12}|
                              3(?:0[0-5]|[68][0-9])[0-9]{11}|
                              3[47][0-9]{13})$/';
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function phone($options, $messages) {
    $options['pattern'] = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}|[0-9]{10}|[(][0-9]{3}[)][0-9]{3}-[0-9]{4}$/';
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function phoneDigit($options, $messages) {
    $options['pattern'] = '/^[0-9]{10}+$/';
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function digit($options, $messages) {
    $options['pattern'] = '/^[0-9]+$/';
    if(isset($options['min'])){
      $messages['min'] = "Minimum " . $options['min'] . " digit(s) required";
    }
    if(isset($options['max'])){
      $messages['max'] = "Maximum " . $options['max'] . " digit(s) allow";
    }
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function amount($options, $messages){
    $options['pattern'] = '/^\d+(\.\d{0,2})?$/';    
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function url($options, $messages) {
    $options['pattern'] = "#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie";
    $this->regex($options, $messages);
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  public function image($options, $message) {
    $mime_types = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif');
    if (isset($options['required']) && $options['required'] && trim($options['value']['name']) == "") {
      $this->errors[$options['field']] = $message['required'];
    } elseif (trim($options['value']['name']) != "") {
      if (!isset($options['size'])) {
        $size = '1048576';
      } else {
        $size = $options['size'];
      }

      if (!in_array($options['value']['type'], $mime_types)) {
        $this->errors[$options['field']] = "Only .jpg, .png, .gif files are allowed";
      } elseif ($size < $options['value']['size']) {
        if (!isset($messages['size'])) {
          $this->errors[$options['field']] = "Upload upto 1Mb..";
        } else {
          $this->errors[$options['field']] = $messages['size'];
        }
      }
    }
  }

  public function regex($options, $messages) {
    if ($options['required']) {
      if (trim($options['value']) == "") {
        $this->errors[$options['field']] = $messages['required'];
      } elseif (!preg_match($options['pattern'], trim($options['value']))) {
        $this->errors[$options['field']] = $messages['invalid'];
      }
    } elseif (!preg_match($options['pattern'], $options['value'])) {
      $this->errors[$options['field']] = $messages['invalid'];
    }
    $this->min($options, $messages);
    $this->max($options, $messages);
  }

  protected function min($options, $messages) {
    if (!isset($this->errors[$options['field']])) {
      $length = strlen($options['value']);
      if (isset($options['min'])) {
        if ($length < $options['min']) {
          if (!isset($messages['min'])) {
            $this->errors[$options['field']] = "Minimum " . $options['min'] . " chracter(s) required";
          } else {
            $this->errors[$options['field']] = $messages['min'];
          }
        }
      }
    }
  }

  protected function max($options, $messages) {
    if (!isset($this->errors[$options['field']])) {
      $length = strlen($options['value']);
      if (isset($options['max'])) {
        if ($length > $options['max']) {
          if (!isset($messages['max'])) {
            $this->errors[$options['field']] = "Maximum " . $options['max'] . " chracter(s) allow";
          } else {
            $this->errors[$options['field']] = $messages['max'];
          }
        }
      }
    }
  }

  public function isValid() {
    if (count($this->errors)) {
      return false;
    }
    return true;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function setError($field, $message) {
    $this->errors[$field] = $message;
  }

  public function getError($field) {
    return isset($this->errors[$field])?$this->errors[$field]:false;
  }

}

?>