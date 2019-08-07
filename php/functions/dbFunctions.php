<?php
require_once(__DIR__.'/../whatShouldIWatchFunctions.php');
function connectToDatabase() {
  $host = 'localhost';
  $username = 'B00652935';
  $password = 'NuQ3Kngv';
  $database = 'b00652935';
  $db = new mysqli($host, $username, $password, $database);
  if (mysqli_connect_errno())
  {
    echo "<div class=\"container\"><div class=\"alert alert-danger\">Could not connect to database</div></div>";
  } else {
    return $db;
  }
}


function register_user($email, $password){
  // connect to db
 $conn = connectToDatabase();

 // check if username is unique
 $result = $conn->query("select * from projectusers where username='".$email."'");
 if (!$result) {
   throw new Exception('Could not execute query');
 }

 if ($result->num_rows>0) {
   throw new Exception('That username is taken - go back and choose another one.');
 }

 // if ok, put in db
 $result = $conn->query("insert into projectusers(username, password, reg_date) values
                        ('".$email."', sha1('".$password."'), now())");
 if (!$result) {
   throw new Exception('Could not register you in database - please try again later.');
 }

 return true;
}


function login($username, $password) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $conn = connectToDatabase();

  // check if username is unique
  $result = $conn->query("select * from projectusers
                         where username='".$username."'
                         and password = sha1('".$password."')");
  if (!$result) {
     throw new Exception('Could not log you in.');
  }

  if ($result->num_rows>0) {
     return true;
  } else {
     throw new Exception('Could not log you in.');
  }
}


function storeMovieAsFavourite($userId, $title, $overview, $posterPath, $genres) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $conn = connectToDatabase();

  $query = 'INSERT INTO projectmovies (title, overview, poster_path) VALUES (?, ?, ?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('sss', $title, $overview, $posterPath);
  $stmt->execute();

  if ($stmt->error) {
    echo "<div class=\"container\"><div class=\"alert alert-danger\">User $title was unable to be stored: $stmt->error</div></div>";
  } else {
    echo "<div class=\"container\"><div class=\"alert alert-success\">User $title successfully stored</div></div>";
  }
  $stmt->free_result();

  $query = 'INSERT INTO projectusermovies (user_id, movie_id) VALUES (?, ?)';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $userId, getMovieId($title, $overview, $posterPath));
  $stmt->execute();
  $stmt->free_result();

  foreach($genres as $genre):
    $api_id = $genre["id"];
    $name = $genre["name"];
    $query = 'INSERT INTO projectgenres (api_id, name) VALUES (?, ?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ds', $api_id, $name);
    $stmt->execute();
    $stmt->free_result();
  endforeach;
}

function displayMyMovies($userId) {
  global $movies;
    $stmt = getAllMoviesFromDatabaseForUser($userId);
    $stmt->bind_result($id, $title, $overview, $posterPath);
    $count = 0;
    while($stmt->fetch()) {
      if ($count == 0) {
        ?><div class="row">
      <?php }
      $count++;
      $movies .= getDeleteCard($id, $title, $overview, $posterPath);
     if ($count == 4) {?>
        </div>;
        <?php $count = 0;
    }
  }
    $stmt->free_result();
    return $movies;
}

function getAllMoviesFromDatabaseForUser($userId) {
  $conn = connectToDatabase();
  $query = 'SELECT `projectmovies`.id, `projectmovies`.title, `projectmovies`.overview, `projectmovies`.poster_path FROM `projectmovies` INNER JOIN projectusermovies ON `projectmovies`.id = `projectusermovies`.movie_id WHERE `projectusermovies`.user_id = ?';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('d', $userId);
  $stmt->execute();
  $stmt->store_result();
  return $stmt;
}

function deleteMovieFromFavourites($id) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $conn = connectToDatabase();

  $query = 'DELETE FROM projectusermovies WHERE movie_id = ?';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('d', $id);
  $stmt->execute();

  if ($stmt->error) {
    return "<div class=\"container\"><div class=\"alert alert-danger\">User $id was unable to be stored: $stmt->error</div></div>";
  } else {
    return "<div class=\"container\"><div class=\"alert alert-success\">User $id successfully stored</div></div>";
  }
  $stmt->free_result();
}

function getUserId($username, $password) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $hashedPassword = sha1($password);
  $conn = connectToDatabase();
  $query = 'SELECT id FROM `projectusers` WHERE username = ? AND password = ?';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $username, $hashedPassword);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($id);
  $stmt->fetch();
  $stmt->close();
  return $id;
}

function getMovieId($title, $overview, $posterPath) {
// check username and password with db
// if yes, return true
// else throw exception

  // connect to db
  $hashedPassword = sha1($password);
  $conn = connectToDatabase();
  $query = 'SELECT id FROM `projectmovies` WHERE title = ? AND overview = ? AND poster_path = ?';
  $stmt = $conn->prepare($query);
  $stmt->bind_param('sss', $title, $overview, $posterPath);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($id);
  $stmt->fetch();
  $stmt->close();
  return $id;
}
?>
