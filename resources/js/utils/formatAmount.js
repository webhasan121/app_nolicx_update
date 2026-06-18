export function formatAmount(value, decimals = 2) {
    const number = Number(value ?? 0);

    if (!Number.isFinite(number)) {
        return (0).toFixed(decimals);
    }

    return number.toFixed(decimals);
}

export function currencySymbol(fallback = "TK") {
    return window.__NOLIX_CURRENCY__?.symbol || fallback;
}

export function currencyCode(fallback = "BDT") {
    return window.__NOLIX_CURRENCY__?.code || fallback;
}

export function formatCurrency(value, decimals = 2) {
    return `${formatAmount(value, decimals)} ${currencySymbol()}`;
}

export function formatTk(value) {
    return formatCurrency(value);
}
