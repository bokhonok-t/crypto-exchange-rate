run docker-compose up -d --build

go to http://localhost:8080/rates?from=2025-04-15&to=2025-04-22 (`from` and `to` params are required)


data will be updated every hour

Response in JSON format:
```
{
  "2025-04-20": [
    {
      "currencyFrom": "BTC",
      "currencyTo": "USD",
      "rate": "87401.01",
      "date": "2025-04-20 19:44"
    },
    {
      "currencyFrom": "ETH",
      "currencyTo": "USD",
      "rate": "1578.88",
      "date": "2025-04-20 19:44"
    },
    {
      "currencyFrom": "ADA",
      "currencyTo": "USD",
      "rate": "0.623607",
      "date": "2025-04-20 19:44"
    }
  ],
 "2025-04-21": [
    {
      "currencyFrom": "BTC",
      "currencyTo": "USD",
      "rate": "87401.01",
      "date": "2025-04-21 19:44"
    },
    {
      "currencyFrom": "ETH",
      "currencyTo": "USD",
      "rate": "1578.88",
      "date": "2025-04-21 19:44"
    },
    {
      "currencyFrom": "ADA",
      "currencyTo": "USD",
      "rate": "0.623607",
      "date": "2025-04-21 19:44"
    },
    {
      "currencyFrom": "BTC",
      "currencyTo": "USD",
      "rate": "87401.01",
      "date": "2025-04-21 19:44"
    }
  ]
}
