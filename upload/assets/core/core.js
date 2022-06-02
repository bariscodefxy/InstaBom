function loadMore(maxID) {
    $('#btnLoadMore').remove();
    $('#entry-list-row').first().append('<div class="tempLoading"><i class="fa fa-spinner fa-spin fa-2x"></i> Yüklüyor..</div>');
    $.ajax({url: '?formType=more', type: 'POST', data: 'maxid=' + maxID}).done(function(response) {
        $('#entry-list-row').append(response);
        $('.tempLoading').remove();
        setLightBox(true);
        $(".lazy").show().lazyload({threshold: 500}).removeClass("lazy");
    });
}
function editMedia(id) {
    $('#modalEditMediaInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
    $.ajax({url: '/account/edit-media/' + id, type: 'GET'}).done(function(data) {
        $('#modalEditMediaInner').html(data);
    });
}
function updateMedia(id) {
    $('#modalEditMedia').modal('hide');
    $.ajax({url: '/account/edit-media/' + id, type: 'POST', dataType: 'json', data: $('#formEditMedia').serialize()}).done(function(data) {
        if(data.status == 'success') {
            getCommentList(id);
        }
        else {
            alert(data.message);
        }
    });
}
function deleteMedia(id) {
    $('#entry' + id).hide();
    $.ajax({url: '/account/delete-media', dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
        if(data.status == 'error') {
            $('#entry' + id).show();
        }
    });
}

function like(id) {
    var likeCount = $('#item' + id + ' .like_count');
    var isLike    = $('#item' + id).attr('data-liked') != '1';
    if(isLike) {
        $('#item' + id + ' i').addClass('text-danger');
        likeCount.html(parseInt(likeCount.html()) + 1);
    }
    else {
        $('#item' + id + ' i').removeClass('text-danger');
        likeCount.html(parseInt(likeCount.html()) - 1);
    }
    var url = (isLike) ? '/account/like' : '/account/unlike';
    $.ajax({url: url, dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
        if(data.status == 'error') {
            if(isLike) {
                $('#item' + id + ' i').removeClass('text-danger');
                likeCount.html(parseInt(likeCount.html()) - 1);
            }
            else {
                $('#item' + id + ' i').addClass('text-danger');
                likeCount.html(parseInt(likeCount.html()) + 1);
            }
        }
        else {
            $('#item' + id).attr('data-liked', isLike ? '1' : '0');
        }
    });
}


function follow(id) {
    btn     = $('#btnUserFollow');
    oldHtml = btn.html();
    btn.html('Bekleyin..');
    $.ajax({url: '/account/follow', dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
        if(data.status == 'error') {
            btn.html(oldHtml);
        }
        else {
            btn.attr("onclick", "unfollow('" + id + "')");
            if(data.is_private == 1) {
                btn.html('<i class="fa fa-clock-o"></i> İstek Gönderildi').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-default');
            } else {
                btn.html('<i class="fa fa-check"></i> Takip Ediyorsun').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-success');
            }
        }
    });
}

function unfollow(id) {
    if(confirm('Takibi bırakmak istediğinden emin misin?')) {
        btn     = $('#btnUserFollow');
        oldHtml = btn.html();
        btn.html('Bekleyin..');
        $.ajax({url: '/account/unfollow', dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
            if(data.status == 'error') {
                btn.html(oldHtml);
            }
            else {
                btn.attr("onclick", "follow('" + id + "')");
                if(data.is_private == 1) {
                    btn.html('<i class="fa fa-plus"></i> Takip Et (İstek Gönder)').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-primary');
                    window.location.href = window.location.href;
                } else {
                    btn.html('<i class="fa fa-plus"></i> Takip Et').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-primary');
                }
            }
        });
    }
}


function block(id) {
    if(confirm('Emin misin?')) {
        btn     = $('#btnUserFollow');
        oldHtml = btn.html();
        btn.html('Bekleyin..');
        $.ajax({url: '/account/block', dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
            if(data.status == 'error') {
                btn.html(oldHtml);
            }
            else {
                $('#btnPopBlock').css('display', 'none');
                $('#btnPopUnblock').css('display', '');
                btn.attr("onclick", "unblock('" + id + "')");
                btn.html('<i class="fa fa-unlock"></i> Engellemeyi Kaldır').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-default');
            }
        });
    }
}

function unblock(id) {
    if(confirm('Emin misin?')) {
        btn     = $('#btnUserFollow');
        oldHtml = btn.html();
        btn.html('Bekleyin..');
        $.ajax({url: '/account/unblock', dataType: 'json', type: 'POST', data: 'id=' + id}).done(function(data) {
            if(data.status == 'error') {
                btn.html(oldHtml);
            }
            else {
                $('#btnPopBlock').css('display', '');
                $('#btnPopUnblock').css('display', 'none');
                btn.attr("onclick", "follow('" + id + "')");
                if(data.is_private == 1) {
                    btn.html('<i class="fa fa-plus"></i> Takip Et (İstek Gönder)').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-primary');
                } else {
                    btn.html('<i class="fa fa-plus"></i> Takip Et').removeClass('btn-default').removeClass('btn-success').removeClass('btn-primary').addClass('btn-primary');
                }
            }
        });
    }
}

function showGeoMap(id, lat, lng) {
    $('#geomapContainer' + id).html('<iframe width="100%" height="100%" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/view?key=AIzaSyAT2XRwYXUTETmiIkYvrCg6_CwxtcdpMhI&center=' + lat + ',' + lng + '&zoom=17&maptype=satellite" allowfullscreen></iframe>');
}

function setLightBox(destroyFirst) {
    $lightBox = $('#entry-list-container');
    if(destroyFirst) {
        $lightBox.data('lightGallery').destroy(true);
    }
    $lightBox.lightGallery({
                               appendSubHtmlTo: '.lg-item',
                               mode           : 'lg-fade',
                               selector       : '.lightGalleryImage',
                               hideBarsDelay  : 2000,
                               enableSwipe    : false,
                               enableDrag     : false,
                               keyPress       : false
                           });
    $lightBox.on('onAfterSlide.lg', function(event, prevIndex, index) {
        if(!$('.lg-outer .lg-item').eq(index).attr('data-fb')) {
            $('.lg-outer .lg-item').eq(index).attr('data-fb', 'loaded');
            setCommentListByIndex(index);
        }
    });

}

var arrCommentLikersData = [];

function setCommentListByIndex(index) {
    contentDiv    = $('.lg-outer .lg-item:eq(' + index + ') .fb-comments');
    id            = contentDiv.attr('data-id');
    carouselIndex = contentDiv.attr('data-carousel-index');
    if(carouselIndex) {
        setCommentList(id, carouselIndex);
    } else {
        setCommentList(id);
    }
}

function getCommentList(id,carouselIndex) {
    $.ajax({url: '/account/load-comments/' + id, type: 'GET'}).done(function(data) {
        if(carouselIndex){
            data = '<h1 style="margin-top:0;">Carousel</h1><h6>' + $('#comments' + id).attr('data-carousel-text') + '</h6>' + data;
        }
        arrCommentLikersData[id] = data;
        setCommentList(id);
    });
}

function setCommentList(id, carouselIndex) {
    if(!arrCommentLikersData[id]) {
        return getCommentList(id,carouselIndex);
    }
    $commentContainer = $('#comments' + id);
    gHtml             = arrCommentLikersData[id];
    if(carouselIndex && carouselIndex != 0) {
        // Nothing yet
        // $commentContainer = $('#comments' + id + carouselIndex);
        // $commentContainer.html('<h1>Carousel</h1><h6>' + $commentContainer.attr('data-carousel-text') + '</h6>');
    }
    else {
        $commentContainer.html(gHtml);
        if(window.innerWidth < 992) {
            $('#comments' + id + ' .lazy').show().lazyload({container: $('.lg-current'), threshold: 500}).removeClass("lazy");
        }
        else {
            $('#comments' + id + ' .lazy').show().lazyload({container: $commentContainer, threshold: 500}).removeClass("lazy");
        }
    }
}


function saveComment(id) {
    $.ajax({url: '/account/add-comment/' + id, type: 'POST', dataType: 'json', data: $('#commentForm' + id).serialize()}).done(function(data) {
        getCommentList(id);
        if(data.status != 'success') {
            alert('Yorum eklenemedi! Tekrar deneyebilirsin.');
        }
    });
}

function deleteComment(commentID, mediaID, captionText) {
    $.ajax({url: '/account/delete-comment/' + commentID, type: 'POST', dataType: 'json', data: 'mediaId=' + mediaID + '&captionText=' + captionText}).done(function(data) {
        getCommentList(mediaID);
        if(data.status != 'success') {
            alert('Yorum silinemedi! Tekrar deneyebilirsin.');
        }
    });
}

function changeProfilePhoto() {
    $('#modalChangeProfilePhotoInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
    $.ajax({url: '/account/change-profile-photo', type: 'GET'}).done(function(data) {
        $('#modalChangeProfilePhotoInner').html(data);
    });
}

function removeProfilePhoto() {
    if(confirm('Profil resminiz silinsin mi?')) {
        $.ajax({url: '/account/remove-profile-photo', type: 'GET', dataType: 'json'}).done(function(data) {
            if(data.status != 'success') {
                alert('Resim silinemedi! Tekrar deneyebilirsin.');
            }
            else {
                $('#profilePhoto').attr('src', data.message);
            }
        });
    }
}


function newMessage(recipients, mediaid) {
    $('#modalNewMessage').modal('show');
    $('#modalNewMessageInner').html('<div class="modal-body"><h2>Bekleyin..</h2></div>');
    url = '/messages/new?';
    if(recipients) {
        url += 'recipients=' + recipients + '&';
    }
    if(mediaid) {
        url += 'mediaid=' + mediaid;
    }
    $.ajax({url: url, type: 'GET'}).done(function(data) {
        $('#modalNewMessageInner').html(data);
    });
}

function searchRecipients(text) {
    if(!text || text.length == 0) {
        return false;
    }
    $.ajax({url: '/messages/search-recipients?q=' + text, type: 'GET'}).done(function(data) {
        $('#foundRecipients').html(data);
    });
}

function addRecipient(id, nick, img) {
    if($('a[data-recipientid=' + id + ']').length == 0) {
        html = '<a class="list-group-item recipient" data-recipientid="' + id + '" href="javascript:void(0);" onclick="$(this).remove();"><img class="img-circle" style="max-width:30px;" src="' + img + '" /> ' + nick + ' <span class="badge"><i class="fa fa-remove"></i></span> </a>';
        $('#addedRecipients').append(html);
    }
    $('#foundRecipients').html('');
    $('#searchuserstext').val('');
}

function sendNewMessage() {
    var recipients    = $('a[data-recipientid]');
    var strNewMessage = $.trim($('#strNewMessage').val());
    var mediaid       = $('#mediaIDNewMessage').length == 1 ? $.trim($('#mediaIDNewMessage').val()) : null;
    var media         = $('#fileNewMessage').length == 1 ? $('#fileNewMessage')[0].files[0] : null;
    if(recipients.length == 0) {
        alert('En az 1 alıcı eklemelisin!');
        return false;
    }
    if(strNewMessage == '' && !mediaid && !media) {
        alert('Mesaj veya medyadan birini eklemek zorunludur!');
        return false;
    }
    var allRecipients = '';
    recipients.each(function() {
        if(allRecipients != '') {
            allRecipients += ',';
        }
        allRecipients += $(this).attr('data-recipientid');
    });
    var messageType = $('#selectNewMessageType').val();
    $('#modalNewMessageInner').html('<div class="modal-body"><h2>Mesaj Gönderiliyor..</h2></div>');

    if(media) {
        var fd = new FormData();
        fd.append('recipients', allRecipients);
        fd.append('type', messageType);
        fd.append('message', strNewMessage);
        fd.append('file', media);
        $.ajax({type: 'POST', url: '/messages/send-message', data: fd, processData: false, contentType: false}).done(function(data) {
            if(data.status !== 'success') {
                $('#modalNewMessageInner').html('<div class="modal-body"><h2 class="text-danger">' + data.error + '</h2></div>');
            }
            else {
                $('#modalNewMessageInner').html('<div class="modal-body"><h2 class="text-success">Mesajınız Gönderildi.</h2></div>');
            }
        });
    }
    else {
        $.ajax({type: 'POST', url: '/messages/send-message', dataType: 'json', data: 'recipients=' + allRecipients + '&type=' + messageType + '&mediaid=' + mediaid + '&message=' + strNewMessage}).done(function(data) {
            $('#modalNewMessageInner').html('<div class="modal-body"><h2 class="text-success">Mesajınız Gönderildi.</h2></div>');
        });
    }
}

function KeepSession() {
    $.ajax({type: 'GET', url: '/ajax/keep-session', dataType: 'json'}).done(function(data) {
        if(data.nonReadThreadCount > 0) {
            $('#nonReadThreadCount').html(data.nonReadThreadCount).removeClass('hidden');
        }
    });
    setTimeout(KeepSession, 5 * 60 * 1000);
}

function initProject() {

    // Show the progress bar
    NProgress.set(0.4);
    // Increase randomly
    var interval = setInterval(function() {
        NProgress.inc();
    }, 1000);
    // Trigger finish when page fully loaded
    jQuery(window).load(function() {
        clearInterval(interval);
        NProgress.done();
    });
    // Trigger bar when exiting the page
    jQuery(window).unload(function() {
        NProgress.set(0.4);
    });


    $("#loginAsUser").fancybox({
                                   type       : 'iframe',
                                   fitToView  : true,
                                   maxWidth   : '460px',
                                   maxHeight  : '600px',
                                   autoSize   : true,
                                   closeClick : false,
                                   openEffect : 'none',
                                   closeEffect: 'none'
                               });
    $('a[rel=fancyboxImage]').fancybox();
    setLightBox();
    $(window).scroll(function() {
        if($('#btnLoadMore').length == 1 && ($(window).scrollTop() > $(document).height() - $(window).height() - 500)) {
            $('#btnLoadMore').click();
        }
    });
    $(".lazy").show().lazyload({threshold: 500}).removeClass("lazy");
    setTimeout(KeepSession, 1 * 60 * 1000);
}