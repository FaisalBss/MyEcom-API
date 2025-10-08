@extends('Layouts.master')

@section('content')
<div class="product-section mt-150 mb-150">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
          <h3><span class="orange-text">Account</span> Login</h3>
          <p>Welcome back! Sign in to continue.</p>
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8 mb-5 mb-lg-0">
        <div class="form-title">
          <h2>Login</h2>
        </div>

        <div class="contact-form">
          <form id="loginForm" style="text-align:left">
            @csrf

            <p>
              <input type="email" style="width:100%" name="email" id="email"
                     placeholder="Email" required>
            </p>

            <p>
              <input type="password" style="width:100%" name="password" id="password"
                     placeholder="Password" required>
            </p>

            <p style="display:flex;justify-content:space-between;align-items:center">
              <a href="{{ url('/forgot-password') }}">Forgot password?</a>
            </p>

            <p><input type="submit" value="Sign in"></p>

            <p>Don’t have an account?
              <a href="{{ route('register') }}">Create one</a>
            </p>
          </form>

          <div id="loginMessage" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById("loginForm").addEventListener("submit", async function(e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const msgDiv = document.getElementById("loginMessage");

  msgDiv.innerHTML = '<div class="alert alert-secondary">⏳ Signing in...</div>';

  try {
    const response = await fetch("/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({ email, password })
    });

    //const data = await response.json();

    const text = await response.text();
console.log('Raw Response:', text);
let data;
try { data = JSON.parse(text); } catch(e) { data = {}; }


    if (response.ok && data.success) {
      localStorage.setItem("token", data.token);
      msgDiv.innerHTML = `<div class="alert alert-success">${data.message || 'Login successful!'}</div>`;

      setTimeout(() => {
        if (data.user && data.user.role === 1) {
          window.location.href = "/admin";
        } else {
          window.location.href = "/";
        }
      }, 1200);
    } else {
      msgDiv.innerHTML = `<div class="alert alert-danger">${data.message || 'Invalid credentials'}</div>`;
    }
  } catch (error) {
    msgDiv.innerHTML = `<div class="alert alert-danger">⚠️ Something went wrong. Please try again.</div>`;
  }
});
</script>
@endsection
