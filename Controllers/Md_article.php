<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
use App\Models\MyMDArticleModel;

class Md_article extends BaseController
{

    public function __construct()
    {
        $this->mydbname = model('App\Models\MyDBNamesModel');
        $this->db_erp = $this->mydbname->medb(0);
        $this->mymdarticle =  new MyMDArticleModel();
    }

    public function index()
    {
        echo view('templates/meheader01');
        echo view('masterdata/md-article');
        echo view('templates/mefooter01');
    } 

	public function recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdarticle->view_recs($mpages,20,$txtsearchedrec);
		return view('masterdata/md-article-recs',$data);
	}    

    public function profile() {
        return view('masterdata/md-article-profile');
    }
    
    public function profile_save() { 
		$this->mymdarticle->profile_save();
    }
    
    public function md_dload() { 
		
	} //end md_dload
	
	public function POSLink() { 
		return view('masterdata/POSLink/md-article-poslink');
	} //end POSLink
	
	public function POSLink_branch() { 
		return view('masterdata/POSLink/md-article-poslink-branch');
	} //endif POSLink_branch
	
	public function POSLink_branch_recs() { 
		$txtsearchedrec = $this->request->getVar('txtsearchedrec');
		$mpages = $this->request->getVar('mpages');
		$mpages = (empty($mpages) ? 0: $mpages);
		$data = $this->mymdarticle->Artm_Branch_recs($mpages,30,$txtsearchedrec);
		return view('masterdata/POSLink/md-article-poslink-branch-recs',$data);
	} //endif POSLink_branch
	
	public function POSLink_branch_download() { 
		$this->mymdarticle->Artm_Branch_dload();
	} //end POSLink_branch_download

}  //end main Md_article
