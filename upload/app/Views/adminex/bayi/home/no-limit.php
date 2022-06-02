<?php
    /**
     * @var \Wow\Template\View      $this
     * @var string                  $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    switch($model) {
        case "like":
            $islemtip = array(
                "titlecase"  => "Beğeni",
                "lowercase"  => "beğeni",
                "dailyLimit" => $logonPerson->member->gunlukBegeniLimit
            );
            break;
            
            case "reellike":
            $islemtip = array(
                "titlecase"  => "Reel Beğeni",
                "lowercase"  => "Reel beğeni",
                "dailyLimit" => $logonPerson->member->gunlukBegeniLimit
            );
            break;
            
        case "follow":
            $islemtip = array(
                "titlecase"  => "Takip",
                "lowercase"  => "takip",
                "dailyLimit" => $logonPerson->member->gunlukTakipLimit
            );
            break;
        case "comment":
            $islemtip = array(
                "titlecase"  => "Yorum",
                "lowercase"  => "yorum",
                "dailyLimit" => $logonPerson->member->gunlukYorumLimit
            );
            break;
        case "commentlike":
            $islemtip = array(
                "titlecase"  => "Yorum Beğeni",
                "lowercase"  => "yorum beğeni",
                "dailyLimit" => $logonPerson->member->gunlukYorumBegeniLimit
            );
            break;
        case "canli":
            $islemtip = array(
                "titlecase"  => "Canlı Yayına Kullanıcı",
                "lowercase"  => "canlı yayına kullanıcı",
                "dailyLimit" => $logonPerson->member->gunlukCanliYayinLimit
            );
            break;
        case "videoview":
            $islemtip = array(
                "titlecase"  => "Video Görüntülenme",
                "lowercase"  => "video görüntülenme",
                "dailyLimit" => $logonPerson->member->gunlukVideoLimit
            );
            break;
            case "igtvvideo":
            $islemtip = array(
                "titlecase"  => "İg Tv Video Görüntülenme",
                "lowercase"  => "İg Tv video görüntülenme",
                "dailyLimit" => $logonPerson->member->gunlukVideoLimit
            );
            break;
            case "reelideo":
            $islemtip = array(
                "titlecase"  => "Reel Video Görüntülenme",
                "lowercase"  => "Reel video görüntülenme",
                "dailyLimit" => $logonPerson->member->gunlukVideoLimit
            );
            break;
        case "save":
            $islemtip = array(
                "titlecase"  => "Kaydetme",
                "lowercase"  => "kaydetme",
                "dailyLimit" => $logonPerson->member->gunlukSaveLimit
            );
            break;
        case "story":
            $islemtip = array(
                "titlecase"  => "Story Görüntülenme",
                "lowercase"  => "story görüntülenme",
                "dailyLimit" => $logonPerson->member->gunlukStoryLimit
            );
            break;
        case "autolike":
            $islemtip = array(
                "titlecase"  => "Oto Beğeni",
                "lowercase"  => "oto beğeni",
                "dailyLimit" => $logonPerson->member->toplamOtoBegeniLimit
            );
            break;
        default:
            $islemtip = array();
    }
?>
    <h4><?php echo $islemtip["titlecase"]; ?> <?php echo $islemtip["dailyLimit"] > 0 ? 'Limitiniz Bugün İçin Tükendi' : 'İşlemi Hakkınız Yok'; ?>!</h4>
<?php if($islemtip["dailyLimit"] > 0) { ?>
    <p class="text-danger">Üzgünüz, <?php echo $islemtip["lowercase"]; ?> işlemi gerçekleştirmek üzere kalan işlem limitiniz bulunmuyor! Bir sonraki gün işlem limitiniz <?php echo $islemtip["dailyLimit"]; ?> adet olarak yeniden ayarlanacak. Limitleriniz yetersiz geliyorsa lütfen bizimle iletişim kurun.</p>
<?php } else { ?>
    <p class="text-danger">Bayilik paketinizde, <?php echo $islemtip["lowercase"]; ?> işlemi gerçekleştirmek üzere tanımlanmış bir hakkınız bulunmamaktadır. Paketinize bu özelliği dahil etmek için bizimle iletişim kurun.</p>
<?php } ?>