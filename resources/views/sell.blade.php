<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sell {{ $holding->ticker }} | Investment Account</title>

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

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 480px;

            padding: 36px;

            background: rgba(24, 24, 27, 0.88);
            border: 1px solid #303034;
            border-radius: 16px;

            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.03);
        }

        .back {
            display: inline-block;
            margin-bottom: 28px;

            color: #a1a1aa;
            font-size: 13px;
        }

        .back:hover {
            color: #ffffff;
        }

        .eyebrow {
            margin-bottom: 8px;

            color: #71717a;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin-bottom: 8px;

            font-size: 28px;
            font-weight: 650;
            letter-spacing: -0.7px;
        }

        .subtitle {
            margin-bottom: 26px;
            color: #a1a1aa;
            font-size: 13px;
        }

        .holding-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;

            margin-bottom: 28px;
        }

        .info-card {
            padding: 16px;

            border: 1px solid #303034;
            border-radius: 10px;

            background: #18181b;
        }

        .info-label {
            margin-bottom: 6px;

            color: #71717a;
            font-size: 11px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 17px;
            font-weight: 600;
        }

        .error {
            margin-bottom: 20px;
            padding: 12px 14px;

            border: 1px solid rgba(248, 113, 113, 0.30);
            border-radius: 8px;

            background: rgba(127, 29, 29, 0.18);
            color: #fca5a5;

            font-size: 13px;
        }

        .form-group {
            margin-bottom: 18px;
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

            outline: none;
        }

        input:focus {
            border-color: #71717a;
            box-shadow: 0 0 0 3px rgba(113, 113, 122, 0.14);
        }

        .sell-button {
            width: 100%;
            height: 46px;

            margin-top: 6px;

            border: 1px solid rgba(248, 113, 113, 0.30);
            border-radius: 9px;

            background: rgba(248, 113, 113, 0.10);
            color: #fca5a5;

            font-weight: 600;
            cursor: pointer;
        }

        .sell-button:hover {
            background: rgba(248, 113, 113, 0.16);
        }
    </style>
</head>

<body>

<div class="page">

    <main class="card">

        <a
            class="back"
            href="{{ route('dashboard') }}"
        >
            ← Back to dashboard
        </a>

        <div class="eyebrow">
            Sell Security
        </div>

        <h1>
            Sell {{ $holding->ticker }}
        </h1>

        <p class="subtitle">
            Enter the quantity and sale price for this transaction.
        </p>

        <div class="holding-info">

            <div class="info-card">
                <div class="info-label">
                    Available Quantity
                </div>

                <div class="info-value">
                    {{ $holding->quantity }}
                </div>
            </div>

            <div class="info-card">
                <div class="info-label">
                    Current Price
                </div>

                <div class="info-value">
                    {{ number_format((float) $holding->current_price, 2) }}
                    {{ $account->currency }}
                </div>
            </div>

        </div>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('transactions.store') }}"
        >
            @csrf

            <input
                type="hidden"
                name="type"
                value="sell"
            >

            <input
                type="hidden"
                name="ticker"
                value="{{ $holding->ticker }}"
            >

            <div class="form-group">
                <label for="quantity">
                    Quantity
                </label>

                <input
                    id="quantity"
                    type="number"
                    name="quantity"
                    min="1"
                    max="{{ $holding->quantity }}"
                    step="1"
                    value="{{ old('quantity') }}"
                    placeholder="1"
                    required
                >
            </div>

            <div class="form-group">
                <label for="price">
                    Sale Price per Unit
                </label>

                <input
                    id="price"
                    type="number"
                    name="price"
                    min="0.01"
                    step="0.01"
                    value="{{ old('price') }}"
                    placeholder="0.00"
                    required
                >
            </div>

            <button
                class="sell-button"
                type="submit"
            >
                Sell {{ $holding->ticker }}
            </button>

        </form>

    </main>

</div>

</body>
</html>