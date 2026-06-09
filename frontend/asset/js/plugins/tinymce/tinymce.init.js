tinymce.init({
    selector: '#pc-tinymce-1',
    toolbar: false,
    height: "400",
    content_style: 'body { font-family: "Inter", sans-serif; }',
    statusbar: false
  });

  tinymce.init({
    selector: '#pc-tinymce-2',
    height: "400",
    content_style: 'body { font-family: "Inter", sans-serif; }'
  });

  tinymce.init({
    height: "400",
    selector: '#pc-tinymce-3',
    content_style: 'body { font-family: "Inter", sans-serif; }',
    toolbar: 'advlist | autolink | link image | lists charmap | print preview',
    plugins: 'advlist autolink link image lists charmap print preview'
  });

  tinymce.init({
    height: "400",
    selector: '#pc-tinymce-4',
    content_style: 'body { font-family: "Inter", sans-serif; }',
    menubar: false,
    toolbar: ['styleselect fontselect fontsizeselect',
      'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
      'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code'
    ],
    plugins: 'advlist autolink link image lists charmap print preview code'
  });