<form id="FromProject">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name Project</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name" required>
    </div>
    <div class="mb-3">
        <label for="code" class="form-label">Code Project</label>
        <input type="text" class="form-control" id="code" name="code" placeholder="code" required>
    </div>
    <div class="mb-3">
        <label for="jenis" class="form-label">Jenis</label>
        <input type="text" class="form-control" id="jenis" name="jenis" placeholder="jenis" required>
    </div>
    <div class="mb-3">
        <label for="regency" class="form-label">Daerah/Kota</label>
        <select name="regency" id="regency" required>
            <option value="" selected>Select Daerah/Kota</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea type="text" class="form-control" id="description" name="description" placeholder="description"></textarea>
    </div>
    <button type="button" onclick="StoreProject()" class="btn btn-primary">Simpan</button>
</form>
