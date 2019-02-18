<?php echo $header; ?>
<main class="main">
    <div class="container rigester-page">
      <div class="row row-centered">
        <div class="col-lg-8 col-xs-12 right-content col-centered">
          <ul class="nav nav-tabs">
              <li class="active"><a href="#ForgotPassword">Forgot your Password?</a></li>
          </ul>
          <div class="tab-content">
              <div id="ForgotPassword" class="tab-pane fade in active">
                <div class="row row-centered">
                  <div class="col-lg-9  col-xs-11 col-centered">
                    <p>Enter the e-mail address associated with your account.Click reset password to have a new password e-mailed to you.</p>
                    <br>
                    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                      <div class="form-group">
                        <label for="inputEmail" class="col-xs-3 control-label">Email</label>
                        <div class="col-xs-9">
                          <input type="text" name="email" class="form-control" placeholder="e.g., mightypirate@grogmail.com" id="inputEmail">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="text-center form-submit col-sm-12">
                          <a href="<?php echo $back; ?>" class="btn btn-secondary pull-left"> BACK TO SIGN IN</a>
                          <input type="submit" value="RESET PASSWORD" class="btn btn-primary pull-right" id="reset-pass-btn">
                        </div>
                      </div>
                      <div class="password-req-note">
                        <br>
                        <p></p>
                      </div>
                    </form> 
                  </div>  
                </div>
              </div>
          </div>    
        </div>
      </div>
    </div>
  </main><!-- @End of main -->
<?php echo $footer; ?>