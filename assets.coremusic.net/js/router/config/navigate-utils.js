export function navigateOrRedirect(router, url) {
    if (url.startsWith('http://') || url.startsWith('https://')) {
        window.location.href = url;
        return;
    }
    router?.navigate(url);
}
