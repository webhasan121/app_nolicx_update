import { usePage } from "@inertiajs/react";

export default function useTranslation() {
    const { language = {}, appConfig = {} } = usePage().props;
    const messages = language?.messages ?? {};
    const currencySymbol = appConfig?.currency?.symbol ?? "TK";

    const t = (key, replacements = {}) => {
        if (key === "TK" || key === "Tk") {
            return currencySymbol;
        }

        if (key === "(TK)") {
            return `(${currencySymbol})`;
        }

        if (key === "TK)") {
            return `${currencySymbol})`;
        }

        let value = messages[key] ?? key;

        Object.entries(replacements).forEach(([name, replacement]) => {
            value = value.replaceAll(`:${name}`, replacement);
        });

        return value;
    };

    return {
        t,
        current: language?.current ?? "en",
        available: language?.available ?? [],
        fixed: Boolean(language?.fixed),
    };
}
