(function () {
    ClassicEditor.create(document.querySelector('#classic-editor'))
        .catch(error => {
            console.error(error);
        });
})();