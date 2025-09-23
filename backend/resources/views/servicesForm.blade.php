<div class="form-container">
    <h2>Add New Service</h2>
    <form action="addService" method="post">
        @csrf
        <div class="input-wrapper">
            <label for="user_id">User ID</label>
            <input type="number" id="user_id" placeholder="Enter User ID" name="user_id">
            <span style="color:red">@error('user_id'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="title">Name</label>
            <input type="text" id="name" placeholder="Name" name="name">
            <span style="color:red">@error('name'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="description">Description</label>
            <input type="text" id="description" placeholder="Service Description" name="description">
            <span style="color:red">@error('description'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="category">Category</label>
            <input type="text" id="category" placeholder="Category" name="category">
            <span style="color:red">@error('category'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="location">Location</label>
            <input type="text" id="location" placeholder="Location" name="location">
            <span style="color:red">@error('location'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="price">Price (tk)</label>
            <input type="text" id="price" placeholder="Price" name="price">
            <span style="color:red">@error('price'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="available_time">Available Time (… am – … pm)</label>
            <input type="text" id="available_time" placeholder="Available time" name="available_time">
            <span style="color:red">@error('available_time'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <button type="submit">Create</button>
        </div>
    </form>
</div>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
}

.form-container {
    background-color: #fff;
    padding: 25px 30px;
    width: 300px;
    margin: 50px auto;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #4CAF50;
    margin-bottom: 20px;
}

.input-wrapper {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
}

.input-wrapper label {
    margin-bottom: 5px;
    font-weight: bold;
    color: #4CAF50;
    font-size: 14px;
}

input {
    padding: 8px 10px;
    border: 1px solid #4CAF50;
    border-radius: 6px;
    outline: none;
    font-size: 14px;
    transition: 0.3s;
}

input:focus {
    border-color: #388E3C;
    box-shadow: 0 0 5px rgba(56,142,60,0.3);
}

button {
    padding: 10px;
    width: 100%;
    border: none;
    border-radius: 6px;
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #388E3C;
}
</style>
