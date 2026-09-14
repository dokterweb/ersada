
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Sign in</title>
    <!-- CSS files -->
    <link href="{{asset('tabler')}}/dist/css/tabler.min.css?1684106062" rel="stylesheet"/>
    <link href="{{asset('tabler')}}/dist/css/tabler-flags.min.css?1684106062" rel="stylesheet"/>
    <link href="{{asset('tabler')}}/dist/css/tabler-payments.min.css?1684106062" rel="stylesheet"/>
    <link href="{{asset('tabler')}}/dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet"/>
    <link href="{{asset('tabler')}}/dist/css/demo.min.css?1684106062" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>
  <body  class=" d-flex flex-column">
    <script src="{{asset('tabler')}}/dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark"><img src="./static/logo.svg" height="36" alt=""></a>
        </div>
        <div class="card card-md">
          <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="text-center mb-4">
              <img src="{{ asset('storage/img/logo.jpg') }}" alt="Safarindo Albarokah Umroh"style="max-width: 180px; height: auto;">
            </div>
            <h2 class="h2 text-center mb-4">Login to your account</h2>
            <form action="{{ route('login') }}" method="POST" autocomplete="off" novalidate>
                @csrf
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="your@email.com" autocomplete="off">
              </div>
              <div class="mb-2">
                <label class="form-label">Password</label>

                <div class="input-group input-group-flat">
                    <input type="password" class="form-control" id="password" name="password"
                        required placeholder="Your password" autocomplete="off">
                    <span class="input-group-text">
                        <a href="#" class="link-secondary toggle-password" title="Show password" data-bs-toggle="tooltip" aria-label="Show password">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-eye"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                            </svg>
                        </a>
                    </span>
                </div>
            </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{asset('tabler')}}/dist/js/tabler.min.js?1684106062" defer></script>
    <script src="{{asset('tabler')}}/dist/js/demo.min.js?1684106062" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function () {

          $('.toggle-password').on('click', function (e) {
              e.preventDefault();

              const passwordInput = $('#password');
              const icon = $(this).find('.icon-eye');

              if (passwordInput.attr('type') === 'password') {

                  // Tampilkan password
                  passwordInput.attr('type', 'text');

                  $(this)
                      .attr('title', 'Hide password')
                      .attr('aria-label', 'Hide password');

                  // Ganti icon menjadi eye-off
                  icon.html(`
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                      <path d="M10.5 10.5a2 2 0 0 0 2.9 2.9"/>
                      <path d="M17 17c-1.5 1-3.2 1.5-5 1.5c-3.6 0-6.6-2-9-6c1.2-2 2.5-3.5 4-4.5"/>
                      <path d="M19.5 14.5c.6-.6 1.1-1.3 1.5-2c-2.4-4-5.4-6-9-6c-1.2 0-2.3.2-3.3.6"/>
                      <path d="M3 3l18 18"/>
                  `);

              } else {

                  // Sembunyikan password
                  passwordInput.attr('type', 'password');

                  $(this)
                      .attr('title', 'Show password')
                      .attr('aria-label', 'Show password');

                  // Kembali ke icon eye
                  icon.html(`
                      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                      <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                      <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4-4 5.4-6 9-6c3.6 0 6.6 2 9 6"/>
                  `);
              }
          });

      });
    </script>
  </body>
</html>