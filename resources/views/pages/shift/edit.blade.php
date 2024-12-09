<form id="FormEditShift">
    @csrf
    <div class="mb-3">
        <label for="kode" class="form-label">Kode</label>
        <input type="text" class="form-control" id="kode" name="kode"
            value="{{ $shifts->kode }}"  readonly>
    </div>
    <div class="mb-3">
        <label for="jenis" class="form-label">Jenis</label>
        <input type="text" class="form-control" id="jenis" name="jenis"
            value="{{ $shifts->jenis }}"  readonly>
    </div>
    <div class="mb-3">
        <label for="jam_masuk" class="form-label">Jam Masuk </label>
        <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" 
            value="{{ $shifts->jam_masuk }}" >
    </div>
    <div class="mb-3">
        <label for="jam_pulang" class="form-label">Jam Pulang </label>
        <input type="text" class="form-control" id="jam_pulang" name="jam_pulang" 
            value="{{ $shifts->jam_pulang }}"  >
    </div>
    <div class="mb-3">
        <label for="awal_masuk" class="form-label">Awal Masuk </label>
        <input type="text" class="form-control" id="awal_masuk" name="awal_masuk" 
            value="{{ $shifts->awal_masuk }}" >
    </div>
    <div class="mb-3">
        <label for="maks_late" class="form-label">Max Late </label>
        <input type="text" class="form-control" id="maks_late" name="maks_late" 
            value="{{ $shifts->maks_late }}" >
    </div>
    <button type="button" onclick="StoreEditShift({{ $shifts->id }})" class="btn btn-primary"
        id="btn-submit">Simpan</button>
</form>