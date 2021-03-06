import './components/schedule.js';
import './components/list.js';

const Icon = wp.element.createElement('svg',
    {
        width: 20,
        height: 20
    },
    wp.element.createElement('path',
        {
            d: "M17.07,2.93c-3.9-3.9-10.25-3.9-14.15,0c-3.43,3.43-3.84,8.74-1.25,12.63l1.96-3.25c0.41,0.86,0.96,1.67,1.68,2.38 c0.71,0.71,1.52,1.27,2.38,1.68l-3.25,1.96c3.89,2.59,9.2,2.18,12.63-1.25C20.98,13.17,20.98,6.83,17.07,2.93z M14.82,11.39 c-0.04,0.03-0.08,0.06-0.12,0.09c-0.07,0.05-0.13,0.1-0.2,0.15c-0.05,0.04-0.11,0.07-0.16,0.1c-0.06,0.03-0.11,0.07-0.17,0.1 c-0.06,0.04-0.13,0.07-0.2,0.1c-0.04,0.02-0.08,0.04-0.12,0.06c-0.4,0.19-0.83,0.31-1.27,0.37l0,0c-1.32,0.17-2.69-0.24-3.7-1.25 s-1.42-2.39-1.25-3.7l0,0C7.69,6.98,7.81,6.55,8,6.15c0.02-0.04,0.04-0.08,0.06-0.12c0.03-0.07,0.07-0.13,0.1-0.2 c0.03-0.06,0.07-0.11,0.1-0.17c0.03-0.05,0.07-0.11,0.1-0.16c0.05-0.07,0.1-0.14,0.15-0.2c0.03-0.04,0.06-0.08,0.09-0.12 c0.09-0.11,0.18-0.21,0.28-0.31c1.72-1.72,4.52-1.72,6.25,0s1.72,4.52,0,6.25C15.03,11.21,14.93,11.31,14.82,11.39z M9.22,15.43 c-1.06-0.33-2.06-0.91-2.91-1.75s-1.42-1.84-1.75-2.91l1.18-1.96L5.76,8.8c0.17,1.34,0.76,2.63,1.79,3.65s2.32,1.62,3.65,1.79 l-0.02,0.01L9.22,15.43z"
        }
    )
);

export { Icon };

(function () {
    wp.blocks.updateCategory('sympose', { icon: Icon });
})();