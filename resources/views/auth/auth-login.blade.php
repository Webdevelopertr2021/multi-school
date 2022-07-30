<!DOCTYPE html>
<html lang="en">


<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>{{ env("APP_NAME") }} - Dashboard Login</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset("backend/css/app.min.css") }}">
  <link rel="stylesheet" href="{{ asset("backend/bundles/bootstrap-social/bootstrap-social.css") }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset("backend/css/style.css") }}">
  <link rel="stylesheet" href="{{ asset("backend/css/components.css") }}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{ asset("backend/css/custom.css") }}">
  <link rel='shortcut icon' type='image/x-icon' href='{{ asset("backend/img/favicon.ico") }}' />
</head>

<body>
  <div class="loader"></div>
  <div id="loginForm">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="card card-primary">
              <div class="card-header">
                <h4>Login</h4>
              </div>
              <div class="card-body">
                <form @submit.prevent="userLogin">
                  <div class="form-group">
                    <label for="email">Email / Phone</label>
                    <input id="email" type="text" class="form-control" :class="{'is-invalid': loginForm.errors.has('email')}" 
                    name="email" tabindex="1" autofocus v-model="loginForm.email" placeholder="Give your email or phone number...">
                    <has-error :form="loginForm" field="email" />
                  </div>
                  <div class="form-group">
                    <div class="d-block">
                      <label for="password" class="control-label">Password</label>
                      <div class="float-right">
                        <a href="auth-forgot-password.html" class="text-small">
                          Forgot Password?
                        </a>
                      </div>
                    </div>
                    <input id="password" type="password" class="form-control" :class="{'is-invalid': loginForm.errors.has('password')}" 
                    name="password" tabindex="2" v-model="loginForm.password" placeholder="Password..." autocomplete="">
                    <has-error :form="loginForm" field="password" />
                  </div>
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" v-model="loginForm.remember">
                      <label class="custom-control-label" for="remember-me">Remember Me</label>
                    </div>
                  </div>
                  <div class="form-group">
                    <button :disabled="loginForm.busy" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- General JS Scripts -->
  <script src="{{ asset("backend/js/app.min.js") }}"></script>
  <script src="{{ asset("js/app.js") }}"></script>
  <!-- JS Libraies -->
  <!-- Page Specific JS File -->
  <!-- Template JS File -->
  <script src="{{ asset("backend/js/scripts.js") }}"></script>
  <!-- Custom JS File -->
  <script src="{{ asset("backend/js/custom.js") }}"></script>
</body>


<!-- auth-login.html  21 Nov 2019 03:49:32 GMT -->
</html>