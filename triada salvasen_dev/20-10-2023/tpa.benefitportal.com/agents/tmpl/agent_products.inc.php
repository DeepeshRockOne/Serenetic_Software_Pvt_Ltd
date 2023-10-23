<?php if (!empty($is_prd_ajaxed)) { ?>
    <p class="agp_md_title">Products</p>
    <div class="table-responsive">
        <table class="<?= $table_class ?> mn">
            <thead>
            <th width="250px">Added Date</th>
            <th width="250px">Status / As of Date</th>
            <th>Product Name/ID</th>
            <th>Pricing</th>
            <th class="text-center">Commission</th>
            <th class="text-center" width="185px">PMPMs / As of Date</th>
            <th class="text-center" width="185px">Advance / As of Date</th>
            </thead>
            <tbody>
            <?php if (!empty($totalProduct) && !empty($fetchProduct)) { ?>
                <?php foreach ($fetchProduct as $product) { ?>
                    <tr>
                        <td><?= $tz->getDate($product['created_at'], 'm/d/Y') ?></td>
                        <td>
                            <div class="theme-form pr w-160 text-center">
                                <?php if (!in_array($product['product_status'], array('Extinct', 'Suspended', 'Pending')) && !in_array($product['status'], array('Extinct', 'Suspended'))) { ?>
                                    <select class="has-value sel_status" disabled="">
                                        <option value="Contracted" <?= $product['status'] == 'Contracted' ? 'selected="selected"' : '' ?>>
                                            Active
                                        </option>
                                        <option value="Pending Approval" <?= $product['status'] == 'Pending Approval' ? 'selected="selected"' : '' ?>>
                                            Pending
                                        </option>
                                        <option value="Suspended" <?= $product['status'] == 'Suspended' ? 'selected="selected"' : '' ?>>
                                            Suspended
                                        </option>
                                        <option value="Extinct" <?= $product['status'] == 'Extinct' ? 'selected="selected"' : '' ?>>
                                            Extinct
                                        </option>
                                    </select>
                                    <div class="clearfix"></div>
                                    <span class="fs12"><?= $tz->getDate($product['updated_at'], 'm/d/Y g:i A T') ?></span>
                                <?php } else { ?>
                                    <?php if (in_array($product['product_status'], array('Extinct', 'Suspended', 'Pending'))) { ?>
                                        <p class="text-red"><?= $product['product_status'] ?></p>
                                    <?php } else { ?>
                                        <p class="text-red"><?= $product['status'] ?></p>
                                    <?php } ?>
                                    <span class="fs12"><?= $tz->getDate($product['updated_at'], 'm/d/Y g:i A T') ?></span>
                                <?php } ?>
                            </div>
                        </td>
                        <td><p class="m-b-5"><?= $product['name'] ?></p>
                            <label class="label label-rounded <?php echo in_array($product['product_status'], array('Extinct', 'Suspended', 'Pending')) ? 'label-danger' : 'label-success' ?>"><?= $product['product_code'] ?></label>
                        </td>
                        <td><a href="javascript:void(0)"
                               data-href="<?=$HOST?>/agents_pricing.php?agent_id=<?= $_GET['id'] ?>&product_id=<?= $product['pid'] ?>"
                               class="red-link agents_pricing"><strong>View</strong></a></td>
                        <td class="text-center">
                            <?php
                            $comm_json = json_decode($product['commission_json'], true);
                            $commission_amt = '0.00';
                            if ($product['commission_on'] == 'Plan') {
                                $commission_arr = isset($comm_json[$product['min_plans']][$level]) ? $comm_json[$product['min_plans']][$level] : array("amount_type"=>"Percentage","amount"=>0);
                                $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] . '%' : displayAmount($commission_arr['amount']);
                            } else {
                                $commission_arr = isset($comm_json[$level]) ? $comm_json[$level] : array("amount_type"=>"Percentage","amount"=>0);
                                $commission_amt = $commission_arr['amount_type'] == 'Percentage' ? $commission_arr['amount'] . '%' : displayAmount($commission_arr['amount']);
                            }
                            ?>
                            <p class="text-red"><?= $commission_amt ?></p>
                        </td>
                        <td class="text-center">
                            <?php
                            $ext_plus = $product['ext_plus'] > 1 ? "+" : '';
                            $pmpm = '-';
                            if (!empty($product['pm_amt_type'])) {
                                if ($product['pm_amt_type'] == 'Percentage') {
                                    $pmpm = $product['amount'] . "%" . $ext_plus;
                                } else {
                                    $pmpm = displayAmount($product['amount']) . $ext_plus;
                                }
                            }
                            $pmdate = '';
                            if (!empty($product['pm_amt_type']) && !empty($product['pm_as_of_date']) && $product['pm_as_of_date'] != '0000-00-00') {
                                $pmdate = date('m/d/Y', strtotime($product['pm_as_of_date']));
                            }
                            if (!empty($product['pm_amt_type'])) {
                                ?>
                                <strong class="text-red"><?= $pmpm ?></strong><br/><?= $pmdate ?>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <?php 
                            if($level != 'LOA'){
                            if (!empty($product['advFeeId'])) { ?>

                                <?php
                                echo "<strong class='text-red'>" . $product['advance_month'] . " Month(s)</strong>";
                                $advRuleCreatedAt = checkIsset($product['advRuleCreatedAt']) != '' && $product['advRuleCreatedAt'] != '0000-00-00' ? date('m/d/Y', strtotime($product['advRuleCreatedAt'])) : '';
                                ?>
                                <br/> <?= $advRuleCreatedAt ?>
                            <?php } else { echo '-' ; } 
                            } else { echo '-' ; } ?>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="7">
                        No rows Found!
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <?php if (!empty($totalProduct) > 0 && !empty($fetchProduct)) { ?>
                    <td colspan="7">
                        <?php echo $paginate_product->links_html; ?>
                    </td>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>
    <hr/>
<?php } else { ?>
    <div id="agent_product_ajax_data"></div>

    <script type="text/javascript">
        function refreshControl(id_class) {
            $(id_class).addClass('form-control');
            $(id_class).selectpicker({
                container: 'body',
                style: 'btn-select',
                noneSelectedText: '',
                dropupAuto: false,
            });
        }

        $(document).ready(function () {
            agent_product_ajax_submit();
            var execute=function(){
                refreshControl('.sel_status');
            }
            dropdown_pagination(execute,'agent_product_ajax_data');

            $(document).off('click', '#agent_product_ajax_data ul.pagination li a');
            $(document).on('click', '#agent_product_ajax_data ul.pagination li a', function (e) {
                e.preventDefault();
                $('#ajax_loader').show();
                $('#agent_product_ajax_data').hide();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET',
                    success: function (res) {
                        $('#ajax_loader').hide();
                        $('#agent_product_ajax_data').html(res).show();
                        refreshControl('.sel_status');
                        common_select();
                    }
                });
            });

            $(document).off('click', '.agents_pricing');
            $(document).on('click', '.agents_pricing', function (e) {
                $href = $(this).data('href');
                $.colorbox({iframe: true, href: $href, width: '530px', height: '350px'});
            });
        });

        function agent_product_ajax_submit() {
            $('#ajax_loader').show();
            $('#agent_product_ajax_data').hide();
            $.ajax({
                url: "agent_products.php?id=<?=$agent_id?>&is_prd_ajaxed=1&per_pages=10&level=<?=$agent_row['agent_coded_level'] ?>",
                type: 'GET',
                success: function (res) {
                    $('#ajax_loader').hide();
                    $('#agent_product_ajax_data').html(res).show();
                    refreshControl('.sel_status');
                    common_select();
                }
            });
            return false;
        }
    </script>
<?php } ?>