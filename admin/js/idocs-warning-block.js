wp.blocks.registerBlockType('namespace/warning-block', {
    title: 'Warning Block',
    icon: 'warning',
    category: 'common',
    attributes: {
        message: {
            type: 'string',
            source: 'html',
            selector: 'p',
        },
    },
    edit: function(props) {
        return wp.element.createElement(
            'div',
            { className: 'warning-block' },
            wp.element.createElement(
                'div',
                { className: 'warning-icon' },
                '⚠️'
            ),
            wp.element.createElement(
                'p',
                { className: 'warning-message' },
                props.attributes.message || 'Your warning message here'
            )
        );
    },
    save: function(props) {
        return wp.element.createElement(
            'div',
            { className: 'warning-block' },
            wp.element.createElement(
                'div',
                { className: 'warning-icon' },
                '⚠️'
            ),
            wp.element.createElement(
                'p',
                { className: 'warning-message' },
                props.attributes.message || 'Your warning message here'
            )
        );
    },
});
