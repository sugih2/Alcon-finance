<form id="FormEditPosition">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name Position</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="name"
            value="{{ $position->name }}" required>
    </div>
    <div class="mb-3">
        <label for="code" class="form-label">Code Position</label>
        <input type="number" class="form-control" id="code" name="code" value="{{ $position->code }}"
            placeholder="code" required>
    </div>
    <div class="mb-3">
        <label for="position" class="form-label">Bawahan</label>
        <select name="position" id="positionsedit" required>
            <option value="" disabled selected>Select Bawahan</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="param_position_id" class="form-label">Param Position</label>
        <select name="param_position_id" id="param_position_id" class="form-control" required>
            <option value="" disabled>Select Param Position</option>
            @foreach ($paramPositions as $item)
                <option value="{{ $item->id }}" {{ $item->id == $position->paramposition->id ? 'selected' : '' }}>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="button" onclick="StoreEditPosition({{ $position->id }})" class="btn btn-primary"
        id="btn-submit">Simpan</button>
</form>
