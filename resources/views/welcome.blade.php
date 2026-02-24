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
        </section>

        <p class="footer">Built for clarity, speed, and practical money insights.</p>
    </main>
</body>
</html>
