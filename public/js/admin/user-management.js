function loadUsers() {
    $.ajax({
        url: '/api/admin/users',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            console.log('Response from server:', response);
            
            let usersHtml = '';
            const users = Array.isArray(response) ? response : 
                         (response.data ? response.data : []);
            
            if (users.length > 0) {
                users.forEach((user, index) => {
                    const roleText = user.role_id === 1 ? 'Admin' : 'Customer';
                    const isAdmin = user.role_id === 1;
                    const isSuperAdmin = user.id === 1;
                    
                    usersHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${roleText}</td>
                            <td>
                                ${!isSuperAdmin ? `
                                    <button class="btn btn-sm btn-warning change-role" 
                                            data-id="${user.id}" 
                                            data-current-role="${user.role_id}">
                                        Ubah ke ${user.role_id === 1 ? 'Customer' : 'Admin'}
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-user" 
                                            data-id="${user.id}">
                                        Hapus
                                    </button>
                                ` : '<span class="badge badge-primary">Super Admin</span>'}
                            </td>
                        </tr>`;
                });
            } else {
                usersHtml = `
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada pengguna</td>
                    </tr>`;
            }
            
            $('#userList').html(usersHtml);
        },
        error: function(xhr) {
            console.error('Error response:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal memuat daftar pengguna'
            });
        }
    });
}

// Event handler untuk ubah role
$(document).on('click', '.change-role', function() {
    const userId = $(this).data('id');
    const currentRole = $(this).data('current-role');
    const newRoleText = currentRole === 1 ? 'Customer' : 'Admin';
    
    Swal.fire({
        title: 'Konfirmasi Perubahan Role',
        text: `Apakah Anda yakin ingin mengubah role pengguna ini menjadi ${newRoleText}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/api/admin/users/${userId}/change-role`,
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            }).catch(error => {
                Swal.showValidationMessage(
                    `Request failed: ${error.responseJSON?.message || 'Gagal mengubah role'}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Role pengguna berhasil diubah'
            }).then(() => {
                loadUsers(); // Reload daftar pengguna
            });
        }
    });
});

// Event handler untuk hapus pengguna
$(document).on('click', '.delete-user', function() {
    const userId = $(this).data('id');
    
    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menghapus pengguna ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/api/admin/users/${userId}`,
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengguna berhasil dihapus'
                    });
                    loadUsers();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menghapus pengguna'
                    });
                }
            });
        }
    });
});