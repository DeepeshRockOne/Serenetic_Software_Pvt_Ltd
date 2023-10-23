<?php

function has_access($access,$check_wr_access = false){
    $flag = false;
    $access_type = "rw"; //Read Write
    if (isset($_SESSION['admin']['access'])) {
        if (is_array($access)) {
            if (count(array_intersect($access, array_values($_SESSION['admin']['access'])))) {
                $flag = true;
            }
        } else {
            if (in_array($access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
            } elseif (in_array("ro_".$access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
                $access_type = "ro"; //Read Only
            }
        }
    }
    if (!$flag) {
        echo "<script>window.parent.location = 'dashboard.php'; window.location = 'dashboard.php'; </script>";
        exit;
    }
    if($check_wr_access == true && $access_type != "rw") {
        echo "<script>window.parent.location = 'dashboard.php'; window.location = 'dashboard.php'; </script>";
        exit;
    }
    return $access_type;
}
function module_access_type($access){
    $flag = false;
    $access_type = "rw"; //Read Write
    if (isset($_SESSION['admin']['access'])) {
        if (is_array($access)) {
            if (count(array_intersect($access, array_values($_SESSION['admin']['access'])))) {
                $flag = true;
            }
        } else {
            if (in_array($access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
            } elseif (in_array("ro_".$access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
                $access_type = "ro"; //Read Only
            }
        }
    }
    if (!$flag) {
        $access_type = "";   
    }
    return $access_type;
}

 
function has_menu_access($access){
    $flag = false;
    if (is_array($access)) {
        if (isset($_SESSION['admin']['access']) && $_SESSION['admin']['access'] != "") {
            if (count(array_intersect($access, array_values($_SESSION['admin']['access'])))) {
                $flag = true;
            }
        } else {
            $flag = false;
        }
    } else {
        if (isset($_SESSION['admin']['access']) && $_SESSION['admin']['access'] != "") {
            if (in_array($access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
            } elseif (in_array("ro_".$access, array_values($_SESSION['admin']['access']))) {
                $flag = true;
            } elseif(is_array($_SESSION['admin']['access']) && in_array($access, $_SESSION['admin']['access'])){
                $flag = true;
            } elseif(is_array($_SESSION['admin']['access']) && in_array("ro_".$access, $_SESSION['admin']['access'])){
                $flag = true;
            }
        } else {
            $flag = false;
        }
    }
    return $flag;
}

function generate_ach_batch_file($rows, $country, $commission_type, $pay_period)
{
    global $pdo,$CREDIT_CARD_ENC_KEY;

    $checkBatch = "SELECT count(id) as total FROM ach_file_export";
    $checkBatchRow = $pdo->selectOne($checkBatch);
    $totalBatch = $checkBatchRow['total'] + 150;

    $setting_keys = array(
                    'immediate_destination',
                    'immediate_destination_name',
                    'immediate_origin',
                    'immediate_origin_name',
                    'company_entry_description',
                    'originating_dfi_id',
                );
    $app_setting_res = get_app_settings($setting_keys);

     $immediate_origin = $pdo->selectOne("SELECT AES_DECRYPT(setting_value,'" . $CREDIT_CARD_ENC_KEY . "') as acctNo FROM app_settings WHERE setting_key = 'immediate_origin'");
    $app_setting_res["immediate_origin"] = $immediate_origin["acctNo"];

    $content = "";
    if (count($rows) > 0) {
        if ($country == 'USA') {
        /* ----------- USA FILE FORMAT START --------------- */
            $transdate = date('ymd'); // TRANSMISSION DATE 24:29
            $transtime = date('Hi'); // TRANSMISSION TIME 30:33

            $company_id = $app_setting_res["immediate_origin"]; //IMMEDIATE ORIGIN
            $immediate_destination = $app_setting_res["immediate_destination"];  // IMMEDIATE DESTINATION

            $company_name = $app_setting_res["immediate_origin_name"]; // IMMEDIATE ORIGIN NAME
            $disp_company_name = $company_name . str_repeat(" ", (25 - strlen($company_name)));

            // FILE HEADER RECORD (1)
            $content .= "101 " . $immediate_destination . " " . $company_id . $transdate . $transtime . "B094101". $app_setting_res["immediate_destination_name"] . $disp_company_name . "        \r\n";
             
            $comp_entry_desc = $app_setting_res["company_entry_description"]; // COMPANY ENTRY DESCRIPTION
             
            $comp_dist_date = get_working_business_day(); // COMPANY DESCRIPTIVE DATE
            $elect_entry_date = get_working_business_day(); // EFFECTIVE ENTRY DATE
           
            $orginate_dfi_id = $app_setting_res["originating_dfi_id"]; // ORIGINATING DFI ID
            
            $disp_company_name = $company_name . str_repeat(" ", (16 - strlen($company_name))); // COMPANY NAME
            $batchNumber = sprintf('%07d', $totalBatch);

            //BATCH HEADER RECORD (5)
             $content .= "5200" . $disp_company_name . $company_id . "PPD" . $comp_entry_desc . $comp_dist_date . $elect_entry_date . "   1" . $orginate_dfi_id . $batchNumber . "\r\n";

            $i = 1;
            $total = 0;
            $entry_hash = 0;
            $total_amount = 0;
            foreach ($rows as $value) {
                $incrment_id = sprintf('%07d', $i);
                 
                $bank_number = sprintf('%09d', $value['routing_number']); //bank_number
                $entry_hash = $entry_hash + array_sum(str_split(substr($bank_number, 0, 9)));

                $account_number = $value['account_number']; // DFI ACCOUNT NUMBER
                $dist_account_number = $account_number . str_repeat(" ", (17 - strlen($account_number)));
                
                // DOLLAR AMOUNT (30:39)
                $amount = str_replace('.', '', number_format($value['total_amount'], 2, ".", ""));
                $disp_amount = sprintf('%010d', $amount);
                $total_amount = $total_amount + $value['total_amount'];
                 
                $rep_id = $value['rep_id']; // INDIVIDUAL ID
                $disp_rep_id = $rep_id . str_repeat(" ", (15 - strlen($rep_id)));

                // INDIVIDUAL NAME
                if ($value['account_name'] != "") {
                    $account_name = html_entity_decode($value['account_name']);
                } else {
                    $account_name = $value['name'];
                }

                $account_name = preg_replace('/[^A-Za-z0-9 ,]/', '', $account_name);

                $account_name = strlen($account_name) > 22 ? substr($account_name, 0, 22) : $account_name;
                $disp_account_name = $account_name . str_repeat(" ", (22 - strlen($account_name)));

                // TRACE NUMBER
                $trace_number = $orginate_dfi_id . $incrment_id;
                $disp_trace_number = $trace_number . str_repeat(" ", (16 - strlen($trace_number)));

                $content .= "622" . $bank_number . $dist_account_number . $disp_amount . $disp_rep_id . $disp_account_name . "  0" . $disp_trace_number . "\r\n";
           
                $i++;
                $total++;
            }
            
            $dist_total_rec = sprintf('%06d', $total); // BATCH CONTROL RECORD
            
            $disp_total_amount = sprintf('%012d', str_replace('.', '', number_format($total_amount, 2, ".", ""))); // TOTAL CREDIT AMOUNT
            
            $disp_entity_hash = sprintf('%010d', $entry_hash); // ENTITY HASH

            //RESERVED
            $reserved_space = str_repeat(" ", 25);
            
            // FILE CONTROL RECORD (9)
            $content .= "8220" . $dist_total_rec . $disp_entity_hash . $disp_total_amount . sprintf('%012d', 0) . $company_id . $reserved_space . $orginate_dfi_id . $batchNumber . "\r\n";

            $batch_count = sprintf('%07d', 1); //BATCH COUNT

            $block_count = sprintf('%06d', ($total + 4)); //BLOCK COUNT

            $dist_total_rec = sprintf('%08d', $total); // ENTRY/ ADDENDA COUNT
            
            $reserved_space = str_repeat(" ", 39); //RESERVED

            $content .= "9" . $batch_count . $block_count . $dist_total_rec . $disp_entity_hash . sprintf('%012d', 0) . $disp_total_amount . $reserved_space . "\r\n";
        /* ----------- USA FILE FORMAT END --------------- */
        } else {
            $content .= "Rep ID\t\t\t Name\t\t\t Amount\t\t\tCountry\n";
            foreach ($rows as $value) {
                $rep_id = $value['rep_id'];
                $name = $value['name'];
                $amount = displayAmount($value['total_amount'], 2, $country);
                $cntry = $value['country_name'];
                $content .= "$rep_id\t\t\t$name\t\t\t$amount\t\t\t$cntry\n";
            }
        }
    }
    return $content;
}

function get_working_business_day()
{
    $day = strtolower(date('D', strtotime('+1 day')));
    $date = date('ymd', strtotime('+1 day'));
    if ($day == 'sat') {
        $date = date('ymd', strtotime('+3 day'));
    } else if ($day == 'sun') {
        $date = date('ymd', strtotime('+1 day'));
    }

    $date = date('ymd', strtotime('+2 day'));
    return $date;
}

 
?>
