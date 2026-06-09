/**
 * Theme Colors — Alogoto
 * Sélecteur de couleur primaire avec sauvegarde localStorage.
 * Ne produit aucune erreur si les éléments DOM sont absents.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Sélecteurs des boutons de couleur
    var primaryDefaultColor1Btn = document.querySelector('#switcher-primary1');
    var primaryDefaultColor2Btn = document.querySelector('#switcher-primary2');
    var primaryDefaultColor3Btn = document.querySelector('#switcher-primary3');
    var primaryDefaultColor4Btn = document.querySelector('#switcher-primary4');
    var primaryDefaultColor5Btn = document.querySelector('#switcher-primary5');

    // Définition des couleurs
    var colors = {
        color1: '82, 72, 223',
        color2: '0, 116, 186',
        color3: '10, 126, 164',
        color4: '250, 137, 107',
        color5: '1, 192, 200'
    };

    // Appliquer une couleur
    function updateColor(color, buttonId) {
        localStorage.setItem('vistaprimaryColor', color);
        localStorage.setItem('selectedButton', buttonId);
        document.documentElement.style.setProperty('--primary-rgb', color);
        document.body.style.setProperty('--primary-rgb', color);
    }

    // Attacher les événements (uniquement si l'élément existe)
    if (primaryDefaultColor1Btn) {
        primaryDefaultColor1Btn.addEventListener('click', function () { updateColor(colors.color1, 'switcher-primary1'); });
    }
    if (primaryDefaultColor2Btn) {
        primaryDefaultColor2Btn.addEventListener('click', function () { updateColor(colors.color2, 'switcher-primary2'); });
    }
    if (primaryDefaultColor3Btn) {
        primaryDefaultColor3Btn.addEventListener('click', function () { updateColor(colors.color3, 'switcher-primary3'); });
    }
    if (primaryDefaultColor4Btn) {
        primaryDefaultColor4Btn.addEventListener('click', function () { updateColor(colors.color4, 'switcher-primary4'); });
    }
    if (primaryDefaultColor5Btn) {
        primaryDefaultColor5Btn.addEventListener('click', function () { updateColor(colors.color5, 'switcher-primary5'); });
    }

    // Restaurer la couleur sauvegardée
    var storedColor = localStorage.getItem('vistaprimaryColor');
    var selectedButton = localStorage.getItem('selectedButton');

    if (storedColor) {
        document.documentElement.style.setProperty('--primary-rgb', storedColor);
        document.body.style.setProperty('--primary-rgb', storedColor);

        if (selectedButton) {
            var btn = document.querySelector('#' + selectedButton);
            if (btn) {
                btn.checked = true;
            }
        }
    }
});
