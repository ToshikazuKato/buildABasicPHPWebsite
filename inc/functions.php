<?php

function get_catalog_count($category = null, $search = null){
  $category = strtolower($category);
  include("connection.php");

  try {
    $sql = "SELECT COUNT(media_id) FROM Media";
    if (!empty($search)) {
      $results = $db->prepare(
        $sql
        . " WHERE title LIKE ?"
      );
      $results->bindValue(1,'%'.$search.'%',PDO::PARAM_STR);
    }else if (!empty($category)) {
      $results = $db->prepare(
        $sql
        . " WHERE LOWER(category) = ? "
      );
      $results -> bindParam(1, $category, PDO::PARAM_STR);
    }else{
        $results = $db->prepare($sql);
    }
    $results->execute();
  } catch (Exception $e) {
    echo "bad query";
  }

  $count = $results->fetchColumn(0);
  return $count;

}

function full_catalog_array($limit = null, $offset = 0){
  include("connection.php");
  //run select query to retrieve title and category
  try {
    $sql = " SELECT media_id, title, category, img
              FROM Media
              ORDER BY
                REPLACE(
                  REPLACE(
                    REPLACE(title,'The ',''),
                    'An ',
                    ''
                  ),
                'A ',
                ''
                )";
    if (is_integer($limit)) {
      $results = $db-> prepare($sql . "LIMIT ? OFFSET ?");
      $results->bindParam(1,$limit,PDO::PARAM_INT);
      $results->bindParam(2,$offset,PDO::PARAM_INT);
    }else{
      $results = $db-> prepare($sql);
    }
    $results->execute();

  } catch (Exception $e) {
    echo "Unable to retrive results";
    exit;
  }

  $catalog = $results->fetchAll(); //PDO::FETCH_ASSOC
  return $catalog;
}

function category_catalog_array($category, $limit = null, $offset = 0){
  include("connection.php");
  $category = strtolower($category);
  //run select query to retrieve title and category
  try {
    $sql = " SELECT media_id, title, category, img
             FROM Media
             WHERE LOWER(category) = ?
             ORDER BY
              REPLACE(
                REPLACE(
                  REPLACE(title,'The ',''),
                  'An ',
                  ''
                ),
                'A ',
                ''
              ) ";
    if (is_integer($limit)) {
      $results = $db-> prepare($sql . "LIMIT ? OFFSET ?");
      $results->bindParam(1,$category, PDO::PARAM_STR);
      $results->bindParam(2,$limit,PDO::PARAM_INT);
      $results->bindParam(3,$offset ,PDO::PARAM_INT);
    }else{
      $results = $db->prepare($sql);
      $results->bindParam(1,$category, PDO::PARAM_STR);
    }

    $results->execute();

  } catch (Exception $e) {
    echo "Unable to retrive results";
    exit;
  }

  $catalog = $results->fetchAll(); //PDO::FETCH_ASSOC
  return $catalog;
}

function random_catalog_array(){
  include("connection.php");
  //run select query to retrieve title and category
  try {
    $results = $db->query(
      " SELECT media_id, title, category, img
      FROM Media
      ORDER BY RANDOM()
      LIMIT 4 "
    );
  } catch (Exception $e) {
    echo "Unable to retrive results";
    exit;
  }

  $catalog = $results->fetchAll(); //PDO::FETCH_ASSOC
  return $catalog;
}

function single_item_array($id){

 //wrong query ?
 // SELECT media_id, title, category, img, format, year, genre, publisher, isbn
 // FROM Media
 // JOIN Genres ON Media.genre_id = Genres.genre_id
 // LEFT OUTER JOIN Books ON Media.media_id = Books.media_id
 // WHERE Media.media_id = $id

  include("connection.php");
  try {
    $results = $db->prepare(
      " SELECT Media.media_id, title, category, img, format, year, genre, publisher, isbn
        FROM Media
        JOIN Genres ON Media.genre_id = Genres.genre_id
        LEFT OUTER JOIN Books
        ON Media.media_id = Books.media_id
        WHERE Media.media_id = ? "
    );
    $results->bindParam(1, $id, PDO::PARAM_INT);
    $results->execute();
  } catch (Exception $e) {
    echo "Unable to retrive results";
    exit;
  }

  $item = $results->fetch(); //PDO::FETCH_ASSOC
  if (empty($item)) {
    return $item; //return false;
  }

  try {
    $results = $db->prepare(
      " SELECT fullname, role
        FROM Media_People
        JOIN People ON Media_People.people_id = People.people_id
        WHERE Media_People.media_id = ? "
    );
    $results->bindParam(1, $id, PDO::PARAM_INT);
    $results->execute();
  } catch (Exception $e) {
    echo "Unable to retrive results";
    exit;
  }
  while($row = $results->fetch(PDO::FETCH_ASSOC)){
    $item[$row["role"]][] = $row["fullname"];
  }
  return $item;
}

function get_item_html($item){
  $output =  "<li><a href='details.php?id="
       . $item["media_id"] . "'><img src='"
       . $item["img"] . "'alt='"
       . $item["title"] . "' />"
       . "<p>View Details</p>"
       . "</a></li>";
  return $output;

}

function search_catalog_array($search, $limit = null, $offset = 0) {
    include("connection.php");

    try {
       $sql = "SELECT media_id, title, category,img
         FROM Media
         WHERE title LIKE ?
         ORDER BY
         REPLACE(
           REPLACE(
              REPLACE(title,'The ',''),
              'An ',
              ''
           ),
           'A ',
           ''
         )";
       if (is_integer($limit)) {
          $results = $db->prepare($sql . " LIMIT ? OFFSET ?");
          $results->bindValue(1,"%".$search."%",PDO::PARAM_STR);
          $results->bindParam(2,$limit,PDO::PARAM_INT);
          $results->bindParam(3,$offset,PDO::PARAM_INT);
       } else {
         $results = $db->prepare($sql);
         $results->bindValue(1,"%".$search."%",PDO::PARAM_STR);
       }
       $results->execute();
    } catch (Exception $e) {
       echo "Unable to retrieved results";
       exit;
    }

    $catalog = $results->fetchAll();
    return $catalog;
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

function genre_array($category = null) {
  $category = strtolower($category);
  include("connection.php");

  try {
    $sql = "SELECT genre, category"
      . " FROM Genres "
      . " JOIN Genre_Categories "
      . " ON Genres.genre_id = Genre_Categories.genre_id ";
    if (!empty($category)) {
      $results = $db->prepare($sql
          . " WHERE LOWER(category) = ?"
          . " ORDER BY genre");
      $results->bindParam(1,$category,PDO::PARAM_STR);
    } else {
      $results = $db->prepare($sql . " ORDER BY genre");
    }
    $results->execute();
  } catch (Exception $e) {
    echo "bad query";
  }
  $genres = array();
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
      $genres[$row["category"]][] = $row["genre"];
  }
  return $genres;
}
