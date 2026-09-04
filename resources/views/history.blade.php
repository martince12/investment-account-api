<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaction History | Investment Account</title>

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

        button {
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

            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease;
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

        .section {
            overflow: hidden;

            border: 1px solid #303034;
            border-radius: 14px;

            background: rgba(24, 24, 27, 0.82);

            box-shadow:
                0 14px 40px rgba(0, 0, 0, 0.16),
                inset 0 1px 0 rgba(255, 255, 255, 0.02);
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

        .transaction-count {
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

        tbody tr {
            transition: background 0.18s ease;
        }

        tbody tr:hover {
            background: rgba(39, 39, 42, 0.42);
        }

        .date {
            color: #a1a1aa;
            white-space: nowrap;
        }

        .type-badge {
            display: inline-flex;
            align-items: center;

            padding: 5px 9px;

            border-radius: 6px;
            border: 1px solid #3f3f46;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .type-deposit {
            background: rgba(34, 197, 94, 0.08);
            border-color: rgba(34, 197, 94, 0.20);
            color: #86efac;
        }

        .type-withdrawal {
            background: rgba(248, 113, 113, 0.08);
            border-color: rgba(248, 113, 113, 0.20);
            color: #fca5a5;
        }

        .type-buy {
            background: rgba(96, 165, 250, 0.08);
            border-color: rgba(96, 165, 250, 0.20);
            color: #93c5fd;
        }

        .type-sell {
            background: rgba(192, 132, 252, 0.08);
            border-color: rgba(192, 132, 252, 0.20);
            color: #d8b4fe;
        }

        .amount {
            color: #fafafa;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .ticker {
            display: inline-flex;
            align-items: center;

            padding: 5px 8px;

            border-radius: 6px;
            border: 1px solid #3f3f46;

            background: #27272a;
            color: #fafafa;

            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .muted {
            color: #52525b;
        }

        .number {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .empty-state {
            padding: 60px 24px;

            text-align: center;
            color: #71717a;

            font-size: 14px;
        }

        @media (max-width: 800px) {
            .page {
                padding: 22px 18px 40px;
            }

            .brand-name {
                display: none;
            }

            .hero h1 {
                font-size: 28px;
            }
        }

        @media (max-width: 520px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }

            .topbar-actions {
                width: 100%;
            }

            .nav-link,
            .logout-button {
                flex: 1;
            }

            .topbar-actions form {
                flex: 1;
            }

            .logout-button {
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

            <span class="brand-name">
                Investment Account
            </span>
        </div>

        <div class="topbar-actions">

            <a
                class="nav-link"
                href="{{ route('dashboard') }}"
            >
                Dashboard
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
                Account Activity
            </div>

            <h1>
                Transaction History
            </h1>

            <p>
                Complete immutable transaction history for {{ $client->name }}.
            </p>

        </section>


        <section class="section">

            <div class="section-header">

                <div>
                    <h2 class="section-title">
                        Transactions
                    </h2>

                    <p class="section-subtitle">
                        Deposits, withdrawals, purchases and sales.
                    </p>
                </div>

                <div class="transaction-count">
                    {{ $transactions->count() }}
                    {{ $transactions->count() === 1 ? 'Transaction' : 'Transactions' }}
                </div>

            </div>


            @if ($transactions->isEmpty())

                <div class="empty-state">
                    No transactions found.
                </div>

            @else

                <div class="table-wrapper">

                    <table>

                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Ticker</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach ($transactions as $transaction)

                            @php
                                $type = $transaction->type->value;
                            @endphp

                            <tr>

                                <td class="date">
                                    {{ $transaction->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td>
                                    <span class="type-badge type-{{ $type }}">
                                        {{ strtoupper($type) }}
                                    </span>
                                </td>

                                <td class="amount">
                                    {{ number_format((float) $transaction->amount, 2) }}
                                    {{ $account->currency }}
                                </td>

                                <td>
                                    @if ($transaction->securityDetail)
                                        <span class="ticker">
                                            {{ $transaction->securityDetail->ticker }}
                                        </span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td class="number">
                                    @if ($transaction->securityDetail)
                                        {{ $transaction->securityDetail->quantity }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td class="number">
                                    @if ($transaction->securityDetail)
                                        {{ number_format((float) $transaction->securityDetail->price, 2) }}
                                        {{ $account->currency }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
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
