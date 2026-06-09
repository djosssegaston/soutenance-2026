/**
 * Gestionnaire de profil pour ALOGOTO
 * Permet de modifier et sauvegarder le profil utilisateur
 */

class ProfileManager {
    constructor() {
        this.isEditing = false;
        this.originalData = {};
        
        this.init();
    }

    async init() {
        // Charger les données du profil
        await this.loadProfile();
        
        // Configurer les boutons
        this.setupEditButton();
        this.setupSaveButton();
        this.setupCancelButton();
        this.setupPasswordForm();
        this.setupAvatarUpload();
        
        console.log('[ProfileManager] Initialisé');
    }

    /**
     * Charge les données du profil depuis l'API
     */
    async loadProfile() {
        try {
            const profile = await apiFetch('profile');
            
            // Remplir les champs
            const fields = ['name', 'email', 'phone', 'bio', 'location', 'organisation'];
            fields.forEach(field => {
                const input = document.getElementById(`profile-${field}`);
                if (input && profile[field] !== undefined) {
                    input.value = profile[field] || '';
                }
            });

            // Mettre à jour l'avatar
            if (profile.avatar_url) {
                const avatarImg = document.getElementById('profile-avatar-img');
                if (avatarImg) {
                    avatarImg.src = profile.avatar_url;
                    avatarImg.style.display = 'block';
                }
            }

            // Mettre à jour les stats si disponibles
            if (profile.stats) {
                this.updateStats(profile.stats);
            }

            // Sauvegarder les données originales
            this.originalData = { ...profile };
            
        } catch (error) {
            console.error('[ProfileManager] Erreur chargement profil:', error);
            showApiError('Impossible de charger le profil');
        }
    }

    /**
     * Configure le bouton modifier
     */
    setupEditButton() {
        const editBtn = document.getElementById('btn-edit-profile');
        if (editBtn) {
            editBtn.addEventListener('click', () => this.enableEdit());
        }
    }

    /**
     * Active le mode édition
     */
    enableEdit() {
        this.isEditing = true;
        
        // Rendre les champs éditables
        const editableFields = ['name', 'phone', 'bio', 'location', 'organisation'];
        editableFields.forEach(field => {
            const input = document.getElementById(`profile-${field}`);
            if (input) {
                input.removeAttribute('readonly');
                input.classList.add('is-editable');
            }
        });

        // Afficher les boutons save/cancel
        const saveBtn = document.getElementById('btn-save-profile');
        const cancelBtn = document.getElementById('btn-cancel-profile');
        const editBtn = document.getElementById('btn-edit-profile');
        
        if (saveBtn) saveBtn.style.display = 'inline-flex';
        if (cancelBtn) cancelBtn.style.display = 'inline-flex';
        if (editBtn) editBtn.style.display = 'none';

        // Focus sur le premier champ
        const firstInput = document.getElementById('profile-name');
        if (firstInput) firstInput.focus();
    }

    /**
     * Configure le bouton sauvegarder
     */
    setupSaveButton() {
        const saveBtn = document.getElementById('btn-save-profile');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveProfile());
            saveBtn.style.display = 'none'; // Caché par défaut
        }
    }

    /**
     * Sauvegarde le profil
     */
    async saveProfile() {
        try {
            // Récupérer les valeurs
            const data = {
                name: document.getElementById('profile-name')?.value,
                phone: document.getElementById('profile-phone')?.value,
                bio: document.getElementById('profile-bio')?.value,
                location: document.getElementById('profile-location')?.value,
                organisation: document.getElementById('profile-organisation')?.value,
            };

            // Envoyer à l'API
            const response = await apiFetch('profile/update', {
                method: 'PUT',
                body: JSON.stringify(data)
            });

            showApiSuccess(response.message || 'Profil mis à jour avec succès');
            
            // Désactiver le mode édition
            this.disableEdit();
            
            // Recharger les données
            await this.loadProfile();
            
        } catch (error) {
            console.error('[ProfileManager] Erreur sauvegarde:', error);
            showApiError('Erreur lors de la sauvegarde du profil');
        }
    }

    /**
     * Configure le bouton annuler
     */
    setupCancelButton() {
        const cancelBtn = document.getElementById('btn-cancel-profile');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelEdit());
            cancelBtn.style.display = 'none'; // Caché par défaut
        }
    }

    /**
     * Annule les modifications
     */
    cancelEdit() {
        this.isEditing = false;
        
        // Restaurer les valeurs originales
        const fields = ['name', 'phone', 'bio', 'location', 'organisation'];
        fields.forEach(field => {
            const input = document.getElementById(`profile-${field}`);
            if (input && this.originalData[field] !== undefined) {
                input.value = this.originalData[field] || '';
            }
        });

        // Désactiver le mode édition
        this.disableEdit();
    }

    /**
     * Désactive le mode édition
     */
    disableEdit() {
        this.isEditing = false;
        
        // Rendre les champs readonly
        const editableFields = ['name', 'phone', 'bio', 'location', 'organisation'];
        editableFields.forEach(field => {
            const input = document.getElementById(`profile-${field}`);
            if (input) {
                input.setAttribute('readonly', 'readonly');
                input.classList.remove('is-editable');
            }
        });

        // Masquer les boutons save/cancel, afficher edit
        const saveBtn = document.getElementById('btn-save-profile');
        const cancelBtn = document.getElementById('btn-cancel-profile');
        const editBtn = document.getElementById('btn-edit-profile');
        
        if (saveBtn) saveBtn.style.display = 'none';
        if (cancelBtn) cancelBtn.style.display = 'none';
        if (editBtn) editBtn.style.display = 'inline-flex';
    }

    /**
     * Configure le formulaire de mot de passe
     */
    setupPasswordForm() {
        const passwordForm = document.getElementById('password-form');
        if (passwordForm) {
            passwordForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.updatePassword();
            });
        }
    }

    /**
     * Met à jour le mot de passe
     */
    async updatePassword() {
        try {
            const currentPassword = document.getElementById('current-password')?.value;
            const newPassword = document.getElementById('new-password')?.value;
            const confirmPassword = document.getElementById('confirm-password')?.value;

            if (newPassword !== confirmPassword) {
                showApiError('Les mots de passe ne correspondent pas');
                return;
            }

            const response = await apiFetch('profile/password', {
                method: 'PUT',
                body: JSON.stringify({
                    current_password: currentPassword,
                    password: newPassword,
                    password_confirmation: confirmPassword,
                })
            });

            showApiSuccess(response.message || 'Mot de passe modifié avec succès');
            
            // Reset du formulaire
            document.getElementById('password-form')?.reset();
            
        } catch (error) {
            console.error('[ProfileManager] Erreur mot de passe:', error);
            showApiError('Erreur lors de la modification du mot de passe');
        }
    }

    /**
     * Configure l'upload d'avatar
     */
    setupAvatarUpload() {
        const avatarInput = document.getElementById('avatar-upload');
        if (avatarInput) {
            avatarInput.addEventListener('change', (e) => this.uploadAvatar(e));
        }
    }

    /**
     * Upload l'avatar
     */
    async uploadAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Vérifier le type et la taille
        if (!file.type.startsWith('image/')) {
            showApiError('Veuillez sélectionner une image');
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            showApiError('L\'image ne doit pas dépasser 2MB');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('avatar', file);

            const response = await apiFetch('profile/avatar', {
                method: 'POST',
                body: formData,
                headers: {} // Laisser fetch définir Content-Type pour FormData
            });

            showApiSuccess(response.message || 'Avatar téléchargé avec succès');
            
            // Mettre à jour l'affichage
            if (response.avatar_url) {
                const avatarImg = document.getElementById('profile-avatar-img');
                if (avatarImg) {
                    avatarImg.src = response.avatar_url;
                }
            }
            
        } catch (error) {
            console.error('[ProfileManager] Erreur upload avatar:', error);
            showApiError('Erreur lors du téléchargement de l\'avatar');
        }
    }

    /**
     * Met à jour les statistiques
     */
    updateStats(stats) {
        if (stats.projects_count) {
            const el = document.getElementById('stat-projects');
            if (el) el.textContent = stats.projects_count;
        }
        
        if (stats.total_funding) {
            const el = document.getElementById('stat-funding');
            if (el) el.textContent = formatMoney(stats.total_funding);
        }
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.profileManager = new ProfileManager();
});
