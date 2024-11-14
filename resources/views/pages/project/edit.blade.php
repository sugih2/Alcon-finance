<form id="FromEditprojects">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name projects</label>
        <input type="text" class="form-control" value="{{ $projects->name }}" id="name" name="name"
            placeholder="name" required>
    </div>
    <div class="mb-3">
        <label for="code" class="form-label">Code projects</label>
        <input type="text" class="form-control" value="{{ $projects->code }}" id="code" name="code"
            placeholder="code" required>
    </div>
    <div class="mb-3">
        <label for="jenis" class="form-label">Jenis</label>
        <input type="text" class="form-control" value="{{ $projects->jenis }}" id="jenis" name="jenis"
            placeholder="jenis" required>
    </div>
    <div class="mb-3">
        <label for="regency" class="form-label">Daerah/Kota</label>
        <select name="regency" id="regencyedit" required>
            <option value="" selected>Select Daerah/Kota</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" class="form-control" value="{{ $projects->description }}" id="description" name="description"
            placeholder="description">{{ $projects->description }}</textarea>
    </div>
    <button type="button" onclick="StoreEditProjects({{ $projects->id }})" id="btn-submit"
        class="btn btn-primary">Update</button>
</form>
