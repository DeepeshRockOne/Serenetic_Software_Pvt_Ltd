<?php
        if (!empty($products_res)) {
            $panel_colors = array("cyan", "success", "info");
            $color_key = 0;
            foreach ($products_res as $key => $product_row) {
                if (isset($panel_colors[$color_key])) {
                    $panel_color = $panel_colors[$color_key];
                    $color_key++;
                } else {
                    $color_key = 0;
                    $panel_color = $panel_colors[$color_key];
                    $color_key++;
                }
                ?>
                <div class="panel panel-default product_detail_panel panel-<?= $panel_color ?>"
                     id="product_detail_<?= $product_row['ws_id']; ?>">
                    <div class="panel-heading ">
                        <h4 class="mn text-white">
                            <?= $product_row['product_name'] ?>
                        </h4>
                    </div>
                    <div class="space p-5">
                        &nbsp;
                    </div>
                    <div class="table-responsive">
                        <table class="<?= $table_class ?> table-<?= $panel_color ?>">
                            <thead>
                            <tr>
                                <th>Added Date</th>
                                <th>Plan Type</th>
                                <th>Effective Date</th>
                                <th>Eligibility Status</th>
                                <th class="text-center">Dependents</th>
                                <th>Price</th>
                                <th width="100px" class="text-center">ID Card</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $product_row['purchase_date']; ?></td>
                                <td><?= $product_row['prd_plan_type']; ?></td>
                                <td><?= $product_row['eligibility_date']; ?></td>
                                <td><?= $product_row['status']; ?></td>
                                <td class="text-center icons">
                                    <?php if ($product_row['prd_plan_type_id'] > 1) { ?>
                                        <a href="<?= $HOST ?>/view_depedents.php?ws_id=<?= md5($product_row['ws_id']); ?>"
                                           class="view_depedents"><i class="fa fa-eye fa-lg"></i></a>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td><?= $product_row['price']; ?></td>
                                <td class="text-center icons">
                                    <?php if($product_row['id_card_available'] == "Y") { ?>
                                    <a href="javascript:void(0);" data-href="<?= $HOST ?>/id_card_popup.php?ws_id=<?= md5($product_row['ws_id']); ?>&user_type=Group&user_id=<?=$_SESSION['groups']['id']?>"
                                       class="id_card_popup"><i class="fa fa-address-card-o fa-lg"
                                                                aria-hidden="true"></i></a>
                                    <?php } else { echo "-"; } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-body">
                        <?php if(!empty($product_row['tab_res']) || !empty($product_row['prd_resources'])) { ?>
                                <ul class="nav nav-tabs tabs customtab nav-noscroll">
                                <?php foreach ($product_row['tab_res'] as $tab_key => $tab_row) { ?>
                                    <li class="<?=$tab_key == 0?'active':''?>"><a href="#tab_pane_<?=$tab_row['id']?>" data-toggle="tab" data-tab_id="<?=$tab_row['id']?>"><?=$tab_row['name'];?></a></li>
                                <?php } ?>
                                <?php if(!empty($product_row['prd_resources'])) { ?>
                                    <li class="<?=empty($product_row['tab_res'])?'active':''?>">
                                        <a href="#resources_pane_<?=$product_row['ws_id']?>" data-toggle="tab">Resources</a>
                                    </li>
                                <?php } ?>
                                </ul>
                                <div class="tab-content">
                                    <?php foreach ($product_row['tab_res'] as $tab_key => $tab_row) {
                                        foreach($smart_tags as $placeholder => $value){
                                            $product_description = str_replace("[[" . $placeholder . "]]", $value,$tab_row['description']);
                                        } ?>
                                        <div class="tab-pane fade in <?=$tab_key == 0?'active':''?> member_prd_tab_pane" id="tab_pane_<?=$tab_row['id']?>" data-tab_id="<?=$tab_row['id']?>"><?=$product_description?></div>
                                    <?php } ?>
                                    <?php if(!empty($product_row['prd_resources'])) { ?>
                                        <div class="tab-pane fade in <?=empty($product_row['tab_res'])?'active':''?>" id="resources_pane_<?=$product_row['ws_id']?>">
                                            <?php 
                                                foreach ($product_row['prd_resources'] as $key => $resource_row) {
                                                    if($resource_row['coll_type'] == "link") {
                                                        ?>
                                                        <p><a href="<?=(substr($resource_row['coll_doc_url'],0,7)=="http://" || substr($resource_row['coll_doc_url'],0,8)=="https://"?$resource_row['coll_doc_url']:'//'.$resource_row['coll_doc_url'])?>" class="red-link res" data-toggle="tooltip" title="<?=$resource_row['description']?>" target="_blank"><?=$resource_row['name']?></a></p>
                                                        <?php
                                                    }
                                                    if($resource_row['coll_type'] == "pdf") {
                                                        ?>
                                                        <p><a href="<?=$COLLATERAL_DOCUMENT_WEB.'pdf/'.$resource_row['coll_doc_url']?>" class="red-link res" data-toggle="tooltip" title="<?=$resource_row['description']?>" target="_blank"><?=$resource_row['name']?></a></p>
                                                        <?php
                                                    }
                                                    if($resource_row['coll_type'] == "video") {
                                                        if($resource_row['video_type'] == "Youtube") {
                                                            ?>
                                                            <p><a href="javascript:void(0);" title="<?=$resource_row['description']?>" class="btn_view_video red-link res" data-toggle="tooltip" data-video_id="<?=$resource_row['coll_doc_url']?>" data-video_type="<?=$resource_row['video_type'];?>"><?=$resource_row['name']?></a></p>
                                                            <?php
                                                        }
                                                        if($resource_row['video_type'] == "Wistia") {
                                                            ?>
                                                            <p><a href="javascript:void(0);" title="<?=$resource_row['description']?>" class="btn_view_video red-link res" data-toggle="tooltip" data-video_id="<?=$resource_row['coll_doc_url']?>" data-video_type="<?=$resource_row['video_type'];?>"><?=$resource_row['name']?></a></p>
                                                            <?php
                                                        }
                                                    }
                                                    if($resource_row['coll_type'] == "html") {
                                                        ?>
                                                        <p><a href="<?=$HOST.'/generate_member_certificate.php?sub_resource_id='.md5($resource_row['id'])?>&user_id=<?= md5($customer_id); ?>&ws_id=<?=md5($product_row['ws_id'])?>" class="red-link res" data-toggle="tooltip" title="<?=$resource_row['description']?>" target="_blank"><?=$resource_row['name']?></a></p>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                        <?php } ?>
                    </div>
                    <!-- <div class="panel-footer text-center">
                        <a href="javascript:void(0)" class="btn red-link btn_hide_product_detail2" data-ws_id="<?= $product_row['ws_id']; ?>">Close</a>
                    </div> -->
                </div>
                <?php
            }
        }
        ?>