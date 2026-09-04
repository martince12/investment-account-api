# Investment Account API

Laravel REST API for managing investment client accounts, cash movements, security transactions, and current portfolio holdings.

The application keeps both:

* the **current account state** for fast access to balances and holdings;
* an **immutable transaction history** containing every account movement.

## Features

* Create and retrieve clients
* Exactly one account per client
* Single account currency
* Deposit cash
* Withdraw cash
* Buy securities
* Sell securities
* Track current cash balance
* Track current holdings balance
* Track total account balance
* Track holdings by ticker and quantity
* Immutable transaction history
* Input validation
* Protection against insufficient cash
* Protection against overselling
* Atomic account updates
* Automated tests
* Sample seeded data

---

## Requirements

Make sure the following are installed locally:

* PHP
* Composer
* SQLite
* PHP BCMath extension

Verify BCMath:

```bash
php -m
```

`bcmath` should appear in the list of enabled extensions.

---

## Local Setup

Clone the repository:

```bash
git clone https://github.com/martince12/investment-account-api.git
cd investment-account-api
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Create the database tables and sample data:

```bash
php artisan migrate:fresh --seed
```

Start the application:

```bash
php artisan serve
```

The API will normally be available at:

```text
http://127.0.0.1:8000/api
```

---

## Running Tests

Run the complete automated test suite:

```bash
php artisan test
```

The tests cover:

* successful deposits;
* successful withdrawals;
* withdrawal with insufficient cash;
* successful buys;
* buy with insufficient cash;
* successful sells;
* selling more units than owned;
* complete sale of a holding;
* preservation of state after rejected operations;
* transaction input validation;
* invalid transaction types;
* zero and negative amounts;
* invalid quantities and prices;
* required security transaction fields;
* clear API error responses.

---

# API

## Clients

### List Clients

```http
GET /api/clients
```

### Create Client

```http
POST /api/clients
Content-Type: application/json
```

Example:

```json
{
    "name": "Ana",
    "currency": "EUR"
}
```

Creating a client also creates the client's account with zero balances.

### Get Client

```http
GET /api/clients/{client}
```

---

# Account

## Get Account Dashboard

```http
GET /api/accounts/{account}
```

Example response:

```json
{
    "id": 1,
    "client": {
        "id": 1,
        "name": "Ana"
    },
    "currency": "EUR",
    "total_balance": "1100.00",
    "cash_balance": "860.00",
    "holdings_balance": "240.00",
    "holdings": [
        {
            "ticker": "AAPL",
            "quantity": 2,
            "current_price": "120.00",
            "current_value": "240.00"
        }
    ]
}
```

---

# Transactions

## Transaction History

```http
GET /api/accounts/{account}/transactions
```

Returns the immutable transaction history for the selected account.

BUY and SELL transactions also contain their security-specific details.

---

## Deposit

```http
POST /api/accounts/{account}/transactions
Content-Type: application/json
```

```json
{
    "type": "deposit",
    "amount": "1000.00"
}
```

---

## Withdrawal

```json
{
    "type": "withdrawal",
    "amount": "200.00"
}
```

A withdrawal is rejected when the requested amount exceeds the account's available cash.

Example error:

```json
{
    "message": "Insufficient cash balance."
}
```

---

## Buy Security

```json
{
    "type": "buy",
    "ticker": "AAPL",
    "quantity": 5,
    "price": "100.00"
}
```

The transaction amount is calculated by the backend:

```text
amount = quantity × price
```

The client therefore does not provide the transaction amount for BUY or SELL operations.

A BUY is rejected when there is insufficient available cash.

---

## Sell Security

```json
{
    "type": "sell",
    "ticker": "AAPL",
    "quantity": 3,
    "price": "120.00"
}
```

Sale proceeds are calculated as:

```text
proceeds = quantity × price
```

A SELL is rejected when the account does not own enough units of the requested instrument.

Example error:

```json
{
    "message": "Insufficient holdings quantity."
}
```

If all units of a security are sold, the current Holding record is removed. The BUY and SELL transaction history remains unchanged.

---

# Business Rules

## Cash

Cash may never become negative.

The following operations consume available cash:

* Withdrawal
* Buy

A transaction that would make the cash balance negative is rejected.

---

## Holdings

An account cannot sell more units of an instrument than it currently owns.

Holdings are uniquely identified by:

```text
account + ticker
```

For example, an account has one current `AAPL` Holding containing its current AAPL quantity.

---

## Transaction Immutability

Transactions form an append-only ledger.

Once created, transactions are never updated or deleted.

There are intentionally no API endpoints for modifying or deleting transaction history.

---

## Prices

There is no external market-price provider.

The price is supplied when a BUY or SELL transaction is created.

The current price of a Holding is the latest BUY or SELL price entered for that ticker.

For example:

```text
BUY 5 AAPL @ 100
```

creates:

```text
quantity = 5
current_price = 100
current_value = 500
```

If later:

```text
SELL 3 AAPL @ 120
```

the remaining Holding becomes:

```text
quantity = 2
current_price = 120
current_value = 240
```

No profit-and-loss calculation is performed.

---

# Balance Model

Each account stores three balances:

```text
cash_balance
holdings_balance
total_balance
```

The relationship between them is:

```text
total_balance = cash_balance + holdings_balance
```

Each Holding has:

```text
current_value = quantity × current_price
```

The holdings balance represents the combined current value of all active Holdings.

---

# Data Model

```text
Client
   │
   │ 1 : 1
   ▼
Account
   │
   ├── 1 : N ──> Holding
   │
   └── 1 : N ──> Transaction
                       │
                       │ 0 : 1
                       ▼
              SecurityTransactionDetail
```

### Client

Identifies the investment client by name.

### Account

Stores the client's currency and current financial state.

### Holding

Stores the current quantity and latest known transaction price for an instrument.

### Transaction

Stores the immutable history of deposits, withdrawals, buys, and sells.

### SecurityTransactionDetail

Stores ticker, quantity, and price information that applies only to BUY and SELL transactions.

A more detailed database design is available in:

```text
docs/domain-database-design.md
```

---

# Why This Way?

## Transaction Ledger and Current State

The system separates:

```text
Current state
+
Historical ledger
```

`Account` and `Holding` models provide efficient access to the current state required by an account dashboard.

`Transaction` and `SecurityTransactionDetail` preserve the complete immutable history of how that state was reached.

This avoids rebuilding the full account state from the complete transaction history on every request while still preserving an audit trail.

---

## Security Transaction Details

Deposit and withdrawal movements only require an amount.

BUY and SELL movements additionally require:

* ticker;
* quantity;
* price.

Instead of storing nullable security-specific fields directly on every Transaction, they are stored in `SecurityTransactionDetail`.

This keeps the main transaction ledger focused on properties shared by every movement.

---

## Financial Precision

Monetary values use fixed-precision database decimals rather than floating-point numbers.

Financial arithmetic in the service layer uses PHP BCMath functions such as:

```text
bcadd
bcsub
bcmul
bccomp
```

This avoids floating-point rounding issues when working with money.

---

## Atomic Operations

Deposit, withdrawal, BUY, and SELL operations may change multiple pieces of state.

For example, a BUY may:

1. decrease cash;
2. update a Holding;
3. update account balances;
4. create a Transaction;
5. create a SecurityTransactionDetail.

These operations execute inside a database transaction.

If any part fails, the entire operation is rolled back and the account remains unchanged.

---

## Concurrency

Account rows are locked while financial movements are being processed.

This prevents concurrent requests from reading the same outdated balance and accidentally allowing situations such as:

* spending the same cash twice;
* withdrawing more cash than available;
* selling the same units twice.

---

## Validation vs Business Rules

Input validation and business rules are deliberately separated.

The Form Request layer validates input such as:

* valid transaction type;
* positive amounts;
* positive prices;
* integer quantities;
* required BUY/SELL fields.

The service layer enforces account-state rules such as:

* sufficient cash;
* sufficient holdings;
* atomic balance updates.

This keeps HTTP input concerns separate from financial business logic.

---

# Seed Data

Sample clients and transactions are provided through the database seeder.

Run:

```bash
php artisan migrate:fresh --seed
```

Example seeded scenario for Ana:

```text
Deposit €1000
Buy 5 AAPL @ €100
Sell 3 AAPL @ €120
```

Result:

```text
Cash balance:     €860
Holdings balance: €240
Total balance:    €1100

AAPL:
quantity:         2
current price:    €120
current value:    €240
```

---

# Scope

The project intentionally does not include:

* frontend UI;
* authentication;
* foreign-exchange conversion;
* external security pricing;
* profit-and-loss calculations;
* security master data;
* transaction editing or deletion.

These features are outside the requirements of the assignment.
