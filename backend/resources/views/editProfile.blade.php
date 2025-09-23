
<div>
    <div class="form-container">
    <h2>Edit Profile</h2>
    <form action="" method="post">
        @csrf
        <div class="input-wrapper">
            <label for="user_id">User ID</label>
            <input type="number" id="user_id" placeholder="Enter User ID" name="user_id">
            <span style="color:red">@error('user_id'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="name">Name</label>
            <input type="text" id="name" placeholder="Name" name="name">
            <span style="color:red">@error('name'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="email">Email</label>
            <input type="email" id="Email" placeholder="Email" name="email">
            <span style="color:red">@error('email'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="phone">Phone</label>
            <input type="text" id="phone" placeholder="Phone" name="phone">
            <span style="color:red">@error('phone'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="address">Address</label>
            <input type="text" id="address" placeholder="Address" name="address">
            <span style="color:red">@error('address'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <label for="role">Role</label>
            <input type="text" id="role" placeholder="Role" name="role">
            <span style="color:red">@error('role'){{ $message }}@enderror</span>
        </div>
        <div class="input-wrapper">
            <button type="submit">Update</button>
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

</div>
