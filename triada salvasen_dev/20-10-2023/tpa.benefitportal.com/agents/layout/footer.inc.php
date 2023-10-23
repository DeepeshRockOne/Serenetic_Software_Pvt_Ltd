<div class="smarte_footer">
    <div class="container">
        <div class="row footer_help m-b-15">
            <div class="col-xs-7">
                <h4 class="text-action m-t-0">NEED HELP?</h4>
                <?php if($_SESSION['agents']['sponsor_display_in_member'] == "N") { ?>
                    <p class="need_help mn"><span><?=$_SESSION['agents']['sponsor_public_name'];?>  </span>  <span><a href="<?='tel:+1'.$_SESSION['agents']['sponsor_public_phone']?>"><?=format_telephone($_SESSION['agents']['sponsor_public_phone']);?></a></span> <span> <a href="mailto:<?=$_SESSION['agents']['sponsor_public_email'];?>"><?=$_SESSION['agents']['sponsor_public_email'];?></a></span> </p>
                <?php } else { ?>
                    <p class="need_help mn"><span>Agent Services  </span> <span>  <a href="<?='tel:+1'.$_SESSION['agents']['agent_services_cell_phone']?>"><?=format_telephone($_SESSION['agents']['agent_services_cell_phone']);?></a> </span> <span> <a href="mailto:<?=$_SESSION['agents']['agent_services_email'];?>"><?=$_SESSION['agents']['agent_services_email'];?></a> </span></p>
                <?php } ?>
            </div>
            <div class="col-xs-5 text-right">
                <div class="powered_by_logo">
                    <?php 
                    $powered_by_logo = $POWERED_BY_LOGO;
                    if($_SESSION["agents"]["is_branding"] == "Y") {
                        if ($_SESSION["agents"]["brand_icon"] != "" && file_exists($AGENTS_BRAND_ICON . $_SESSION["agents"]["brand_icon"])) {
                            $powered_by_logo = $HOST . '/uploads/agents/brand_icon/' . $_SESSION["agents"]["brand_icon"];
                        }
                    }
                    ?>                
                    <img src="<?=$powered_by_logo?>" id="img_powered_by_logo" width="43px" height="43px"/>
                </div>
              </div>
        </div>
    </div>
    <div class="bottom_footer">
        <div class="container">
            <?php if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_type']=='Admin'){
            }else{ ?>
             <button id="footer_btn_open_chat" class="btn btn-white-o btn_open_chat">Live Chat</button>
            <?php } ?>
            <!-- <a href="javascript:void(0);" class="btn btn-white-o">Live Chat</a> -->
            <ul>
                <!-- <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms & Conditions</a></li>
                <li><a href="#">Legal</a></li>
                <li><a href="#">FAQ</a></li> -->
                <li><?= $DEFAULT_SITE_NAME ?> &copy; <?php echo date('Y')?> </li>
            </ul>
        </div>
    </div>
</div>
<script src="<?=$LIVE_CHAT_HOST?>/js/init.js<?=$cache?>"></script>
<script type="text/javascript">
    <?php if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_type']=='Agent'){ ?>
        $(document).on("SBReady", function () {
            $("#footer_btn_open_chat").hide();
            SBChat.initChat();
        });
    <?php } ?>

    $(document).ready(function(){
        isAdminOnline();
        setInterval(function(){
            isAdminOnline();
        },10000);
        <?php /*if(!isset($_SESSION['agents']['chat_logout'])) { ?>
            $(document).on("SBReady", function () {
                <?php if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_id'] != $_SESSION['agents']['id'] && $_SESSION['sb-session']['app_user_type'] != "Agent") { ?>
                localStorage.setItem("chatOpen",false);
                SBF.reset();
                <?php } ?>
                openChatFooter();
            });
        <?php } */?>

        $(document).off("click",".btn_open_chat");
        $(document).on("click",".btn_open_chat", function () {
            localStorage.setItem("chatOpen",true);
            openChatFooter();
        });

        $(document).off("click",".btn_close_conversation");
        $(document).on("click",".btn_close_conversation", function () {
          $conversationID = $(this).attr('data-conversation-id');
          $userID = $(this).attr('data-user-id');
          closeChat($conversationID,$userID);
        });

        $(document).off("click",".btn_logout_portal");
        $(document).on("click",".btn_logout_portal", function () {
            $("#ajax_loader").show();
            localStorage.setItem("chatOpen",false);
            SBF.logout(false);
            var pre_page = $("#previous_page").val();
            if(pre_page != undefined && pre_page!=''){
                window.location.href="logout.php?previous_page="+pre_page;
            }else{
                window.location.href="logout.php";
            }            
        });
    });

    openChatFooter = function(){
        $("#ajax_loader").show();
        $.ajax({
            method: 'GET',
            url: '<?=$HOST?>/login_chat_account.php?action=login_chat_account&location=agent',
            dataType : 'json',
        }).done((response) => {
            if(response.status == "success") {
                $("#ajax_loader").hide();
                SBChat.initChat();
                $("#footer_btn_open_chat").hide();
                setTimeout(function () {
                    if(localStorage.getItem("chatOpen") == "true"){
                        
                    } else {
                        SBChat.customClose()
                    }
                }, 500);
            } else {
                localStorage.setItem("chatOpen", false);
                window.location.reload();
            }
        }); 
    }
    closeChat = function($conversationID,$userID){
      $("#ajax_loader").show();
      $.ajax({
          method: 'GET',
          url: '<?=$HOST?>/login_chat_account.php?action=logout_chat_account&location=agent&userID='+$userID+'&conversationID='+$conversationID,
          dataType : 'json',
      }).done((response) => {
          $("#ajax_loader").hide();
          if(response.status == "success") {
              localStorage.setItem("chatOpen",false);
              SBF.logout();
          } else {
              window.location.reload();
          }
      });
    }

    isAdminOnline = function(){
        $.ajax({
            url:'<?= $HOST ?>/ajax_is_chat_admin_online.php',
            type:'POST',
            dataType:'JSON',
            success:function(res){
                if(res.status=="Online"){
                    $("#footer_btn_open_chat").attr('disabled',false);
                    $("#footer_btn_open_chat").removeClass('text-danger');
                    $("#footer_btn_open_chat").addClass('text-success');
                }else{
                    $("#footer_btn_open_chat").attr('disabled',true);
                    $("#footer_btn_open_chat").removeClass('text-success');
                    $("#footer_btn_open_chat").addClass('text-danger');
                }
            }
        })
    }
</script>
