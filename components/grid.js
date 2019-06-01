wp.blocks.registerBlockType('nrk/grid', {
  title: 'Grid',
  icon: 'grid-view',
  category: 'layout',
  attributes: {
    backgroundColor: { type: 'string' },
    parentId: { type: 'string' },
    view: { type: 'string', default: 'icons' },
  },
  supports: { customClassName: false },
  edit: wp.data.withSelect(function (select) {
    var postId = select('core/editor').getCurrentPostId()
    var query = { per_page: -1, status: 'any', orderby: 'menu_order', order: 'asc' }
    var pages = select('core').getEntityRecords('postType', 'page', query) || []

    return {
      postId: postId,
      pages: pages
    }
  })(wp.editor.withColors({
    backgroundColor: 'background-color'
  })(function(props) {
    var el = wp.element.createElement
    var parentId = props.attributes.parentId
    var selectedId = typeof parentId === 'undefined' ? props.postId : (parentId || 0)
    var byParent = props.pages.reduce(function (acc, page) {
      (acc[page.parent] = acc[page.parent] || []).push(page)
      return acc
    }, {})

    var withChildren = function (pages) {
      pages.forEach(function (page) {
        page.name = page.title.raw || page.id
        page.children = withChildren(byParent[page.id] || [])
      })
      return pages
    }

    return el(wp.element.Fragment, null,
      el(wp.editor.BlockControls, null, el(wp.components.Toolbar, {
        controls: [{
          icon: 'translation',
          title: 'Ikoner',
          isActive: props.attributes.view === 'icons',
          onClick: function () { props.setAttributes({ view: 'icons' }) }
        }, {
          icon: 'format-gallery',
          title: 'Bilder',
          isActive: props.attributes.view === 'images',
          onClick: function () { props.setAttributes({ view: 'images' }) },
        }, {
          icon: 'list-view',
          title: 'Liste',
          isActive: props.attributes.view === 'text',
          onClick: function () { props.setAttributes({ view: 'list' }) },
        }]
      })),
      el(wp.components.ServerSideRender, {
        block: 'nrk/grid',
        attributes: props.attributes,
        urlQueryArgs: { post_id: props.postId } // Needed to set context on renderer
      }),
      el(wp.editor.InspectorControls, null,
        el(wp.editor.PanelColorSettings, {
          initialOpen: true,
          title: 'Bakgrunn',
          colorSettings: [{
            label: 'Farge',
            value: props.backgroundColor.color,
            onChange: props.setBackgroundColor
          }]
        }),
        el(props.pages.length ? wp.components.TreeSelect : wp.components.Spinner, {
          label: 'Forelder',
          noOptionLabel: '(Ingen forelder)',
          selectedId: String(selectedId),
          onChange: function (id) { props.setAttributes({ parentId: id }) },
          tree: withChildren(byParent[0] || [])
        })
      )
    );
  })),
  save: function () {
    return null // Rendered in PHP
  }
})
