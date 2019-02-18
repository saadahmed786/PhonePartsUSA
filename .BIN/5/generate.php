<?php
function generate_rand_str($length) {
      $char = "123456789"; //string of 61 characters
      $rand_str = "";
      for($i=0; $i<$length; $i++) {
         $rand_num = mt_rand(0,8); //mt_rand(); to generate rand no. from 0-61
         $rand_str .=substr($char, $rand_num, 1);
      }
      return $rand_str;
}
?>
