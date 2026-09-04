# Domain & Database Design

## Overview

The system manages investment client accounts, their current financial state, and an immutable history of all account movements.

Each client has exactly one account. An account contains:

* Total balance
* Available cash balance
* Holdings balance
* Current security holdings
* Complete transaction history

Transactions are append-only and cannot be edited or deleted.

---

## Domain Model

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

---

## Client

Represents an investment client.

### Fields

| Field        | Description           |
| ------------ | --------------------- |
| `id`         | Primary key           |
| `name`       | Client name           |
| `created_at` | Creation timestamp    |
| `updated_at` | Last update timestamp |

### Constraints

* `name` is required.
* `name` is unique because clients are identified by name only.

### Relationships

* A Client has exactly one Account.

---

## Account

Represents the client's investment account and its current financial state.

### Fields

| Field              | Description                                           |
| ------------------ | ----------------------------------------------------- |
| `id`               | Primary key                                           |
| `client_id`        | Foreign key referencing Client                        |
| `currency`         | Account currency                                      |
| `cash_balance`     | Cash currently available for withdrawals or purchases |
| `holdings_balance` | Current monetary value of all holdings                |
| `total_balance`    | Combined cash and holdings value                      |
| `created_at`       | Creation timestamp                                    |
| `updated_at`       | Last update timestamp                                 |

### Balance Rules

```text
total_balance = cash_balance + holdings_balance
```

`cash_balance` can be used for:

* Withdrawals
* Buying securities

`holdings_balance` represents the current value of all securities held by the account.

### Constraints

* Each Account belongs to exactly one Client.
* `client_id` is unique, enforcing one account per client.
* Monetary values cannot be negative.
* The account uses exactly one currency.
* No currency conversion is performed.

### Relationships

* Account belongs to Client.
* Account has many Holdings.
* Account has many Transactions.

---

## Holding

Represents the current quantity and value of a security owned by an account.

### Fields

| Field           | Description                                               |
| --------------- | --------------------------------------------------------- |
| `id`            | Primary key                                               |
| `account_id`    | Foreign key referencing Account                           |
| `ticker`        | Security/instrument ticker                                |
| `quantity`      | Currently owned quantity                                  |
| `current_price` | Latest price entered for this ticker during a BUY or SELL |
| `current_value` | Current holding value                                     |
| `created_at`    | Creation timestamp                                        |
| `updated_at`    | Last update timestamp                                     |

### Value Rule

```text
current_value = quantity × current_price
```

The account holdings balance is:

```text
holdings_balance = SUM(all holding current_value values)
```

### Current Price Rule

Because the system has no external market-price provider, `current_price` represents the latest transaction price entered for that ticker.

Example:

```text
BUY 5 AAPL @ 100
```

results in:

```text
AAPL
quantity = 5
current_price = 100
current_value = 500
```

If later:

```text
SELL 2 AAPL @ 120
```

the remaining holding becomes:

```text
AAPL
quantity = 3
current_price = 120
current_value = 360
```

### Constraints

* `quantity` must always be a positive integer.
* `current_price` must be positive.
* `(account_id, ticker)` must be unique.
* If the quantity reaches zero after a SELL, the Holding record is deleted.
* Deleting a Holding does not remove its transaction history.

---

## Transaction

Represents an immutable account movement.

### Supported Types

```text
deposit
withdrawal
buy
sell
```

### Fields

| Field        | Description                       |
| ------------ | --------------------------------- |
| `id`         | Primary key                       |
| `account_id` | Foreign key referencing Account   |
| `type`       | Transaction type                  |
| `amount`     | Monetary value of the transaction |
| `created_at` | Transaction timestamp             |

Transactions intentionally do not support updates or deletion.

### Amount Rules

For deposits and withdrawals:

```text
amount = user-provided amount
```

For BUY and SELL:

```text
amount = quantity × price
```

The server calculates this value rather than trusting an externally supplied calculation.

### Cash Effects

```text
DEPOSIT     → cash_balance increases
WITHDRAWAL  → cash_balance decreases
BUY         → cash_balance decreases
SELL        → cash_balance increases
```

### Constraints

* `amount` must be positive.
* Zero and negative amounts are rejected.
* Transactions are append-only.
* Transactions from different clients/accounts are isolated.

---

## SecurityTransactionDetail

Contains information that applies specifically to BUY and SELL transactions.

Deposit and withdrawal transactions do not have a SecurityTransactionDetail.

### Fields

| Field            | Description                         |
| ---------------- | ----------------------------------- |
| `id`             | Primary key                         |
| `transaction_id` | Foreign key referencing Transaction |
| `ticker`         | Security/instrument ticker          |
| `quantity`       | Quantity bought or sold             |
| `price`          | Price entered for this transaction  |

### Constraints

* `transaction_id` is unique.
* `ticker` is required.
* `quantity` must be a positive integer.
* `price` must be positive.
* A SecurityTransactionDetail exists only for BUY or SELL transactions.

### Relationship

```text
Transaction 1 ─── 0..1 SecurityTransactionDetail
```

---

# Business Operations

## Deposit

A deposit:

1. Validates the amount.
2. Increases `cash_balance`.
3. Recalculates `total_balance`.
4. Creates an immutable DEPOSIT Transaction.

---

## Withdrawal

Before withdrawal:

```text
requested amount <= cash_balance
```

If insufficient cash exists, the operation is rejected.

On success:

1. Decrease `cash_balance`.
2. Recalculate `total_balance`.
3. Create an immutable WITHDRAWAL Transaction.

Holdings cannot directly be used for withdrawals. Securities must first be sold.

---

## Buy

Before BUY:

```text
cost = quantity × price
cost <= cash_balance
```

If there is insufficient cash, the entire operation is rejected.

On success:

1. Decrease `cash_balance`.
2. Create or update the Holding.
3. Increase its quantity.
4. Set `current_price` to the BUY price.
5. Recalculate `current_value`.
6. Recalculate `holdings_balance`.
7. Recalculate `total_balance`.
8. Create an immutable BUY Transaction.
9. Create its SecurityTransactionDetail.

---

## Sell

Before SELL:

```text
requested quantity <= currently owned quantity
```

If the account does not own enough units, the entire operation is rejected.

Sale proceeds:

```text
proceeds = quantity × price
```

On success:

1. Increase `cash_balance`.
2. Decrease the Holding quantity.
3. Set `current_price` to the SELL price.
4. Recalculate the Holding value.
5. Delete the Holding if its quantity becomes zero.
6. Recalculate `holdings_balance`.
7. Recalculate `total_balance`.
8. Create an immutable SELL Transaction.
9. Create its SecurityTransactionDetail.

---

# Atomicity and Concurrency

Account movements modify multiple related records and must behave as one atomic operation.

All DEPOSIT, WITHDRAWAL, BUY, and SELL operations will execute inside a database transaction.

Conceptually:

```text
Lock Account
    ↓
Validate current state
    ↓
Update Account / Holding
    ↓
Create Transaction
    ↓
Create SecurityTransactionDetail if needed
    ↓
Commit
```

If any step fails:

```text
ROLLBACK
```

The database state remains exactly as it was before the operation.

Account-level row locking will be used when changing financial state to prevent concurrent requests from causing overspending, overselling, or inconsistent balances.

---

# Monetary Data

Money and prices will use fixed-precision decimal database types rather than floating-point types.

This avoids floating-point rounding errors when performing financial calculations.

Quantities are always integers.

---

# Source of Truth

The application maintains two complementary forms of data:

### Current State

Stored in:

```text
Account
Holding
```

Used for fast access to:

* Total balance
* Cash available
* Holdings value
* Current securities and quantities

### Historical Ledger

Stored in:

```text
Transaction
SecurityTransactionDetail
```

Used to preserve the complete immutable history of every account movement.

All modifications to current financial state must occur together with the corresponding immutable transaction record inside the same database transaction.

---

# Final Domain Entities

```text
Client
Account
Holding
Transaction
SecurityTransactionDetail
```
