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
http POST :80/accounts/createAccount fiscalNumber="1111111111" name="John Doe" # Create account
```
