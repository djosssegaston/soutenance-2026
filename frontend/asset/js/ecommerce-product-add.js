"use strict";

document.addEventListener("DOMContentLoaded", function () {
    var e = document.querySelector("#dropzone-basic");

    if (e) {
        new Dropzone(e, {
            previewTemplate: `<div class="dz-preview dz-file-preview">
                <div class="dz-details">
                    <div class="dz-thumbnail">
                        <img data-dz-thumbnail>
                        <span class="dz-nopreview">No preview</span>
                        <div class="dz-success-mark"></div>
                        <div class="dz-error-mark"></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                        </div>
                    </div>
                    <div class="dz-filename" data-dz-name></div>
                    <div class="dz-size" data-dz-size></div>
                </div>
            </div>`,
            parallelUploads: 5,
            maxFilesize: 5,
            acceptedFiles: ".jpg,.jpeg,.png,.gif",
            addRemoveLinks: true,
            maxFiles: 5
        });
    }
    $('.fc-datepicker').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true
    });
});
