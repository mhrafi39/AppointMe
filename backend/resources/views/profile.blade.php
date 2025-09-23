<div>
    <h1>Profile Page</h1>
</div>

<div>
    <h2>Upload Profile Picture</h2>

    <!-- Show messages from redirect (optional) -->
    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <!-- Upload Form -->
    <form action="{{ url('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="profile_picture" required>
        <button type="submit">Upload</button>
    </form>
</div>

<div>
    <h2>Profile Images</h2>

    @php
        // Ensure $imgData is always defined
        $imgData = isset($imgData) ? $imgData : \App\Models\Image::all();
    @endphp

    @foreach($imgData as $img)
        <img src="{{ asset('storage/' . $img->path) }}" alt="Profile Image" style="width:150px; height:150px; margin:5px;">
    @endforeach
</div>
