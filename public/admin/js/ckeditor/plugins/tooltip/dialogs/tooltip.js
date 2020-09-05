CKEDITOR.dialog.add('tooltipDialog', function(editor) {

  return {
    title: editor.lang.tooltip.title,
    minWidth: 600,
    minHeight: 150,

    contents: [
      {
        id: 'tab-basic',
        label: editor.lang.tooltip.tab,
        elements: [
          {
            type: 'text',
            id: 'title',
            label: editor.lang.tooltip.txtTitle,
            setup: function(element) {
              this.setValue(element.getText());
            },
            commit: function(element) {
              element.setText(this.getValue());
            }

          }, {
            type: 'textarea',
            id: 'tooltip',
            label: editor.lang.tooltip.txtArea,
            validate: function() {
              if (editor.config.tooltip_html === true)
                CKEDITOR.instances[this._.inputId].updateElement();
            },
            setup: function(element) {
              this.setValue(element.getAttribute("data-tooltip"));
            },
            commit: function(element) {
              if (editor.config.tooltip_html === true)
                CKEDITOR.instances[this._.inputId].updateElement();
              if (this.getValue()) {
                element.setAttribute("data-tooltip", this.getValue());
                element.setAttribute("data-cke-saved-data-tooltip", this.getValue());
              } else {
                if (element.hasAttribute("data-tooltip")) {
                  element.removeAttribute("data-tooltip");
                }
                if (element.hasAttribute("data-cke-saved-data-tooltip")) {
                  element.removeAttribute("data-cke-saved-data-tooltip");
                }
                if (element.hasClass("question-tooltip")) {
                  element.removeClass("question-tooltip");
                }
              }
            },
            onShow: function() {
              if (editor.config.tooltip_html === true)
                CKEDITOR.replace(this._.inputId, {toolbar: editor.config.tooltip_toolbar, stylesSet: editor.config.tooltip_styleSet, baseFloatZIndex: 10010});
              }
            ,
            onHide: function() {
              if (editor.config.tooltip_html === true)
                CKEDITOR.instances[this._.inputId].destroy();
              }

          }
        ]
      }
    ],

    onShow: function() {
      var selection = editor.getSelection();
      var element = selection.getStartElement();
      var textSelected = selection.getSelectedText();

      if (element)
        element = element.getAscendant(editor.config.tooltip_tag, true);

      if (!element || element.getName() != editor.config.tooltip_tag) {
        element = editor.document.createElement(editor.config.tooltip_tag);
        this.insertMode = true;
      } else
        this.insertMode = false;

      this.element = element;
      if (!this.insertMode)
        this.setupContent(this.element);

      if (this.insertMode)
        this.setValueOf('tab-basic', 'title', textSelected);

      }
    ,
    onOk: function() {
      var dialog = this;
      var tooltip = this.element;
      tooltip.setAttribute('class', editor.config.tooltip_class);

      this.commitContent(tooltip);

      if (this.insertMode)
        editor.insertElement(tooltip);
      }

  };
});
