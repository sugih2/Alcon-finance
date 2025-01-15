<form id="deductionGroupForm">
    @csrf
    <input type="hidden" name="payroll_id" value="{{ $payrollHistoryId }}">
    <input type="hidden" name="group_id" value="{{ $groupId }}">
    <div class="form-group">
        <label for="amount">Amount</label>
        <input type="text" class="form-control" id="amount" name="amount" required>
    </div>
    <button type="button" onclick="storeDeductionGroup()" id="btn-deduction-group"
        class="btn btn-primary">Submit</button>
</form>

<script>
    $('#amount').on('input', function() {
        var value = $(this).val();

        value = value.replace(/[^\d]/g, '');

        var rupiah = '';
        var valueLength = value.length;

        for (var i = valueLength; i > 0; i--) {
            if ((valueLength - i) % 3 === 0 && i !== valueLength) {
                rupiah = '.' + rupiah;
            }
            rupiah = value[i - 1] + rupiah;
        }

        $(this).val(rupiah);
        $(this).data('numericValue', value);
    });
</script>
