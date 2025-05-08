class Masonry {
    constructor (model) {
        this.model = model;

        this.sizeSetAt = null;

        this.container = model.refs.container;

        this.wrapperContainerEl = this.container.parentNode;

        this.itemEls = this.container.querySelectorAll('[ref="masonry-item"]');

        this.placeItems();

        window.addEventListener('resize', () => {
            this.placeItems();
        });
    }

    placeItems () {
        let columnCount = 1;

        if (window.innerWidth > 899) {
            columnCount = 2;
        }

        if (window.innerWidth > 1699) {
            columnCount = 3;
        }

        if (window.innerWidth > 2499) {
            columnCount = 4;
        }

        if (columnCount === this.sizeSetAt) {
            return;
        }

        this.itemEls.forEach((item) => {
            item.remove();
        });

        this.container.classList.remove(
            'columns-1',
            'columns-2',
            'columns-3',
            'columns-4',
        );

        this.container.classList.add(
            'masonry-layout',
            `columns-${columnCount}`,
        );

        this.container.querySelectorAll('[ref="masonry-column"]')
            .forEach((col) => {
                col.remove();
            });

        for (let i = 1; i <= columnCount; i += 1) {
            const column = document.createElement('div');

            column.classList.add(`masonry-column-${i}`);

            column.setAttribute('ref', 'masonry-column');

            this.container.appendChild(column);
        }

        let countColumn = 1;

        this.itemEls.forEach((item) => {
            const col = this.container.querySelector(
                `.masonry-column-${countColumn}`,
            );

            col.appendChild(item);

            countColumn = countColumn < columnCount ? countColumn + 1 : 1;
        });

        this.sizeSetAt = columnCount;

        this.model.data.isReady = true;
    }
}

export default (model) => {
    // eslint-disable-next-line no-new
    new Masonry(model);
};
