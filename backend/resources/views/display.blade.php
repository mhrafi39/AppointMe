<div>
    <h1>Profile Image</h1>
    @foreach($imgData as $img)
        <img src="{{ asset('storage/' . $img->path) }}" alt="Profile Image" style="width:150px; height:150px;">
    @endforeach
</div>
