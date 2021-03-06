// src/index.js
import { registerBlockType } from '@wordpress/blocks';
import { SelectControl, CheckboxControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
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
        categories: {
            type: 'object'
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
        if (!props.attributes.categories) {
            let categoryList = [];
            categoryList.push({ label: __('Select a category'), value: 'all' });
            apiFetch({ path: '/wp/v2/' + props.attributes.type + '-category?parent=0' }).then(data => {
                data.map((item) => {
                    categoryList.push({ label: item.name, value: item.slug });
                    apiFetch({ path: '/wp/v2/' + props.attributes.type + '-category?parent=' + item.id }).then(children => {
                        children.map((child) => {
                            categoryList.push({ label: '-- ' + child.name, value: child.slug });
                        });
                    })
                });
                props.setAttributes(
                    {
                        categories: categoryList
                    }
                )
            });
        }

        const List = withState({})(({ size, setState }) => (


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
                    options={props.attributes.categories}
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