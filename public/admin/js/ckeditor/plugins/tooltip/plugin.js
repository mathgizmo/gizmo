CKEDITOR.plugins.add('tooltip', {
  lang: ['en'],
  afterInit: function(editor) {
    CKEDITOR.addCss('.cke_editable .' + editor.config.tooltip_class + '{' + 'cursor:pointer' + '}');
  },
  init: function(editor) {

    editor.ui.addButton('Tooltip', {
      label: editor.lang.tooltip.button,
      command: 'tooltip',
      toolbar: 'insert',
      icon: this.path + 'icons/tooltip.png'
    });

    editor.addCommand('tooltip', new CKEDITOR.dialogCommand('tooltipDialog'));
    CKEDITOR.dialog.add('tooltipDialog', this.path + 'dialogs/tooltip.js');

    if (editor.contextMenu) {
      editor.addMenuGroup('tooltipGroup');
      if (editor.addMenuItems) {
        editor.addMenuItems({
          tooltipItem: {
            label: 'Insert a Tooltip',
            command: 'tooltip',
            group: 'tooltipGroup',
            icon: this.path + 'icons/tooltip.png',
            order: 1
          }
        });
      }
      editor.contextMenu.addListener(function(element) {
        if (element.getAscendant(editor.config.tooltip_tag, true)) {
          var menu = {};
          if (element.hasClass(editor.config.tooltip_class)) {
            menu = {
              'tooltipItem': CKEDITOR.TRISTATE_OFF
            };
            return menu;
          }
        }
      });
    }

    editor.on('doubleclick', function(evt) {
      var element = evt.data.element;
      if (element.is(editor.config.tooltip_tag) && element.hasClass(editor.config.tooltip_class))
        evt.data.dialog = 'tooltipDialog';
      }
    );

  }
});

CKEDITOR.config.tooltip_tag = 'span';
CKEDITOR.config.tooltip_class = 'question-tooltip';
CKEDITOR.config.tooltip_html = false;
CKEDITOR.tooltip_toolbar = [
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
	{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
	{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
	{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
	'/',
	{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
	{ name: 'others', items: [ '-' ] },
	{ name: 'about', items: [ 'About' ] }
];
CKEDITOR.config.tooltip_styleSet = 'default';
