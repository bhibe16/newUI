<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRIS | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            overflow: hidden;
        }
        
        .image-container {
    width: 75vw; /* 50% of the viewport width */
    height: 95vh; /* 100% of the viewport height */
    background-image: url('{{ asset('images/hr3.gif') }}');
    background-repeat: no-repeat;
    background-size: cover; /* ensures the image covers the whole container */
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

        
        .image-content {
            width: 80%;
            height: 80%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.1));
        }
        
        .login-box {
            width: 50%;
            max-width: 420px;
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: left;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .login-box h2 {
            margin-bottom: 1.5rem;
            color: #2d3748;
            font-size: 1.75rem;
            font-weight: 600;
            text-align: center;
        }
        
        .logo {
            display: block;
            margin: 0 auto 1.5rem auto;
            max-width: 120px;
            height: auto;
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        
        .input-field:focus {
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        
        .login-btn {
            background: linear-gradient(135deg, #f6ad55 0%, #f6871f 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(246, 173, 85, 0.3);
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(246, 173, 85, 0.4);
        }
        
        .forgot-password:hover {
            color: #3182ce;
            text-decoration: underline;
        }
        
        .remember-me {
            accent-color: #f6871f;
        }
        
        .error-message {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .floating-decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(246, 173, 85, 0.1);
            z-index: -1;
        }
        
        .decoration-1 {
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
        }
        
        .decoration-2 {
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
        }
        
        @media (max-width: 1024px) {
            .image-container {
                background-size: 60%;
            }
        }
        
        @media (max-width: 768px) {
            body {
                flex-direction: column;
                justify-content: center;
                padding: 20px;
            }
            
            .image-container {
                display: none;
            }
            
            .login-box {
                width: 100%;
                max-width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="image-container">
        <div class="image-content"></div>
    </div>
    
    <div class="login-box">
        <div class="floating-decoration decoration-1"></div>
        <div class="floating-decoration decoration-2"></div>
        
        <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="logo">
        <h2>Welcome to HRIS</h2>
        <p class="text-center text-gray-600 mb-6">Please login to access your account</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Custom Error Messages -->
        @if (session('error'))
            <div class="mb-4 text-center text-red-600 bg-red-50 p-3 rounded-md error-message">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->has('role'))
            <div class="mb-4 text-center text-red-600 bg-red-50 p-3 rounded-md error-message">
                {{ $errors->first('role') }}
            </div>
        @endif

        @if(request()->has('session_expired'))
            <div class="mb-4 text-center text-red-600 bg-red-50 p-3 rounded-md error-message">
                You were logged out due to inactivity.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input id="email" class="input-field block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email">
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input id="password" class="input-field block w-full"
                    type="password"
                    name="password"
                    required autocomplete="current-password" 
                    placeholder="Enter your password">
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="remember-me rounded border-gray-300 shadow-sm focus:ring-orange-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-orange-600 hover:text-orange-700 forgot-password" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" class="login-btn w-full py-3 px-4 text-white font-semibold rounded-lg shadow-md transition">
                {{ __('Log in') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline ml-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </form>
        
        <!-- Footer Note -->
        <p class="text-center text-xs text-gray-500 mt-6">
            Need help? Contact IT support at <a href="mailto:support@company.com" class="text-orange-600">support@company.com</a>
        </p>
    </div>
</body>
</html>