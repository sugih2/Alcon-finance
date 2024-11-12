<form id="FormEditParamPosition">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name Parameter Position</label>
        <input type="text" value="{{ $paramPosition->name }}" class="form-control" id="name" name="name"
            placeholder="name" required>
    </div>
    <button type="button" id="btn-submit" onclick="StoreEditParamPosition({{ $paramPosition->id }})"
        class="btn btn-primary">Update</button>
</form>
