@extends('Layouts.master')

@section('content')
<div class="product-section mt-150 mb-150">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2 text-center">
        <div class="section-title">
          <h3><span class="orange-text">Create</span> Account</h3>
          <p>Join us and start exploring our products.</p>
        </div>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8 mb-5 mb-lg-0">
        <div class="form-title">
          <h2>Register</h2>
        </div>

        <div class="contact-form">
          <form id="registerForm" style="text-align:left">
            @csrf
            <p>
              <input type="text" style="width:100%" name="name" id="name"
                     placeholder="Full name" required>
            </p>

            <p>
              <input type="email" style="width:100%" name="email" id="email"
                     placeholder="Email" required>
            </p>

            <p style="display:flex;gap:2%">
              <input type="password" style="width:49%" name="password" id="password"
                     placeholder="Password" required>
              <input type="password" style="width:49%" name="password_confirmation"
                     id="password_confirmation" placeholder="Confirm password" required>
            </p>

            <p><button type="submit">Create Account</button></p>

            <p>Already have an account?
              <a href="{{ route('login') }}">Login</a>
            </p>
          </form>

          <div id="responseMessage" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const data = {
    name: document.getElementById('name').value.trim(),
    email: document.getElementById('email').value.trim(),
    password: document.getElementById('password').value,
    password_confirmation: document.getElementById('password_confirmation').value,
  };
  const messageDiv = document.getElementById('responseMessage');
  messageDiv.innerHTML = '<span style="color:gray;">⏳ Creating account...</span>';
  try {
    const response = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(data)
    });
    const result = await response.json();
    messageDiv.innerHTML = '';
    if (response.ok) {
      messageDiv.innerHTML = `<span style="color:green;">✅ ${result.message}</span>`;
      localStorage.setItem('token', result.token);
      setTimeout(() => {
        window.location.href = '/';
      }, 1500);
    } else {
      messageDiv.innerHTML = `<span style="color:red;">❌ ${result.message || 'Registration failed.'}</span>`;
      if (result.errors) {
        for (const [key, val] of Object.entries(result.errors)) {
          messageDiv.innerHTML += `<div style="color:red;">${val}</div>`;
        }
      }
    }
  } catch (error) {
    messageDiv.innerHTML = `<span style="color:red;">⚠️ Something went wrong. Please try again.</span>`;
  }
});
</script>
@endsection
