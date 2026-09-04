<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Investment Account</title>

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
                radial-gradient(circle at top left, rgba(82, 82, 91, 0.24), transparent 35%),
                radial-gradient(circle at bottom right, rgba(63, 63, 70, 0.16), transparent 35%),
                #0f0f10;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input {
            font: inherit;
        }

        .page {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 32px 48px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 42px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-badge {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9px;
            background: #27272a;
            border: 1px solid #3f3f46;

            font-weight: 700;
            font-size: 15px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 600;
            color: #e4e4e7;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link,
        .logout-button {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0 15px;

            border-radius: 8px;
            border: 1px solid #3f3f46;
            background: rgba(39, 39, 42, 0.7);

            color: #d4d4d8;
            font-size: 13px;
            font-weight: 500;

            cursor: pointer;
        }

        .nav-link:hover,
        .logout-button:hover {
            background: #27272a;
            border-color: #52525b;
            color: #ffffff;
        }

        .hero {
            margin-bottom: 30px;
        }

        .eyebrow {
            margin-bottom: 8px;
            color: #71717a;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero h1 {
            margin-bottom: 8px;
            font-size: 34px;
            font-weight: 650;
            letter-spacing: -1px;
        }

        .hero p {
            color: #a1a1aa;
            font-size: 14px;
        }

        .alert {
            margin-bottom: 24px;
            padding: 14px 16px;

            border-radius: 9px;
            font-size: 13px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.08);
            border: 1px solid rgba(34, 197, 94, 0.22);
            color: #86efac;
        }

        .alert-error {
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.22);
            color: #fca5a5;
        }

        .balance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .balance-card {
            padding: 24px;

            border: 1px solid #303034;
            border-radius: 14px;

            background: rgba(24, 24, 27, 0.86);

            box-shadow:
                0 14px 40px rgba(0, 0, 0, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.025);
        }

        .balance-card.primary {
            background:
                linear-gradient(
                    135deg,
                    rgba(39, 39, 42, 0.95),
                    rgba(24, 24, 27, 0.90)
                );
        }

        .card-label {
            margin-bottom: 14px;
            color: #a1a1aa;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .balance-value {
            font-size: 29px;
            font-weight: 650;
            letter-spacing: -0.8px;
        }

        .currency {
            margin-left: 5px;
            color: #71717a;
            font-size: 14px;
            font-weight: 500;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .action-card {
            padding: 22px;

            border: 1px solid #303034;
            border-radius: 14px;

            background: rgba(24, 24, 27, 0.82);
        }

        .action-card h3 {
            margin-bottom: 6px;
            font-size: 15px;
            font-weight: 600;
        }

        .action-card p {
            margin-bottom: 18px;
            color: #71717a;
            font-size: 12px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 7px;

            color: #a1a1aa;
            font-size: 12px;
            font-weight: 500;
        }

        input {
            width: 100%;
            height: 42px;
            padding: 0 12px;

            border: 1px solid #3f3f46;
            border-radius: 8px;

            background: #18181b;
            color: #fafafa;

            outline: none;
        }

        input:focus {
            border-color: #71717a;
            box-shadow: 0 0 0 3px rgba(113, 113, 122, 0.12);
        }

        .action-button {
            width: 100%;
            height: 42px;

            margin-top: 5px;

            border: 1px solid #52525b;
            border-radius: 8px;

            background: #f4f4f5;
            color: #18181b;

            font-size: 13px;
            font-weight: 600;

            cursor: pointer;
        }

        .action-button:hover {
            background: #ffffff;
        }

        .section {
            overflow: hidden;

            border: 1px solid #303034;
            border-radius: 14px;

            background: rgba(24, 24, 27, 0.82);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 20px 22px;

            border-bottom: 1px solid #303034;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
        }

        .section-subtitle {
            margin-top: 4px;
            color: #71717a;
            font-size: 12px;
        }

        .holding-count {
            padding: 6px 10px;

            border-radius: 999px;
            border: 1px solid #3f3f46;

            background: #27272a;
            color: #a1a1aa;

            font-size: 11px;
            font-weight: 600;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 13px 22px;

            background: rgba(39, 39, 42, 0.45);
            color: #71717a;

            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 18px 22px;
            border-top: 1px solid #27272a;

            color: #d4d4d8;
            font-size: 14px;
        }

        tbody tr:hover {
            background: rgba(39, 39, 42, 0.42);
        }

        .ticker {
            display: inline-flex;
            align-items: center;

            padding: 6px 9px;

            border-radius: 7px;
            border: 1px solid #3f3f46;

            background: #27272a;
            color: #fafafa;

            font-size: 12px;
            font-weight: 700;
        }

        .sell-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            height: 34px;
            padding: 0 12px;

            border-radius: 7px;
            border: 1px solid rgba(248, 113, 113, 0.25);

            background: rgba(248, 113, 113, 0.07);
            color: #fca5a5;

            font-size: 12px;
            font-weight: 600;
        }

        .sell-button:hover {
            background: rgba(248, 113, 113, 0.13);
        }

        .empty-state {
            padding: 55px 24px;
            text-align: center;
            color: #71717a;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .balance-grid,
            .actions-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .page {
                padding: 22px 18px 40px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }

            .topbar-actions {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <header class="topbar">

        <div class="brand">
            <div class="brand-badge">IA</div>
            <span class="brand-name">Investment Account</span>
        </div>

        <div class="topbar-actions">

            <a
                class="nav-link"
                href="{{ route('transactions.history') }}"
            >
                Transaction History
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="logout-button" type="submit">
                    Logout
                </button>
            </form>

        </div>

    </header>


    <main>

        <section class="hero">

            <div class="eyebrow">
                Account Dashboard
            </div>

            <h1>
                Welcome, {{ $client->name }}
            </h1>

            <p>
                Manage your cash and portfolio holdings.
            </p>

        </section>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif


        <section class="balance-grid">

            <article class="balance-card primary">
                <div class="card-label">Total Balance</div>

                <div class="balance-value">
                    {{ number_format((float) $account->total_balance, 2) }}

                    <span class="currency">
                        {{ $account->currency }}
                    </span>
                </div>
            </article>

            <article class="balance-card">
                <div class="card-label">Available Cash</div>

                <div class="balance-value">
                    {{ number_format((float) $account->cash_balance, 2) }}

                    <span class="currency">
                        {{ $account->currency }}
                    </span>
                </div>
            </article>

            <article class="balance-card">
                <div class="card-label">Holdings Value</div>

                <div class="balance-value">
                    {{ number_format((float) $account->holdings_balance, 2) }}

                    <span class="currency">
                        {{ $account->currency }}
                    </span>
                </div>
            </article>

        </section>


        <section class="actions-grid">

            {{-- Deposit --}}
            <article class="action-card">

                <h3>Deposit Cash</h3>

                <p>
                    Add available cash to your account.
                </p>

                <form
                    method="POST"
                    action="{{ route('transactions.store') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="type"
                        value="deposit"
                    >

                    <div class="form-group">
                        <label for="deposit_amount">
                            Amount
                        </label>

                        <input
                            id="deposit_amount"
                            type="number"
                            name="amount"
                            min="0.01"
                            step="0.01"
                            placeholder="0.00"
                            required
                        >
                    </div>

                    <button
                        class="action-button"
                        type="submit"
                    >
                        Deposit
                    </button>
                </form>

            </article>


            {{-- Withdrawal --}}
            <article class="action-card">

                <h3>Withdraw Cash</h3>

                <p>
                    Withdraw from your currently available cash balance.
                </p>

                <form
                    method="POST"
                    action="{{ route('transactions.store') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="type"
                        value="withdrawal"
                    >

                    <div class="form-group">
                        <label for="withdrawal_amount">
                            Amount
                        </label>

                        <input
                            id="withdrawal_amount"
                            type="number"
                            name="amount"
                            min="0.01"
                            step="0.01"
                            placeholder="0.00"
                            required
                        >
                    </div>

                    <button
                        class="action-button"
                        type="submit"
                    >
                        Withdraw
                    </button>
                </form>

            </article>


            {{-- Buy --}}
            <article class="action-card">

                <h3>Buy Security</h3>

                <p>
                    Enter an instrument, quantity and transaction price.
                </p>

                <form
                    method="POST"
                    action="{{ route('transactions.store') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="type"
                        value="buy"
                    >

                    <div class="form-group">
                        <label for="ticker">
                            Ticker
                        </label>

                        <input
                            id="ticker"
                            type="text"
                            name="ticker"
                            list="ticker-options"
                            maxlength="20"
                            placeholder="AAPL"
                            required
                        >

                        <datalist id="ticker-options">
                            <option value="AAPL">
                            <option value="MSFT">
                            <option value="NVDA">
                            <option value="AMZN">
                            <option value="GOOGL">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="buy_quantity">
                            Quantity
                        </label>

                        <input
                            id="buy_quantity"
                            type="number"
                            name="quantity"
                            min="1"
                            step="1"
                            placeholder="1"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="buy_price">
                            Price per unit
                        </label>

                        <input
                            id="buy_price"
                            type="number"
                            name="price"
                            min="0.01"
                            step="0.01"
                            placeholder="0.00"
                            required
                        >
                    </div>

                    <button
                        class="action-button"
                        type="submit"
                    >
                        Buy Security
                    </button>
                </form>

            </article>

        </section>


        <section class="section">

            <div class="section-header">

                <div>
                    <h2 class="section-title">
                        Portfolio Holdings
                    </h2>

                    <p class="section-subtitle">
                        Current instruments held in your account.
                    </p>
                </div>

                <div class="holding-count">
                    {{ $account->holdings->count() }}
                    {{ $account->holdings->count() === 1 ? 'Holding' : 'Holdings' }}
                </div>

            </div>


            @if ($account->holdings->isEmpty())

                <div class="empty-state">
                    No current holdings.
                </div>

            @else

                <div class="table-wrapper">

                    <table>

                        <thead>
                        <tr>
                            <th>Ticker</th>
                            <th>Quantity</th>
                            <th>Current Price</th>
                            <th>Current Value</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach ($account->holdings as $holding)

                            <tr>

                                <td>
                                    <span class="ticker">
                                        {{ $holding->ticker }}
                                    </span>
                                </td>

                                <td>
                                    {{ $holding->quantity }}
                                </td>

                                <td>
                                    {{ number_format((float) $holding->current_price, 2) }}
                                    {{ $account->currency }}
                                </td>

                                <td>
                                    {{ number_format((float) $holding->current_value, 2) }}
                                    {{ $account->currency }}
                                </td>

                                <td>
                                    <a
                                        class="sell-button"
                                        href="{{ route('transactions.sell', $holding) }}"
                                    >
                                        Sell
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>

    </main>

</div>

</body>
</html>