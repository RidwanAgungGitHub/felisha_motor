<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Falisa Inventory - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(1deg);
            }
        }

        .login-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
            z-index: 1;
            position: relative;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #333333 0%, #1a1a1a 100%);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
            border-bottom: none;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="30" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="20" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="60" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }

        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 3rem 2.5rem;
        }

        .form-floating {
            position: relative;
            margin-bottom: 2rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            height: 60px;
            line-height: 1.25;
            padding: 1rem 1rem 1rem 3rem;
            /* Add left padding for icon space */
            direction: ltr;
            text-align: left;
        }

        .form-control:focus {
            border-color: #333333;
            box-shadow: 0 0 0 0.25rem rgba(51, 51, 51, 0.25);
            background: white;
        }

        .form-control:focus~label,
        .form-control:not(:placeholder-shown)~label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            /* Adjusted positioning for better alignment */
        }

        .form-floating>label {
            position: absolute;
            top: 0;
            left: 3rem;
            /* Align with text input area after icon */
            z-index: 2;
            height: 100%;
            padding: 1rem 0.25rem;
            overflow: hidden;
            text-align: start;
            text-overflow: ellipsis;
            white-space: nowrap;
            pointer-events: none;
            border: 1px solid transparent;
            transform-origin: 0 0;
            transition: opacity 0.15s ease-in-out, transform 0.15s ease-in-out;
            color: #6c757d;
            font-weight: 500;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        /* Icon animation when focused or filled - same as label */
        .form-control:focus~.input-icon,
        .form-control:not(:placeholder-shown)~.input-icon {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            top: 0;
            left: 2.8rem;
            color: #333333;
        }

        .form-check {
            margin-bottom: 2.5rem;
        }

        .form-check-input:checked {
            background-color: #333333;
            border-color: #333333;
        }

        .form-check-label {
            font-weight: 500;
            color: #495057;
        }

        .btn-login {
            background: linear-gradient(135deg, #333333 0%, #1a1a1a 100%);
            border: none;
            border-radius: 15px;
            padding: 1.3rem 2rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(51, 51, 51, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            margin-bottom: 1.5rem;
        }

        .alert ul {
            margin-bottom: 0;
        }

        /* Loading animation */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .login-container {
                padding: 15px;
                max-width: 95%;
            }

            .card-body {
                padding: 2.5rem 2rem;
            }

            .brand-title {
                font-size: 1.8rem;
            }

            .brand-subtitle {
                font-size: 1rem;
            }
        }

        /* Focus indicators for accessibility */
        .form-control:focus,
        .btn-login:focus,
        .form-check-input:focus {
            outline: 2px solid #333333;
            outline-offset: 2px;
        }

        /* Fix for input text alignment */
        .form-control::placeholder {
            color: transparent;
        }

        /* Ensure proper text direction and alignment */
        .form-control[type="email"],
        .form-control[type="password"] {
            unicode-bidi: normal;
            text-align: left !important;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <div class="brand-title">
                    <i class="fas fa-boxes me-2"></i>
                    FALISA INVENTORY
                </div>
                <div class="brand-subtitle">Sistem Manajemen Inventori</div>
            </div>
            <div class="card-body">
                <!-- Error messages display -->
                <div id="error-container" style="display: none;" class="alert alert-danger">
                    <ul class="mb-0" id="error-list">
                    </ul>
                </div>

                <!-- Success messages display -->
                <div id="success-container" style="display: none;" class="alert"
                    style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                    <div id="success-message"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder=" "
                            required autofocus value="{{ old('email') }}">
                        <i class="fas fa-envelope input-icon"></i>
                        <label for="email">Alamat Email</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder=" "
                            required>
                        <i class="fas fa-lock input-icon"></i>
                        <label for="password">Kata Sandi</label>
                    </div>

                    <button type="submit" class="btn btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        <span id="btnText">Masuk ke Sistem</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script>
        // Add loading state to login button
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');

            btn.classList.add('loading');
            btnText.textContent = 'Memproses...';
            btn.disabled = true;
        });

        // Add smooth focus transitions
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Ensure proper text alignment on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                // Force text alignment to left
                this.style.textAlign = 'left';
            });
        });

        // Display Laravel errors if any
        @if ($errors->any())
            document.getElementById('error-container').style.display = 'block';
            const errorList = document.getElementById('error-list');
            @foreach ($errors->all() as $error)
                const li = document.createElement('li');
                li.textContent = "{{ $error }}";
                errorList.appendChild(li);
            @endforeach
        @endif

        // Display success message if any
        @if (session('success'))
            document.getElementById('success-container').style.display = 'block';
            document.getElementById('success-message').textContent = "{{ session('success') }}";
        @endif
    </script> --}}
</body>

</html>
