export const OptionalProxy = new Proxy({}, {
    get: function () {
        return function () {
            return OptionalProxy;
        };
    }
});
