<?php echo $header; ?>
<!-- @End of header -->
<head>  
<!-- ***************************** Google Login ******************************* -->
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script src="https://apis.google.com/js/api:client.js"></script>
<script>
  var googleUser = {};
  var startApp = function() {
    gapi.load('auth2', function(){
      // Retrieve the singleton for the GoogleAuth library and set up the client.
      auth2 = gapi.auth2.init({
        client_id: '746672538561-1fn5ahs25js9agf9mjd0md6tqup46rg8.apps.googleusercontent.com',
        cookiepolicy: 'single_host_origin',
        // Request scopes in addition to 'profile' and 'email'
        //scope: 'additional_scope'
      });
      attachSignin(document.getElementById('googleBtn'));
    });
  };

  function attachSignin(element) {
    console.log(element.id);
    auth2.attachClickHandler(element, {},
        function(googleUser) {
              $('#is_google').val('1');
              $('#inputEmail').val(googleUser.getBasicProfile().getEmail());
              $('#google_fname').val(googleUser.getBasicProfile().getGivenName());
              $('#google_lname').val(googleUser.getBasicProfile().getFamilyName());
              var pw = "";
              var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
              for( var i=0; i < 5; i++ ){
                pw += possible.charAt(Math.floor(Math.random() * possible.length));}
                $('#inputPassword').val(pw);
                $( "#login" ).submit();
        }, function(error) {
          alert(JSON.stringify(error, undefined, 2));
        });
  }
  </script>

  <!-- ***************************** LinkedIN Login ******************************* -->
<script type="text/javascript" src="http://platform.linkedin.com/in.js">
api_key:81be87pfhxnucx
authorization:true
scope: r_basicprofile r_emailaddress
onLoad: OnLinkedInFrameworkLoad
</script>

<script type="text/javascript">
    function OnLinkedInFrameworkLoad() {
     $('a[id*=li_ui_li_gen_]').css({marginBottom:'20px'}).html('<button class="linkedin-btn block-btn" style="padding-right: 62px;"><i class="fa fa-linkedin"></i>Sign In with Linkedin</button>');
      IN.Event.on(IN, "auth", OnLinkedInAuth);
    }

    function OnLinkedInAuth() {
        IN.API.Profile("me").result(ShowProfileData);
    }

    

    function ShowProfileData(profiles) {
        var member = profiles.values[0];
        var id=member.id;
        var firstName=member.firstName; 
        var lastName=member.lastName; 
        var photo=member.pictureUrl; 
        var headline=member.headline; 
        console.log(profiles);
        $('#is_linkedin').val('1');
        $('#inputEmail').val(member.emailAddress);
        $('#linkedin_fname').val(member.firstName);
        $('#linkedin_lname').val(member.lastName);
        var pw = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for( var i=0; i < 5; i++ ){
          pw += possible.charAt(Math.floor(Math.random() * possible.length));}
          $('#inputPassword').val(pw);
          $( "#login" ).submit();

      //use information captured above
    }

    // Handle an error response from the API call
    function onError(error) {
        alert(error);
    }

    // Use the API call wrapper to request the member's basic profile data
    function getProfileData() {
        IN.API.Raw("/people/~:(id,firstName,lastName,emailAddress)?format=json").result(onSuccess).error(onError);
    }

</script>
</head>
  <main class="main">
    <div class="container rigester-page">
      <div class="row row-centered">
        <div class="col-lg-8 col-xs-12 right-content col-centered">
          <ul class="nav nav-tabs">
              <li class="active"><a id="signin_button" href="<?php echo $_SERVER['REQUEST_URI']; ?>#signin-tab">Sign In</a></li>
              <li><a id="register_button" href="<?php echo $_SERVER['REQUEST_URI']; ?>#register-tab" >Register</a></li>
          </ul>
          <div class="tab-content">
              <div id="signin-tab" class="tab-pane fade in active">
                <div class="row row-centered">
                  
                  <div class="col-lg-9  col-xs-11 col-centered">
                  <?php
                  if($error_warning)
                  {
                    ?>
                    <div class="alert alert-danger alert-dismissible"  role="alert"><?php echo $error_warning; ?></div>
                    <?php
                  }


                  ?>

                  <?php
                  if(isset($this->session->data['success']))
                  {
                    ?>
                    <div class="alert alert-success alert-dismissible"  role="alert"><?php echo $this->session->data['success']; ?></div>
                    <?php
                  }

                  
                  ?>

                    <form id="login" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                      <div class="form-group has-feedback">
    <!-- <label class="control-label">Email</label> -->
    <i class="glyphicon glyphicon-user form-control-feedback"></i>
    <input type="text" class="form-control" name="email" placeholder="Email Address" value="<?php echo $email;?>" required />
    
</div>

<div class="form-group has-feedback" >
    <!-- <label class="control-label">Email</label> -->
    <i class="glyphicon glyphicon-lock form-control-feedback"></i>
    <input type="password" name="password" class="form-control" placeholder="Password" value="<?php echo $password;?>" required />
    
</div>
 <input type="hidden" name="fb_fname" id="fb_fname" value= "x" />
                          <input type="hidden" name="fb_lname" id="fb_lname" value= "x" />
                          <input type="hidden" name="is_fb" id="is_fb" value= "0" />
                          <input type="hidden" name="google_fname" id="google_fname" value= "x" />
                          <input type="hidden" name="google_lname" id="google_lname" value= "x" />
                          <input type="hidden" name="is_google" id="is_google" value= "0" />
                          <input type="hidden" name="linkedin_fname" id="linkedin_fname" value= "x" />
                          <input type="hidden" name="linkedin_lname" id="linkedin_lname" value= "x" />
                          <input type="hidden" name="is_linkedin" id="is_linkedin" value= "0" />

                      
                     
                      <div class="form-group">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-10 text-right">
                          <a href="<?php echo $forgotten; ?>" style="font-size: 12px; color: grey; margin-top: -10px;" class="underline">Forgot your password?</a>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="text-center form-submit">
                          <input type="submit" value="Sign in" class="btn btn-primary">
                          <?php if ($redirect) { ?>
                            <input type="hidden" name="redirect" value="<?php echo ($redirect) ? $redirect: 'account/account'; ?>" />
                           <?php } ?>
                      </div>
                    </form> 
                  </div>  
                </div>
                <!-- <div align="center" class="border"></div> -->
                <div class="row row-centered hidden">
                  <div class="col-md-10 col-centered">
                  
                    <div class="big-social row">
                      <div class="col-sm-6">
                        <button class="fb-btn block-btn" onclick="loginFacebook();"><i class="fa fa-facebook"></i>Sign In with facebook</button>
                      </div>
                      <div class="col-sm-6">
                        <a href='<?php echo $url ?>' class="twitter-btn block-btn"><i class="fa fa-twitter"></i>Sign In with twitter</a>
                      </div>
                    </div>
                    <div class="big-social row">
                      <div class="col-sm-6">
                        <div  style=""><script type="in/Login"></script></div>
                      </div>
                      <div class="col-sm-6">
                        <button id="googleBtn" class="gplus-btn block-btn" ><i class="fa fa-google-plus"></i>Sign In with google</button>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>  
              </div>
              <div id="register-tab" class="tab-pane fade">
                 
                <?php echo $register_template;?>
                
              </div>
          </div>    
        </div>
      </div>
    </div>
  </main><!-- @End of main -->
  <script type="text/javascript"><!--
$('#login input').keydown(function(e) {
  if (e.keyCode == 13) {
    $('#login').submit();
  }
});
//--></script>
<script>startApp();</script>
<script>
//********************************** Facebook Login ***********************************
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1345064072180669',
      xfbml      : true,
      version    : 'v2.8'
    });
   /* FB.getLoginStatus(function(response){
      if(response.status == 'connected'){
        $('#status').html('We Are Connected to Faceboook.');
      }else if (response.status == 'not_authorized') {
        $('#status').html('Not Authorized PPUSA app on Facebook.');
      }else {
        $('#status').html('Not Logged into Facebook. Please Login.');
      }
    });*/
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
  function loginFacebook(){
    FB.login(function(response){
      if (response.authResponse) {
     FB.api('/me',{ locale: 'en_US', fields: 'first_name,last_name,name, email' }, function(response) {
       $('#is_fb').val('1');
       $('#inputEmail').val(response.email);
       $('#fb_fname').val(response.first_name);
       $('#fb_lname').val(response.last_name);
       var pw = "";
       var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
       for( var i=0; i < 5; i++ ){
        pw += possible.charAt(Math.floor(Math.random() * possible.length));}
       $('#inputPassword').val(pw);
       $( "#login" ).submit();
     });
    } else {
     alert('Did not fully authorize. Please Login Again.');
    }
      /*if(response.status == 'connected'){
        $('#status').html('We Are Connected to Facebook');
      }else if (response.status == 'not_authorized') {
        $('#status').html('Not Authorized PPUSA app on Facebook.');
      }else {
        $('#status').html('Not Logged into Facebook. Please Login.');
      }*/
    },{scope: 'email'});
  }
</script>


<!-- <script type="text/javascript">
  jQuery(document).ready(function(){
        jQuery('#click_register').on('click', function(event) {        
           jQuery('#signin-tab').toggle('hide');
             jQuery('#register-tab').toggle('show');
            
        });
        
    });
</script>

<script type-"text/javascript">
jQuery(document).ready(function(){
        jQuery('#click_signin').on('click', function(event) {
           jQuery('#register-tab').toggle('hide');        
           jQuery('#signin-tab').toggle('show');
            
        });
        
    });
</script>

<script>
document.getElementById("signin_button").addEventListener("click", function(event){
    event.preventDefault()
});
</script>
<script>
document.getElementById("register_button").addEventListener("click", function(event){
    event.preventDefault()
});
</script> -->

<?php echo $footer; ?>