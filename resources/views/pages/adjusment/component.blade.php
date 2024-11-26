<div class="p2">
    <div class="row selectkar">
        <!--TAMPILAN FILTERR-->
        <div class="col-md-6 filter" id="filterFormCom" style="display: none;">
            <h2>Filter Selected Components</h2>
            <form id="filterCriteriaCom">
                <div class="form-group">
                    <label for="cabang">Region:</label>
                    <select id="region" name="region">
                        <option value="" disabled selected>Pilih Region</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" id="loadEmployees">Submit</button>
                <button type="button" id="closeFilterFormCom" class="btn btn-secondary">Close</button>
            </form>
        </div>
        <!-- Kolom untuk daftar karyawan -->
        <div class="col-md-6 choice" id="choiceSection">
            <div class="head">
                <div class="head-main">
                    <h6 id="view-component">View 0 of 0 employee(s)</h6>
                    <span class="btn-select" id="btn-add-all-com">Select All</span>
                </div>
                <div class="filter">
                    <form class="search" id="searchForm">
                        <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" class="form-control" placeholder="Search" id="searchComponent"
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
                    Add Components
                </div>
                <div class="card-body">
                    <select name="items[]" id="comitems" class="form-control" multiple>
                    </select>
                </div>
                <button type="button" id="btn-add-com" class="btn btn-outline-primary">Add</button>
            </div>
        </div>
        <!-- Kolom untuk hasil pemilihan karyawan -->
        <div class="col-md-6 result" id="resultSection">
            <div class="head">
                <div class="head-main">
                    <h6 id="view-select-com">0 Component(s) Selected</h6>
                    <span class="btn-select" id="btn-remove-all-com">Clear Selection</span>
                </div>
                <form class="search" id="searchForm">
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" placeholder="Search" id="searchSelectedComponent"
                            name="search">
                    </div>
                </form>
            </div>
            <!--cardemploye-->
            <div class="card">
                <div class="card-header">
                    Selected Components
                </div>
                @csrf
                <select name="selectedCom[]" class="form-control" id="selectedcom" multiple></select>
                <button type="button" id=btn-remove-com class="btn btn-outline-danger">Remove</button>
            </div>
        </div>
    </div>
    <div class="footer">
        <button type="button" class="btn btn-secondary mr-2" onclick="Close()">Cancel</button>
        <button type="button" onclick="storecomponent()" id="btn-save-com" class="btn btn-primary">Submit</button>
    </div>
</div>



<!-----SELECTCOMPONENT----->
<!--SELECTED COMPONENT-->
<script>
    // Get references to the select elements
    var ComSelect = document.getElementById('comitems');
    var selectedSelect = document.getElementById('selectedcom');
    var globalComponentData;

    // Add event listener to add button
    document.getElementById('btn-add-com').addEventListener('click', function() {
        moveItems(ComSelect, selectedSelect);
    });

    // Add event listener to remove button
    document.getElementById('btn-remove-com').addEventListener('click', function() {
        moveItems(selectedSelect, ComSelect);
    });

    // Add event listener to add all button
    document.getElementById('btn-add-all-com').addEventListener('click', function() {
        moveAllItems(ComSelect, selectedSelect);
    });

    // Add event listener to remove all button
    document.getElementById('btn-remove-all-com').addEventListener('click', function() {
        moveAllItems(selectedSelect, ComSelect);
    });

    function moveItems(source, destination) {
        var selectedComOptions = Array.from(source.selectedOptions);
        selectedComOptions.forEach(function(option) {
            option.selected = true; // Set the selected property to true
            destination.appendChild(option);
        });

        // Update the selectedComponent array
        var selectedComOptions = Array.from(destination.options).filter(option => option.selected);
        selectedComponent = [];
        selectedComOptions.forEach(function(option) {
            var id_com = option.value;
            var nama_component = option.getAttribute('data-nama'); // Mengambil nilai atribut data-nama
            var komponen = option.getAttribute('data-komponen'); // Mengambil nilai atribut data-component-type
            var amount = option.getAttribute('data-amount'); // Mengambil nilai atribut data-current-amount;
            var component = {
                id_com: id_com,
                nama_component: nama_component,
                komponen: komponen,
                amount: amount
            };
            selectedComponent.push(component);
        });

        // Call the updateViewComponentCount and updateSelectedComponentCount functions
        updateViewComponentCount();
        updateSelectedComponentCount();
    }

    function moveAllItems(source, destination) {
        var options = Array.from(source.options);

        // Move all options from source to destination
        options.forEach(function(option) {
            option.selected = true; // Set the selected property to true
            destination.appendChild(option);
        });

        // Update the selectedComponent array
        var selectedComOptions = Array.from(destination.options).filter(option => option.selected);
        selectedComponent = [];
        selectedComOptions.forEach(function(option) {
            var id_com = option.value;
            var nama_component = option.getAttribute('data-nama'); // Mengambil nilai atribut data-nama
            var komponen = option.getAttribute('data-komponen'); // Mengambil nilai atribut data-component-type
            var amount = option.getAttribute('data-amount'); // Mengambil nilai atribut data-current-amount;
            var component = {
                id_com: id_com,
                nama_component: nama_component,
                komponen: komponen,
                amount: amount
            };
            selectedComponent.push(component);
        });

        // Call the updateViewComponentCount and updateSelectedComponentCount functions
        updateViewComponentCount();
        updateSelectedComponentCount();
    }

    function storecomponent() {
        var selectElement = document.querySelector('select[name="selectedCom[]"]');
        var selectedOptions = Array.from(selectElement.selectedOptions);

        var selectedComponents = selectedOptions.map(option => {
            return {
                id_com: option.value
            };
        });

        fetch('{{ url('adjusment/store-component') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(selectedComponents)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $("#pagemanage").html("");
                    $("#exampleModal1").modal("hide");
                    $("#cover-spin").hide();
                    Swal.fire({
                        icon: "success",
                        title: data.message,
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        timer: 1500,
                    });
                    populateComponentData(data.employees);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message,
                        showConfirmButton: true,
                    });
                    console.error('Request failed. Server returned error.');
                }
            })

            .catch(error => {
                Swal.fire({
                    icon: "error",
                    title: "Network Error",
                    text: "Failed to send request to the server. Please try again.",
                    showConfirmButton: true,
                });
                console.error('Request failed. Network error.', error);
            });
    }
</script>

<!--SCRIPT SEARCHING-->
<script>
    // Fungsi untuk melakukan pencarian berdasarkan teks yang dimasukkan pengguna
    function searchComponent() {
        var input, filter, select, options, i, option, txtValue;
        input = document.getElementById('searchComponent'); // Gunakan ID yang sesuai untuk elemen pencarian karyawan
        filter = input.value.toUpperCase();
        select = document.getElementById('comitems');
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
    document.getElementById('searchComponent').addEventListener('input', searchComponent);

    // Fungsi untuk melakukan pencarian berdasarkan teks yang dimasukkan pengguna untuk karyawan yang telah dipilih
    function searchSelectedComponent() {
        var input, filter, select, options, i, option, txtValue;
        input = document.getElementById(
            'searchSelectedComponent'); // Gunakan ID yang sesuai untuk elemen pencarian karyawan yang telah dipilih
        filter = input.value.toUpperCase();
        select = document.getElementById('selectedcom');
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
    document.getElementById('searchSelectedComponent').addEventListener('input', searchSelectedComponent);
</script>

<!--COUNT INFORMATION-->
<script>
    let totalComponents = document.getElementById('comitems').getElementsByTagName('option').length;

    document.getElementById('view-component').innerText = "View 0 of " + totalComponents + " Component(s)";

    function updateViewComponentCount() {
        var select = document.getElementById('comitems');
        var options = select.getElementsByTagName('option');
        var visibleCount = options.length;

        document.getElementById('view-component').innerText = "View " + visibleCount + " of " + totalComponents +
            " Component(s)";
    }

    function updateSelectedComponentCount() {
        var select = document.getElementById('selectedcom');
        var options = select.getElementsByTagName('option');
        var count = options.length;

        document.getElementById('view-select-com').innerText = count + " Component(s) Selected";
    }

    updateViewComponentCount();
    updateSelectedComponentCount();
</script>

<!-----ENDSELECTCOMPONENT----->

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

    #filterFormCom {
        background-color: #f9f9f9;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
        height: 100%;
        width: 100%;
    }
</style>
