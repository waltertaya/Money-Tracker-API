<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Tracker API</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="container">
        <section class="hero">
            <span class="badge">Project Documentation</span>
            <h1>Money Tracker API</h1>
            <p>
                A RESTful Laravel backend built to manage wallets and track financial transactions (income and expenses) with a clean, predictable data model.
            </p>
            <p>
                This project is designed for frontend/mobile integration and focuses on reliable balance tracking, simple extensibility, and backend best practices.
            </p>
            <p>Help users understand where money comes from, where it goes, and what remains per wallet and overall.</p>

            <div class="grid">
                <article class="card">
                    <h3>Tech Stack</h3>
                    <ul>
                        <li>Laravel (PHP)</li>
                        <li>MySQL</li>
                        <li>RESTful architecture</li>
                        <li>Git-based workflow</li>
                    </ul>
                </article>
            </div>
        </section>

        <section>
            <h2>What This API Covers</h2>
            <div class="grid">
                <article class="card">
                    <h3>Users</h3>
                    <p>Own one or more wallets that separate personal, business, or goal-based finances.</p>
                </article>
                <article class="card">
                    <h3>Wallets</h3>
                    <p>Containers for transactions, each linked to a specific user.</p>
                </article>
                <article class="card">
                    <h3>Transactions</h3>
                    <p>Records of <strong>income</strong> and <strong>expense</strong>, with amount, date, and optional description.</p>
                </article>
            </div>
        </section>

        <section>
            <h2>Typical Flow</h2>
            <div class="card">
                <ul>
                    <li>Create or authenticate a user account.</li>
                    <li>Create one or more wallets (e.g. Personal, Savings, Business).</li>
                    <li>Add transactions with type, amount, date, and notes.</li>
                    <li>Compute balances from wallet transaction history.</li>
                    <li>Expose clean JSON responses for frontend dashboards.</li>
                </ul>
            </div>
        </section>

        <section>
            <h2>API ROUTES/ENDPOINTS</h2>
            <p><strong>Base URL:</strong> <code>http://localhost:8000/api/v1</code></p>

            <h3>User Endpoints</h3>
            <div class="card">
                <h4>POST /users</h4>
                <p><strong>Description:</strong> Create a new user account</p>
                <p><strong>Request Body:</strong></p>
                <pre><code>{
  "name": "string (required)",
  "email": "string (required, unique)",
  "password": "string (required, min 8 characters)"
}</code></pre>
                <p><strong>Response (201):</strong></p>
                <pre><code>{
  "message": "User created successfully.",
  "data": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "created_at": "timestamp"
  }
}</code></pre>
            </div>

            <div class="card">
                <h4>GET /users/{user}</h4>
                <p><strong>Description:</strong> Get user profile with all wallets and overall balance</p>
                <p><strong>Parameters:</strong> user (UUID)</p>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "user": {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "created_at": "timestamp"
  },
  "wallets": [
    {
      "id": "uuid",
      "name": "string",
      "balance": "decimal",
      "created_at": "timestamp"
    }
  ],
  "overall_balance": "decimal"
}</code></pre>
            </div>

            <h3>Wallet Endpoints</h3>
            <div class="card">
                <h4>POST /wallets</h4>
                <p><strong>Description:</strong> Create a new wallet for a user</p>
                <p><strong>Request Body:</strong></p>
                <pre><code>{
  "user_id": "uuid (required, must exist)",
  "name": "string (required, max 255 characters)"
}</code></pre>
                <p><strong>Response (201):</strong></p>
                <pre><code>{
  "message": "Wallet created successfully.",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "name": "string",
    "balance": "decimal",
    "created_at": "timestamp"
  }
}</code></pre>
            </div>

            <div class="card">
                <h4>GET /wallets/{wallet}</h4>
                <p><strong>Description:</strong> Get wallet details with balance and all transactions</p>
                <p><strong>Parameters:</strong> wallet (UUID)</p>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "wallet": {
    "id": "uuid",
    "user_id": "uuid",
    "name": "string",
    "balance": "decimal",
    "created_at": "timestamp"
  },
  "transactions": [
    {
      "id": "uuid",
      "wallet_id": "uuid",
      "type": "income|expense",
      "amount": "decimal",
      "description": "string|null",
      "date": "date",
      "created_at": "timestamp"
    }
  ]
}</code></pre>
            </div>

            <div class="card">
                <h4>PUT /wallets/{wallet}</h4>
                <p><strong>Description:</strong> Update wallet name</p>
                <p><strong>Parameters:</strong> wallet (UUID)</p>
                <p><strong>Request Body:</strong></p>
                <pre><code>{
  "name": "string (required, max 255 characters)"
}</code></pre>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "message": "Wallet updated successfully.",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "name": "string",
    "balance": "decimal",
    "updated_at": "timestamp"
  }
}</code></pre>
            </div>

            <div class="card">
                <h4>DELETE /wallets/{wallet}</h4>
                <p><strong>Description:</strong> Delete a wallet and all associated transactions</p>
                <p><strong>Parameters:</strong> wallet (UUID)</p>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "message": "Wallet deleted successfully."
}</code></pre>
            </div>

            <h3>Transaction Endpoints</h3>
            <div class="card">
                <h4>POST /wallets/{wallet}/transactions</h4>
                <p><strong>Description:</strong> Create a new transaction (income or expense) in a wallet</p>
                <p><strong>Parameters:</strong> wallet (UUID)</p>
                <p><strong>Request Body:</strong></p>
                <pre><code>{
  "type": "income|expense (required)",
  "amount": "numeric, > 0 (required)",
  "description": "string (optional)",
  "date": "date YYYY-MM-DD (required)"
}</code></pre>
                <p><strong>Response (201):</strong></p>
                <pre><code>{
  "message": "Transaction created successfully.",
  "data": {
    "id": "uuid",
    "wallet_id": "uuid",
    "type": "income|expense",
    "amount": "decimal",
    "description": "string|null",
    "date": "date",
    "created_at": "timestamp"
  },
  "wallet_balance": "decimal"
}</code></pre>
            </div>

            <div class="card">
                <h4>GET /wallets/{wallet}/transactions</h4>
                <p><strong>Description:</strong> Get all transactions for a specific wallet (ordered by date descending)</p>
                <p><strong>Parameters:</strong> wallet (UUID)</p>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "wallet": {
    "id": "uuid",
    "name": "string",
    "balance": "decimal"
  },
  "transactions": [
    {
      "id": "uuid",
      "wallet_id": "uuid",
      "type": "income|expense",
      "amount": "decimal",
      "description": "string|null",
      "date": "date",
      "created_at": "timestamp"
    }
  ]
}</code></pre>
            </div>

            <div class="card">
                <h4>GET /transactions/{transaction}</h4>
                <p><strong>Description:</strong> Get details of a specific transaction</p>
                <p><strong>Parameters:</strong> transaction (UUID)</p>
                <p><strong>Response (200):</strong></p>
                <pre><code>{
  "data": {
    "id": "uuid",
    "wallet_id": "uuid",
    "type": "income|expense",
    "amount": "decimal",
    "description": "string|null",
    "date": "date",
    "created_at": "timestamp"
  }
}</code></pre>
            </div>

            <h3>Validation Rules</h3>
            <div class="card">
                <ul>
                    <li><strong>User name:</strong> Required, string, max 255 characters</li>
                    <li><strong>User email:</strong> Required, email format, unique in database</li>
                    <li><strong>User password:</strong> Required, string, minimum 8 characters</li>
                    <li><strong>Wallet name:</strong> Required, string, max 255 characters</li>
                    <li><strong>Transaction type:</strong> Required, must be "income" or "expense"</li>
                    <li><strong>Transaction amount:</strong> Required, numeric, must be greater than 0</li>
                    <li><strong>Transaction date:</strong> Required, valid date format (YYYY-MM-DD)</li>
                    <li><strong>Transaction description:</strong> Optional, string</li>
                </ul>
            </div>

            <h3>Balance Calculation</h3>
            <div class="card">
                <p><strong>Wallet Balance:</strong> Sum of all income transactions minus sum of all expense transactions</p>
                <p><strong>Overall Balance:</strong> Sum of all wallet balances for a user</p>
                <pre><code>Wallet Balance = (Σ income) - (Σ expense)
Overall Balance = Σ (all wallet balances)</code></pre>
            </div>
        </section>

        <p class="footer">Built for clarity, speed, and practical money insights.</p>
    </main>
</body>
</html>
