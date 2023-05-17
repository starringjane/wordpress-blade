const components = new Map();

class Component {
    constructor (el) {
        el.__livewire = this;
        this.el = el;

        this.updateId();
        this.updateData();
        this.observe();
    }

    observe () {
        this.observer = new MutationObserver((mutationList, observer) => {
            for (const mutation of mutationList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'wire:data') {
                    this.updateData();
                }
            }
        });

        this.observer.observe(this.el, { attributes: true, childList: false, subtree: false });
    }

    updateId () {
        this.id = this.el.getAttribute('wire:id');
    }

    updateData () {
        if (this.el.getAttribute('wire:data')) {
            this.data = JSON.parse(this.el.getAttribute('wire:data'));
            this.el.removeAttribute('wire:data');
        }
    }

    get $wire () {
        const $this = this;

        return new Proxy({}, {
            get (_, prop) {
                return $this.get(prop);
            },

            set (_, prop, value) {
                return $this.set(prop, value);
            },
        });
    }

    get (prop) {
        const $el = this.el;
        const $data = this.data;

        // Check of public property exists
        // Example: x-text="$wire.count"
        if (prop in $data.properties) {
            return $data.properties[prop];
        }

        // Asume method call
        // Example: @click="$wire.increaseCounter()"
        return function () {
            const data = {
                fingerprint: {
                    id: this.id,
                },
                call: {
                    method: prop,
                    arguments: [...arguments],
                },
                serverMemo: $data,
            };

            fetch('/wp-json/wire/v1/wire', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'json=' + encodeURI(JSON.stringify(data)),
            })
                .then((response) => response.json())
                .then(function (response) {
                    if (response && response.html) {
                        window.Alpine.morph($el, response.html);
                    }
                })
            ;
        };
    }

    set (prop, value) {
        if (prop in this.data.properties) {
            this.data.properties[prop] = value;
        }

        return true;
    }
};

function registerComponents () {
    document.querySelectorAll('[wire\\:id]').forEach(function (el) {
        const id = el.getAttribute('wire:id');
        components.set(id, new Component(el));
    });
}

function registerWire () {
    window.Alpine.magic('wire', function (el) {
        const wireEl = el.closest('[wire\\:id]');
        const id = wireEl.getAttribute('wire:id');

        if (!wireEl) {
            console.warn(
                'Alpine: Cannot reference "$wire" outside a Livewire component.'
            );
        };

        return components.get(id).$wire;
    });
}

document.addEventListener('alpine:init', () => {
    registerComponents();
    registerWire();
});
