<form id="FormEditPresence">
    @csrf
    <div class="mb-3">
        <label for="employee_name" class="form-label">Employee Name</label>
        <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name"
            value="{{ $presence->employee->name }}" >
    </div>
    <div class="mb-3">
        <label for="tanggal_scan" class="form-label">Tanggal Scan</label>
        <input type="date" class="form-control" id="tanggal_scan" name="tanggal_scan" 
            value="{{ date('Y-m-d', strtotime($presence->tanggal_scan)) }}" >
    </div>
    <div class="mb-3">
        <label for="tanggal" class="form-label">Tanggal </label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" 
            value="{{ date('Y-m-d', strtotime($presence->tanggal)) }}" required>
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
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <input type="text" class="form-control" id="status" name="status" placeholder="Status"
            value="{{ $presence->presensi_status }}" >
    </div>
    <button type="button" onclick="StoreEditPresence({{ $presence->id }})" class="btn btn-primary"
        id="btn-submit">Simpan</button>
</form>