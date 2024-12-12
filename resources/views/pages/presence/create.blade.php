<form id="FormPresence">
    @csrf
    <div class="mb-3">
        <label for="employed_id" class="form-label">Employee Name</label>
        <select name="employed_id" id="employeeName" required>
            <option value="" selected>Employe uuuuu</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal_scan" class="form-label">Tanggal Scan</label>
        <input type="datetime-local" step="1" class="form-control" id="tanggal_scan" name="tanggal_scan" >
    </div>
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="datetime-local" step="1" class="form-control" id="tanggal" name="tanggal" >
    </div>
    <div class="mb-3">
        <label for="jam_masuk" class="form-label">Jam Masuk </label>
        <input type="time" step="1" class="form-control" id="jam_masuk" name="jam_masuk" >
    </div>
    <div class="mb-3">
        <label for="jam_pulang" class="form-label">Jam Pulang </label>
        <input type="time" step="1" class="form-control" id="jam_pulang" name="jam_pulang" >
    </div>
    <button type="button" id="btn-submit" onclick="StorePresence()" class="btn btn-primary">Simpan</button>
</form>
