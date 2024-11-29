<form id="FromParamPosition">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name Parameter Position</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name" required>
    </div>
    <button type="button" onclick="StoreParamPosition()" id="btn-submit" class="btn btn-primary">Simpan</button>
</form>
