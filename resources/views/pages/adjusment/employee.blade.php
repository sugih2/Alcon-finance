<script src="{{ url('assets/bootstrap/js/select2.min.js') }}"></script>
<div class="p2">
    <div class="row selectkar">
        <!-- Kolom untuk filterr -->
        <div class="col-md-6 filter" id="filterForm" style="display: none;">
            <div class="d-flex">
                <h2>Filter Selected Employees</h2>
                <button type="button" id="closeFilterForm" class="close d-flex ml-auto" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form id="filterCriteria">
                <div class="form-group">
                    <label for="cabang">Region:</label>
                    <select id="region" name="region">
                        <option value="" disabled selected>Pilih Region</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cabang">Cabang:</label>
                    <select id="cabang" name="cabang">
                        <option value="" disabled selected>Pilih Cabang</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bagian">Bagian:</label>
                    <select id="bagian" name="bagian">
                        <option value="" disabled selected>Pilih Bagian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="par_level_jabatan">Par Level Jabatan:</label>
                    <select id="par_level_jabatan" name="par_level_jabatan">
                        <option value="" disabled selected>Pilih Par Jabatan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="level_jabatan">Level Jabatan:</label>
                    <select id="level_jabatan" name="level_jabatan">
                        <option value="" disabled selected>Pilih Jabatan</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" id="closeFilterFormKar" class="btn btn-secondary mr-2"
                        onclick="Reset()">Reset</button>
                    <button type="submit" class="btn btn-primary" id="loadEmployees">Filter</button>
                </div>
            </form> --}}
        </div>
        <!-- Kolom untuk daftar karyawan -->
        <div class="col-md-6 choice" id="choiceSection">
            <div class="head">
                <div class="head-main">
                    <h6 id="view-employ">View 0 of 0 Employee(s)</h6>
                    <span class="btn-select" id="btn-add-all">Select All</span>
                </div>
                <div class="filter">
                    <form class="search" id="searchForm">
                        <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" class="form-control" placeholder="Search" id="searchEmployees"
                                name="search">
                        </div>
                    </form>
                    <div>
                        <button type="button" id="filterButton" type="button" class="btn btn-outline-secondary">Filter
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-funnel-fill" viewBox="0 0 16 16">
                                <path
                                    d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!--cardemploye-->
            <div class="card">
                <div class="card-header">
                    Add Employees
                </div>
                <div class="card-body">
                    <select name="items[]" id="items" class="form-control" multiple>
                    </select>
                </div>
                <button type="button" id="btn-add" class="btn btn-outline-primary">Add</button>
            </div>
        </div>
        <!-- Kolom untuk hasil pemilihan karyawan -->
        <div class="col-md-6 result" id="resultSection">
            <div class="head">
                <div class="head-main">
                    <h6 id="view-select">0 Employee(s) Selected</h6>
                    <span class="btn-select" id="btn-remove-all">Clear Selection</span>
                </div>
                <form class="search" id="searchForm">
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" placeholder="Search" id="searchSelectedEmployees"
                            name="search">
                    </div>
                </form>
            </div>
            <!--cardemploye-->
            <div class="card">
                <div class="card-header">
                    Selected Employees
                </div>
                @csrf
                <select name="selectedItems[]" class="form-control" id="selected" multiple></select>
                <button type="button" id="btn-remove" class="btn btn-outline-danger">Remove</button>
            </div>
        </div>
    </div>
    <div class="footer">
        <button type="button" class="btn btn-secondary mr-2" onclick="Close()">Cancel</button>
        <button type="button" onClick="storeemployee()" id="btn-save" class="btn btn-primary">Submit</button>
    </div>
</div>


<!-----SELECTKAR----->
<!--SELECTED EMPLOYEE-->
<script>
    var itemsSelect = document.getElementById('items');
    var selectedSelect = document.getElementById('selected');

    document.getElementById('btn-add').addEventListener('click', function() {
        moveItems(itemsSelect, selectedSelect);
    });

    document.getElementById('btn-remove').addEventListener('click', function() {
        moveItems(selectedSelect, itemsSelect);
    });

    document.getElementById('btn-add-all').addEventListener('click', function() {
        moveAllItems(itemsSelect, selectedSelect);
    });

    document.getElementById('btn-remove-all').addEventListener('click', function() {
        moveAllItems(selectedSelect, itemsSelect);
    });

    function moveItems(source, destination) {
        var selectedOptions = Array.from(source.selectedOptions);
        selectedOptions.forEach(function(option) {
            option.selected = true;
            destination.appendChild(option);
        });

        var selectedOptions = Array.from(destination.options).filter(option => option.selected);
        selectedEmployees = [];
        selectedOptions.forEach(function(option) {
            var id = option.value;
            var nama_lengkap = option.text;
            var nomor_induk_karyawan = option.getAttribute('data-nomor-induk-karyawan');
            var karyawan = {
                id: id,
                nama_lengkap: nama_lengkap,
                nomor_induk_karyawan: nomor_induk_karyawan
            };
            selectedEmployees.push(karyawan);
        });

        updateViewEmployeeCount();
        updateSelectedEmployeeCount();
    }

    function moveAllItems(source, destination) {
        var options = Array.from(source.options);

        options.forEach(function(option) {
            option.selected = true;
            destination.appendChild(option);
        });

        var selectedOptions = Array.from(destination.options).filter(option => option.selected);
        selectedEmployees = [];
        selectedOptions.forEach(function(option) {
            var id = option.value;
            var nama_lengkap = option.text;
            var nomor_induk_karyawan = option.getAttribute('data-nomor-induk-karyawan');
            var karyawan = {
                id: id,
                nama_lengkap: nama_lengkap,
                nomor_induk_karyawan: nomor_induk_karyawan
            };
            selectedEmployees.push(karyawan);
        });

        updateViewEmployeeCount();
        updateSelectedEmployeeCount();
    }

    function storeemployee() {
        var storeTable = document.getElementById('store');
        storeTable.style.display = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ url('adjusment/store-employee') }}', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        xhr.onload = function() {
            if (xhr.status === 200) {
                $("#pageemploy").html("");
                $("#exampleModal").modal("hide");
                $("#cover-spin").hide();
                Swal.fire({
                    icon: "success",
                    title: xhr.message,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    timer: 1500,
                });
                var responseData = JSON.parse(xhr.responseText);
                console.log(responseData);
                insertResponseDataIntoTable(responseData);
                storeTable.style.display = 'block';
            } else {
                console.error('Request failed. Status code: ' + xhr.status);
            }
        };
        xhr.onerror = function() {
            console.error('Request failed. Network error.');
        };
        xhr.send(JSON.stringify(selectedEmployees));
    }
</script>

<!--SCRIPT SEARCHING-->
<script>
    // Fungsi untuk melakukan pencarian berdasarkan teks yang dimasukkan pengguna
    function searchEmployees() {
        var input, filter, select, options, i, option, txtValue;
        input = document.getElementById('searchEmployees'); // Gunakan ID yang sesuai untuk elemen pencarian karyawan
        filter = input.value.toUpperCase();
        select = document.getElementById('items');
        options = select.getElementsByTagName('option');

        // Loop melalui setiap opsi dalam elemen <select>
        for (i = 0; i < options.length; i++) {
            option = options[i];
            txtValue = option.textContent || option.innerText;
            // Jika teks opsi cocok dengan nilai pencarian, tampilkan opsi
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                option.style.display = '';
            } else {
                // Jika tidak cocok, sembunyikan opsi
                option.style.display = 'none';
            }
        }
    }

    // Tambahkan event listener untuk input pencarian karyawan
    document.getElementById('searchEmployees').addEventListener('input', searchEmployees);

    // Fungsi untuk melakukan pencarian berdasarkan teks yang dimasukkan pengguna untuk karyawan yang telah dipilih
    function searchSelectedEmployees() {
        var input, filter, select, options, i, option, txtValue;
        input = document.getElementById(
            'searchSelectedEmployees'); // Gunakan ID yang sesuai untuk elemen pencarian karyawan yang telah dipilih
        filter = input.value.toUpperCase();
        select = document.getElementById('selected');
        options = select.getElementsByTagName('option');

        // Loop melalui setiap opsi dalam elemen <select> untuk karyawan yang telah dipilih
        for (i = 0; i < options.length; i++) {
            option = options[i];
            txtValue = option.textContent || option.innerText;
            // Jika teks opsi cocok dengan nilai pencarian, tampilkan opsi
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                option.style.display = '';
            } else {
                // Jika tidak cocok, sembunyikan opsi
                option.style.display = 'none';
            }
        }
    }

    // Tambahkan event listener untuk input pencarian karyawan yang telah dipilih
    document.getElementById('searchSelectedEmployees').addEventListener('input', searchSelectedEmployees);
</script>

<!--COUNT INFORMATION-->
<script>
    var totalEmployees = document.getElementById('items').getElementsByTagName('option').length;

    document.getElementById('view-employ').innerText = "View 0 of " + totalEmployees + " Employee(s)";

    function updateViewEmployeeCount() {
        var select = document.getElementById('items');
        var options = select.getElementsByTagName('option');
        var visibleCount = options.length;

        document.getElementById('view-employ').innerText = "View " + visibleCount + " of " + totalEmployees +
            " Employee(s)";
    }

    function updateSelectedEmployeeCount() {
        var select = document.getElementById('selected');
        var options = select.getElementsByTagName('option');
        var count = options.length;

        document.getElementById('view-select').innerText = count + " Employee(s) Selected";
    }

    updateViewEmployeeCount();
    updateSelectedEmployeeCount();
</script>

<!--FILTER-->
<script>
    $(document).ready(function() {
        $('#filterButton').click(function() {
            $('#filterForm').css('left', '0'); // Atur posisi ke kiri saat ditampilkan
            $('#filterForm').toggle(); // Menampilkan atau menyembunyikan tampilan filter
        });

        // Ketika tombol "Tutup" pada form diklik
        $('#closeFilterFormKar').click(function() {
            $('#filterForm').css('left', '-100%'); // Set position to left when closing
            $('#filterForm').hide();

            $('#filterCriteria select').each(function() {
                var selectizeInstance = $(this)[0].selectize;
                if (selectizeInstance) {
                    selectizeInstance.clear(); // Clear selected values
                    selectizeInstance.setValue(''); // Reset to the default value
                } else {
                    $(this).val(''); // Fallback for regular select elements
                    $(this).prop('selectedIndex', 0); // Set to the first option
                }
            });
        });


        // Atur tindakan yang dijalankan ketika form diserahkan (submit)
        $('#filterCriteria').submit(function(event) {
            // Lakukan sesuatu dengan aturan filter, misalnya memfilter data atau menampilkan konten yang sesuai
            $('#filterForm').hide(); // Sembunyikan form filter
            event.preventDefault(); // Mencegah pengiriman form secara default
        });
    });
</script>
<!-----ENDSELECTKAT----->


<style>
    .p2 {
        font-family: "Nunito Sans", sans-serif;
    }

    .p2 .footer {
        display: flex;
        justify-content: flex-end;
        margin: 0px;
        padding: 5px 0px 0px 0px;
    }

    .p2 .selectkar .choice {
        border-right: 1px solid var(--Stroke-Grey, #C2C2C2);
        position: relative;
    }

    .p2 .selectkar .choice .head .head-main {
        display: flex;
        justify-content: space-between;
    }

    .p2 .selectkar .choice .head .filter {
        display: flex;
        justify-content: space-between;
    }

    .p2 .selectkar .result .head .head-main {
        display: flex;
        justify-content: space-between;
    }

    .p2 .selectkar .choice .head .head-main .btn-select {
        text-decoration: underline;
        margin-bottom: 0px;
        color: var(--primary-1, #4A62B4);
        cursor: pointer;
    }

    .p2 .selectkar .result .head .head-main .btn-select {
        text-decoration: underline;
        margin-bottom: 0px;
        color: var(--primary-1, #4A62B4);
        cursor: pointer;
    }

    .p2 .selectkar .choice .card {
        height: 400px;
        overflow-y: auto;
    }

    .p2 .selectkar .result .card .form-control {
        height: 317px;
        overflow-y: auto;
    }

    .p2 .selectkar .choice .card .card-body {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        margin: 0px;
        padding: 0px;
    }

    .p2 .selectkar .choice .card .card-body .kar {
        margin: 0px;
        font-weight: 2px;
    }

    .p2 .selectkar .choice .card .card-body .btn-add {
        justify-content: center;
        color: #4A62B4;
        cursor: pointer;
        align-items: center;
        display: flex;
        margin-right: 20px;
    }

    .has-search .form-control {
        padding-left: 2.375rem;
        border-radius: 10px;

    }

    .has-search .form-control-feedback {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
        color: #aaa;
    }

    /* public/css/payroll.css */

    /* Styling untuk filter form */
    #filterForm {
        background-color: #f9f9f9;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
        height: 100%;
        width: 100%;
    }
</style>
