<form id="FormComponen">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
    </div>
    <div class="mb-3">
        <label for="type" class="form-label">Type</label>
        <select class="form-control" id="type" name="type" required>
            <option value="" selected disabled>-- Select Type --</option>
            <option value="Pokok">Pokok</option>
            <option value="Lembur">Lembur</option>
            <option value="Tunjangan Luar Kota">Tunjangan Luar Kota</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Amount</label>
        <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
    </div>
    <button type="button" id="btn-submit" onclick="StoreComponen()" class="btn btn-primary">Simpan</button>
</form>
