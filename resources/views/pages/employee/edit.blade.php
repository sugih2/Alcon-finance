<form id="FromEditEmployee">
    @csrf
    <div class="mb-3">
        <label for="nip" class="form-label">NIP</label>
        <input type="text" class="form-control" id="nip" value="{{ $employee->nip }}" name="nip"
            placeholder="NIP" maxlength="10" required>
    </div>
    <div class="mb-3">
        <label for="nik" class="form-label">NIK</label>
        <input type="text" class="form-control" id="nik" value="{{ $employee->nik }}" name="nik"
            placeholder="Masukkan NIK" required maxlength="16" minlength="16" pattern="\d{16}">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Name Employee</label>
        <input type="text" class="form-control" id="name" value="{{ $employee->name }}" name="name"
            placeholder="Name Employee" required>
    </div>
    <div class="mb-3">
        <label for="birth_date" class="form-label">Brith Date</label>
        <input type="date" class="form-control" id="birth_date" value="{{ $employee->birth_date }}" name="birth_date"
            placeholder="Brith Date" required>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" value="{{ $employee->address }}" name="address"
            placeholder="Address" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" value="{{ $employee->email }}" name="email"
            placeholder="Email" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="number" class="form-control" id="phone" value="{{ $employee->phone }}" name="phone"
            placeholder="Phone Number" required>
    </div>
    <div class="mb-3">
        <label for="position" class="form-label">Position</label>
        <select name="position" id="position" required>
            <option value="position" selected>Select Position</option>
        </select>
    </div>
    {{-- <div class="form-group">
        <label for="status">Status</label>
        <select class="form-control" id="status" name="status">
            <option value="Aktif" {{ $employee->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="NonAktif" {{ $employee->status == 'NonAktif' ? 'selected' : '' }}>NonAktif</option>
        </select>
    </div> --}}
    <button type="button" id="btn-submit" onclick="StoreEditEmployee({{ $employee->id }})" class="btn btn-primary">Simpan</button>
</form>

<script>
    document.querySelectorAll('#nik, #nip, #phone').forEach((element) => {
        element.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });
</script>

