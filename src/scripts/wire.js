const components = new Map();

class Component {
    constructor (el) {
        this.el = el;
        this.updateId();
        this.updateData();
    }

    updateId () {
        this.id = this.el.getAttribute('x-wire');
    }

    updateData () {
        if (!this.el.getAttribute('x-wire-data')) {
            return;
        }

        this.data = this.data || window.Alpine.reactive({});

        const data = JSON.parse(this.el.getAttribute('x-wire-data'));

        Object.entries(data).forEach(([key, value]) => {
            this.data[key] = value;
        });

        this.el.removeAttribute('x-wire-data');

        this.onDataUpdated();
    }

    onDataUpdated () {
        // this.updatePath();
        this.logErrors();
    }

    updatePath () {
        updatePathFromUrl(this.data.serverMemo.path);
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
                path: window.location.href,
                call: {
                    method: method,
                    arguments: [...arguments],
                },
            };

            fetch('/wp-json/wire/v1/wire', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            })
                .then((response) => response.json())
                .then(function (response) {
                    if (response && response.path) {
                        updatePathFromUrl(response.path);
                    }

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
        this.updatePath();

        document.addEventListener('alpine:init', () => {
            this.registerWireDirective();
            this.registerWireMacicProperty();
            this.validate();
        });
    }

    updatePath () {
        if (window.LIVEWIRE_PATH) {
            updatePathFromUrl(window.LIVEWIRE_PATH);
        }
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

        window.Alpine.directive('wire-data', (el, { expression }) => {
            const id = el.getAttribute('x-wire');

            if (components.has(id)) {
                components.get(id).updateData();
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

function getPathFromUrl(url) {
    const urlObject = new URL(url);
    return urlObject.pathname + urlObject.search;
}

function updatePathFromUrl(url) {
    const currentPath = getPathFromUrl(window.location.href);
    const newPath = getPathFromUrl(url);

    if (currentPath !== newPath) {
        window.history.replaceState({}, '', newPath);
    }
}
