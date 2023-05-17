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
        if (!this.el.getAttribute('wire:data')) {
            return;
        }

        this.data = this.data || window.Alpine.reactive({});

        const data = JSON.parse(this.el.getAttribute('wire:data'));

        Object.entries(data).forEach(([key, value]) => {
            this.data[key] = value;
        });

        this.el.removeAttribute('wire:data');

        this.logErrors();
    }

    logErrors () {
        this.data.serverMemo.errors.forEach(error => {
            console.error(error);
        });
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

    hasPropertyValue (prop) {
        return prop in this.data.serverMemo.data;
    }

    getPropertyValue (prop) {
        return this.data.serverMemo.data[prop];
    }

    call (method) {
        const $el = this.el;
        const $data = this.data;

        return function () {
            const payload = {
                fingerprint: $data.fingerprint,
                serverMemo: $data.serverMemo,
                call: {
                    method: method,
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

    get (prop) {
        // Check of public property exists
        // Example: x-text="$wire.count"
        if (this.hasPropertyValue(prop)) {
            return this.getPropertyValue(prop);
        }

        // Asume method call
        // Example: @click="$wire.increaseCounter()"
        return this.call(prop);
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
        this.forceDataDirectiveToBody();

        document.addEventListener('alpine:init', () => {
            this.registerWireDirective();
            this.registerWireMacicProperty();
            this.validate();
        });
    }

    validate () {
        setTimeout(() => {
            if (!window.Alpine.morph) {
                console.error('Plugin "morph" is not included. Find out more here: https://alpinejs.dev/plugins/morph');
            }
        }, 1);
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

    forceDataDirectiveToBody () {
        if (!document.body) {
            setTimeout(() => {
                this.forceDataDirectiveToBody();
            });

            return;
        }

        if (!document.body.hasAttribute('x-data')) {
            document.body.setAttribute('x-data', '');
        }
    }
}

new Livewire();
