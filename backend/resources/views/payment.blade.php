@if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        {{ session('error') }}
    </div>
@endif






<!DOCTYPE html>
<html>
<head>
    <title>Payment Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            display: flex;
            justify-content: center;
        }

        .form-container {
            width: 400px;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            margin-top: 0;
            color: #4CAF50;
            text-align: center;
        }

        .input-wrapper {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .input-wrapper label {
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        .input-wrapper input,
        .input-wrapper select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .input-wrapper input:focus,
        .input-wrapper select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.3);
        }

        .input-wrapper button {
            padding: 10px;
            border: none;
            border-radius: 6px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }

        .input-wrapper button:hover {
            background-color: #388E3C;
        }

        .input-wrapper span {
            color: red;
            font-size: 13px;
            margin-top: 3px;
        }

        @media (max-width: 500px) {
            .form-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Payment</h2>
        <form action="{{ url('addPayment') }}" method="post">
            @csrf
            <div class="input-wrapper">
                <label for="booking_id">Booking ID</label>
                <input type="number" id="booking_id" name="booking_id" placeholder="Enter Booking ID">
                <span>@error('booking_id'){{ $message }}@enderror</span>
            </div>

            <div class="input-wrapper">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method">
                    <option value="bank">Bank</option>
                    <option value="cash">Cash</option>
                    <option value="bkash">Bkash</option>
                </select>
                <span>@error('payment_method'){{ $message }}@enderror</span>
            </div>

            <div class="input-wrapper">
                <label for="amount_paid">Amount Paid</label>
                <input type="number" id="amount_paid" name="amount_paid" placeholder="Enter amount">
                <span>@error('amount_paid'){{ $message }}@enderror</span>
            </div>

            <div class="input-wrapper">
                <button type="submit">Pay Now</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
