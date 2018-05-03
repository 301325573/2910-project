<?php
require_once('view/top.php');
require_once('lib/connect.php');
require_once('config/config.php');
$conn = db_init($config["host"], $config["dbuser"], $config["dbpw"], $config["dbname"]);
 ?>

<div class="banner">
  <p id="textAni1"></p>
  <p id="textAni2"></p>
  <div id="searchcontainer">
  <form action="index.php" method="GET" name="searching">
    <input id="searchbox" type="text" name="search" placeholder="Search..." >
    <button id="searchbtn" type="submit">Search</button>
  </form>
  </div>
</div>

<?php
  //retriving list
  $count = 3;

  if(count($_GET) == 0) {
  $sql = "SELECT * FROM list ORDER BY id DESC LIMIT {$count}";
    $sql_last = "SELECT id FROM list LIMIT 1";
  } else {
    $q = $_GET['search'];
    $sql = "SELECT * FROM list WHERE title LIKE '%$q%' OR description LIKE '%$q%' ORDER BY id DESC LIMIT {$count}";
    $sql_last = "SELECT id FROM list WHERE title LIKE '%$q%' OR description LIKE '%$q%' LIMIT 1";
  }

  echo "<div id=\"content\">";

  // returns the last item's id
  $result_last = mysqli_query($conn, $sql_last);
  $last_array = mysqli_fetch_array($result_last);
  $last = is_null($last_array[0]) ? 0 : $last_array[0];

  $result = mysqli_query($conn, $sql);

  $lastid = 0;
  if ($result != null){

    while($row = mysqli_fetch_array($result)) {
    //prevent cross scripting attack
    $escaped = array(
        'title' => htmlspecialchars($row['title']),
        'image' => htmlspecialchars($row['image']),
        'status' => htmlspecialchars($row['status']),
    );

    // substring y-m-d
    $created = substr($row['created'], 0, 10);

    // listing each poast
    echo "<a href=\"detail.php?id={$row['id']}\"><div class='list_item'><p class='list_title'>{$escaped['title']}</p>";
    echo "<img src=\"{$escaped['image']}\" class='uploadedImg'>
    <p>Status:&nbsp; {$escaped['status']}<br>Posted: &nbsp; {$created}</p>
    </div></a>";

    $lastid = $row['id'];
    }
  }
  echo "</div>";
?>
<br>
<div id="loaded"></div>
<button id="loadButton">More results &nbsp; <img src="img/arrow-down.png"></button>

<button id="plusButton"><img src="img/plus1.png" alt="post">
</button>

<script>

  $("#plusButton").click(function(event){
    location.href = "post.php";
  });

  /* loading more feature */
  var lastId = <?=$lastid?>;
  var result = <?=$last?>;

  (function(){
      if(lastId == result){
            $("#loadButton").css("display", "none");
          }
    })();

  var search = "<?php
      if(isset($_GET['search'])){
        echo $_GET['search'];
      } else {
      }
      ?>";

    $("#loadButton").click(function(event){
      $.ajax({
        url: "load_process.php",
        type: "POST",
        data: {id: lastId, q: search},
        success: function(data){
          $("#loaded").append(data);
          lastId = newid;
          if(lastId == result){
            $("#loadButton").css("display", "none");
          }
        }

      });
    });

    if (!$('#content').is(':parent')){
      $("#content").html("Oops, there is no food item related to your search!");
      alert("Oops, there is no food item related to your search! \n\nYou will be redirected to home page.");
      location.href = "index.php"
    }
</script>

<script src="js/typewriter.js"></script>
<?php
require_once('view/footer.php');
 ?>
