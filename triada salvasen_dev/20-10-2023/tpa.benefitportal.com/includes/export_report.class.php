<?php
include_once dirname(__DIR__) . "/includes/functions.php";
include_once dirname(__DIR__) . "/includes/Validation.php";
include_once dirname(__DIR__) . "/includes/reports.php";
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

/*
 * Class for Export Report
 */
class ExportReport {
    private $pdo;
    private $REPORT_DB;
    private $validate;
    private $report_id;
    private $report_key = '';
    private $report_name;
    private $export_file_report_name;
    private $report_row;
    private $user_id = '';
    private $user_type = '';
    private $user_rep_id = '';
    private $user_profile_page = '';
    private $timezone = '';
    private $file_type = 'EXCEL';
    private $report_location = "set_report";
    private $incr = '';
    private $sch_params = array();
    private $extra_params = array();
    private $report_api_url = '';
	private $join_range = '';
	private $fromdate = '';
	private $todate = '';
	private $added_date = '';
	private $post_data = array();
	private $check_validation = true;

    public function __construct($report_id,$config_arr = array()) {
        global $pdo,$REPORT_DB,$AWS_REPORTING_URL;
        $this->pdo = $pdo;
        $this->REPORT_DB = $REPORT_DB;
        $this->validate = new Validation();
        foreach ($config_arr as $key => $value) {
        	if(isset($this->{$key})) {
        		$this->{$key} = $value;
        	}
        }
        if($report_id === 0) {
        	$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE report_key=:report_key",array(':report_key' => $this->report_key));	
        } else {
        	$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(':id' => $report_id));	
        }        
        if(!empty($report_row)) {
        	$this->report_id = $report_row['id']; //MD5
        	$this->report_key = $report_row['report_key'];
        	$this->report_name = $report_row['report_name'];
        	$this->export_file_report_name = $report_row['export_file_report_name'];
        	$this->report_row = $report_row;
        	$this->report_api_url = isset($AWS_REPORTING_URL[$this->report_key])?$AWS_REPORTING_URL[$this->report_key]:'';
        }
        $this->extra_params['report_id'] = $this->report_id;
        $this->extra_params['timezone'] = $this->timezone;

        $this->post_data = array_merge($_POST,$_GET);
        $this->join_range = isset($this->post_data['join_range'])?$this->post_data['join_range']:"";
		$this->fromdate = isset($this->post_data["fromdate"])?$this->post_data["fromdate"]:"";
		$this->todate = isset($this->post_data["todate"])?$this->post_data["todate"]:"";
		$this->added_date = isset($this->post_data["added_date"])?$this->post_data["added_date"]:"";
		$this->join_range = strtolower($this->join_range);

		/*pre_print($config_arr,false);
		pre_print($this->incr,false);
		pre_print($this->sch_params);*/
    }

    public function run() {
    	if(empty($this->report_row)) {
    		return array('status' => 'report_not_found', 'message' => 'Report not found');
    	}

    	if($this->report_key == "admin_export") {
    		return $this->admin_export();

    	} elseif($this->report_key == "admin_history") {
    		return $this->admin_history();

    	} elseif($this->report_key == "agent_export") {
    		return $this->agent_export();

    	} elseif($this->report_key == "agent_history") {
    		return $this->agent_history();

    	} elseif($this->report_key == "agent_license") {
    		return $this->agent_license();
    	
    	} elseif($this->report_key == "agent_merchant_assignment") {
    		return $this->agent_merchant_assignment();

    	} elseif($this->report_key == "agent_quick_sales_summary") {
    		return $this->agent_quick_sales_summary();
    	} elseif($this->report_key == "agent_eo_coverage") {
    		return $this->agent_eo_coverage();
    	} elseif(in_array($this->report_key,array("agent_interactions","member_interactions","group_interactions"))) {
    		return $this->user_interactions();
    	
    	} elseif($this->report_key == "agent_new_business_post_payments") {
    		return $this->agent_new_business_post_payments();
    		
    	} elseif($this->report_key == "agent_declines_summary") {
    		return $this->agent_declines_summary();
    		
    	} elseif($this->report_key == "agent_p2p_comparison") {
    		return $this->agent_p2p_comparison();

    	} elseif($this->report_key == "agent_member_persistency") {
    		return $this->agent_member_persistency();
    	
    	} elseif($this->report_key == "agent_debit_balance" || $this->report_key == "admin_agent_debit_balance") {
    		return $this->agent_debit_balance();

    	} elseif($this->report_key == "agent_debit_ledger" || $this->report_key == "admin_agent_debit_ledger") {
    		return $this->agent_debit_ledger();

    	} elseif($this->report_key == "agent_monthly_forecasting") {
			return $this->agent_monthly_forecasting();
		} elseif($this->report_key == "agent_product_persistency" || $this->report_key == "admin_product_persistency") {
			return $this->agent_product_persistency();
		} elseif($this->report_key == "payables_export") {
			return $this->payables_export();
		} elseif($this->report_key == "daily_order_summary") {
			return $this->daily_order_summary();
		} elseif($this->report_key == "top_performing_agency") {
			return $this->top_performing_agency();
        } elseif($this->report_key == "admin_next_billing_date") {
			return $this->admin_next_billing_date();
        } elseif($this->report_key == "admin_new_business_post_payments_org") {
			return $this->admin_new_business_post_payments_org();
        } elseif($this->report_key == "admin_payment_outstanding_renewals") {
			return $this->admin_payment_outstanding_renewals();
        } elseif($this->report_key == "admin_payment_transaction_report") {
			return $this->admin_payment_transaction_report();
        } elseif($this->report_key == "admin_payment_failed_payment_recapture_analytics") {
			return $this->admin_payment_failed_payment_recapture_analytics();
        } elseif($this->report_key == "admin_payment_reversal_transactions") {
			return $this->admin_payment_reversal_transactions();
        } elseif($this->report_key == "admin_payment_p2p_renewal_comparison") {
			return $this->admin_payment_p2p_renewal_comparison();
        } elseif($this->report_key == "platform_pmpm") {
			return $this->platform_pmpm();
		} elseif($this->report_key == "participants_pmpm") {
			return $this->participants_pmpm();
		} elseif(in_array($this->report_key,array("payment_policy_overview","member_summary"))) {
			return $this->payment_policy_overview();
		} elseif(in_array($this->report_key,array("member_verifications","member_paid_through"))) {
			return $this->member_verifications();
		} elseif($this->report_key == "member_age_out") {
			return $this->member_age_out();
		} elseif($this->report_key == "member_history") {
    		return $this->member_history();
    	} elseif($this->report_key == "list_bill_overview") {
    		return $this->list_bill_overview();
    	} elseif($this->report_key == "list_bill_overview_export") {
    		return $this->list_bill_overview_export();
    	} elseif($this->report_key == "member_product_cancellations") {
    		return $this->member_product_cancellations();

    	} elseif($this->report_key == "admin_member_persistency") {
    		return $this->admin_member_persistency();
    	}elseif($this->report_key == "life_insurance_beneficiaries") {
    		return $this->life_insurance_beneficiaries();
    	}elseif($this->report_key == "advance_funding" || $this->report_key == "advance_collection" ) {
    		return $this->advance_funding();
    	}elseif($this->report_key == "commission_setup") {
    		return $this->commission_setup();
    	}elseif($this->report_key == "debit_balance_overview") {
    		return $this->debit_balance_overview();
    	}elseif($this->report_key == "product_overview") {
    		return $this->product_overview();
    	}elseif(in_array($this->report_key,array('carrier_overview','membership_overview','vendor_overview'))) {
    		return $this->carrier_overview();
    	}elseif($this->report_key == "group_summary") {
    		return $this->group_summary();
    	}elseif($this->report_key == "group_summary_export") {
    		return $this->group_summary_export();
    	}elseif($this->report_key == "group_history") {
    		return $this->group_history();
    	}elseif($this->report_key == "group_history_export") {
    		return $this->group_history_export();
    	}elseif($this->report_key == "lead_summary") {
    		return $this->lead_summary();
    	}elseif($this->report_key == "participants_summary") {
    		return $this->participants_summary();
    	}elseif($this->report_key == "payables_reconciliation") {
    		return $this->payables_reconciliation();
    	}elseif($this->report_key == "payment_nb_sales" || $this->report_key == "payment_rb_sales") {
				return $this->payment_nb_sales();
		}elseif($this->report_key == "group_full_coverage") {
			return $this->group_full_coverage();
		}elseif($this->report_key == "group_full_coverage_gp") {
			return $this->group_full_coverage_gp();
		}elseif($this->report_key == "group_enroll_overview") {
			return $this->group_enroll_overview();
		}elseif($this->report_key == "group_enroll_overview_gp") {
			return $this->group_enroll_overview_gp();
		}elseif($this->report_key == "group_member_age_out") {
			return $this->group_member_age_out();
		}elseif($this->report_key == "group_member_age_out_gp") {
			return $this->group_member_age_out_gp();
		}elseif($this->report_key == "group_change_product") {
			return $this->group_change_product();
		}elseif($this->report_key == "group_change_product_gp") {
			return $this->group_change_product_gp();
		}elseif($this->report_key == "eticket_overview") {
			return $this->eticket_overview();
		}elseif($this->report_key == "eticket_script") {
			return $this->eticket_script();
		}
    }

    private function check_validation() {
    	if($this->check_validation == false) {
    		return array('status' => 'success', 'message' => 'Skipped Validation Checking');
    	}

    	if (!$this->validate->isValid()) {
    		if(!empty($this->validate->getError('custom_error'))) {
    			return array("status" => "custom_error","message"=>$this->validate->getError('custom_error'));
    		} else {
    			return array('status' => 'fail', 'errors' => $this->validate->getErrors());
    		}
    	} else {   		
    		return array('status' => 'success', 'message' => 'Validation checked success');
    	}
    }

    private function admin_export() {
    	$display_id = isset($this->post_data['display_id'])?$this->post_data['display_id']:array();

		if(empty($display_id)) {
			$this->validate->setError('display_id',"Please select at least one Admin");
		}
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($display_id)) {
				$display_id = array_map('trim',$display_id);
				$this->incr .= " AND display_id IN ('".implode("','",$display_id)."')";
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    }

    private function admin_history() {
    	$admin_id = isset($this->post_data['admin_id'])?$this->post_data['admin_id']:'';

		if(empty($admin_id)) {
			$this->validate->setError('admin_id',"Please select Admin");
		}

		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->incr .= " AND md5(user_id)=:id";
    		$this->sch_params[':id'] = md5($admin_id);

			$this->setdateRangeAF();

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    }

    private function agent_export() {
    	$display_id = isset($this->post_data['display_id'])?$this->post_data['display_id']:array();
    	$report_type = isset($this->post_data['report_type']) ? $this->post_data['report_type']:'masked';

    	$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->export_file_report_name = strtoupper($report_type).' '.$this->export_file_report_name;

			if (!empty($display_id)) {
				$display_id = array_unique(array_map('trim',$display_id));
				$this->incr .= " AND c.id IN ('".implode("','",$display_id)."')";
			}

			$this->extra_params['report_type'] = $report_type;

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    	
    }

    private function agent_history() {
        $agent_id = isset($this->post_data['agent_id'])?$this->post_data['agent_id']:'';

        if(empty($agent_id)) {
            $this->validate->setError('agent_id',"Please select Agent");
        }

        $validate_res =  $this->check_validation();
        if($validate_res['status'] == "success") {
            $this->sch_params[':id'] = md5($agent_id);

            if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
                $this->setdateRangeAF();
            }
            return $this->add_export_request();
        } else {
            return $validate_res;
        }
    }

    private function agent_license() {
    	$agent_ids = isset($this->post_data['agent_ids'])?$this->post_data['agent_ids']:'';
    	$license_state = isset($this->post_data['license_state']) ? $this->post_data['license_state'] : array();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($agent_ids)) {
				$agent_ids = array_unique(array_map('trim',$agent_ids));
				$this->incr .= " AND c.id IN (".implode(",",$agent_ids).")";
			}
			if(!empty($license_state)) {
				$licenseArr = implode("','",$license_state);
				$this->incr .= " AND (l.new_selling_licensed_state IN('".$licenseArr."') OR l.selling_licensed_state IN('".$licenseArr."'))";
			}

			if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
				$this->setdateRangeAF(false,'l.created_at');
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    }

    private function agent_merchant_assignment() {
    	$agent_ids = isset($this->post_data['agent_ids']) ? $this->post_data['agent_ids']:'';
		$merchant_processor = isset($this->post_data['merchant_processor']) ? $this->post_data['merchant_processor']:'';
			$this->extra_params['processor_incr'] = '';
			$processor_incr = '';

			if($this->join_range == "range") {
				if ($this->fromdate != "") {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->fromdate));
					$processor_incr .= " AND (DATE(p.created_at) >= :from_date OR DATE(pmaa.created_at) >= :from_date) ";
				}
				if ($this->todate != "") {
					$this->sch_params[':to_date'] = date('Y-m-d', strtotime($this->todate));
					$processor_incr .= " AND (DATE(p.created_at) <= :from_date OR DATE(pmaa.created_at) <= :from_date) ";
				}
			} else {
				if ($this->added_date != "") {
					if($this->join_range == 'exactly') {
						$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
						$processor_incr .= " AND (DATE(p.created_at) = :from_date OR DATE(pmaa.created_at) = :from_date) ";
		
					} else if($this->join_range == 'before') {
						$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
						$processor_incr .= " AND (DATE(p.created_at) < :from_date OR DATE(pmaa.created_at) < :from_date) ";
		
					} else if($this->join_range == 'after') {
						$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
						$processor_incr .= " AND (DATE(p.created_at) > :from_date OR DATE(pmaa.created_at) > :from_date) ";
					}
				}
			}

			if (!empty($agent_ids)) {
				$agent_ids = array_unique(array_map('trim',$agent_ids));
				$processor_incr .= " AND c.id IN (".implode(",",$agent_ids).")";
			}
			if(!empty($merchant_processor)) {
				$processor_incr .= " AND p.id IN(".implode(',',$merchant_processor).") ";
			}

			if(!empty($processor_incr)) {
				$this->extra_params['processor_incr'] = $processor_incr ;
			}
			return $this->add_export_request();
    }

    private function agent_quick_sales_summary() {
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->setdateRange('t.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_declines_summary() {
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->setdateRange('t.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_p2p_comparison() {
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->setdateRange('t.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_new_business_post_payments() {
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->setdateRange('o.post_date');
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_new_business_post_payments_org() {
    	$added_or_post_payment_date = isset($this->post_data['added_or_post_payment_date'])?$this->post_data['added_or_post_payment_date']:'';
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();

    	if(empty($added_or_post_payment_date)) {
    		$this->validate->setError('added_or_post_payment_date',"Please select an option");
    	}

    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}

			if($added_or_post_payment_date == "added_date") {
				$this->setdateRange('t.created_at');
				$period_text = "Added Date - ";
			} else {
				$this->setdateRange('o.post_date');
				$period_text = "Post Payment Date - ";
			}
			$period_text .= $this->getPeriodText(); 
			$this->extra_params['period_text'] = $period_text; 

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_payment_outstanding_renewals() {
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			$this->setdateRange('ws.next_purchase_date');
			
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_payment_transaction_report() {
    	$transaction_or_effective_date = isset($this->post_data['transaction_or_effective_date'])?$this->post_data['transaction_or_effective_date']:'';
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();
    	$product_ids = isset($_POST['product_ids']) ? $_POST['product_ids']:array();

    	if(empty($transaction_or_effective_date)) {
    		$this->validate->setError('transaction_or_effective_date',"Please select an option");
    	}
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}

			if(!empty($transaction_or_effective_date)) {
				if($transaction_or_effective_date == "transaction_date") {
					$period_text = "Transaction Date - ";
					$this->setdateRange('t.created_at');
				} else if($transaction_or_effective_date == "effective_date") {
					$period_text = "Effective Date - ";
					$this->setdateRange('ws.eligibility_date');	
				}else if($transaction_or_effective_date == "coverage_period"){
					$period_text = "Coverage Period - ";
					$this->setdateRangeForCoverage('od.start_coverage_period','od.end_coverage_period');
				}
				$period_text .= $this->getPeriodText();
				$this->extra_params['period_text'] = $period_text; 
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_payment_failed_payment_recapture_analytics() {
    	$transaction_or_effective_date = isset($this->post_data['transaction_or_effective_date'])?$this->post_data['transaction_or_effective_date']:'';
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();
    	$product_ids = isset($_POST['product_ids']) ? $_POST['product_ids']:array();

    	if(empty($transaction_or_effective_date)) {
    		$this->validate->setError('transaction_or_effective_date',"Please select an option");
    	}
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}

			if($transaction_or_effective_date == "transaction_date") {
				$period_text = "Transaction Date - ";
				$this->setdateRange('t.created_at');
			} else {
				$period_text = "Effective Date - ";
				$this->setdateRange('ws.eligibility_date');	
			}
			$period_text .= $this->getPeriodText();
			$this->extra_params['period_text'] = $period_text; 

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_payment_reversal_transactions() {
    	$transaction_or_effective_date = isset($this->post_data['transaction_or_effective_date'])?$this->post_data['transaction_or_effective_date']:'';
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();
    	$product_ids = isset($_POST['product_ids']) ? $_POST['product_ids']:array();

    	if(empty($transaction_or_effective_date)) {
    		$this->validate->setError('transaction_or_effective_date',"Please select an option");
    	}
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}

			if(!empty($transaction_or_effective_date)) {
				if($transaction_or_effective_date == "transaction_date") {
					$period_text = "Transaction Date - ";
					$this->setdateRange('t.created_at');
				} else {
					$period_text = "Effective Date - ";
					$this->setdateRange('ws.eligibility_date');	
				}
				$period_text .= $this->getPeriodText();
				$this->extra_params['period_text'] = $period_text; 
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function admin_payment_p2p_renewal_comparison() {
    	$agent_ids = isset($_POST['agent_ids']) ? $_POST['agent_ids']:array();
    	$product_ids = isset($_POST['product_ids']) ? $_POST['product_ids']:array();
        $sale_type = isset($_POST['sale_type']) ? $_POST['sale_type']:'';

    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}

            if(!empty($sale_type)) {
                $this->extra_params['sale_type'] = $sale_type;
            }

			$this->setdateRange('t.created_at');

			$period_text = $this->getPeriodText();
			$this->extra_params['period_text'] = $period_text; 

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}
	
	private function platform_pmpm() {
		$select_month = isset($this->post_data['select_month'])?$this->post_data['select_month']:'';

		if(empty($select_month)){
			$this->validate->setError("select_month","Please select Any Month");
		}
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if (!empty($select_month)) {
				$this->extra_params['selected_month'] = $select_month;
			}
						

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function participants_pmpm() {
		$select_month = isset($this->post_data['select_month'])?$this->post_data['select_month']:'';

		if(empty($select_month)){
			$this->validate->setError("select_month","Please select Any Month");
		}
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if (!empty($select_month)) {
				$this->extra_params['selected_month'] = $select_month;
			}
						

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function payment_policy_overview() {

		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$enroll_agent_ids = isset($this->post_data['enroll_agent_ids']) ? $this->post_data['enroll_agent_ids']:array();
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();
		$report_type = isset($this->post_data['report_type']) ? $this->post_data['report_type']:'unmasked';
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if($this->report_key == 'member_summary'){
				$this->export_file_report_name = strtoupper($report_type).' '.$this->export_file_report_name;
			}

			if($this->report_key == 'member_summary'){
				$this->setdateRange('res.joined_date');
			}else{
				$this->setdateRange('w.created_at');
			}

			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($enroll_agent_ids)) {
				$this->extra_params['enroll_agent_ids'] = $enroll_agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}			
			
			$this->extra_params['report_type'] = $report_type;

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function member_verifications() {

		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$verification_method = isset($this->post_data['verification_method']) ? $this->post_data['verification_method']:'';
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();

		$lpd_join_range = isset($this->post_data['lpd_join_range'])?$this->post_data['lpd_join_range']:"";
		$lpd_fromdate = isset($this->post_data["lpd_fromdate"])?$this->post_data["lpd_fromdate"]:"";
		$lpd_todate = isset($this->post_data["lpd_todate"])?$this->post_data["lpd_todate"]:"";
		$lpd_added_date = isset($this->post_data["lpd_added_date"])?$this->post_data["lpd_added_date"]:"";
		$lpd_join_range = strtolower($lpd_join_range);
		
		$this->setdateRangeAFError();
		if($lpd_todate !='' && $lpd_fromdate!='') {
			$no_days=0;
			if($lpd_todate != '' && $lpd_fromdate != '') {
				$date1 = date_create($lpd_fromdate);
				$date2 = date_create($lpd_todate);
				$diff = date_diff($date1,$date2);
				$no_days = $diff->format("%a");
			}
			
			if($no_days > 62) {
				$this->validate->setError('custom_error',"Please enter proper date range. A maximum date range of 60 days is allowed per request.");
			}
		}
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$report_type = '';

			if($this->report_key == 'member_paid_through'){
				$report_type = 'member_paid_through';
			}

			$this->extra_params["report_type"] = $report_type;
			
			$this->setdateRange('c.created_at');

			$ldateIncr = " DATE(ws.last_purchase_date) ";

			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($verification_method)) {
				$this->extra_params['verification_method'] = $verification_method;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}	
			if($lpd_join_range == "range") {
				if ($lpd_fromdate != "") {
					$this->sch_params[':lpd_from_date'] = date('Y-m-d', strtotime($lpd_fromdate));
					$this->incr .= " AND $ldateIncr >= :lpd_from_date";
				}
				if ($lpd_todate != "") {
					$this->sch_params[':lpd_to_date'] = date('Y-m-d', strtotime($lpd_todate));
					$this->incr .= " AND $ldateIncr <= :lpd_to_date";
				}
			} else {
				if ($lpd_added_date != "") {
					if($lpd_join_range == 'exactly') {
						$this->sch_params[':lpd_from_date'] = date('Y-m-d', strtotime($lpd_added_date));
						$this->incr .= " AND $ldateIncr = :lpd_from_date";
	
					} else if($lpd_join_range == 'before') {
						$this->sch_params[':lpd_from_date'] = date('Y-m-d', strtotime($lpd_added_date));
						$this->incr .= " AND $ldateIncr < :lpd_from_date";
	
					} else if($lpd_join_range == 'after') {
						$this->sch_params[':lpd_from_date'] = date('Y-m-d', strtotime($lpd_added_date));
						$this->incr .= " AND $ldateIncr > :lpd_from_date";
					}
				}
			}		

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function member_age_out() {

		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$policy_status = isset($this->post_data['policy_status']) ? $this->post_data['policy_status']:'';
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('ws.created_at');

			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($policy_status)) {
				$this->extra_params['policy_status'] = $policy_status;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}			

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function member_history() {
    	$member_id = isset($this->post_data['member_id'])?$this->post_data['member_id']:'';

		if(empty($member_id)) {
			$this->validate->setError('member_id',"Please select Member");
		}

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->sch_params[':member_id'] = $member_id;
			$this->extra_params['from_report']= 'Y';

			if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
				$this->setdateRangeAF();
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}
	
	private function list_bill_overview() {

		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$group_ids = isset($this->post_data['group_ids']) ? $this->post_data['group_ids']:array();
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('lb.created_at');

			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($group_ids)) {
				$this->extra_params['group_ids'] = $group_ids;
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}
	
	private function list_bill_overview_export() {
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('lb.created_at');

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function lead_summary() {

		$agent_ids = isset($this->post_data['agent_ids']) ? $this->post_data['agent_ids']:array();
		$group_ids = isset($this->post_data['group_ids']) ? $this->post_data['group_ids']:array();
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('l.created_at');

			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($group_ids)) {
				$this->extra_params['group_ids'] = $group_ids;
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

	private function participants_summary() {

		$agent_ids = isset($this->post_data['agent_ids']) ? $this->post_data['agent_ids']:array();
		$group_ids = isset($this->post_data['group_ids']) ? $this->post_data['group_ids']:array();
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('p.created_at');

			if(!empty($agent_ids)) {
				$this->extra_params['agent_ids'] = $agent_ids;
			}
			if(!empty($group_ids)) {
				$this->extra_params['group_ids'] = $group_ids;
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}

	}

    private function admin_member_persistency() {
    	$added_or_effective_date = isset($this->post_data['added_or_effective_date'])?$this->post_data['added_or_effective_date']:'';
    	$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
    	if(empty($added_or_effective_date)) {
    		$this->validate->setError('added_or_effective_date',"Please select an option");
    	}
    	
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			
			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}

			if($added_or_effective_date == "added_date") {
				$this->setdateRange('c.joined_date');
				$period_text = "Added Date - ";
			} else {
				$this->setdateRange('w.eligibility_date');	
				$period_text = "Effective Date - ";
			}

			$period_text .= $this->getPeriodText(); 
			$this->extra_params['period_text'] = $period_text; 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function member_product_cancellations() {
		$termination_or_terminated_date = isset($this->post_data['termination_or_terminated_date'])?$this->post_data['termination_or_terminated_date']:'';
		
		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$enroll_agent_ids = isset($this->post_data['enroll_agent_ids']) ? $this->post_data['enroll_agent_ids']:array();
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {


			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($enroll_agent_ids)) {
				$this->extra_params['enroll_agent_ids'] = $enroll_agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}	
			$period_text = '';
			if($termination_or_terminated_date == "termination_date") {
				$period_text = "Termination Date - ";
				$this->setdateRange('ws.termination_date');
			} else if($termination_or_terminated_date == "date_terminated") {
				$period_text = "Date terminated - ";
				$this->setdateRange('ws.term_date_set');	
			}

			$period_text .= $this->getPeriodText();
			$this->extra_params['period_text'] = $period_text; 

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function life_insurance_beneficiaries() {
    
		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();
	
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
	
			$this->setdateRange('ws.created_at');
	
			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
			$period_text = $this->getPeriodText();
			$this->extra_params['period_text'] = $period_text; 
	
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function advance_funding() {
    
		$this->setdateRangeAFError();
		$validate_res =  $this->check_validation();		
		if($validate_res['status'] == "success") {

			$this->extra_params['report_type'] = $this->report_key == 'advance_collection' ? 'monthly' : 'weekly' ;

			$this->setdateRange('cs.created_at');

			$this->extra_params['period_text'] = $this->getPeriodText();

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}
	private function commission_setup() {
    
		$this->setdateRangeAFError();
		$commission_ids = isset($this->post_data['commission_ids']) ? $this->post_data['commission_ids']:array();
		$product_ids = isset($this->post_data['product_ids']) ? $this->post_data['product_ids']:array();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->setdateRange('cr.created_at');

			if(!empty($commission_ids)) {
				$this->extra_params['commission_ids'] = $commission_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
			
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function admin_next_billing_date() {
    	$this->setdateRangeAFError();
		$agents_id = isset($_POST['agents_id']) ? $_POST['agents_id']:'';
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->extra_params['period_text'] = $this->getPeriodText();
			$this->extra_params['f_join_range'] = $this->join_range;
			$this->extra_params['f_added_date'] = $this->added_date;
			$this->extra_params['f_fromdate'] = $this->fromdate;
			$this->extra_params['f_todate'] = $this->todate; 

			if (!empty($agents_id)) {
				$agents_id = array_unique(array_map('trim',$agents_id));
				$this->incr .= " AND c.sponsor_id IN(".implode(',',$agents_id).") ";
			}

			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_member_persistency() {
    	$added_or_effective_date = isset($this->post_data['added_or_effective_date'])?$this->post_data['added_or_effective_date']:'';

    	if(empty($added_or_effective_date)) {
    		$this->validate->setError('added_or_effective_date',"Please select an option");
    	}
    	
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if($added_or_effective_date == "added_date") {
				$this->setdateRange('c.joined_date');
				$period_text = "Added Date - ";
			} else {
				$this->setdateRange('w.eligibility_date');	
				$period_text = "Effective Date - ";
			}
			$period_text .= $this->getPeriodText(); 
			$this->extra_params['period_text'] = $period_text; 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_debit_balance() {    	
    	$this->setdateRangeAFError();
		$agent_id = isset($this->post_data['agent_id'])?$this->post_data['agent_id']:'';
		if($this->report_key == 'admin_agent_debit_balance' && empty($agent_id)){
			$this->validate->setError('agent_id',"Please select any Agent");
		}
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if($this->report_key == 'admin_agent_debit_balance'){
				$this->extra_params['agent_id'] = $agent_id;
			}
			$this->setdateRange('h.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

    private function agent_debit_ledger() {    	
    	$this->setdateRangeAFError();
		$agent_ids = isset($this->post_data['agent_ids'])?$this->post_data['agent_ids']:'';
		if($this->report_key == 'admin_agent_debit_ledger' && empty($agent_ids)){
			$this->validate->setError('agent_ids',"Please select any Agent");
		}
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if($this->report_key == 'admin_agent_debit_ledger'){
				if (!empty($agent_ids)) {
					$agent_ids = array_unique(array_map('trim',$agent_ids));
					$this->incr .= " AND h.agent_id IN(".implode(',',$agent_ids).") ";
				}
			}
			$this->setdateRange('h.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function debit_balance_overview() {    	
    	$this->setdateRangeAFError();
		$agent_ids = isset($this->post_data['agent_ids'])?$this->post_data['agent_ids']:'';
		// if(empty($agent_ids)){
		// 	$this->validate->setError('agent_ids',"Please select any Agent");
		// }
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($agent_ids)) {
				$agent_ids = array_unique(array_map('trim',$agent_ids));
				$this->incr .= " AND h.agent_id IN(".implode(',',$agent_ids).") ";
			}
			$this->setdateRange('h.created_at');
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function product_overview() {    	
    	$this->setdateRangeAFError();
		$products = isset($this->post_data['products'])?$this->post_data['products']:'';
		// if(empty($products)){
		// 	$this->validate->setError('product_ids',"Please select any Product");
		// }
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($products)) {
				$products = array_unique(array_map('trim',$products));
				$this->incr .= " AND p.id IN(".implode(',',$products).") ";
			}
			$this->setdateRange('p.create_date');
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function carrier_overview() {    	
    	$this->setdateRangeAFError();
		$carriers = isset($this->post_data['carriers'])?$this->post_data['carriers']:'';
		$type = isset($this->post_data['feeType'])?$this->post_data['feeType']:'';
		// if(empty($carriers)){
		// 	$this->validate->setError('carriers',"Please select any ".$type);
		// }
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($carriers)) {
				$carriers = array_unique(array_map('trim',$carriers));
				$this->incr .= " AND pf.id IN(".implode(',',$carriers).") ";
			}
			$this->incr .= " AND pf.setting_type=:setting_type ";
			$this->sch_params[':setting_type'] = $type;
			$this->extra_params['setting_type'] = $type;

			$this->setdateRange('pf.created_at');
			
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function group_summary() {    	
    	$this->setdateRangeAFError();
		$group_ids = isset($this->post_data['group_ids']) ? $this->post_data['group_ids']:array();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($group_ids)) {
				$group_ids = array_unique(array_map('trim',$group_ids));
				$this->incr .= " AND c.id IN(".implode(',',$group_ids).") ";
			}

			$this->setdateRange('c.created_at');
			
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function group_summary_export() {    	
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->setdateRange('c.created_at');

			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function group_history() {
    	$group_id = isset($this->post_data['group_id'])?$this->post_data['group_id']:'';

		if(empty($group_id)) {
			$this->validate->setError('group_id',"Please select Group");
		}

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->sch_params[':id'] = md5($group_id);

			if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
				$this->setdateRangeAF();
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    }

	private function group_history_export() {
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->sch_params[':id'] = md5($this->user_id);

			if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
				$this->setdateRangeAF();
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    }

	private function agent_monthly_forecasting() {
		$select_month = isset($this->post_data['select_month'])?$this->post_data['select_month']:'';
		$agent_id = isset($this->post_data['agent_id'])?$this->post_data['agent_id']:'';
		if(empty($select_month)){
			$this->validate->setError("select_month","Please select Any Month");
		}
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->extra_params['agent_id'] = $agent_id;
			if (!empty($select_month)) {
				$this->extra_params['selected_month'] = $select_month;
			}
			
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}

	private function agent_product_persistency() {
		$added_or_effective_date = isset($this->post_data['added_or_effective_date'])?$this->post_data['added_or_effective_date']:'';
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();
		$agent_id = isset($this->post_data['agent_ids']) ? $this->post_data['agent_ids']:array();

		// if($this->report_key == 'admin_product_persistency'){

		// 	if(empty($agent_id)){
		// 		$this->validate->setError('agent_ids',"Please select any Agent");
		// 	}
		// 	if(empty($product_ids)){
		// 		$this->validate->setError('product_ids',"Please select any Product");
		// 	}			
		// }

    	if(empty($added_or_effective_date)) {
    		$this->validate->setError('added_or_effective_date',"Please select an option");
    	}
    	
    	$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if($this->report_key == 'admin_product_persistency'){
				if(!empty($product_ids)){
					$this->incr .= " AND p.id IN(".implode(',',$product_ids).") ";
				}
				if(!empty($agent_id)){
					$this->incr .= " AND s.id IN(".implode(',',$agent_id).") ";
				}
			}

			if($added_or_effective_date == "added_date") {
				$this->setdateRange('ws.created_at');
				$period_text = "Added Date - ";
			} else {
				$this->setdateRange('ws.eligibility_date');	
				$period_text = "Effective Date - ";
			}
			$period_text .= $this->getPeriodText(); 
			$this->extra_params['period_text'] = $period_text; 
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function payables_export() {
		$added_or_transaction_date = isset($this->post_data['added_or_transaction_date'])?$this->post_data['added_or_transaction_date']:'';
		$payee_type = isset($this->post_data['payee_type'])?$this->post_data['payee_type']:array();
		$products = isset($this->post_data['products'])?$this->post_data['products']:array();
		$payee = isset($this->post_data['payee'])?$this->post_data['payee']:array();
		$regeneratedPayableId = isset($this->post_data['regeneratedPayableId'])?$this->post_data['regeneratedPayableId']:'';
    	
    	if(!empty($added_or_transaction_date)) {
    		$this->setdateRangeAFError();
    	}

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->extra_params['added_or_transaction_date'] = $added_or_transaction_date;
			$this->extra_params['join_range'] = $this->join_range;
			$this->extra_params['added_date'] = $this->added_date;
			$this->extra_params['fromdate'] = $this->fromdate;
			$this->extra_params['todate'] = $this->todate;

			if(!empty($payee_type)) {
				$this->extra_params['payee_type'] = $payee_type;
			}
			if(!empty($payee)) {
				$this->extra_params['payee'] = $payee;
			}
			if(!empty($products)) {
				$this->extra_params['products'] = $products;
			}
			if(!empty($regeneratedPayableId)) {
				$this->extra_params['regeneratedPayableId'] = $regeneratedPayableId;
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function daily_order_summary() {
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();

		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('t.created_at');
			return $this->add_export_request();
			
		} else {
			return $validate_res;
		}
	}

	private function top_performing_agency() {
		
		$this->setdateRangeAFError();

		$validate_res =  $this->check_validation();

		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('t.created_at');
			return $this->add_export_request();
			
		} else {
			return $validate_res;
		}
	}
	
	private function agent_eo_coverage() {
    	$agent_ids = isset($this->post_data['agent_ids'])?$this->post_data['agent_ids']:array();

    	$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($agent_ids)) {
				$agent_ids = array_unique(array_map('trim',$agent_ids));
				$this->incr .= " AND c.id IN ('".implode("','",$agent_ids)."')";
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    	
	}
	private function user_interactions() {
		$user_ids = isset($this->post_data['user_ids'])?$this->post_data['user_ids']:array();
		$interactions = isset($this->post_data['interactions'])?$this->post_data['interactions']:array();
		$user_type = isset($this->post_data['user_type'])?$this->post_data['user_type']:array();
		
    	$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			$this->extra_params['user_type'] = $user_type;
			
			$this->incr .=" AND c.type=:user_type ";
			$this->sch_params[':user_type'] = $user_type;

			if (!empty($user_ids)) {
				if($user_type == 'customer'){
					$userRepIds = explode(",",$user_ids);
					$userRepIds = array_map('trim',$userRepIds);
					$this->incr .= " AND c.rep_id IN ('".implode("','",$userRepIds)."')";
				}else{
					$user_ids = array_unique(array_map('trim',$user_ids));
					$this->incr .= " AND c.id IN ('".implode("','",$user_ids)."')";
				}
			}
			if (!empty($interactions)) {
				$interactions = array_unique(array_map('trim',$interactions));
				$this->incr .= " AND i.id IN ('".implode("','",$interactions)."')";
			}
			if(!empty($this->join_range) && (!empty($this->added_date) || (!empty($this->fromdate) && !empty($this->todate)))){
				$this->setdateRangeAF(false,'id.created_at');
			}
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
    	
    }

    private function payables_reconciliation() {    	
    	$this->setdateRangeAFError();
		$products = isset($this->post_data['products'])?$this->post_data['products']:'';
		// if(empty($products)){
		// 	$this->validate->setError('product_ids',"Please select any Product");
		// }
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
			if (!empty($products)) {
				$products = array_unique(array_map('trim',$products));
				$this->incr .= " AND p.id IN(".implode(',',$products).") ";
			}
			$this->setdateRange('p.create_date');
			$this->extra_params['period_text'] = $this->getPeriodText();
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
	}

	private function payment_nb_sales() {

		$tree_agent_ids = isset($this->post_data['tree_agent_ids']) ? $this->post_data['tree_agent_ids']:array();		
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			if($this->report_key == 'payment_nb_sales'){
				$this->extra_params['report_type'] = 'new_business'; 
			}else{
				$this->extra_params['report_type'] = 'renewal'; 
			}
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('t.created_at');
		
			if(!empty($tree_agent_ids)) {
				$this->extra_params['tree_agent_ids'] = $tree_agent_ids;
			}
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}

	private function group_full_coverage() {

		$group_ids = isset($this->post_data['group_id']) ? $this->post_data['group_id']:array();		
		$report_type = isset($this->post_data['report_type']) ? $this->post_data['report_type']:"masked";		
		$product_ids = isset($this->post_data['product']) ? $this->post_data['product']:array();		
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('w.created_at');
		
			if(!empty($group_ids)) {
				$this->extra_params['group_id'] = $group_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		    
		    $this->extra_params['report_type'] = $report_type;
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_full_coverage_gp() {

		$report_type = isset($this->post_data['report_type']) ? $this->post_data['report_type']:"masked";		
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();		
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRange('w.created_at');
			
			$this->extra_params['group_id'] = array($this->user_id);

			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		    
		    $this->extra_params['report_type'] = $report_type;
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_enroll_overview() {

		$group_ids = isset($this->post_data['group_id']) ? $this->post_data['group_id']:array();		
				
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();

		$coverage_period = isset($this->post_data['date_picker']) ? $this->post_data['date_picker']:"";
		if(empty($coverage_period)){
			$this->validate->setError('date_picker','Please Select Month');
		}		
		// $this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText();

			$month = explode('/', $coverage_period)[0]; 
			$year = explode('/', $coverage_period)[1];

			$from_date = $month .'/'. '01' . '/' . $year;
			$to_date = date("m/t/Y", strtotime($from_date));

			$this->fromdate = $from_date;
			$this->todate = $to_date;
			$this->join_range = 'range';

			$this->setdateRange('ord_det.end_coverage_period');
		
			if(!empty($group_ids)) {
				$this->extra_params['group_id'] = $group_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_enroll_overview_gp() {

		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();
		
		$coverage_period = isset($this->post_data['date_picker']) ? $this->post_data['date_picker']:"";
		if(empty($coverage_period)){
			$this->validate->setError('date_picker','Please Select Month');
		}

		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 

			$month = explode('/', $coverage_period)[0]; 
			$year = explode('/', $coverage_period)[1];

			$from_date = $month .'/'. '01' . '/' . $year;
			$to_date = date("m/t/Y", strtotime($from_date));

			$this->fromdate = $from_date;
			$this->todate = $to_date;
			$this->join_range = 'range';
			
			$this->setdateRange('ord_det.end_coverage_period');
		
			$this->extra_params['group_id'] = array($this->user_id);
			
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_member_age_out() {

		$group_ids = isset($this->post_data['group_id']) ? $this->post_data['group_id']:array();		
				
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();

		$policy_status = isset($this->post_data['policy_status']) ? $this->post_data['policy_status']:"Active";

		$coverage_period = isset($this->post_data['date_picker']) ? $this->post_data['date_picker']:"";
		// if(empty($coverage_period)){
		// 	$this->validate->setError('date_picker','Please Select Month');
		// }		
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText();

			// $month = explode('/', $coverage_period)[0]; 
			// $year = explode('/', $coverage_period)[1];

			// $from_date = $month .'/'. '01' . '/' . $year;
			// $to_date = date("m/t/Y", strtotime($from_date));

			// $this->fromdate = $from_date;
			// $this->todate = $to_date;
			// $this->join_range = 'range'; 
			
			$this->setdateRangeForAgeOut();
		
			if(!empty($group_ids)) {
				$this->extra_params['group_id'] = $group_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
			$this->extra_params['policy_status'] = $policy_status;
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_member_age_out_gp() {

		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();

		$policy_status = isset($this->post_data['policy_status']) ? $this->post_data['policy_status']:"Active";		
		$this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$this->setdateRangeForAgeOut();
		
			$this->extra_params['group_id'] = array($this->user_id);
			
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
			$this->extra_params['policy_status'] = $policy_status;
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_change_product() {

		$group_ids = isset($this->post_data['group_id']) ? $this->post_data['group_id']:array();		
				
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();

		$coverage_period = isset($this->post_data['date_picker']) ? $this->post_data['date_picker']:"";
		if(empty($coverage_period)){
			$this->validate->setError('date_picker','Please Select Month');
		}
	
		// $this->setdateRangeAFError();
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText();

			$month = explode('/', $coverage_period)[0]; 
			$year = explode('/', $coverage_period)[1];

			$from_date = $month .'/'. '01' . '/' . $year;
			$to_date = date("m/t/Y", strtotime($from_date));

			$this->fromdate = $from_date;
			$this->todate = $to_date;
			$this->join_range = 'range'; 
			
			$this->setdateRange('wh.created_at');
		
			if(!empty($group_ids)) {
				$this->extra_params['group_id'] = $group_ids;
			}
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}
	private function group_change_product_gp() {
		$product_ids = isset($this->post_data['products']) ? $this->post_data['products']:array();
	
		$coverage_period = isset($this->post_data['date_picker']) ? $this->post_data['date_picker']:"";
		if(empty($coverage_period)){
			$this->validate->setError('date_picker','Please Select Month');
		}
		
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {
		
			$this->extra_params['period_text'] = $this->getPeriodText(); 
			
			$month = explode('/', $coverage_period)[0]; 
			$year = explode('/', $coverage_period)[1];

			$from_date = $month .'/'. '01' . '/' . $year;
			$to_date = date("m/t/Y", strtotime($from_date));

			$this->fromdate = $from_date;
			$this->todate = $to_date;
			$this->join_range = 'range'; 
			
			$this->setdateRange('wh.created_at');
		
			$this->extra_params['group_id'] = array($this->user_id);
			
			if(!empty($product_ids)) {
				$this->extra_params['product_ids'] = $product_ids;
			}
		
			return $this->add_export_request();
		} else {
			return $validate_res;
		}
		
	}

	private function eticket_overview() {
	
		$this->setdateRangeAFError();
		$validate_res =  $this->check_validation();

		if($validate_res['status'] == "success") {

			$this->extra_params['period_text'] = $this->getPeriodText(); 
			$this->setdateRange('s.created_at');
			return $this->add_export_request();

		} else {
			return $validate_res;
		}
		
	}

	private function eticket_script() {
	
		global $ETICKET_DOCUMENT_WEB;
		$eTicketIds = isset($this->post_data['etickets']) ? $this->post_data['etickets']:array();
		if(empty($eTicketIds)){
			$this->validate->setError('etickets','Please Select E-Tickets');
		}	
		$validate_res =  $this->check_validation();
		if($validate_res['status'] == "success") {

			$this->extra_params['document_url'] = $ETICKET_DOCUMENT_WEB;

			if(!empty($eTicketIds)) {
				$this->extra_params['eTicketIds'] = $eTicketIds;
			}

			return $this->add_export_request();

		} else {
			return $validate_res;
		}
		
	}

    private function add_export_request() {
		global $SITE_ENV;
		$reports_class = new Report();

		$responseArray = array("status"=>"success","message"=>"Your export request is added");
		$report_columns = checkIsset($this->post_data['hdn_columns']);
		$report_columns_arr = array();
		if($report_columns){
			$report_columns_arr = explode(",", $report_columns);
		}
		if(!empty($report_columns_arr)) {
			$this->extra_params['report_columns'] = $report_columns_arr;

			$setting_data = array();
			$setting_data['selected_columns'] = '';
			if(isset($this->post_data['columns_save_as_defualt'])) {
				$setting_data['selected_columns'] = implode(',',$report_columns_arr);
			}
			$reports_class->update_rps_user_report_settings($this->user_id,$this->user_type,$this->report_id,$setting_data);
		}
		$job_id = add_export_request_api($this->file_type,$this->user_id,$this->user_type,$this->export_file_report_name,$this->report_location,$this->incr,$this->sch_params,$this->extra_params,'',$this->report_api_url);

		/*--- Activity Feed -----*/
		$desc = array();
		$desc['ac_message'] =array(
		'ac_red_1'=> array(
		'href'=> $this->user_profile_page,
		'title'=> $this->user_rep_id,
		),
		'ac_message_1' =>' manually created report <span class="text-action">'.$this->report_name.'</span>',
		);
		$desc = json_encode($desc);
		activity_feed(3,$this->user_id,$this->user_type,$this->user_id,$this->user_type,'Manually Created Report.',"","",$desc);
		/*---/Activity Feed -----*/

		$reportDownloadURL = $this->report_api_url."&job_id=".$job_id;
		if($SITE_ENV == "Local"){
			$ch = curl_init($reportDownloadURL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			$api_response = curl_exec($ch);
			curl_close($ch);

			$responseArray['api_response'] = $api_response;
		}else{
			$ch = curl_init($reportDownloadURL);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			// $api_response = curl_exec($ch);
			curl_close($ch);
		}

		return $responseArray;
	}

    function setdateRangeAFError(){
		if ($this->join_range == '') {
			$this->validate->setError('join_range','Please select Date Type');
		}

		if ($this->join_range == 'range') {
			if($this->fromdate == "") {
				$this->validate->setError('fromdate','Please select From Date');
			} else {
				list($mm, $dd, $yyyy) = explode('/', $this->fromdate);
				if (!checkdate($mm, $dd, $yyyy)) {
					$this->validate->setError('fromdate','Valid Date is required');
				}
			}
			if($this->todate == "") {
				$this->validate->setError('todate','Please select To Date');
			} else {
				list($mm, $dd, $yyyy) = explode('/', $this->todate);
				if (!checkdate($mm, $dd, $yyyy)) {
					$this->validate->setError('todate','Valid Date is required');
				}
			}
		} else {
			if($this->added_date == "") {
				$this->validate->setError('added_date','Please select Date');
			} else {
				list($mm, $dd, $yyyy) = explode('/', $this->added_date);
				if (!checkdate($mm, $dd, $yyyy)) {
					$this->validate->setError('added_date','Valid Date is required');
				}
			}
		}

		if(empty($this->validate->getError('todate')) && empty($this->validate->getError('fromdate'))) {
			$no_days=0;
			if($this->fromdate != '' && $this->todate != '') {
				$date1 = date_create($this->fromdate);
				$date2 = date_create($this->todate);
				$diff = date_diff($date1,$date2);
				$no_days = $diff->format("%a");
			}
			
			if($no_days > 62) {
				if(!in_array($this->report_key,array("payment_policy_overview","member_summary"))) {
					$this->validate->setError('custom_error',"Please enter proper date range. A maximum date range of 60 days is allowed per request.");
				}
			}
		}
	}

	function setdateRangeAF($forActivity=true,$feild = 'changed_at') {
		$dateIncr = " DATE(CONVERT_TZ(".$feild.",'+00:00','".$this->timezone."')) ";
		if(!$forActivity){
			$dateIncr = " DATE($feild) ";
		}

		if($this->join_range == "range") {
			if ($this->fromdate != "") {
				$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->fromdate));
				$this->incr .= " AND $dateIncr >= :from_date";
			}
			if ($this->todate != "") {
				$this->sch_params[':to_date'] = date('Y-m-d', strtotime($this->todate));
				$this->incr .= " AND $dateIncr <= :to_date";
			}
		} else {
			if ($this->added_date != "") {
				if($this->join_range == 'exactly') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr = :from_date";

				} else if($this->join_range == 'before') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr < :from_date";

				} else if($this->join_range == 'after') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr > :from_date";
				}
			}
		}
	}

	function setdateRange($column = 'created_at') {
		$dateIncr = " DATE($column) ";

		if($this->join_range == "range") {
			if ($this->fromdate != "") {
				$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->fromdate));
				$this->incr .= " AND $dateIncr >= :from_date";
			}
			if ($this->todate != "") {
				$this->sch_params[':to_date'] = date('Y-m-d', strtotime($this->todate));
				$this->incr .= " AND $dateIncr <= :to_date";
			}
		} else {
			if ($this->added_date != "") {
				if($this->join_range == 'exactly') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr = :from_date";

				} else if($this->join_range == 'before') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr < :from_date";

				} else if($this->join_range == 'after') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND $dateIncr > :from_date";
				}
			}
		}

		$this->extra_params['f_join_range'] = $this->join_range;
		$this->extra_params['f_added_date'] = $this->added_date;
		$this->extra_params['f_fromdate'] = $this->fromdate;
		$this->extra_params['f_todate'] = $this->todate;
	}

	function setdateRangeForCoverage($fromColumn = 'od.start_coverage_period',$endColumn='od.end_coverage_period') {

		if($this->join_range == "range") {
			if ($this->fromdate != "") {
				$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->fromdate));
				$this->incr .= " AND DATE($fromColumn) >= :from_date";
			}
			if ($this->todate != "") {
				$this->sch_params[':to_date'] = date('Y-m-d', strtotime($this->todate));
				$this->incr .= " AND DATE($endColumn) <= :to_date";
			}
		} else {
			if ($this->added_date != "") {
				if($this->join_range == 'exactly') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND (DATE($fromColumn) = :from_date OR DATE($endColumn) = :from_date)";

				} else if($this->join_range == 'before') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND (DATE($fromColumn) < :from_date OR DATE($endColumn) < :from_date)";

				} else if($this->join_range == 'after') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND (DATE($fromColumn) > :from_date OR DATE($endColumn) > :from_date)";
				}
			}
		}

		$this->extra_params['f_join_range'] = $this->join_range;
		$this->extra_params['f_added_date'] = $this->added_date;
		$this->extra_params['f_fromdate'] = $this->fromdate;
		$this->extra_params['f_todate'] = $this->todate;
	}

	function setdateRangeForAgeOut($column = 'created_at') {
		$dateIncr = " DATE($column) ";

		if($this->join_range == "range") {
			if ($this->fromdate != "" && $this->todate != "") {
				$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->fromdate));
				$this->sch_params[':to_date'] = date('Y-m-d', strtotime($this->todate));
				$this->incr .= " AND 
                (
                 (p.is_primary_age_restrictions ='Y' AND (DATE_ADD(c.birth_date, INTERVAL p.primary_age_restrictions_to YEAR) >= :from_date AND DATE_ADD(c.birth_date, INTERVAL p.primary_age_restrictions_to YEAR) <= :to_date)) OR
                 (p.is_children_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.children_age_restrictions_to YEAR) >= :from_date AND DATE_ADD(cd.birth_date, INTERVAL p.children_age_restrictions_to YEAR) <= :to_date) AND LOWER(cd.relation) IN ('son','daughter')) OR
                 (p.is_spouse_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.spouse_age_restrictions_to YEAR) <= :from_date AND DATE_ADD(cd.birth_date, INTERVAL p.spouse_age_restrictions_to YEAR) <= :to_date) AND LOWER(cd.relation) IN ('wife','husband'))
                )";
			}
		} else {
			if ($this->added_date != "") {
				if($this->join_range == 'exactly') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= " AND 
                (
                 (p.is_primary_age_restrictions ='Y' AND (
                 	DATE_ADD(c.birth_date, INTERVAL p.primary_age_restrictions_to YEAR) = :from_date)
                 	) OR
                 (p.is_children_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.children_age_restrictions_to YEAR) = :from_date) AND LOWER(cd.relation) IN ('son','daughter')) OR
                 (p.is_spouse_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.spouse_age_restrictions_to YEAR) = :from_date) AND LOWER(cd.relation) IN ('wife','husband'))
                )";

				} else if($this->join_range == 'before') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= "  AND 
                (
                 (p.is_primary_age_restrictions ='Y' AND (
                 	DATE_ADD(c.birth_date, INTERVAL p.primary_age_restrictions_to YEAR) < :from_date)
                 	) OR
                 (p.is_children_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.children_age_restrictions_to YEAR) < :from_date) AND LOWER(cd.relation) IN ('son','daughter')) OR
                 (p.is_spouse_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.spouse_age_restrictions_to YEAR) < :from_date) AND LOWER(cd.relation) IN ('wife','husband'))
                )";

				} else if($this->join_range == 'after') {
					$this->sch_params[':from_date'] = date('Y-m-d', strtotime($this->added_date));
					$this->incr .= "  AND 
	                (
	                 (p.is_primary_age_restrictions ='Y' AND (
	                 	DATE_ADD(c.birth_date, INTERVAL p.primary_age_restrictions_to YEAR) > :from_date)
	                 	) OR
	                 (p.is_children_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.children_age_restrictions_to YEAR) > :from_date) AND LOWER(cd.relation) IN ('son','daughter')) OR
	                 (p.is_spouse_age_restrictions ='Y' AND (DATE_ADD(cd.birth_date, INTERVAL p.spouse_age_restrictions_to YEAR) > :from_date) AND LOWER(cd.relation) IN ('wife','husband'))
	                )";
				}
			}
		}

		$this->extra_params['f_join_range'] = $this->join_range;
		$this->extra_params['f_added_date'] = $this->added_date;
		$this->extra_params['f_fromdate'] = $this->fromdate;
		$this->extra_params['f_todate'] = $this->todate;
	}

	function getPeriodText() {
		if($this->join_range == "range") {
			return date('m/d/Y',strtotime($this->fromdate)).' To '.date('m/d/Y',strtotime($this->todate));
		} else {
			if ($this->added_date != "") {
				if($this->join_range == 'exactly') {
					return date('m/d/Y',strtotime($this->added_date));

				} else if($this->join_range == 'before') {
					return 'Before '.date('m/d/Y',strtotime($this->added_date));

				} else if($this->join_range == 'after') {
					return 'After '.date('m/d/Y',strtotime($this->added_date));
				}
			}
		}
		return '';
	}
}
