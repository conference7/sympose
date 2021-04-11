// src/index.js
import { registerBlockType } from '@wordpress/blocks';
import { SelectControl } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import { CheckboxControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { Icon } from './../index.js'

let eventList = [];
eventList.push({ label: __('Select an event'), value: 'all' });
apiFetch({ path: '/wp/v2/event?parent=0' }).then(data => {
    data.map((item) => {
        eventList.push({ label: item.name, value: item.slug });
        apiFetch({ path: '/wp/v2/event?parent=' + item.id }).then(children => {
            children.map((child) => {
                eventList.push({ label: '-- ' + child.name, value: child.slug });
            });
        })
    });
});

registerBlockType('sympose/schedule', {
    title: 'Sympose Schedule',
    icon: 'calendar-alt',
    category: 'sympose',
    attributes: {
        event: {
            type: 'string',
            default: 'all'
        },
        show_read_more: {
            type: 'boolean',
            default: true
        },
        show_people: {
            type: 'boolean',
            default: true
        },
        show_organisations: {
            type: 'boolean',
            default: true
        },
        hide_title: {
            type: 'boolean',
            default: false
        },
    },
    edit(props) {

        const Schedule = withState({})(({ size, setState }) => (

            <div className="sympose-block sympose-schedule-block">
                <div className="logo">{Icon}</div>
                <p className="title">Sympose Schedule</p>
                <SelectControl
                    label={__('Select an event')}
                    options={eventList}
                    value={props.attributes.event}
                    onChange={(value) => props.setAttributes({ event: value })}
                />

                <CheckboxControl
                    label="Show people"
                    checked={props.attributes.show_people}
                    onChange={(value) => props.setAttributes({ show_people: value })}
                />

                <CheckboxControl
                    label="Show organisations"
                    checked={props.attributes.show_organisations}
                    onChange={(value) => props.setAttributes({ show_organisations: value })}
                />

                <CheckboxControl
                    label="Hide schedule title"
                    checked={props.attributes.hide_title}
                    onChange={(value) => props.setAttributes({ hide_title: value })}
                />

                <CheckboxControl
                    label="Show read more"
                    checked={props.attributes.show_read_more}
                    onChange={(value) => props.setAttributes({ show_read_more: value })}
                />
            </div>
        ));

        return (
            <Schedule />
        );

    },
    save: () => {
        return null;
    },
});