import { updatePathFromUrl } from "./utils.js";

export class Component {
    constructor (el) {
        this.el = el;
        this.updateId();
    }

    getEl() {
        return document.querySelector(`[x-wire="${this.id}"]`) || this.el;
    }

    updateId () {
        this.id = this.el.getAttribute('x-wire');
    }

    updateData () {
        const el = this.getEl();

        if (!el.getAttribute('x-wire-data')) {
            return;
        }

        this.data = this.data || window.Alpine.reactive({});

        const data = JSON.parse(el.getAttribute('x-wire-data'));

        Object.entries(data).forEach(([key, value]) => {
            this.data[key] = value;
        });

        el.removeAttribute('x-wire-data');

        this.onDataUpdated();
    }

    onDataUpdated () {
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
        const $this = this;

        return function () {
            const payload = {
                fingerprint: $this.data.fingerprint,
                serverMemo: $this.data.serverMemo,
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
                        window.Alpine.morph($this.getEl(), response.html);
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

        // $refresh method call
        // Example: @click="$wire.$refresh()"
        if (prop === '$refresh') {
            return this.call('dollarRefresh');
        }

        // $set method call
        // Example: @click="$wire.$set('prop', 'value')"
        if (prop === '$set') {
            return this.call('dollarSet');
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
