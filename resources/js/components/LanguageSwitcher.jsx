import { router } from "@inertiajs/react";
import useTranslation from "../hooks/useTranslation";

export default function LanguageSwitcher({ compact = false, className = "" }) {
    const { current, available, fixed = false } = useTranslation();

    if (!available.length) {
        return null;
    }

    const switchLanguage = (event) => {
        router.post(
            route("language.switch"),
            { locale: event.target.value },
            {
                preserveScroll: true,
                preserveState: false,
            }
        );
    };

    return (
        <label className={`inline-flex items-center gap-2 ${className}`}>
            {!compact ? (
                <span className="text-sm text-slate-600">
                    <i className="fas fa-language"></i>
                </span>
            ) : null}
            <select
                value={current}
                onChange={switchLanguage}
                disabled={fixed}
                className="h-9 rounded-md border border-slate-200 bg-white px-2 text-sm text-slate-700 shadow-sm focus:border-indigo-400 focus:ring-indigo-200"
                title="Language"
            >
                {available.map((language) => (
                    <option key={language.code} value={language.code}>
                        {language.icon ? `${language.icon} ` : ""}
                        {language.name}
                    </option>
                ))}
            </select>
        </label>
    );
}
