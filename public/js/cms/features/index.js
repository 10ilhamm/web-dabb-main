function featureManager() {
    return {
        editModal: { open: false, id: null, name: '', type: 'link', path: '', order: 0 },
        addModal: { open: false, type: 'link' },
        deleteModal: { open: false, id: null, name: '' },

        openEditModal(id, name, type, path, order) {
            this.editModal = { open: true, id, name, type, path, order };
        },
        openAddModal() {
            this.addModal = { open: true, type: 'link' };
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        }
    }
}
