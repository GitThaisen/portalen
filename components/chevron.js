wp.richText.registerFormatType('nrk/chevron', {
  title: 'Pil',
  tagName: 'pil',
  className: null,
  edit: function (props) {
    return wp.element.createElement(wp.editor.RichTextToolbarButton, {
      icon: 'arrow-right-alt2',
      title: 'Pil',
      isActive: props.isActive,
      onClick: function() {
        props.onChange(wp.richText.insert(props.value, '›'));
       }
    });
  }
});