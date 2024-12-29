<form id="FormPayment">
    @csrf
    <div class="mb-3">
        <label for="payment-date" class="form-label">Payment Date</label>
        <input type="date" class="form-control" id="payment-date" name="payment_date" placeholder="Payment Date" required>
    </div>
    <div class="mb-3">
        <label for="payment-amount" class="form-label">Payment Amount</label>
        <input type="text" class="form-control" id="payment-amount" name="payment_amount"
            placeholder="Payment Amount" required>
    </div>
    <div class="mb-3">
        <label for="payment-method" class="form-label">Payment Method</label>
        <div>
            <input type="radio" id="transfer" name="payment_method" value="transfer" required>
            <label for="transfer">Transfer</label>
        </div>
        <div>
            <input type="radio" id="cash" name="payment_method" value="cash" required>
            <label for="cash">Cash</label>
        </div>
    </div>
    <button type="button" id="btn-submit" onclick="PaymentProcess({{ $payrollHistory->id }})"
        class="btn btn-primary">Process</button>
</form>
