<form id="FromEmployee">
    @csrf
    <div class="mb-3">
        <label for="nip" class="form-label">NIP</label>
        <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP" maxlength="10" required>
    </div>
    <div class="mb-3">
        <label for="nik" class="form-label">NIK</label>
        <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK"
        maxlength="16" minlength="16" pattern="\d{16}" required>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Name Employee</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name Employee" required>
    </div>
    <div class="mb-3">
        <label for="birth_date" class="form-label">Brith Date</label>
        <input type="date" class="form-control" id="birth_date" name="birth_date" placeholder="Brith Date" required>
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" maxlength="13" required>
    </div>
    <div class="mb-3">
        <label for="position" class="form-label">Position</label>
        <select name="position" id="position" required>
            <option value="" selected>Select Position</option>
        </select>
    </div>
    <button type="button" id="btn-submit" onclick="StoreEmployee()" class="btn btn-primary">Simpan</button>
</form>

<script>
    document.querySelectorAll('#nik, #nip, #phone').forEach((element) => {
        element.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    });
</script>
