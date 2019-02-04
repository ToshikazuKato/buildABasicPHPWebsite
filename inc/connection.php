<?php

//connecting to database
try{
  $db = new PDO("sqlite:".__DIR__."/database.db");
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  echo "Unable to connect";
  echo $e->getMessage();
  exit;
}

// //run select query to retrieve title and category
// try {
//   $result = $db->query("SELECT title, category, img FROM Media");
// } catch (Exception $e) {
//   echo "Unable to retrive results";
//   exit;
// }
//
// $catalog = $result->fetchAll(PDO::FETCH_ASSOC);
