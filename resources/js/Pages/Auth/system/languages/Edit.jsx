import { useMemo, useState } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import useTranslation from "../../../../hooks/useTranslation";

export default function Edit() {
    const { t } = useTranslation();
    const {
        pageTitle = "Translations",
        languageItem = {},
        defaultLanguage = {},
        translations = [],
    } = usePage().props;
    const [items, setItems] = useState(translations);
    const [saving, setSaving] = useState(false);

    const total = items.length;

    const updateValue = (key, value) => {
        setItems((current) =>
            current.map((item) => (item.key === key ? { ...item, value } : item))
        );
    };

    const done = useMemo(
        () => items.filter((item) => String(item.value ?? "").trim() !== "").length,
        [items]
    );

    const submit = () => {
        setSaving(true);
        router.post(
            route("system.languages.update", languageItem.id),
            { translations: items },
            {
                preserveScroll: true,
                onFinish: () => setSaving(false),
            }
        );
    };

    return (
        <AppLayout
            title={pageTitle}
            header={
                <div className="flex items-center justify-between gap-4">
                    <PageHeader>
                        <span>{t("Translations")}</span>
                        <span className="text-slate-400"> | </span>
                        <span className="text-red-600">{languageItem.name}</span>
                        <div className="mt-1 text-sm font-normal text-slate-500">{total}{t("Total")}</div>
                    </PageHeader>
                    <button
                        type="button"
                        onClick={submit}
                        disabled={saving}
                        className="inline-flex items-center gap-2 rounded-md bg-slate-900 px-5 py-3 text-sm font-bold uppercase tracking-wide text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <i className="far fa-save"></i>
                        {saving ? "Saving..." : "Save Translation"}
                    </button>
                </div>
            }
        >
            <Container>
                <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div className="flex items-center justify-between border-b border-slate-200 px-6 py-6">
                        <div className="flex items-center gap-4">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <i className="fas fa-language text-xl"></i>
                            </div>
                            <div>
                                <h1 className="text-lg font-bold text-slate-950">{t("Translations")}</h1>
                                <p className="text-sm text-slate-600">{languageItem.name}</p>
                            </div>
                        </div>
                        <Link
                            href={route("system.languages.index")}
                            className="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Back
                        </Link>
                    </div>
                    <div className="grid grid-cols-1 border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 md:grid-cols-2">
                        <div className="px-6 py-4">{defaultLanguage.name ?? "Default"}</div>
                        <div className="px-6 py-4">{languageItem.name}</div>
                    </div>
                    <div className="divide-y divide-slate-100">
                        {items.map((item) => (
                            <div key={item.key} className="grid grid-cols-1 gap-4 px-6 py-4 md:grid-cols-2 md:items-center">
                                <div>
                                    <div className="font-semibold text-slate-950">{item.source}</div>
                                    <div className="mt-2 text-xs font-bold uppercase tracking-wide text-slate-400">
                                        {item.key}
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    value={item.value ?? ""}
                                    onChange={(event) => updateValue(item.key, event.target.value)}
                                    className="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-100"
                                />
                            </div>
                        ))}
                    </div>
                    <div className="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4 text-sm text-slate-600">
                        <span>{done}{t("of")}{total}{t("translated")}</span>
                        <button
                            type="button"
                            onClick={submit}
                            disabled={saving}
                            className="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-700 disabled:opacity-60"
                        >
                            {saving ? "Saving..." : "Save Translation"}
                        </button>
                    </div>
                </div>
            </Container>
        </AppLayout>
    );
}
