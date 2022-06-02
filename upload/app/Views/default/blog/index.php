<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $helper = new \App\Libraries\Helpers();
?>
<style type="text/css">::selection{
  background: rgba(249, 230, 5, 0.5);
  color: #496174;
}

body{
	background-color:#fafafa;
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

.blog-list {
  margin-top: 50px;
  font-family: 'Titillium Web', Roboto;
}

.blog-list h1 {
  padding: 0;
  margin-top: 0;
  margin-bottom: 10px;
  font-size: 26px;
  font-weight: 700;
  letter-spacing: -1px;
}

.blog-list h2 {
  padding: 0;
  margin-top: 0;
  margin-bottom: 10px;
  font-size: 26px;
  color: #2c3e50;
  font-weight: 700;
  letter-spacing: -1px;
}

.img-profile{display: inline-block !important;background-color: #fff;border-radius: 6px;margin-top: -50%;padding: 1px;vertical-align: bottom;border: 2px solid #fff;-moz-box-sizing: border-box;box-sizing: border-box;color: #fff;z-index: 2;}
.img-box{box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23);border-radius: 2px;border: 0;}

@media (max-width: 767px) {
  .blog-list {
    max-width: 90%;
    margin-top: 30px;
  }

  .blog-list h1 {
    margin-top: 15px;
  }
  
  .blog-list h2 {
    margin-top: 15px;
  }
}
</style>

<div class="container blog-list">
    <?php echo isset($model["pageContent"]) ? $model["pageContent"] : ''; ?>
	<h1 style="font-size: 35px">Instagram Blog Yazıları</h1>
	<div style="clear: both; margin-bottom: 30px"></div>
<div class="cl10"></div>
    <?php foreach($model["blogList"] AS $blog) { ?> 
		  <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-3">
					<a href="/blog/<?php echo $blog["seoLink"]; ?>" title="<?php echo $blog["baslik"]; ?>">
						<img src="<?php echo $blog["anaResim"]; ?>" style="width:262.5px;height:120px;" class="img-responsive img-box img-thumbnail"/>
					</a>
                </div>
                <div class="col-xs-12 col-sm-9 col-md-9">
                     <a href="/blog/<?php echo $blog["seoLink"]; ?>" title="<?php echo $blog["baslik"]; ?>">
						 <h2><?php echo $blog["baslik"]; ?></h2>
					</a>
                    <p style="font-size: 15px;margin:0px;"><?php echo $helper->blogExcerpt($blog["icerik"], 400); ?></p>
					<p style="font-size: 15px;margin:0px;"><?php echo date("d-m-Y H:i", strtotime($blog["registerDate"])); ?> <a href="/blog/<?php echo $blog["seoLink"]; ?>" title="<?php echo $blog["baslik"]; ?>">Devamını Oku</a></p>
                </div>
            </div>
			<hr>
			
    <?php } ?>
    <ul class="pager">
        <li class="previous<?php echo empty($this->get("previousPage")) ? ' disabled':''; ?>"><a href="<?php echo empty($this->get("previousPage")) ? 'javascript:;':'?page='.$this->get("previousPage"); ?>"><i class="fa fa-chevron-left"></i> Önceki</a></li>
        <li class="next<?php echo empty($this->get("nextPage")) ? ' disabled':''; ?>"><a href="<?php echo empty($this->get("nextPage")) ? 'javascript:;':'?page='.$this->get("nextPage"); ?>">Sonraki <i class="fa fa-chevron-right"></i></a></li>
    </ul>
</div>