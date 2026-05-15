import { usePage } from "@inertiajs/react";

export default function useTranslation() {
    const { language = {} } = usePage().props;
    const messages = language?.messages ?? {};

    const t = (key, replacements = {}) => {
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
