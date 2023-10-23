<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/function.class.php';
//  Task EL8-1300 generate schedule class date for next 1 year . 
$function_list = new functionsList();
$month_mid = $month_last = $first_date = $last_date = '';
$date_arr = [];
$sql_group_classes = "SELECT id,pay_period FROM group_classes WHERE is_deleted='N' GROUP BY id";
$res_group_classes = $pdo->select($sql_group_classes);
if(!empty($res_group_classes)){

    foreach($res_group_classes as $res){

        if(!empty($res['id'])){
            $arr_date  = [];
            $class_id = $res['id'];

            $sql_grp_cls_paydates = "SELECT * FROM group_classes_paydates WHERE class_id=:class_id and is_deleted='N' GROUP BY id ORDER BY id DESC LIMIT 1";
            $res_grp_cls_paydates = $pdo->selectOne($sql_grp_cls_paydates,array(":class_id" => $class_id));
            
            if(!empty($res_grp_cls_paydates)){
                $paydate = $res_grp_cls_paydates['paydate'];

                if(strtotime($paydate) > strtotime('+3 months') && strtotime($paydate) < strtotime('+4 months')){

                    $grp_class_id = $res_grp_cls_paydates['class_id'];
                    $group_id = $res_grp_cls_paydates['group_id'];
                    $pay_period = $res_grp_cls_paydates['pay_period'];
                    
                    if(!empty($paydate)){
                        if($pay_period == 'Monthly'){
                            // start Monthly class date added to 1 year
                            $startDate = new DateTime($paydate);
                            
                            for($i=0; $i <= 12; $i++) {
                                $numberOfMonthsToAdd = $i;
                                $startDateDay = (int) $startDate->format('j');
                                 $startDateMonth = (int) $startDate->format('n');
                                 $startDateYear = (int) $startDate->format('Y');
                             
                                 $numberOfYearsToAdd = floor(($startDateMonth + $numberOfMonthsToAdd) / 12);
                                 if ((($startDateMonth + $numberOfMonthsToAdd) % 12) === 0) {
                                   $numberOfYearsToAdd--;
                                 }
                                 $year = $startDateYear + $numberOfYearsToAdd;
                             
                                 $month = ($startDateMonth + $numberOfMonthsToAdd) % 12;
                                 if ($month === 0) {
                                   $month = 12;
                                 }
                                 $month = sprintf('%02s', $month);
                             
                                 $numberOfDaysInMonth = (new DateTime("$year-$month-01"))->format('t');
                                 $day = $startDateDay;
                                 if ($startDateDay > $numberOfDaysInMonth) {
                                   $day = $numberOfDaysInMonth;
                                 }
                                 $day = sprintf('%02s', $day);
                             
                                 $month_date =  new DateTime("$year-$month-$day");
                                 $date_format =   $month_date->format('Y-m-d');
                                 $arr_date[] = $date_format;
                            }
                            
                            // end Monthly class date added to 1 year
    
                        }else if($pay_period == 'Bi-Weekly'){
                         
                            // start Bi-Weekly class date added to 1 year
                            $start_date = date('Y-m-d', strtotime('+14 day', strtotime($paydate)) );
                            $end_date = date('Y-m-d', strtotime('+1 year', strtotime($paydate)) );
                            $begin = new DateTime($start_date);
                            $end   = new DateTime($end_date);
    
                            for($i = $begin; $i <= $end; $i->modify('+14 day')){
                                $date = $i->format("Y-m-d");
                                $arr_date[] = $date;
                            }
                            // end Bi-Weekly class date added to 1 year
    
                        }else if($pay_period == 'Weekly'){
                           
                            // start Weekly class date added to 1 year
                            $start_date = date('Y-m-d', strtotime('+7 day', strtotime($paydate)) );
                            $end_date = date('Y-m-d', strtotime('+1 year', strtotime($paydate)) );
                            $begin = new DateTime($start_date);
                            $end   = new DateTime($end_date);
    
                            for($i = $begin; $i <= $end; $i->modify('+7 day')){
                                $date = $i->format("Y-m-d");
                                $arr_date[] = $date; 
                            }
                            // end Weekly class date added to 1 year
    
                        }else if($pay_period == 'Semi-Monthly'){
                            $sql_cls_paydates = "SELECT id,paydate FROM group_classes_paydates WHERE class_id=:class_id and is_deleted='N' GROUP BY id ORDER BY id DESC LIMIT 2";
                            $res_cls_paydates = $pdo->select($sql_cls_paydates,array(":class_id" => $grp_class_id));

                            $first_date_arr = $res_cls_paydates[1]['paydate'];
                            $second_date_arr = $res_cls_paydates[0]['paydate'];

                            if(date('m', strtotime($first_date_arr)) < date('m', strtotime($second_date_arr))){
                                $date_first = new DateTime($first_date_arr);
                                $date_first->modify('first day of next month');
                                $first_date_pay = $date_first->format("Y-m-d");
                                $second_date_pay = $second_date_arr;
                            }else{
                                $first_date_pay = $first_date_arr;
                                $second_date_pay = $second_date_arr;
                            }
                            
                            // start Semi-Monthly class date added to 1 year
                            $date_of_first = date('d', strtotime($first_date_pay));
                            $date_of_second = date('d', strtotime($second_date_pay));
                            $start_date = date("Y-m-d",strtotime($first_date_pay." +1month"));
                            $y = 1;
                            $period = 12;

                            for ($y; $y <= $period; $y++) {
                                $month_mid = date("Y-m-".$date_of_first, strtotime($start_date));
                                $month_last = date("Y-m-".$date_of_second, strtotime($start_date));
                                if(!empty($month_mid)){
                                    $first_date = $function_list->checkHoliday($month_mid);
                                } 
                                if(!empty($month_last)){
                                    $last_date = $function_list->validateDate($month_last);
                                }
                                array_push($arr_date, $month_mid, $last_date);
                                $start_date = date("m/d/Y",strtotime($start_date." +1month"));
                            } 
                            // end Semi-Monthly class date added to 1 year
                        }
                        if(!empty($arr_date)){
                            foreach($arr_date as $res){
                                $insert_params = array(
                                    'class_id' => $grp_class_id,
                                    'group_id'=> $group_id,
                                    'pay_period' => $pay_period,
                                    'paydate' => $res,
                                );
                            $class_pay_date = $pdo->insert('group_classes_paydates',$insert_params);
                            }
                        }
                    }
                }
            }
        }
    }
}
echo "Complete";
dbConnectionClose();
exit;
?>