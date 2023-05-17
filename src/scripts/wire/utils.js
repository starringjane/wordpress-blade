export function getPathFromUrl(url) {
    const urlObject = new URL(url);
    return urlObject.pathname + urlObject.search;
}

export function updatePathFromUrl(url) {
    const currentPath = getPathFromUrl(window.location.href);
    const newPath = getPathFromUrl(url);

    if (currentPath !== newPath) {
        window.history.replaceState({}, '', newPath);
    }
}
