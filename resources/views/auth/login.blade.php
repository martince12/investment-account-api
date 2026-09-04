<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | Investment Account</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f5f5f5;

            background:
                radial-gradient(circle at top left, rgba(82, 82, 91, 0.30), transparent 38%),
                radial-gradient(circle at bottom right, rgba(63, 63, 70, 0.20), transparent 35%),
                #0f0f10;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;

            padding: 40px;

            background: rgba(24, 24, 27, 0.88);
            border: 1px solid #303034;
            border-radius: 16px;

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.03);

            backdrop-filter: blur(12px);
        }

        .brand {
            margin-bottom: 32px;
        }

        .brand-badge {
            width: 42px;
            height: 42px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 20px;

            border-radius: 10px;
            background: #27272a;
            border: 1px solid #3f3f46;

            font-weight: 700;
            font-size: 18px;
            color: #ffffff;
        }

        h1 {
            font-size: 28px;
            font-weight: 650;
            letter-spacing: -0.7px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #a1a1aa;
            font-size: 14px;
            line-height: 1.5;
        }

        .error {
            margin-bottom: 22px;
            padding: 12px 14px;

            border: 1px solid rgba(248, 113, 113, 0.30);
            border-radius: 8px;

            background: rgba(127, 29, 29, 0.18);
            color: #fca5a5;

            font-size: 13px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;

            color: #d4d4d8;
            font-size: 13px;
            font-weight: 500;
        }

        input {
            width: 100%;
            height: 46px;

            padding: 0 14px;

            border: 1px solid #3f3f46;
            border-radius: 9px;

            background: #18181b;
            color: #fafafa;

            font-size: 14px;
            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        input::placeholder {
            color: #71717a;
        }

        input:focus {
            border-color: #71717a;
            background: #1c1c1f;
            box-shadow: 0 0 0 3px rgba(113, 113, 122, 0.14);
        }

        .sign-in-button {
            width: 100%;
            height: 46px;

            margin-top: 8px;

            border: 1px solid #52525b;
            border-radius: 9px;

            background: #f4f4f5;
            color: #18181b;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition:
                transform 0.15s ease,
                background 0.2s ease;
        }

        .sign-in-button:hover {
            background: #ffffff;
            transform: translateY(-1px);
        }

        .sign-in-button:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 26px;

            text-align: center;
            color: #71717a;
            font-size: 12px;
        }

        @media (max-width: 520px) {
            .login-card {
                padding: 30px 24px;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <main class="login-card">

        <div class="brand">
            <div class="brand-badge">IA</div>

            <h1>Welcome back</h1>

            <p class="subtitle">
                Sign in to access your investment account.
            </p>
        </div>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label for="email">
                    Email address
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="name@example.com"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">
                    Password
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button class="sign-in-button" type="submit">
                Sign in
            </button>
        </form>

        <p class="footer-text">
            Investment Account Management
        </p>

    </main>

</div>

</body>
</html>
```
