<?php 
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Myauthuser implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('__xsys_myuserzn8v8_is_logged__')) 
        {
            return redirect()
                ->to('/mylogin');
        }        
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}  //end main class
