<?php

function get_item_html($id, $item){
  $output =  "<li><a href='#'><img src='"
       . $item["img"] . "'alt='"
       . $item["title"] . "' />"
       . "<p>View Details</p>"
       . "</a></li>";
  return $output;
}


function array_category($catalog, $category){

  // if($category == null){
  //   return array_keys($catalog);
  // }

  $output = array();

  foreach($catalog as $id => $item){
    // var_dump($item);
    if($category == null OR strtolower($category) == strtolower($item["category"])){
      $sort = $item["title"];
      $sort = ltrim($sort, "The ");
      $sort = ltrim($sort, "A ");
      $sort = ltrim($sort, "An ");
      $output[$id] = $sort;
    }
  }
  asort($output);
  return array_keys($output);
}

 ?>
