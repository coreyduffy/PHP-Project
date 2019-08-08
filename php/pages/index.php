<?php
require_once(__DIR__.'/../whatShouldIWatchFunctions.php');
if (isset($_POST['submit']) )
{
   $keyword = getKeyWordIfPresent();

   if (empty($keyword)) {
       $response = array(
             "type" => "error",
             "message" => "Please enter a keyword."
       );
   }
}
getHeader("Search For Movie");?>
    <div class="container">
    <h1 class="text-center pt-5">Search for your favourite movies</h1>
  	<div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <form class="card card-sm" method="post" action="">
                <div class="card-body row no-gutters align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-search h4 text-body"></i>
                    </div>
                    <!--end of col-->
                    <div class="col">
                        <input class="form-control form-control-lg form-control-borderless" type="search" id="keyword" name="keyword" placeholder="Search titles or keywords">
                    </div>
                    <!--end of col-->
                    <div class="col-auto">
                        <button class="btn btn-lg btn-primary" type="submit" name="submit">Search</button>
                    </div>
                    <!--end of col-->
                </div>
            </form>
            <?php if(!empty($response)) {
              getErrorMessage($response['message']);
            } ?>
        </div>
        <!--end of col-->
    </div>

   <?php
    if (isset($_POST['submit']) )
    {
      if (!empty($keyword))
      {
        $movieChunks = searchForMovies($keyword);
        getCardRows($movieChunks);
      }
    }?>
    </div>
    <?php
    getFooter();
    ?>
