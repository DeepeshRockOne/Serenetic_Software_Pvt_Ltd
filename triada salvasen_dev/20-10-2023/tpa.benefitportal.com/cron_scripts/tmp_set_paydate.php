<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/function.class.php';

$arr_date = [];
$paydatesql = "SELECT c.id,c.rep_id,gc.class_name,gc.pay_period,gcp.paydate,gc.id AS class_id
    FROM customer c
    JOIN group_classes gc ON(c.id=gc.group_id AND gc.pay_period='Monthly' AND gc.is_deleted='N')
    LEFT JOIN group_classes_paydates gcp ON(gcp.group_id=c.id AND gcp.class_id = gc.id AND gcp.is_deleted='N')
    WHERE c.is_deleted='N' AND  c.type='Group' AND gcp.id IS NULL GROUP BY gc.id  ORDER BY c.id";
$paydateres = $pdo->select($paydatesql);
$paydate = '2023-05-05';
$startDate = new DateTime($paydate);
for($i=0; $i <= 12; $i++) {
    $numberOfMonthsToAdd = $i;
    $startDateDay = $startDate->format('j');
     $startDateMonth = $startDate->format('n');
     $startDateYear = $startDate->format('Y');

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
if(!empty($arr_date)){
    foreach($paydateres as $res){
        foreach($arr_date as $date){
            $insert_params = array(
                'class_id' => $res['class_id'],
                'group_id'=> $res['id'],
                'pay_period' => $res['pay_period'],
                'paydate' => $date,
            );
         $class_pay_date = $pdo->insert('group_classes_paydates',$insert_params);                                                               
        }
    }
}
echo "Completed";
?>
