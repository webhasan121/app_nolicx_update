export function formatAmount(value, decimals = 2) {
    const number = Number(value ?? 0);

    if (!Number.isFinite(number)) {
        return (0).toFixed(decimals);
    }

    return number.toFixed(decimals);
}

export function formatTk(value) {
    return `${formatAmount(value)} TK`;
}
