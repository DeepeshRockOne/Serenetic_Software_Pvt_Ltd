<div class="panel panel-default panel-block Font_Roboto connect_processor">
    <div class="panel-heading">
        <h4 class="panel-title fs16">Test Processor   - <span class="fw300"><?=$payment_master_res['name']?></span></h4>
    </div>
    <div class="panel-body text-center">
        <div class="p-t-30 ">
        <img src="<?=$link?>" width="77px"> 
        <h3 class="fs16 m-b-20 m-t-20"><?=$text?></h3>
        <a href="javascript:void(0);" class="btn btn-action mw-90" id="<?=$btn_id?>" <?=$onClick?>><?=$btn_text?></a>
        </div> 
    </div>
</div>
<script type="text/javascript">
    <?php if($btn_text == 'Continue'){ ?>
        parent.$.colorbox.resize({
		        height:325
		    });
    <?php  }else{ ?>
        parent.$.colorbox.resize({
		        height:326
		    });
    <?php } ?>
    $(document).off('click','#btn_retry');
    $(document).on('click','#btn_retry',function(e){
        $("#processor_name_title_id").html("<?=$payment_master_res['name']?>");
        $link = 'connect_processor_popup.php?pay_id=<?=$payment_master_id?>';
        parent.$.colorbox({
            href : $link,
            iframe: 'true', 
            width:'768px',
            height:"450px",
        });
    });
</script>