<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $isAjaxLoaded = intval($this->get("ajaxLoaded")) == 1 ? TRUE : FALSE;
    if(!$isAjaxLoaded) { ?>
        <div id="entry-list-container">
        <div id="entry-list-row">
        <?php
    }
    $stories = array();
    if(isset($model["stories"])) {
        $stories = array_merge($stories, $model["stories"]);
    }
    if(isset($model["new_stories"])) {
        $stories = array_merge($stories, $model["new_stories"]);
    }
    if(isset($model["old_stories"])) {
        $stories = array_merge($stories, $model["old_stories"]);
    }

    foreach($stories as $item) {
        $text       = $item["args"]["text"];
        $arrFind    = array();
        $arrReplace = array();

        foreach($item["args"]["links"] as $link) {
            $arrFind[]    = substr($text, $link["start"], intval($link["end"]) - intval($link["start"]));
            $arrReplace[] = '<a href="/user/' . $link["id"] . '">' . substr($text, $link["start"], intval($link["end"]) - intval($link["start"])) . '</a>';
        }
        $text = str_replace($arrFind, $arrReplace, $text); ?>
        <div>
            <a href="/user/<?php echo $item["args"]["profile_id"]; ?>"><img style="width: 30px;" src="<?php echo str_replace("http:","https:",$item["args"]["profile_image"]); ?>" class="img-circle"/></a> <?php echo $text; ?> <?php echo date("d.m.Y H:i:s", $item["args"]["timestamp"]);
                if(isset($item["args"]["media"])) { ?>
                    <p>
                        <?php foreach($item["args"]["media"] as $img) { ?>
                            <a class="lightGalleryImage" data-sub-html='<div class="fb-comments" id="comments<?php echo $img["id"]; ?>"><p class="text-center"><i class="fa fa-spinner fa-spin fa-4x active"></i></p></div>' href="<?php echo str_replace("http:","https:",str_replace("/s150x150", "", $img["image"])); ?>"><img style="margin:5px 5px 5px 0;" src="<?php echo str_replace("http:","https:",$img["image"]); ?>"/></a>
                        <?php } ?>
                    </p>
                <?php } ?>
        </div>
        <hr/>
        <?php
    }
    if(isset($model["auto_load_more_enabled"]) && $model["auto_load_more_enabled"] == 1) { ?>
        <button id="btnLoadMore" onclick="loadMore('<?php echo $model["next_max_id"]; ?>');" class="btn btn-block btn-success" type="button">
            <i class="fa fa-plus"></i> Daha Fazla Yükle
        </button>
    <?php }
    if(!$isAjaxLoaded) { ?>
        </div>
        </div>
    <?php } ?>