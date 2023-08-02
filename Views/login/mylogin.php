<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My NLINK Systems</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- Choices CSS-->
    <link rel="stylesheet" href="<?=base_url('assets-login/vendor/choices.js/styles/choices.min.css');?>">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?=base_url('assets-login/css/style.green.min.css');?>" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?=base_url('assets-login/css/custom.css');?>">
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?=base_url('assets-login/img/novo.ico');?>">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  </head>
  <body>
    <div class="login-page">
      <div class="container d-flex align-items-center position-relative py-5">
        <div class="card shadow-sm w-100 rounded overflow-hidden bg-none">
          <div class="card-body p-0">
            <div class="row gx-0 align-items-stretch">
              <!-- Logo & Information Panel-->
              <div class="col-lg-6">
                <div class="info d-flex justify-content-center flex-column p-4 h-100">
                  <div class="py-5">
                    <h1 class="display-6 fw-bold">Novo Links</h1>
                    <p class="fw-light mb-0">Quality of Life Begins at Novo</p>
                  </div>
                </div>
              </div>
              <!-- Form Panel    -->
              <div class="col-lg-6 bg-white">
                <div class="d-flex align-items-center px-4 px-lg-5 h-100">
                  <?=form_open('mylogin-auth',' method="post" role="form" class="login-form py-5 w-100" autocomplete="off" ');?>
                    <div class="input-material-group mb-3">
                      <input class="input-material" id="mUsername" type="text" name="mUsername" autocomplete="off" required data-validate-field="mUsername">
                      <label class="label-material" for="mUsername">User Name</label>
                    </div>
                    <div class="input-material-group mb-4">
                      <input class="input-material" id="mPassword" type="password" name="mPassword" required data-validate-field="mPassword">
                      <label class="label-material" for="mPassword">Password</label>
                    </div>
                    <button class="btn btn-success mb-3" id="login" type="submit">Login</button><br><a class="text-sm text-paleBlue" href="#">Forgot Password?</a><br><small class="text-gray-500">Do not have an account? </small><a class="text-sm text-paleBlue" href="<?=site_url();?>mylogin">Signup</a>      
                    <div class="js-validate-error-label" style="color: #B81111"><?=session()->getFlashdata('n8v8_memsg_login');?></div>
                  <?=form_close();?> 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center position-absolute bottom-0 start-0 w-100 z-index-20">
        <p class="text-white">Design by <a class="external" href="https://bootstrapious.com/p/admin-template">Bootstrapious</a>
          <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
        </p>
      </div>
    </div>
    <!-- JavaScript files-->
    <!-- <script src="<?=base_url('assets-login/vendor/bootstrap/js/bootstrap.bundle.min.js');?>"></script> -->
    <link href="<?=base_url('assets/vendor/bootstrap/css/bootstrap.min.css');?>" rel="stylesheet">
    <script src="<?=base_url('assets-login/vendor/chart.js/Chart.min.js');?>"></script>
    <script src="<?=base_url('assets-login/vendor/just-validate/js/just-validate.min.js');?>"></script>
    <script src="<?=base_url('assets-login/vendor/choices.js/scripts/choices.min.js');?>"></script>
    <!-- Main File-->
    <script src="<?=base_url('assets-login/js/front.js');?>"></script>
    <script src="<?=base_url('assets-login/js/jquery.min.js');?>"></script>
    <script>
      // ------------------------------------------------------- //
      //   Inject SVG Sprite - 
      //   see more here 
      //   https://css-tricks.com/ajaxing-svg-sprite/
      // ------------------------------------------------------ //
      function injectSvgSprite(path) {
      
          var ajax = new XMLHttpRequest();
          ajax.open("GET", path, true);
          ajax.send();
          ajax.onload = function(e) {
          var div = document.createElement("div");
          div.className = 'd-none';
          div.innerHTML = ajax.responseText;
          document.body.insertBefore(div, document.body.childNodes[0]);
          }
      }
      // this is set to BootstrapTemple website as you cannot 
      // inject local SVG sprite (using only 'icons/orion-svg-sprite.svg' path)
      // while using file:// protocol
      // pls don't forget to change to your domain :)
      injectSvgSprite('https://bootstraptemple.com/files/icons/orion-svg-sprite.svg'); 
      
      
    </script>
    <!-- FontAwesome CSS - loading as last, so it doesn't block rendering-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  </body>
</html>
