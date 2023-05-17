import { Component } from "./Component.js";
import { OptionalProxy } from "./OptionalProxy.js";
import { updatePathFromUrl } from "./utils.js";

export class Livewire {
    constructor () {
        this.components = new Map();
        this.forceDataDirectiveToBody();
        this.updatePath();

        if (window.Alpine) {
            this.register();   
        } else {
            document.addEventListener('alpine:init', () => {
                this.register();
            });
        }
    }

    static create () {
        return new Livewire();
    }

    register () {
        this.registerWireDirective();
        this.registerWireDataDirective();
        this.registerWireMacicProperty();
        this.validate();
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

    createComponent(id, el) {
        if (!this.components.has(id)) {
            this.components.set(id, new Component(el));
        }
    }

    registerWireDirective () {
        window.Alpine.directive('wire', (el, { expression }) => {
            const id = expression;
            this.createComponent(id, el);
        });
    }

    registerWireDataDirective () {
        window.Alpine.directive('wire-data', (el) => {
            const updateData = () => {
                const id = el.getAttribute('x-wire');

                if (!this.components.has(id)) {
                    this.createComponent(id, el);
                }

                this.components.get(id).updateData();
            }

            updateData();

            // Try a second time, because sometimes the directive was not loaded yet
            setTimeout(() => {
                updateData();
            }, 10);
        });
    }

    registerWireMacicProperty () {
        window.Alpine.magic('wire', (el) => {
            if (el.__$wire) {
                return el.__$wire;
            }

            const wireEl = el.closest('[x-wire]');
    
            if (!wireEl) {
                console.error('Alpine: Cannot reference "$wire" outside a Livewire component.');
                return OptionalProxy;
            };

            const id = wireEl.getAttribute('x-wire');
    
            if (!this.components.has(id)) {
                console.error(`Alpine: Cannot reference "$wire" for Livewire component with id ${id}.`);
                return OptionalProxy;
            };

            return el.__$wire = this.components.get(id).$wire;
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
