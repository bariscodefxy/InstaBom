<?php
    /**
     * @var \Wow\Template\View $this
     * @var array              $model
     */
    $this->set("title", "İnstagram İşlemleri"); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        İnstagram İşlemleri
    </div>
    <div class="panel-body">
        <form method="post">
            <div class="form-group">
                <label>Instagram Kullanıcı Adı</label>
                <input type="text" class="form-control" name="username">
            </div>
            <div class="form-group">
                <button type="submit"  class="btn btn-primary">Kullanıcı Bul</button>
            </div>
        </form>
    </div>
</div>