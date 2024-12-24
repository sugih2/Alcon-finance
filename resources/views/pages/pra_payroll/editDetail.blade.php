<form id="FormEditDetailPraPayroll">
    @csrf
    <div class="mb-3">
        <label for="id_transaksi" class="form-label">ID Transaksi</label>
        <input type="text" class="form-control" id="id_transaksi" name="id_transaksi" placeholder="Id Transaksi"
            value="{{ $details->id_transaksi}}"  readonly>
    </div>
    <div class="mb-3">
        <label for="employee_name" class="form-label">Name Employee</label>
        <input type="text" class="form-control" id="employee_name" name="employee_name" placeholder="Employee Name"
            value="{{ $details->employee->name }}"  readonly>
    </div>
    <div class="mb-3">
        <label for="component" class="form-label">Name Component</label>
        <select name="component" id="component" required>
            <option value=""  selected  >Selected Component</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="Amount" class="form-label">Amount </label>
        <input type="text" class="form-control" id="Amount" name="Amount" 
            value="{{ $details->amount  }}" disabled>
    </div>
    <div class="mb-3">
        <label for="new_amount" class="form-label">New Amount</label>
        <button type="button" class="btn btn-link text-primary mb-0" id="addNewAmountBtn">(Add New Amount)</button>
        <input type="text" class="form-control" id="new_amount" name="new_amount" 
            value="{{ $details->new_amount }}" disabled>
    </div>
    <button type="button" onclick="StoreEditDetail({{ $details->id }})" class="btn btn-primary"
        id="btn-submit">Simpan</button>
</form>
<script>
document.getElementById('component').addEventListener('change', function() {
        console.log('cek amount : ', this.value)
        const selectedValue = this.value; // Ambil nilai dari dropdown
        const amountInput = document.getElementById('Amount'); // Ambil elemen input

        if (selectedValue) {
            amountInput.value = selectedValue; // Ubah nilai input
        } else {
            amountInput.value = "{{ $details->amount }}"; // Kembalikan ke nilai awal jika tidak ada yang dipilih
        }
    });
document.getElementById('addNewAmountBtn').addEventListener('click', function(event) {
        event.preventDefault(); // Mencegah halaman refresh
        const inputField = document.getElementById('new_amount');
        inputField.disabled = false; // Mengaktifkan input saat tombol diklik
    });

</script>
