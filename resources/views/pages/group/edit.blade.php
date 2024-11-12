<form id="FormEditGroup">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name Group</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name" required>
    </div>
    <div class="mb-3">
        <label for="code" class="form-label">Code</label>
        <input type="text" class="form-control" id="code" name="code" placeholder="code" required>
    </div>
    <div class="mb-3">
        <label for="project" class="form-label">Project</label>
        <select name="project" id="projectedit" required>
            <option value="" selected>Select Project</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="leader" class="form-label">Leader</label>
        <select name="leader" id="leaderedit" required>
            <option value="" selected>Select Leader</option>
        </select>
    </div>
    <button type="button" id="btn-submit" onclick="StoreGroup()" class="btn btn-primary">Simpan</button>
</form>
