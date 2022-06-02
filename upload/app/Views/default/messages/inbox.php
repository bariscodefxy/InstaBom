<?php
    /**
     * @var \Wow\Template\View      $this
     * @var array                   $model
     * @var \App\Models\LogonPerson $logonPerson
     */
    $logonPerson = $this->get("logonPerson");
    $this->set("title", "Mesaj Kutum");
?>
<div class="container">
    <div class="cl10"></div>
        <a class="btn btn-default" href="javascript:void(0);"onclick="newMessage();"><i class="fa fa-plus"></i> Yeni Mesaj</a>
    <div id="threadsCape">
        <div id="threadList">
            <?php $this->renderView("messages/threads", $model); ?>
        </div>
        <div id="threadDetails">
            <div id="threadLoader" style="display:none;"><i class="fa fa-spinner fa-spin fa-5x"></i></div>
            <div id="threadUserList" style="display: none;"></div>
            <div id="messageList"><p class="text-primary" style="font-size:18px;padding:20px 40px;"><i class="fa fa-info-circle"></i> Lütfen sol bölümden görüntülemek istediğiniz mesajı seçin.</p></div>
            <div id="messageSendCape" style="display: none;">
                <form onsubmit="return sendMessage();">
                    <div class="form-group" style="padding:10px;">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Mesaj Yaz" id="strMessage">
                            <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><i class="fa fa-play"></i></button>
                        </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php $this->section("section_scripts");
    $this->parent(); ?>
<script type="text/javascript">
    var activeThreadID, activeThreadUserIDs, messageTimer, xhrObject;
    function getThreadDetails(threadID, userIDs, doSpin) {
        if(doSpin) {
            $('#threadUserList').hide();
            $('#messageSendCape').hide();
            $('#threadLoader').show();
        }
        if(activeThreadID != threadID && messageTimer) {
            clearTimeout(messageTimer);
            if(xhrObject) {
                xhrObject.abort();
            }
        }
        xhrObject = $.ajax({type: 'GET', url: '/messages/thread/' + threadID, dataType: 'json'}).done(function(data) {
            activeThreadID      = threadID;
            activeThreadUserIDs = userIDs;
            var beforeHeight    = $('#messageList').prop('scrollHeight');
            $('#messageList').html(data.messages);
            $('#threadUserList').html(data.users);
            $('#threadUserList').show();
            $('#messageSendCape').show();
            $('#threadLoader').hide();
            var afterHeight = $('#messageList').prop('scrollHeight');
            if(doSpin || afterHeight > beforeHeight) {
                $('#messageList').scrollTop(afterHeight);
                $('#strMessage').focus();
            }
            messageTimer = setTimeout(function() {
                getThreadDetails(threadID, userIDs);
            }, 8000);
        });
    }
    function sendMessage() {
        var strMessage = $('#strMessage').val();
        $('#strMessage').val('');
        $('#threadLoader').show();
        $.ajax({type: 'POST', url: '/messages/send-message', dataType: 'json', data: 'recipients=' + activeThreadUserIDs + '&message=' + strMessage}).done(function(data) {
            getThreadDetails(activeThreadID, activeThreadUserIDs);
        });
        return false;
    }

    function loadThreads() {
        $.ajax({type: 'GET', url: '/messages/threads'}).done(function(data) {
            $('#threadList').html(data);
            setTimeout(function() {
                loadThreads();
            }, 20000);
        });
    }

    var timerThreads = setTimeout(function() {
        loadThreads();
    }, 20000);
</script>
<?php $this->endSection(); ?>
