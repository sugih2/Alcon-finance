<form id="FormPresence">
    @csrf
    <div class="mb-3">
        <label for="employe_name" class="form-label">Position</label>
        <select name="employe_name" id="employe_name" required>
            <option value="" selected>Employe Name</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="tanggal_scan" class="form-label">Tanggal Scan</label>
        <input type="datetime-local" class="form-control" id="tanggal_scan" name="tanggal_scan" 
               value="{{ date('Y-m-d\TH:i:s', strtotime($shifts->tanggal_scan)) }}">
    </div>
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" 
               value="{{ date('Y-m-d\TH:i:s', strtotime($shifts->tanggal)) }}">
    </div>
    <div class="mb-3">
        <label for="jam_masuk" class="form-label">Jam Masuk </label>
        <input type="time" class="form-control" id="jam_masuk" name="jam_masuk" 
            value="{{ $presence->jam_masuk }}" >
    </div>
    <div class="mb-3">
        <label for="jam_pulang" class="form-label">Jam Pulang </label>
        <input type="time" class="form-control" id="jam_pulang" name="jam_pulang" 
            value="{{ $presence->jam_pulang }}" >
    </div>
    <button type="button" id="btn-submit" onclick="StoreEmployee()" class="btn btn-primary">Simpan</button>
</form>
