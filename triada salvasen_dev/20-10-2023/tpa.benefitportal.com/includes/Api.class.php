<?php
class Api {
    private $postData, $API_TOKEN = null;

    public function __construct() {
        global $API_TOKEN;
        $this->API_TOKEN = $API_TOKEN;
    }

    /**
     * this is used to call api and get data from passed url
     * @param $urlKey is required for get url
     * @param $postData is pass required data to api
     * */
    public function ajaxApiCall($postData, $jsonDecode = false){
        global $HOST;
        $valueReplace = false;
        $this->postData = $postData;
        $header = array(
            "Content-type: application/json",
            "Authorization: Bearer ".$this->API_TOKEN
        );

        $urlKey = !empty($this->postData['api_key']) ? $this->postData['api_key'] : '';
        $api_url = $this->getUrl($urlKey);
        if(empty($api_url)){
            return json_encode(array('status'=>'fail','message'=>"Url not found."),true);
        }
        $apiUrl = $api_url['url']; 
        $method = $api_url['method'];
        $count = substr_count($apiUrl,'/{');
        for ($i=1;$i<=$count;$i++){
            $parsed = $this->getStringBetweenPosition($apiUrl, '{', '}',$i);
            if(array_key_exists($parsed,$postData) && !empty($postData[$parsed])){
                $apiUrl = str_replace($parsed,$postData[$parsed],$apiUrl);
                $valueReplace = true;
            }
        }
        if($valueReplace){
            $apiUrl = str_replace(["{","}"],["",""],$apiUrl);
        }
        $postData['BROWSER'] = getBrowser();
        $postData['OS'] = getOS($_SERVER['HTTP_USER_AGENT']);
        $postData['REQ_URL'] = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        $postData['HOST'] = $HOST;
        $postData['REAL_IP_ADDRESS'] = get_real_ipaddress();
        //pre_print($apiUrl);
        $data = json_encode($postData);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $method);
        // curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);    
        
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $apiResponse = curl_exec($ch);
        if(!empty($postData['customError'])){
            pre_print($apiResponse,false);
            $apiError = curl_error($ch);
            pre_print($apiError);
        }
        curl_close($ch);
        if($jsonDecode){
            $apiResponse = json_decode($apiResponse,true);
        }
        return $apiResponse;
    }

    public function paginate($dataList,$pageName){

        $current_page = isset($dataList['current_page']) ? $dataList['current_page'] : ''; 
        $first_page_url = isset($dataList['first_page_url']) ? $dataList['first_page_url'] : ''; 
        $from = isset($dataList['from']) ? $dataList['from'] : '';
        $last_page = isset($dataList['last_page']) ? $dataList['last_page'] : ''; 
        $last_page_url = isset($dataList['last_page_url']) ? $dataList['last_page_url'] : ''; 
        $links = isset($dataList['last_page_url']) ? $dataList['last_page_url'] : array(); 
        $next_page_url = isset($dataList['next_page_url']) ? $dataList['next_page_url'] : '';
        $custom_next_page = ($next_page_url != '' ? ($current_page+1) : '');
        $nextPage = ($next_page_url == '' ? 'javascript:void(0);' : $pageName);
        $nextDisabled = empty($next_page_url) ? 'disabled' : '';
        $nexrStop = ($nextDisabled != '' ? "onclick='event.stopPropagation();'" : '');

        $path = isset($dataList['path']) ? $dataList['path'] : '';
        $per_page = isset($dataList['per_page']) ? $dataList['per_page'] : '';
        $prev_page_url = isset($dataList['prev_page_url']) ? $dataList['prev_page_url'] : '';
        $to = isset($dataList['to']) ? $dataList['to'] : '';
        $total = isset($dataList['total']) ? $dataList['total'] : '';
        $prevDisabled = empty($prev_page_url) ? 'disabled' : '';
        $custom_prev_page = ($prevDisabled == '' ? ($current_page-1) : '');
        $prevPage = ($prevDisabled != '' ? 'javascript:void(0);' : $pageName);
        $preStop = ($prevDisabled != '' ? "onclick='event.stopPropagation();'" : '');
        $total_pages = isset($dataList['last_page']) ? $dataList['last_page'] : '';

        $select =" <li class='live-link theme-form pr'>
        <select class='form-control api_pagination_select' data-size='3'>";
        for ($i=1; $i <= $total_pages; $i++) {
        $is_selected = ($current_page == $i) ? "selected" : "";
        $select .= "<option value='".$i."' data-page_url='".$pageName."' data-page='".$i."' ".$is_selected.">".$i."</option>";
        }
        $select .= "</select> </li>";

        $paginageLinks = '
            <div class="pull-left">
                <input type="hidden" name="page" value="'.$current_page.'" id="page">
                <span class="m-l-10 m-t-10"> '.$from.' to '.$to.' of '.$total.'</span>
            </div>
            <div class="pull-right">
                <div class="paging_bs_new">
                <ul class="pagination">
                    <li class="prev '.$prevDisabled.'" '.$preStop.'>
                        <a href="'.$prevPage.'" data-page="'.$custom_prev_page.'"><</a>
                    </li>
                    '.$select.'
                    <li class="'.$nextDisabled.'" '.$nexrStop.'>
                        <a href="'.$nextPage.'" data-page="'.$custom_next_page.'">
                            >
                        </a>
                    </li>
                </ul>
                </div>
            </div>
        ';

        return ['links'=>$paginageLinks,'per_page'=>$per_page];
    }

    /**
     * used for get url using key
     *  */
    protected function getUrl($urlKey){
        global $SITE_ENV;
        require dirname(__DIR__) .'/includes/apiUrlKey.php';
        if(!empty($urlKey)){
            return !empty($API_URL_KEY[$urlKey]) ? $API_URL_KEY[$urlKey] : "";
        }
    }

    protected function getStringBetweenPosition($string, $start, $end, $pos){
        $cPos = 0;
        $ini = 0;
        $result = '';
        for($i = 0; $i < $pos; $i++){
            $ini = strpos($string, $start, $cPos);
            if ($ini == 0) return '';
            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            $result = substr($string, $ini, $len);
            $cPos = $ini + $len;
        }
        return $result;
    }

    public function sendTriggerMail($apiKey,$params){
        global $pdo, $ENROLLMENT_WEBSITE_HOST;

        $mail_data = array();

        if(!empty($params['displayID'])){
            $trigger_row = $pdo->selectOne("SELECT * FROM triggers WHERE display_id=:display_id",[':display_id' => $params['displayID']]);
        }

        if(!empty($params['userID']) && !empty($params['userType'])){
            $smart_tags = get_user_smart_tags($params['userID'],$params['userType']);
        }

        if($apiKey == 'pageBuilderContactDetails'){
            $url_link = $ENROLLMENT_WEBSITE_HOST . '/' . $params['userName'];

            $mail_data['link'] = $url_link;
            $mail_data['name'] = $params['name'];
            $mail_data['email'] = $params['email'];
            $mail_data['phone'] = format_telephone($params['phone']);
            $mail_data['Comment'] = $params['comment'];
                        
            if(!empty($smart_tags)){
                $mail_data = array_merge($mail_data,$smart_tags);
            }
            
            $to_email = explode(';',$params['contactUsEmails']);
        }

        if (!empty($trigger_row) && !empty($to_email)) {
            trigger_mail($trigger_row['id'],$mail_data,$to_email);
        }
    }
}

?>