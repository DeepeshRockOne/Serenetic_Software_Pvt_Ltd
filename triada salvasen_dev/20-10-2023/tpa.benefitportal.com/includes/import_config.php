<?php

if($IMPORTDB == 'BENADMIN'){
    if($SITE_ENV=='Local'){
        $OTHER_DBSERVER = "common.cr0prkmmcsog.ap-south-1.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "Y43SYFeypNKx23Dk";
        
        $OTHER_DATABASENAME = "BenAdmin_main";
        $OTHER_LOG_DB = "BenAdmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "BenAdmin_emailer";
        $OTHER_AWIS_REPORT = "BenAdmin_report";
        $OTHER_HOST = "http://localhost/benadmin.net";
    }elseif($SITE_ENV=='Live'){
        $OTHER_DBSERVER = "op29-aurora-rds-final.czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "1awNPBctTaRU6HyopMSj";
        
        $OTHER_DATABASENAME = "BenAdmin_main";
        $OTHER_LOG_DB = "BenAdmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "BenAdmin_emailer";
        $OTHER_AWIS_REPORT = "BenAdmin_report";
        $OTHER_HOST = "https://benadmin.net";
    }elseif($SITE_ENV=='Development' || $SITE_ENV=='Stag'){
        $OTHER_DBSERVER = "op29-stag.cluster-czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "OyJFA5aO5vjxNiHrKZsR";
        
        $OTHER_DATABASENAME = "stag_BenAdmin_main";
        $OTHER_LOG_DB = "stag_BenAdmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "stag_BenAdmin_emailer";
        $OTHER_AWIS_REPORT = "stag_BenAdmin_report";
        if($SITE_ENV=='Development'){
        $OTHER_HOST = "https://dev.benadmin.net";
        }else{
        $OTHER_HOST = "https://stag.benadmin.net";
        }
    }
}elseif($IMPORTDB == 'OPERATION'){

    if($SITE_ENV=='Local'){
        $OTHER_DBSERVER = "common.cr0prkmmcsog.ap-south-1.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "Y43SYFeypNKx23Dk";
        
        $OTHER_DATABASENAME = "operation29_main";
        $OTHER_LOG_DB = "operation29_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "operation29_emailer";
        $OTHER_AWIS_REPORT = "operation29_report";
        $OTHER_HOST = "http://localhost/operation29.com";
    }elseif($SITE_ENV=='Live'){
        $OTHER_DBSERVER = "op29-aurora-rds-final.czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "1awNPBctTaRU6HyopMSj";
        
        $OTHER_DATABASENAME = "operation29_main";
        $OTHER_LOG_DB = "operation29_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "operation29_emailer";
        $OTHER_AWIS_REPORT = "operation29_report";
        $OTHER_HOST = "https://operation29.com";
    }elseif($SITE_ENV=='Development' || $SITE_ENV=='Stag'){
        $OTHER_DBSERVER = "op29-stag.cluster-czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "OyJFA5aO5vjxNiHrKZsR";
        
        $OTHER_DATABASENAME = "stag_operation29_main";
        $OTHER_LOG_DB = "stag_operation29_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "stag_operation29_emailer";
        $OTHER_AWIS_REPORT = "stag_operation29_report";
        if($SITE_ENV=='Development'){
        $OTHER_HOST = "https://dev.operation29.com";
        }else{
        $OTHER_HOST = "https://stag.operation29.com";
        }
    }

}elseif($IMPORTDB == 'HIK'){

    if($SITE_ENV=='Local'){
        $OTHER_DBSERVER = "common.cr0prkmmcsog.ap-south-1.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "Y43SYFeypNKx23Dk";
        
        $OTHER_DATABASENAME = "hikadmin_main";
        $OTHER_LOG_DB = "hikadmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "hikadmin_emailer";
        $OTHER_AWIS_REPORT = "hikadmin_report";
        $OTHER_HOST = "http://localhost/hikadmin.com";
    }elseif($SITE_ENV=='Live'){
        $OTHER_DBSERVER = "op29-aurora-rds-final.czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "1awNPBctTaRU6HyopMSj";
        
        $OTHER_DATABASENAME = "hikadmin_main";
        $OTHER_LOG_DB = "hikadmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "hikadmin_emailer";
        $OTHER_AWIS_REPORT = "hikadmin_report";
        $OTHER_HOST = "https://hikadmin.com";
    }elseif($SITE_ENV=='Development' || $SITE_ENV=='Stag'){
        $OTHER_DBSERVER = "op29-stag.cluster-czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "OyJFA5aO5vjxNiHrKZsR";
        
        $OTHER_DATABASENAME = "stag_hikadmin_main";
        $OTHER_LOG_DB = "stag_hikadmin_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "stag_hikadmin_emailer";
        $OTHER_AWIS_REPORT = "stag_hikadmin_report";
        if($SITE_ENV=='Development'){
        $OTHER_HOST = "https://dev.hikadmin.com";
        }else{
        $OTHER_HOST = "https://stag.hikadmin.com";
        }
    }

}elseif($IMPORTDB == 'BENEFITS_PORTAL'){

    if($SITE_ENV=='Local'){
        $OTHER_DBSERVER = "common.cr0prkmmcsog.ap-south-1.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "Y43SYFeypNKx23Dk";
        
        $OTHER_DATABASENAME = "benefitsportal_main";
        $OTHER_LOG_DB = "benefitsportal_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "benefitsportal_emailer";
        $OTHER_AWIS_REPORT = "benefitsportal_report";
        $OTHER_HOST = "http://localhost/benefitsportal.net";
    }elseif($SITE_ENV=='Live'){
        $OTHER_DBSERVER = "op29-aurora-rds-final.czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "1awNPBctTaRU6HyopMSj";
        
        $OTHER_DATABASENAME = "benefitsportal_main";
        $OTHER_LOG_DB = "benefitsportal_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "benefitsportal_emailer";
        $OTHER_AWIS_REPORT = "benefitsportal_report";
        $OTHER_HOST = "https://benefitsportal.net/";
    }elseif($SITE_ENV=='Development' || $SITE_ENV=='Stag'){
        $OTHER_DBSERVER = "op29-stag.cluster-czknhikpkiar.us-west-2.rds.amazonaws.com";
        $OTHER_USERNAME = "admin";
        $OTHER_PASSWORD = "OyJFA5aO5vjxNiHrKZsR";
        
        $OTHER_DATABASENAME = "stag_benefitsportal_main";
        $OTHER_LOG_DB = "stag_benefitsportal_log";
        $OTHER_WEBIM_DB = "";
        $OTHER_MIBEW_DB = "";
        $OTHER_EMAILER_DB = "stag_benefitsportal_emailer";
        $OTHER_AWIS_REPORT = "stag_benefitsportal_report";
        if($SITE_ENV=='Development'){
        $OTHER_HOST = "https://dev.benefitsportal.net";
        }else{
        $OTHER_HOST = "https://stag.benefitsportal.net";
        }
    }

}
//For product import specific product
    // $productIDArray = array('P872107_ADDON','P158103_ADDON','P408635_ADDON','P195640','P420482','P105692','P560917','P932977','P514460','P495112','P848463','P105219','P793254','P909388','P275998','P342302','P412401','P240467');
    // $productIDArray = array('P206050_20','P122599_20','P819891_20');
    $productIDArray = array('P123537','P519609','P267252','P543978','P740378','P849282','P504580','P483523','P957292','P468685_ADDON','P201031_ADDON','P525013_ADDON','P354583_ADDON','P876671_ADDON','P246540_20','P314614_30','P149499_20','P901782_30','P466330_20','P636262_30','P578184','P692589','P700201','P296631','P907636');
//For product import specific product
?>