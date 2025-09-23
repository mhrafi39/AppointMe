@if(session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;" id="success-message">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;" id="error-message">
        {{ session('error') }}
    </div>
@endif

<script>
    // Auto-hide message after 3 seconds
    setTimeout(function(){
        let msg = document.getElementById('success-message');
        if(msg) msg.style.display = 'none';

        let err = document.getElementById('error-message');
        if(err) err.style.display = 'none';
    }, 3000);
</script>





@if(session('success'))
    <div style="color: green; font-weight: bold;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="color: red; font-weight: bold;">
        {{ session('error') }}
    </div>
@endif






<!DOCTYPE html>
<html>
<head>
    <title>Service Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* Left column (User/Service Info) */
        .left-column {
            flex: 1 1 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .left-column img {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .left-column p {
            margin: 6px 0;
            font-size: 14px;
            color: #333;
        }

        /* Right column (Forms) */
        .right-column {
            flex: 2 1 500px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .form-container h2 {
            margin-top: 0;
            color: #4CAF50;
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
        .input-wrapper textarea,
        .input-wrapper select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }

        .input-wrapper input:focus,
        .input-wrapper textarea:focus,
        .input-wrapper select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76,175,80,0.3);
        }

        .input-wrapper textarea {
            resize: vertical;
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
        }

        .input-wrapper button:hover {
            background-color: #388E3C;
        }

        /* Payment button styling */
        .payment-btn {
            background-color: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .payment-btn:hover {
            background-color: darkgreen;
        }

        /* Responsive adjustments */
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Left Column -->
    <div class="left-column">
        <img src="{{ $service->profile_picture 
             ? asset('storage/' . $service->profile_picture) 
             : asset('storage/default.jpeg') }}" 
     alt="Profile Picture">

        <h3><strong>User ID:</strong> {{ $service->user_id }}</h3>
        <h3><strong>Services ID:</strong> {{ $service->services_id }}</h3>
        <h3><strong>Name:</strong> {{ $service->name }}</h3>
        <h3><strong>Category:</strong> {{ $service->category }}</h3>
        <h3><strong>Location:</strong> {{ $service->location }}</h3>
        <h3><strong>Price:</strong> {{ $service->price }} Tk</h3>
        <h3><strong>Available Time:</strong> {{ $service->available_time }}</h3>
    </div>

    <!-- Right Column -->
    <div class="right-column">
        <!-- Booking Form -->
        <div class="form-container">
            <h2>Booking</h2>
            <form action="{{ url('addBookings') }}" method="post">
                @csrf
                <div class="input-wrapper">
                    <label for="services_id">Services Id</label>
                    <input type="number" id="services_id" name="services_id" value="{{ $service->services_id }}" readonly>
                </div>
                <div class="input-wrapper">
                    <label for="user_id">User ID</label>
                    <input type="number" id="user_id" placeholder="Enter User ID" name="user_id">
                </div>
                <div class="input-wrapper">
                    <label for="booking_time">Booking time</label>
                    <input type="text" id="booking_time" placeholder="Booking time" name="booking_time">
                </div>
                <div class="input-wrapper">
                    <label for="status">Status</label>
                    <input type="text" id="status" name="status" placeholder="Enter status (e.g., confirmed or unconfirmed)">
                </div>
                <div class="input-wrapper">
                    <label for="payment_status">Payment Status</label>
                    <input type="text" id="payment_status" name="payment_status" placeholder="Enter payment status (e.g., done or yet to do)">
                </div>
                <div class="input-wrapper">
                    <button type="submit">Book</button>
                </div>
            </form>
        </div>

        <!-- Review Form -->
        <div class="form-container">
            <h2>Submit Review</h2>
            <form action="{{ url('addReview') }}" method="post">
                @csrf
                <div class="input-wrapper">
                    <label for="services_id">Service ID</label>
                    <input type="number" id="services_id" name="services_id" value="{{ $service->services_id }}" readonly>
                </div>
                <div class="input-wrapper">
                    <label for="rating">Rating (out of 5)</label>
                    <input type="number" id="rating" name="rating" placeholder="Enter rating" min="1" max="5">
                </div>
                <div class="input-wrapper">
                    <label for="comment">Comment</label>
                    <textarea id="comment" name="comment" placeholder="Write your review here"></textarea>
                </div>
                <div class="input-wrapper">
                    <button type="submit">Submit Review</button>
                </div>
            </form>
        </div>

        <!-- Payment Section -->
        <div class="form-container">
            <h2>Payment</h2>
            <p>Is your booking confirmed?</p>
            <a href="{{ url('payment') }}">
                <button type="button" class="payment-btn">Pay Now</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
