<form id="editShiftForm">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="min_minutes" class="form-label">Min Minutes</label>
            <input type="number" class="form-control" id="min_minutes" name="min_minutes" required>
        </div>
        <div class="mb-3">
            <label for="max_minutes" class="form-label">Max Minutes</label>
            <input type="number" class="form-control" id="max_minutes" name="max_minutes" required>
        </div>
        <div class="mb-3">
            <label for="deduction_type" class="form-label">Deduction Type</label>
            <input type="text" class="form-control" id="deduction_type" name="deduction_type" required>
        </div>
        <div class="mb-3">
            <label for="deduction_value" class="form-label">Deduction Value</label>
            <input type="number" step="0.01" class="form-control" id="deduction_value" name="deduction_value"
                required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
