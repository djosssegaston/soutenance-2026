/**
 * Gestionnaire de graphiques pour ALOGOTO
 * Assure l'affichage correct de tous les graphiques avec données réelles
 */

class ChartManager {
    constructor() {
        this.charts = {};
        this.colors = {
            primary: '#00C486',
            secondary: '#0048DC',
            warning: '#FCA028',
            danger: '#F35120',
            gray: '#6A726F',
            borderColor: 'rgba(233,233,232,0.5)',
        };
    }

    /**
     * Initialise un graphique Chart.js
     * @param {string} canvasId - ID du canvas HTML
     * @param {object} config - Configuration Chart.js
     * @returns {Chart|null} - Instance Chart.js ou null
     */
    initChart(canvasId, config) {
        try {
            const canvas = document.getElementById(canvasId);
            
            if (!canvas) {
                console.warn(`[ChartManager] Canvas "${canvasId}" non trouvé`);
                return null;
            }

            // Détruire l'ancien graphique s'il existe
            if (this.charts[canvasId]) {
                this.charts[canvasId].destroy();
            }

            // Vérifier les données
            if (!config.data || !config.data.labels || config.data.labels.length === 0) {
                console.warn(`[ChartManager] Données vides pour "${canvasId}"`);
                this.showEmptyState(canvas, 'Aucune donnée disponible');
                return null;
            }

            // Configuration par défaut
            const defaultConfig = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            font: { size: 11, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#FFFFFF',
                        titleColor: '#07261E',
                        bodyColor: '#6A726F',
                        borderColor: '#E9E9E8',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#6A726F', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(233,233,232,0.5)', drawBorder: false },
                        ticks: { color: '#6A726F', font: { size: 11 } }
                    }
                }
            };

            // Merge des configurations
            const finalConfig = this.deepMerge(defaultConfig, config);
            
            // Créer le graphique
            this.charts[canvasId] = new Chart(canvas, finalConfig);
            
            console.log(`[ChartManager] Graphique "${canvasId}" initialisé avec succès`);
            return this.charts[canvasId];

        } catch (error) {
            console.error(`[ChartManager] Erreur initialisation "${canvasId}":`, error);
            const canvas = document.getElementById(canvasId);
            if (canvas) {
                this.showEmptyState(canvas, 'Erreur de chargement du graphique');
            }
            return null;
        }
    }

    /**
     * Affiche un message d'état vide
     */
    showEmptyState(canvas, message) {
        const parent = canvas.parentElement;
        if (parent) {
            parent.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6A726F; font-size: 14px; text-align: center; padding: 20px;">
                    <div>
                        <i class="bi bi-bar-chart-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2 mb-0">${message}</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Met à jour un graphique existant
     */
    updateChart(canvasId, newData) {
        if (!this.charts[canvasId]) {
            console.warn(`[ChartManager] Graphique "${canvasId}" non initialisé`);
            return;
        }

        try {
            const chart = this.charts[canvasId];
            
            if (newData.labels) chart.data.labels = newData.labels;
            if (newData.datasets) chart.data.datasets = newData.datasets;
            
            chart.update('active');
            console.log(`[ChartManager] Graphique "${canvasId}" mis à jour`);
        } catch (error) {
            console.error(`[ChartManager] Erreur mise à jour "${canvasId}":`, error);
        }
    }

    /**
     * Merge profond d'objets
     */
    deepMerge(target, source) {
        const output = Object.assign({}, target);
        
        Object.keys(source).forEach(key => {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                if (!output[key]) output[key] = {};
                output[key] = this.deepMerge(output[key], source[key]);
            } else {
                output[key] = source[key];
            }
        });
        
        return output;
    }

    /**
     * Détruit tous les graphiques
     */
    destroyAll() {
        Object.keys(this.charts).forEach(canvasId => {
            if (this.charts[canvasId]) {
                this.charts[canvasId].destroy();
            }
        });
        this.charts = {};
    }
}

// Instance globale
const chartManager = new ChartManager();
