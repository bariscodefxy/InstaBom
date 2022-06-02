<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $helper = new \App\Libraries\Helpers();
?>
<style>
body{
	background-color:#fafafa;
}
::selection{
  background: rgba(249, 230, 5, 0.5);
  color: #496174;
}

::-moz-selection {
  background: rgba(249, 230, 5, 0.5);
  color: #496174;
}

a:hover,a:focus,.a:active {
  text-decoration: none;
  outline: none !important;
  color: #496174;
}

.blog-post {
  margin-top: 30px;
  font-family: 'Titillium Web', Roboto;
  color: #496174;
  background: #fff;
  font-size: 15px;
  font-weight: 500;
  line-height: 22px;
  overflow-x:hidden;
}

.blog-post h1 {
  padding: 0;
  margin-top: 10px;
  margin-bottom: 10px;
  font-weight: 700;
  font-size: 46px;
  letter-spacing: -1px;
}

.blog-post h3 {
	color: #337ab7;
	font-size: 24px;
}

.img-profile{display: inline-block !important;background-color: #fff;border-radius: 6px;margin-top: -50%;padding: 1px;vertical-align: bottom;border: 2px solid #fff;-moz-box-sizing: border-box;box-sizing: border-box;color: #fff;z-index: 2;}
.img-box{box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23);border-radius: 2px;border: 0;}

@media (max-width: 767px) {

  .blog-post {
    max-width: 95%;
    margin-top: 10px;
  }

  .blog-post h1 {
    font-size: 30px;
  }
}


</style>
<div class="container blog-post">
	<div class="row">
		<div class="col-lg-12">
			<h1><?php echo $model["baslik"]; ?></h1>
			<p class="lead"><i class="fa fa-date"></i> <?php echo $model["registerDate"]; ?></p>
			<hr>
			<img src="<?php echo $model["anaResim"]; ?>" style="width:728px;height:270px;" class="img-responsive img-box img-thumbnail" alt="<?php echo $model["baslik"]; ?>" title="<?php echo $model["baslik"]; ?>"/>
			<hr>
			<?php echo $model["icerik"]; ?>
		</div>
	</div>
	<hr>
	<div style="clear: both; margin-bottom: 30px"></div>
    <h2 style="letter-spacing: -1px; font-weight: 600; font-size: 28px; color: #496174">İlginizi Çekebilecek Diğer Yazılar</h2>
	<div style="clear: both; margin-bottom: 30px"></div>
    <div class="row">
        <?php foreach($model["otherBlogs"] AS $other) { ?>
            <div class="col-md-4">
                <a href="/blog/<?php echo $other["seoLink"]; ?>"><img src="<?php echo $other["anaResim"]; ?>" style="width:400px;height:131px;" alt="<?php echo $other["baslik"]; ?>" title="<?php echo $other["baslik"]; ?>" class="img-responsive img-box img-thumbnail"/></a>
                <a href="/blog/<?php echo $other["seoLink"]; ?>">
                    <h3><?php echo $other["baslik"]; ?></h3>
                </a>
                <p><?php echo $helper->blogExcerpt($other["icerik"], 200); ?></p>
            </div>
        <?php } ?>

    </div>
</div>