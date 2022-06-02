<?php foreach($model["geo_media"] as $item) { ?>
    <div class="panel panel-default">
        <div class="panel-body" style="padding:0;">
            <div style="display: table-row">
                <div style="display: table-cell;width: 50%;">
                    <a class="lightGalleryImage" data-sub-html='<div class="fb-comments" id="comments<?php echo $item["media_id"]; ?>"><p class="text-center"><i class="fa fa-spinner fa-spin fa-4x active"></i></p></div>' href="<?php echo str_replace("http:","https:",$item["display_url"]); ?>"><img style="width:100%;" class="lazy" data-original="<?php echo str_replace("http:","https:",$item["display_url"]); ?>"/></a>
                </div>
                <div style="display:table-cell;width:50%;background-color:#F7F7F7;" id="geomapContainer<?php echo $item["media_id"]; ?>">
                    <p><button class="btn btn-lg btn-primary" style="margin: auto;display: block;" onclick="showGeoMap('<?php echo $item["media_id"]; ?>','<?php echo $item["lat"]; ?>','<?php echo $item["lng"]; ?>')">Haritayı Göster</button></p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>