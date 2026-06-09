(function () {
    new SimpleMDE({ element: document.querySelector("#pc_demo1") });
})();
(function () {
    new SimpleMDE({
        element: document.getElementById("pc_demo2"),
        autosave: {
            enabled: true,
            unique_id: "demo2",
        },
    });
})();
(function () {
    new SimpleMDE({
        element: document.getElementById("pc_demo3"),
        status: false,
        toolbar: false,
    });
})();