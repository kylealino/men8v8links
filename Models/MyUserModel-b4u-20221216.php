<?php
namespace App\Models;
use CodeIgniter\Model;

class MyUserModel extends Model
{
    // .. other member variables
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->session = session();
        //$this->db = \Config\Database::connect();
        // OR $this->db = db_connect();
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mylibzdb = model('App\Models\MyLibzDBModel');
    }

    public function medbzz() { 
        $str = "select * from ap2.mysysuser";
        $q = $this->mylibzdb->myoa_sql_exec($str);
        return $q;
    }
    
    public function mysys_user() { 
        return $this->session->get('__xsys_myuserzn8v8__');
    }   
    
    public function msys_pw_salt() { 
        return "mysyztemn8v8my";
    } 
    
    public function msys_is_logged() { 
        return $this->session->get('__xsys_myuserzn8v8_is_logged__');
    }
    
    public function mpw_tkn() { 
        return self::mysys_user() . self::msys_pw_salt();
    }    
    
	public function mysys_usergrp() { 
		return $this->session->__xsys_myuserzn8v8group__;
	}
	public function mysys_userlvl() { 
		return $this->session->__xsys_myuserzn8v8level__;
	}
	
	public function mysys_userdept() { 
		return $this->session->__xsys_myuserzn8v8dept__;
	}
	public function mysys_userrema() { 
		return $this->session->__xsys_myuserzn8v8rema__;
	}	
	    
    
    public function Verify_User($cuser='') { 
		$str = "select myusername,myuservalis,myuservalie,myuserlevel,myusername,myusercostc,myusertype,myusergroup,myuserfulln,myuser_new_ui,myuserpass,
		myuseracomp,myuserrema,current_date() xcurdate,myuser_dept,`myuser_aremote` from {$this->db_erp}.myusers where myusername = '{$cuser}' limit 1 ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);		
		return $q;
	}  //end Verify_User

    public function Verify_Password($cuserpassdb='',$cuserpass='') { 
		$str = "select if('{$cuserpassdb}' = md5('{$cuserpass}'),1,0) metruefalse limit 1 ";
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);		
		//$row = $q->getRow();		
		$row = $q->getRowArray();
		$q->freeResult();
		return $row['metruefalse'];
	}  //end Verify_User

    // ======== start for user access per module
    public function get_Active_menus($dbname,$cuser,$field='',$tblname='') { 
        $adata = '';
        $str = "select * from {$dbname}.`$tblname` WHERE myusername='$cuser' AND ISACTIVE='Y' AND $field limit 1";
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        $adata=$q->resultID->num_rows;
        $q->freeResult();
        return $adata;
    } //end get_Active_menus


    public function ua_brnch($dbname,$uname){
        $adata = array();
        $str = "select myuabranch from {$dbname}.`myua_branch` where myusername ='$uname' AND ISACTIVE='Y'";
        //$q = $this->db->query($str);
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->getNumRows() > 0) { 
            $qrw = $q->getResultArray();
            foreach($qrw as $rw): 
                $adata[] = $rw['myuabranch'];
            endforeach;
        }
        $q->freeResult();
        return $adata;
    } //end ua_brnch

    public function ua_comp_code($dbname,$uname){
        $adata = array();
        $str = "select myuacomp,bb.COMP_CODE from {$dbname}.`myua_company` aa
        JOIN {$this->db_erp}.mst_company bb ON(aa.myuacomp=bb.recid)
         where myusername ='$uname' AND ISACTIVE='Y'";
        //$q = $this->db->query($str);
        $q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
        if($q->resultID->num_rows > 0) { 
            $qrw = $q->getResultArray();
            foreach($qrw as $rw): 
                $adata[] = $rw['COMP_CODE'];
            endforeach;
        }
        $q->freeResult();
        return $adata;    
    }  //end ua_comp_code

	public function ua_comp($dbname,$uname){
		$adata = array();
		$str = "select myuacomp from {$this->db_erp}.`myua_company` where myusername ='$uname' AND ISACTIVE='Y'";
		//$q = $this->db->query($str);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['myuacomp'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	}
	
	public function ua_supp($dbname,$uname){
		$adata = array();
		$str = "select myuasupp_id from {$this->db_erp}.`myua_supp` where myusername ='$uname' AND ISACTIVE='Y'";
		//$q = $this->db->query($str);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['myuasupp_id'];
			endforeach;
		}
		$q->freeResult();
		return $adata;
	}  //end ua_supp
	
	public function ua_cust($dbname,$uname){
		$adata = array();
		$str = "select myuacust_id from {$this->db_erp}.`myua_cust` where myusername ='$uname' AND ISACTIVE='Y'";
		//$q = $this->db->query($str);
		$q = $this->mylibzdb->myoa_sql_exec($str,'URI: ' . $_SERVER['PHP_SELF'] . chr(13) . chr(10) . 'File: ' . __FILE__  . chr(13) . chr(10) . 'Line Number: ' . __LINE__);
		if($q->getNumRows() > 0) { 
			$qrw = $q->getResultArray();
			foreach($qrw as $rw): 
				$adata[] = $rw['myuacust_id'];
			endforeach;
		}
		$q->free_result();
		return $adata;
	}  //end ua_cust
    
}  //end main class
?>
