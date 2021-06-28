CKEDITOR.editorConfig = function( config ) {
	// The toolbar groups arrangement, optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'forms' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others' }
	];

	// The default plugins included in the basic setup define some buttons that
	// are not needed in a basic editor. They are removed here.
	config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Strike,Subscript,Superscript';

	config.mathJaxLib = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML';

    config.extraPlugins = 'filebrowser,image,sourcedialog,dialog,dialogui,font,format,youtube';

	config.youtube_related = false;

    config.filebrowserUploadUrl = '/admin/questions/uploadImage';
    config.filebrowserBrowseUrl = '/admin/js/ckeditor/plugins/imagebrowser/imagebrowser.html?imgroot=/admin/uploads';

    // Enable all default text formats
    config.format_tags = 'p;h1;h2;h3;h4;h5;h6;pre;address;div';

	config.allowedContent = true;

	config.height = 500;

};
