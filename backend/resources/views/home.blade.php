<!DOCTYPE html>
<html>
<head>
    <title>Home - Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        /* Flash message */
        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: black;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; }
        }

        /* Top-right section */
        .top-right-section {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            background-color: #333;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 16px;
        }

        .create-service-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background-color: black;
            color: white;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .create-service-btn:hover {
            background-color: #333;
        }

        .plus-icon {
            font-size: 18px;
            line-height: 1;
        }

        /* Search box */
        .search-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-container input {
            padding: 8px 12px;
            width: 250px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
        }

        .search-container button {
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        /* Container for all cards */
        .all-cards-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 60px; /* leave space for top-right section */
        }

        /* Card styles */
        .service-card {
            width: 200px;  
            height: 250px; 
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 10px;
            display: flex;
            flex-direction: column;
        }

        .service-img img {
            width: 100%;
            height: 120px; 
            object-fit: cover;
        }

        .service-info {
            padding: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .service-header h4 {
            font-size: 14px;
            font-weight: bold;
        }

        .user_id, .job {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        .price-section {
            text-align: right;
        }

        .price-section .icon {
            width: 30px;   
            height: 30px;  
            display: block;
            margin-bottom: 2px;
        }

        .price {
            font-size: 14px;
            font-weight: bold;
            color: black;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #555;
        }

        .service-card-link {
            text-decoration: none;
            color: inherit; 
            display: inline-block;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-card-link:hover .service-card {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Pagination styles */
        .pagination-links {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pagination-links a,
        .pagination-links span {
            padding: 8px 14px;
            border-radius: 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            min-width: 36px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .pagination-links span {
            background-color: black;
            color: white;
            border-color: black;
            font-weight: bold;
        }

        .pagination-links a:hover {
            background-color: #333;
            color: white;
            border-color: #333;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

<!-- Top-right section -->
<div class="top-right-section">
    <!-- Create Service button -->
    <a href="{{ url('services-form') }}" class="create-service-btn">
        <span class="plus-icon">+</span> Create Services
    </a>
    <!-- Notification icon -->
    <div class="icon-btn" title="Notifications">🔔</div>
    <!-- Profile icon -->
     <div>
        <a href="{{ url('profile') }}" class="icon-btn" title="Profile">👤</a>
     </div>
</div>

<!-- Flash message -->
@if(session('success'))
    <div class="flash-message">{{ session('success') }}</div>
@endif

<!-- Search box -->
<div class="search-container">
    <form action="search" method="get">
        <input type="text" placeholder="Search with job name" name="search"/>
        <button>Search</button>
    </form>
</div>

<h1>All Services</h1>

<div class="all-cards-container">
    @foreach($services as $service)
        <a href="{{ url('servicePage/'.$service->services_id) }}" class="service-card-link">
            <div class="service-card">
                <div class="service-img">
                    <img src="{{ $service->profile_picture 
                                ? asset('storage/' . $service->profile_picture) 
                                : asset('storage/default.jpeg') }}" 
                        alt="Service Image">
                </div>

                <div class="service-info">
                    <div class="service-header">
                        <div>
                            <h4>{{ $service->name }}</h4>
                            <p class="user_id">User ID: {{ $service->user_id }}</p>
                            <p class="job">Job: {{ $service->category }}</p>
                        </div>
                        <div class="price-section">
                            <img src="{{ asset('storage/tools.png') }}" alt="Tools" class="icon">
                            <p class="price">{{ $service->price }} Tk</p>
                        </div>
                    </div>
                    <div class="service-footer">
                        <div class="time">⏰ {{ $service->available_time }}</div>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>

<!-- Custom Pagination -->
<div class="pagination-links">
    @if ($services->onFirstPage())
        <span>&lt; Previous</span>
    @else
        <a href="{{ $services->previousPageUrl() }}">&lt; Previous</a>
    @endif

    @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
        @if ($page == $services->currentPage())
            <span>{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    @if ($services->hasMorePages())
        <a href="{{ $services->nextPageUrl() }}">Next &gt;</a>
    @else
        <span>Next &gt;</span>
    @endif
</div>

</body>

@if(session('reload'))
<script>
    window.location.reload(true);
</script>
@endif

</html>
