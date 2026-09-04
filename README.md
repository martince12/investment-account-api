
# Investment Account API

A Laravel application for managing client investment accounts, cash balances, security holdings, and immutable account transactions.

The project was developed as a practical Software Engineering Internship assignment.

The core of the application is a Laravel backend that enforces financial business rules, validation, account isolation, and atomic transaction processing.

An optional Blade-based frontend demo is also included on this branch, providing authentication and a simple user interface for interacting with an investment account.

---

## Features

### Backend

- Client and investment account management
- One account per client
- Single account currency
- Cash balance tracking
- Security holdings tracking
- Immutable transaction history
- Deposit transactions
- Withdrawal transactions
- Security purchases
- Security sales
- Validation of transaction input
- Prevention of negative cash balances
- Prevention of over-withdrawal
- Prevention of over-selling
- Account isolation between clients
- Atomic financial operations
- Database row locking for balance-changing operations
- Automated tests for main business rules
- Seeded sample data
- REST API endpoints

### Optional Frontend Demo

The frontend demo is implemented using Laravel Blade and session authentication.

It includes:

- user login and logout;
- authenticated account dashboard;
- total account balance;
- available cash balance;
- holdings balance;
- current portfolio holdings;
- deposit form;
- withdrawal form;
- security purchase form;
- security sale form;
- transaction history;
- validation and business-rule error messages.

The frontend uses the same validation and `TransactionService` business logic as the API rather than duplicating the financial rules.

---

## Technology

- PHP
- Laravel
- Blade
- SQLite
- Laravel session authentication
- Vite
- BCMath
- PHPUnit / Pest

---

## Requirements

Make sure the following are installed:

- PHP
- Composer
- PHP BCMath extension
- Node.js and npm

SQLite is used by default.

---

## Installation

Clone the repository:

```bash
git clone https://github.com/martince12/investment-account-api.git
cd investment-account-api
````

For the optional frontend demo:

```bash
git checkout feature/frontend-demo
```

Install PHP dependencies:

```bash
composer install
```

Create the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell, you can use:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Create and seed the database:

```bash
php artisan migrate:fresh --seed
```

Install frontend dependencies:

```bash
npm install
```

Start Vite:

```bash
npm run dev
```

Start the Laravel application:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

Guests are redirected to the login page.

---

## Demo Users

The database seeder creates two users for testing the frontend.

### Ana

```text
Email: ana@example.com
Password: password
```

### Mark

```text
Email: mark@example.com
Password: password
```

Each authenticated user can access only their own investment account.

---

## Frontend Flow

After login, the user is redirected to the account dashboard.

The dashboard displays:

* total balance;
* available cash;
* holdings value;
* current securities;
* deposit functionality;
* withdrawal functionality;
* buy functionality.

Each current holding also has a **Sell** action that opens a dedicated sale form.

The transaction history page displays:

* transaction date;
* transaction type;
* amount;
* ticker;
* quantity;
* transaction price.

Deposit and withdrawal transactions do not contain security-specific information.

---

## Transaction Types

The application supports four transaction types:

```text
deposit
withdrawal
buy
sell
```

### Deposit

Adds cash to the account.

Required input:

```json
{
    "type": "deposit",
    "amount": 1000
}
```

### Withdrawal

Removes cash from the account.

Required input:

```json
{
    "type": "withdrawal",
    "amount": 200
}
```

A withdrawal is rejected if the account does not contain enough available cash.

### Buy

Purchases a security.

Required input:

```json
{
    "type": "buy",
    "ticker": "AAPL",
    "quantity": 5,
    "price": 100
}
```

The transaction amount is calculated by the server:

```text
quantity × price
```

A purchase is rejected if the account does not contain enough available cash.

### Sell

Sells an owned security.

Required input:

```json
{
    "type": "sell",
    "ticker": "AAPL",
    "quantity": 3,
    "price": 120
}
```

Sale proceeds are calculated by the server:

```text
quantity × price
```

A sale is rejected if the requested quantity is greater than the quantity currently owned.

---

## REST API

### Clients

List clients:

```http
GET /api/clients
```

Create a client:

```http
POST /api/clients
```

View a client:

```http
GET /api/clients/{client}
```

### Accounts

View current account state:

```http
GET /api/accounts/{account}
```

### Transactions

View transaction history:

```http
GET /api/accounts/{account}/transactions
```

Create a transaction:

```http
POST /api/accounts/{account}/transactions
```

---

## Example Account Flow

Example for Ana:

### 1. Deposit 1000 EUR

```text
Cash: 1000 EUR
```

### 2. Buy 5 AAPL at 100 EUR

```text
Cost: 500 EUR

Cash: 500 EUR
AAPL: 5
```

### 3. Attempt to buy securities costing 700 EUR

The transaction is rejected because only 500 EUR of cash is available.

The account state remains unchanged.

### 4. Attempt to sell 8 AAPL

The transaction is rejected because only 5 shares are owned.

The account state remains unchanged.

### 5. Sell 3 AAPL at 120 EUR

```text
Sale proceeds: 360 EUR

Cash: 860 EUR
AAPL: 2
```

---

## Validation

Transaction validation is handled by `StoreTransactionRequest`.

Examples of invalid input include:

* missing transaction type;
* unsupported transaction type;
* zero or negative monetary amount;
* zero or negative security price;
* non-integer quantity;
* zero quantity;
* missing ticker for buy or sell;
* security fields supplied for cash transactions;
* amount supplied manually for buy or sell.

Financial business-rule validation is handled separately by `TransactionService`.

This includes:

* insufficient cash;
* insufficient security quantity.

Separating request validation from domain rules keeps HTTP validation independent from financial business logic.

---

## Data Model

```text
User
  |
  | 1:1
  v
Client
  |
  | 1:1
  v
Account
  |
  |---- 1:N ---- Holding
  |
  |---- 1:N ---- Transaction
                     |
                     | 0..1
                     v
              SecurityTransactionDetail
```

### Client

Represents the financial client.

### Account

Stores the current account state:

* currency;
* cash balance;
* holdings balance;
* total balance.

### Holding

Stores the current position for one ticker:

* ticker;
* quantity;
* current price;
* current value.

There can only be one holding row for the same ticker within an account.

### Transaction

Represents the immutable financial transaction ledger.

Transactions are never edited or deleted.

### SecurityTransactionDetail

Contains security-specific information for BUY and SELL transactions:

* ticker;
* quantity;
* price.

Deposit and withdrawal transactions therefore do not require nullable security fields directly on the transaction record.

---

## Balance Rules

The account stores:

```text
total_balance = cash_balance + holdings_balance
```

A holding stores:

```text
current_value = quantity × current_price
```

Because the assignment does not use an external pricing provider, the latest BUY or SELL price entered for a security is treated as its current price for account valuation purposes.

The application does not calculate realized or unrealized profit and loss.

---

## Transaction Safety

Every balance-changing operation is executed inside a database transaction.

The relevant account row is retrieved using:

```text
lockForUpdate()
```

This prevents concurrent operations from independently reading the same balance and both modifying it based on stale state.

If any part of the operation fails, the database transaction is rolled back.

This means an invalid transaction cannot partially modify:

* cash;
* holdings;
* account balances;
* transaction history.

---

## Money Calculations

Financial calculations do not use PHP floating-point arithmetic.

BCMath is used for operations such as:

```text
bcadd
bcsub
bcmul
bccomp
```

with a scale of two decimal places.

This avoids common floating-point precision problems when working with monetary values.

---

## Account Isolation

Financial operations are always executed against a specific account.

In the frontend demo, the account is resolved from the authenticated user:

```text
Authenticated User
    -> Client
        -> Account
```

The frontend does not accept an arbitrary account ID from the user when creating transactions.

This prevents one authenticated user from selecting another user's account through a modified form request.

---

## Seed Data

Running:

```bash
php artisan migrate:fresh --seed
```

creates sample clients, users, accounts, and transactions.

Example seeded data includes:

### Ana

* currency: EUR
* deposit: 1000
* buy: 5 AAPL at 100
* sell: 3 AAPL at 120

Final example state:

```text
Cash balance: 860 EUR
AAPL quantity: 2
AAPL current price: 120 EUR
AAPL current value: 240 EUR
Total balance: 1100 EUR
```

### Mark

Includes a USD account and an example MSFT purchase.

---

## Tests

Run the automated test suite with:

```bash
php artisan test
```

The tests cover the main application rules, including:

* deposits;
* withdrawals;
* insufficient cash;
* purchases;
* insufficient cash when buying;
* sales;
* over-selling;
* full sale of a holding;
* transaction validation;
* clean API error responses;
* unchanged state after rejected transactions;
* account isolation;
* frontend home-page redirect behavior.

---

## Why This Way?

### Immutable Transaction Ledger

Transactions represent financial events that occurred in the past.

They are therefore append-only and are not exposed through update or delete endpoints.

### Current State + Historical Ledger

`Account` and `Holding` contain the current state required for fast reads.

`Transaction` and `SecurityTransactionDetail` preserve the historical record.

This avoids recalculating the entire account from all historical transactions every time the current state is requested.

### Separate Security Details

Security-specific data is stored separately because deposit and withdrawal transactions do not have:

* ticker;
* quantity;
* price.

This keeps the base transaction model small while still preserving complete BUY and SELL information.

### Service Layer

Financial logic is located in `TransactionService` rather than controllers.

This allows the same business logic to be reused by:

* REST API controllers;
* Blade frontend controllers;
* database seeders;
* automated tests.

### Request Validation vs Business Rules

`StoreTransactionRequest` validates the structure and values of incoming data.

`TransactionService` validates financial state-dependent rules such as available cash and owned quantity.

### Authentication Separation

`User` represents authentication identity.

`Client` represents the financial domain entity.

Keeping them separate avoids coupling authentication concerns directly to the investment account model.

---

## Project Scope

The application currently includes both the required Laravel backend and an optional authenticated Blade frontend demo.

The following features are intentionally outside the scope of the assignment:

* user registration and account-management UI;
* foreign-exchange conversion;
* external or real-time security pricing;
* profit-and-loss calculations;
* security master data;
* transaction editing;
* transaction deletion.

Security tickers are represented only by their ticker labels, as required by the assignment.

Transaction prices are entered manually at transaction time.

---

## Branches

### `master`

Contains the completed backend implementation required by the assignment.

The backend-final version is also marked with:

```text
backend-final
```

### `feature/frontend-demo`

Contains the backend plus the optional authenticated Blade frontend demo.

The frontend is an additional demonstration and is not required for the core assignment.

---

## Additional Design Documentation

Additional information about the domain and database design is available in:

```text
docs/domain-database-design.md
```

