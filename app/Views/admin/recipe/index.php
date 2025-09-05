<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Liste des recettes</h3>
                <a href="<?= base_url("admin/recipe/new") ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Recette
                </a>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered table-striped" id="tableRecipe">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Créateur</th>
                        <th>Date modif.</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var baseUrl = "<?= base_url(); ?>";
        var table = $('#tableRecipe').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('datatable/searchdatatable') ?>',
                type: 'POST',
                data: {
                    model: 'RecipeModel'
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'id_user' },
                { data: 'updated_at' },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        if (data === 'active' || row.deleted_at === null) {
                            return '<span class="badge text-bg-success">Actif</span>';
                        } else {
                            return '<span class="badge text-bg-danger">Inactif</span>';
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const isActive = (row.status === 'active' || row.deleted_at === null);
                        const toggleButton = isActive
                            ? `<button class="btn btn-sm btn-danger" onclick="toggleRecipeStatus(${row.id}, 'deactivate')" title="Désactiver">
                                 <i class="fas fa-user-times"></i>
                               </button>`
                            : `<button class="btn btn-sm btn-success" onclick="toggleRecipeStatus(${row.id}, 'activate')" title="Activer">
                                 <i class="fas fa-user-check"></i>
                               </button>`;

                        return `
                            <div class="btn-group" role="group">
                                <a href="<?= base_url('/admin/recipe/') ?>${row.id}" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                ${toggleButton}
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                url: baseUrl + 'js/datatable/datatable-2.1.4-fr-FR.json',
            }
        });

        // Fonction pour actualiser la table
        window.refreshTable = function() {
            table.ajax.reload(null, false); // false pour garder la pagination
        };
    });

    function toggleRecipeStatus(id, action) {
        const actionText = action === 'activate' ? 'activer' : 'désactiver';
        const actionColor = action === 'activate' ? '#28a745' : '#dc3545';

        Swal.fire({
            title: `Êtes-vous sûr ?`,
            text: `Voulez-vous vraiment ${actionText} cette recette ?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: actionColor,
            cancelButtonColor: "#6c757d",
            confirmButtonText: `Oui, ${actionText} !`,
            cancelButtonText: "Annuler",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('/admin/recipe/switch-active'); ?>",
                    type: "POST",
                    data: {
                        'id': id,
                    },
                    success: function (response) {
                        console.log(response);

                        if (response.success) {
                            Swal.fire({
                                title: 'Succès !',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Actualiser la table
                            refreshTable();
                        } else {
                            Swal.fire({
                                title: 'Erreur !',
                                text: response.message || 'Une erreur est survenue',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', error);
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Erreur de communication avec le serveur',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    }
</script>