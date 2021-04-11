// src/index.js
const { registerBlockType } = wp.blocks;
import { SelectControl, CheckboxControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import { Icon } from './../index.js'

registerBlockType('sympose/list', {
    title: 'Sympose List',
    icon: 'list-view',
    category: 'sympose',
    attributes: {
        type: {
            type: 'string',
            default: 'all'
        },
        category: {
            type: 'string',
            default: 'all'
        },
        align: {
            type: 'string',
            default: 'left'
        },
        name: {
            type: 'boolean',
            default: true
        },
    },
    edit(props) {

        let categoryList = [];
        categoryList.push({ label: __('Select a category'), value: 'all' });
        wp.apiFetch({ path: '/wp/v2/' + props.attributes.type + '-category?parent=0' }).then(data => {
            data.map((item) => {
                categoryList.push({ label: item.name, value: item.slug });
                wp.apiFetch({ path: '/wp/v2/' + props.attributes.type + '-category?parent=' + item.id }).then(children => {
                    children.map((child) => {
                        categoryList.push({ label: '-- ' + child.name, value: child.slug });
                    });
                })
            });
        });

        const List = withState({
            size: '50%',
        })(({ size, setState }) => (

            <div className="sympose-block sympose-list">
                <div className="logo">{Icon}</div>
                <p className="title">Sympose List</p>
                <SelectControl
                    label={__('Post Type')}
                    options={[
                        {
                            'label': __('People'),
                            'value': 'person',
                        },
                        {
                            'label': __('Organisations'),
                            'value': 'organisation',
                        }
                    ]}
                    value={props.attributes.type}
                    onChange={(value) => props.setAttributes({ type: value })}
                />
                <SelectControl
                    label={__('Category')}
                    options={categoryList}
                    value={props.attributes.category}
                    onChange={(value) => props.setAttributes({ category: value })}
                />
                <SelectControl
                    label={__('Alignment')}
                    options={[
                        {
                            'label': __('Left'),
                            'value': 'left',
                        },
                        {
                            'label': __('Center'),
                            'value': 'center',
                        },
                        {
                            'label': __('Right'),
                            'value': 'right',
                        }
                    ]}
                    value={props.attributes.align}
                    onChange={(value) => props.setAttributes({ align: value })}
                />

                <CheckboxControl
                    label="Show name"
                    checked={props.attributes.name}
                    onChange={(value) => props.setAttributes({ name: value })}
                />

                {/* <CheckboxControl
                    label="Show organisations"
                    checked={props.attributes.show_organisations}
                    onChange={(value) => props.setAttributes({ show_organisations: value })}
                /> */}
            </div>
        ));

        return (
            <List />
        );

    },
    save: () => {
        return null;
    },
});