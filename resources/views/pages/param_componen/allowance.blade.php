<form id="allowanceForm" class="border-form">
    @csrf

    <input name="type" type="hidden" class="form-control form-control-sm mr-1" value="Penambah">
    <input name="componen" type="hidden" class="form-control form-control-sm mr-1" value="Allowance">

    <h5 class="headform"><span id="selectedComponentType" style="color: green;"></h5>
    <div class="subtitle">
        <h6>Employee Setting</h6>
    </div>
    <div class="row mb-3 select">

        <div class="col-4">
            <div class="form-group">
                <label for="region">Regency</label>
                <select class="form-control cihuy" id="id_regency" name="id_regency" required>
                    <option value="" disabled selected>-- Select Regency --</option>
                    @foreach ($regency as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="subtitle">
        <h6>Components Settings</h6>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="category1" value="TLK"
                        required>
                    <label class="form-check-label" for="category1"> Luar Kota </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="category" id="category2" value="UM"
                        required>
                    <label class="form-check-label" for="category2"> Uang Makan </label>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <label for="nilai">Nilai</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control" style="height: 40px;" id="nilai" name="amount"
                    placeholder="Masukan Nilai">
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <button class="btn btn-secondary mr-2" id="closeButton"> Close</button>
        <button class="btn btn-primary" onclick="createallowance()">Submit</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        document.getElementById('closeButton').addEventListener('click', function() {
            document.getElementById('allowanceForm').style.display = 'none';
        });
        $('.cihuy').select2({
            placeholder: "-- Select Regency --",
            allowClear: true
        });

        $('#allowanceForm').on('submit', function(e) {
            var selectedCity = $('#id_regency').val();
            if (!selectedCity) {
                alert('Please select a regency.');
                e.preventDefault();
            }
        });
    });
    $('#nilai').on('input', function() {
        var value = $(this).val();

        value = value.replace(/[^\d]/g, '');
        var numericValue = parseFloat(value);

        var rupiah = '';
        var valueLength = value.length;

        for (var i = valueLength; i > 0; i--) {
            if ((valueLength - i) % 3 === 0 && i !== valueLength) {
                rupiah = '.' + rupiah;
            }
            rupiah = value[i - 1] + rupiah;
        }

        $(this).val(rupiah);
        $(this).data('numericValue', numericValue);
    });
</script>
