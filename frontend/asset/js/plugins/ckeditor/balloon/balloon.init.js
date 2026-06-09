(function () {
    BalloonEditor.create(document.querySelector('#cke5-balloon-block-demo'))
        .catch(error => {
            console.error(error);
        });
})();