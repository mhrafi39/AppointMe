<!-- <div>
    <div class="card">
    <h4>{{ $service->title }}</h4>
    <p>{{ $service->description }}</p>
    <p>Category: {{ $service->category }}</p>
    <p>Location: {{ $service->location }}</p>
    <p>Price: {{ $service->price }} Tk</p>
    <p>Available Time: {{ $service->available_time }}</p>
    </div>
</div> -->

<div class="service-card">
    <!-- Image -->
    <div class="service-img">
        <img src="{{ asset('storage/image/electrician.jpeg') }}" alt="Electrician">
    </div>

    <!-- Content -->
    <div class="service-info">
        <div class="service-header">
            <div>
                <h4>{{ $service->title }}</h4>
                <br>
                <p class="user_id">User-Id: 2005</p>
                <br>
                <p class="job">Job: {{ $service->category }}</p>
            </div>
            <div class="price-section">
                <img src="{{ asset('storage/image/tools-icon.jpeg') }}" alt="Tools" class="icon">
                <p class="price">{{ $service->price }}</p>
            </div>
        </div>

        <div class="service-footer">
            <div class="time">⏰ {{ $service->available_time }}</div>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

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
</style>

















<!-- 

/* Fade out after 3 seconds */
@keyframes fadeOut {
    0% {opacity: 1;}
    80% {opacity: 1;}
    100% {opacity: 0;}
}
</style>




<button class="create-service-btn">
    <span class="plus-icon">+</span> Create Services
</button>

<style>
.create-service-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px; /* space between icon and text */
    padding: 10px 16px;
    background-color: #4CAF50; /* green */
    color: white;
    font-size: 14px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}

.create-service-btn:hover {
    background-color: #45a049;
}

.plus-icon {
    font-size: 18px; /* bigger icon */
    line-height: 1;
}
</style> -->
