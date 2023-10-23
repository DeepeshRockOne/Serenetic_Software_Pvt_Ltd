<style type="text/css">
    .chat-dask {
    }

    .chat-dask ul {
        margin: 0;
        padding: 0;
        display: table;
        clear: both;
        width: 100%;
    }

    .chat-dask ul li {
        background-color: #f7fafc;
        border: 1px solid #e7eaec;
        border-radius: 5px;
        display: block;
        margin-bottom: 7px;
        padding: 5px;
        width: calc(100% - 50px);
        float: left;
        position: relative;
    }

    .chat-dask ul li.customer-rply {
        background-color: transparent;
        float: right;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .chat-dask ul li.customer-rply + li.customer-rply {
        margin-top: -7px;
    }

    .chat-dask ul li .date-inp {
        font-size: 12px;
        font-weight: 400;
        margin-bottom: 5px;
        color: #727272;
    }

    .chat-dask ul li .mess-content {
        font-weight: 400;
    }

    .panel-body h4 {
        margin-top: 0px;
    }

    .title-cus a {
        font-size: 16px;
        margin-right: 5px !important;
    }

    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        Customer :
        <?= $resU['userName'] ?><?php if (isset($_REQUEST['back'])) { ?>
            <div class="pull-right"><a href="<?= $_SERVER["HTTP_REFERER"] ?>"
                                       class="back_toDetail btn btn-default">Back </a></div>
        <?php } ?>

    </div>

    <div class="panel-body">
        <h4>History</h4>
        <!-- <div class="table-responsive"> -->
        <div class="chat-dask" id="chat-dask">
            <ul class="chat-scroll">

                <?php if (count($result) > 0) { ?>
                    <?php foreach ($result as $res) {

                        ?>
                        <li class="<?= ($res['ikind'] == '1') ? 'customer-rply' : '' ?>">
                            <div class="date-inp">
                <span class="title-cus">
                <?php
                if ($res['tname'] != "") {
                    echo " <a href='javascript:void(0)' class='m-r-10'>" . $res['tname'] . "</a>" . date("h:i:s a", strtotime($res['dtmcreated']));
                } else {
                    echo date("h:i:s a", strtotime($res['dtmcreated']));
                }
                ?>
                </span>
                            </div>
                            <div class="mess-content"><span><?= $res['tmessage'] ?></span></div>
                        </li>
                    <?php } ?>
                <?php } else { ?>
                    <li>
                        <p>No record(s) found</p>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

    });
</script>