const components = new Map();

class Component {
    constructor (el) {
        this.el = el;

        this.updateId();
        this.updateData();
        this.observe();
    }

    /**
     * Updates the inital data when el changes
     */
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
        this.id = this.el.getAttribute('x-wire');
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
        if (prop in $data.serverMemo.data) {
            return $data.serverMemo.data[prop];
        }

        // Asume method call
        // Example: @click="$wire.increaseCounter()"
        return function () {
            const payload = {
                fingerprint: $data.fingerprint,
                serverMemo: $data.serverMemo,
                call: {
                    method: prop,
                    arguments: [...arguments],
                },
            };

            fetch('/wp-json/wire/v1/wire', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'json=' + encodeURI(JSON.stringify(payload)),
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
        if (prop in this.data.serverMemo.data) {
            this.data.serverMemo.data[prop] = value;
        }

        return true;
    }
};

class Livewire {
    constructor () {
        this.registerWireDirective();
        this.registerWireMacicProperty();
    }

    registerWireDirective () {
        window.Alpine.directive('wire', (el, { expression }) => {
            const id = expression;

            if (!components.has(id)) {
                components.set(id, new Component(el));
            }
        })
    }

    registerWireMacicProperty () {
        window.Alpine.magic('wire', function (el) {
            const wireEl = el.closest('[x-wire]');
            const id = wireEl.getAttribute('x-wire');
    
            if (!wireEl) {
                console.error('Alpine: Cannot reference "$wire" outside a Livewire component.');
                return null;
            };
    
            if (!components.has(id)) {
                console.error(`Alpine: Cannot reference "$wire" for Livewire component with id ${id}.`);
                return null;
            };
    
            return components.get(id).$wire;
        });
    }
}

document.addEventListener('alpine:init', () => {
    window.__livewire = new Livewire();
});
