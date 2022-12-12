# bank.php

A playground application to explore how to move from MVC to Clean to CQRS to EventSourcing.
The changing of architectures are meant to support the move from monolith to microservices.

## Use cases

1. **Open account**: To open an account new customer will provide name and fiscal number.

    - A fiscal number is used as the account handler, so each customer can have only one account in the system.

2. **Deposit funds**: Existing customers can deposit funds into their accounts.
    - The bank will give a 0.5% bonus for each deposit.
3. **Withdraw funds**: Existing customers can withdraw funds from their accounts.
    - The bank will charge a 1% fee for each withdraw.
    - The value of withdraw + fee cannot exceed the current balance.
4. **Transfer funds**: Existing customers can transfer funds between themselves.
    - Amount to be transfered cannot exceed the current balance.
5. **Account statement**: Existing customers can view their deposits, withdraws, and transfers.
    - Deposits show date and time.
    - Bonus are shown in their own line.
    - Withdraws show date and time.
    - Fees are shown in their own line.
    - Transfers show date, time and the name of the other party.

## Usage

```bash
docker-compose exec php php /app/app/migration.php
http POST ":80/accounts/createAccount" fiscalNumber=123 name="John Doe"
http POST ":80/accounts/depositFunds"  fiscalNumber=123 amount=5000
http POST ":80/accounts/withdrawFunds" fiscalNumber=123 amount=200
http POST ":80/accounts/createAccount" fiscalNumber=456 name="Jane Smith"
http POST ":80/accounts/transferFunds" origin=123 destination=456 amount=200
http GET  ":80/accounts/accountStatement?fiscalNumber=123"
http GET  ":80/accounts/accountStatement?fiscalNumber=456"
```

## MVC

This is not a proper MVC implementation but it's good enough for the purposes of this example. MVC, as done in most web frameworks, is more about code organization than about architecture because all relationship are static and known at build time.

## Clean Architecture

The main improvement to be made is that MVC is monolithic in nature and thus harder to evolve incrementally. This is mostly because code related to business logic and infrastructure are mingled together.

To add modularity to this code I'm going to implement a Clean Architecture which is more suitable for request-response style of communication, in contrast with the Hexagonal Architecture that is more reactive.

I'm migrating one Use Case at a time. This is very important as I'm gonna add tests strategically to support the refactoring, not to test the application. Those tests will function as scaffolds, and will be replaced by a better suite of tests as code improves.
