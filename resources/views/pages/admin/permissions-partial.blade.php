<form id="permissionsForm">
    @csrf
    <input type="hidden" name="role_id" value="{{ $role->id }}">
    <table class="table">
        <thead>
            <tr>
                <th>Menu</th>
                <th>Create</th>
                <th>Read</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($menus as $menu)
            <tr>
                <td>
                    <input type="checkbox" name="menus[{{ $menu->id }}][enabled]" value="1"
                        {{ $menu->menuPermissions->isNotEmpty() ? 'checked' : '' }}>
                    {{ $menu->name }}
                </td>
                @foreach (['c', 'r', 'u', 'd'] as $perm)
                <td>
                    <input type="checkbox" name="menus[{{ $menu->id }}][{{ $perm }}]" value="1"
                        {{ $menu->menuPermissions->isNotEmpty() && $menu->menuPermissions->first()->$perm ? 'checked' : '' }}>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="savePermissions" data-role-id="{{ $role->id }}">Save Changes</button>
    </div>
</form>
<script>
    document.getElementById('savePermissions').addEventListener('click', function () {
            const roleId = this.getAttribute('data-role-id');
            savePermissions(roleId);
        });
    function savePermissions(roleId) {
        // Ambil data checkbox dari form
        const formData = $('#permissionsForm').serialize();

        $.ajax({
            url: `/user-management/roles/${roleId}/permissions/save`,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token
            },
            success: function (response) {
                alert(response.message);
            },
            error: function (xhr) {
                alert('Failed to save permissions.');
            }
        });
    }
</script>